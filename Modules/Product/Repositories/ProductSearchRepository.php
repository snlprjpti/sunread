<?php

namespace Modules\Product\Repositories;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\PriceFormat;
use Modules\Core\Facades\SiteConfig;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\BulkIndexing;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class ProductSearchRepository extends ElasticSearchRepository
{
    use HasIndexing;

    protected $model, $mainFilterKeys, $attributeFilterKeys, $searchKeys, $staticFilterKeys, $sortByKeys, $listSource;

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->mainFilterKeys = [ "brand_id", "attribute_set_id", "type", "website_id" ];

        $this->attributeFilterKeys = Attribute::where('use_in_layered_navigation', 1)->pluck('slug')->toArray();
        $this->searchKeys = Attribute::where('is_searchable', 1)->pluck('slug')->toArray();

        $this->sortByKeys = [ "sort_by_id", "sort_by_name", "sort_by_price" ];

        $this->staticFilterKeys = ["color", "size", "collection", "configurable_size", "configurable_color"];

        $this->listSource = [ "id", "parent_id", "website_id", "name", "sku", "type", "is_in_stock", "stock_status_value", "url_key", "quantity", "visibility", "visibility_value", "price", "special_price", "special_from_date", "special_to_date", "new_from_date", "new_to_date", "base_image", "thumbnail_image", "color", "color_value"];
    }

    public function search(object $request): array
    {
        try
        {
            $this->searchKeys = array_unique(array_merge($this->searchKeys, ["name", "sku", "description"]));
            $search = [];

            if(isset($request->q)) {
                // $search[] = $this->queryString($this->searchKeys, $request->q);
                foreach($this->searchKeys as $key) $search[] = $this->wildcard($key, $request->q);
            }
            $query = $this->orwhereQuery($search);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return [
            "query" => $query,
            "sort" => []
        ];
    }

    public function filterAndSort(?object $request = null, ?int $category_id = null): array
    {
        try
        {
            $filter = [];
            $sort = [];
    
            if($category_id) $filter[]= $this->term("categories.id", $category_id);
    
            if ($request && count($request->all()) > 0) {
                if ($request->sort_by) $sort = $this->sort($request->sort_by, $request->sort_order ?? "asc");
                
                foreach($request->all() as $key => $value) 
                {
                    if(in_array($key, $this->attributeFilterKeys) && $value) {
                        if($key == "size" || $key == "color") {
                            $size = [$this->terms("configurable_{$key}", $value), $this->terms($key, $value)];
                            $filter[] = $this->OrwhereQuery($size); 
                        }
                        else $filter[] = $this->terms($key, $value);
                    } 
                }
            }
    
            $query = $this->whereQuery($filter);   
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return [
            "query" => $query,
            "sort" => $sort
        ];
    }

    public function getStore(object $request): object
    {
        $website = Website::whereHostname($request->header("hc-host"))->firstOrFail();
        $store = Store::whereCode($request->header("hc-store"))->firstOrFail();
        
        if($store->channel->website->id != $website->id) throw ValidationException::withMessages(["hc-store" => "Store does not belong to this website"]);
        return $store;
    }

    public function getFilterProducts(object $request, int $category_id, object $store): ?array
    {
        $filter = $this->filterAndSort($request, $category_id);
        return $this->getProductWithPagination($filter, $request, $store);
    }

    public function getSearchProducts(object $request, object $store): ?array
    {
        $filter = $this->search($request);
        return $this->getProductWithPagination($filter, $request, $store);
    }

    public function getProductWithPagination(array $filter, object $request, object $store): ?array
    {
        try
        {
            $data = [];
            $data = $this->finalQuery($filter, $request, $store);
            $total = isset($data["products"]["hits"]["total"]["value"]) ? $data["products"]["hits"]["total"]["value"] : 0;
            $products = isset($data["products"]["hits"]["hits"]) ? collect($data["products"]["hits"]["hits"])->pluck("_source")->toArray() : [];
            $data["products"] = $this->productWithPriceFormat($products, $store);
            $data["last_page"] = (int) ceil($total/$data["limit"]);
            $data["total"] = $total;    
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data; 
    }

    public function productWithPriceFormat(array $products, object $store): array
    {
        try
        {
            $today = date('Y-m-d');
            $currentDate = date('Y-m-d H:m:s', strtotime($today));
            foreach($products as &$product)
            {
                $product["price_formatted"] = isset($product["price"]) ? PriceFormat::get($product["price"], $store->id, "store") : null;

                if(isset($product["special_price"])) {
                    if(isset($product["special_from_date"])) $fromDate = date('Y-m-d H:m:s', strtotime($product["special_from_date"]));
                    if(isset($product["special_to_date"])) $toDate = date('Y-m-d H:m:s', strtotime($product["special_to_date"])); 
                    if(!isset($fromDate) && !isset($toDate)) $product["special_price_formatted"] = PriceFormat::get($product["special_price"], $store->id, "store");
                    else $product["special_price_formatted"] = (($currentDate >= $fromDate) && ($currentDate <= $toDate)) ? PriceFormat::get($product["special_price"], $store->id, "store") : null;
                }
                else {
                    $product["special_price"] = null;
                    $product["special_price_formatted"] = null;
                }

                if(isset($product["new_from_date"]) && isset($product["new_to_date"])) { 
                    if(isset($product["new_from_date"])) $fromNewDate = date('Y-m-d H:m:s', strtotime($product["new_from_date"]));
                    if(isset($product["new_to_date"])) $toNewDate = date('Y-m-d H:m:s', strtotime($product["new_to_date"])); 
                    if(isset($fromNewDate) && isset($toNewDate)) $product["is_new_product"] = (($currentDate >= $fromNewDate) && ($currentDate <= $toNewDate)) ? 1 : 0;
                }
                
                $product["image"] = isset($product["thumbnail_image"]) ? $product["thumbnail_image"] : $product["base_image"];
                $product["quantity"] = (int) $product["quantity"];
                $product["color"] = isset($product["color"]) ? $product["color"] : null;
                $product["color_value"] = isset($product["color_value"]) ? $product["color_value"] : null;
                unset($product["thumbnail_image"], $product["base_image"]);      
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $products;
    }

    public function getProduct(object $request): ?array
    {
        try
        {
            $data = [];
            $data[] = $this->search($request);
    
            $filter = $this->filterAndSort($request);
            $data[] = $filter["query"];
    
            $query = $this->whereQuery($data);
            $final_query = [];      
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $final_query;
    }

    public function aggregation(): ?array
    {
        try
        {
            $aggregate = [];
            foreach($this->staticFilterKeys as $field) 
            {
                $aggregate[$field] = $this->aggregate($field);
                // $aggregate["{$field}_value"] = $this->aggregate("{$field}_value");
            }         
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $aggregate;   
    }

    public function getFilterOptions(int $category_id, object $store): ?array
    {
        try
        {
            $filters = [];
            $data = $this->filterAndSort(category_id:$category_id);
            $aggregate = $this->aggregation();

            $final_l[] = $data["query"];
            $final_l[] = $this->term("list_status", 1);
            $final_q = $this->whereQuery($final_l);
    
            $query = [
                "size"=> 0,
                "query"=> $final_q,
                "aggs"=> $aggregate
            ];
    
            $fetched = $this->searchIndex($query, $store);

            $fetched["aggregations"]["size"]["buckets"] = array_merge($fetched["aggregations"]["size"]["buckets"], $fetched["aggregations"]["configurable_size"]["buckets"]);
            $fetched["aggregations"]["color"]["buckets"] = array_merge($fetched["aggregations"]["color"]["buckets"], $fetched["aggregations"]["configurable_color"]["buckets"]);
            
            foreach(["color", "size", "collection"] as $field) 
            {
                $state = [];
                $filter["label"] = Attribute::whereSlug($field)->first()?->name;
                $filter["name"] = $field;
                $filter["values"] = collect($fetched["aggregations"][$field]["buckets"])->map(function($bucket) use(&$state) {
                    if(!in_array($bucket["key"], $state)) {
                        $state[] = $bucket["key"];
                        return [
                            "name" => AttributeOption::find($bucket["key"])?->name,
                            "value" =>  $bucket["key"]  
                        ];
                    }
                })->filter()->values();
                $filters[] = $filter;

            } 

            $filters[] = [
                "label" => "Sort By",
                "name" => "sort_by",
                "values" => [
                    [
                        "label" => "Name",
                        "name" => "name",
                        "value" =>  "asc"
                    ],
                    [
                        "label" => "Price",
                        "name" => "price",
                        "value" =>  "asc"
                    ]
                ]
                
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $filters;
    }

    public function finalQuery(array $filter, object $request, object $store): ?array
    {
        try
        {
            $page = $request->page ?? 1;
            $limit = SiteConfig::fetch("pagination_limit", "global", 0) ?? 10;

            $final_l[] = $filter["query"];
            $final_l[] = $this->term("list_status", 1);
            $final_q = $this->whereQuery($final_l);

            $fetched = [
                "_source" => $this->listSource,
                "from"=> ($page-1) * $limit,
                "size"=> $limit,
                "query"=> $final_q,
                "sort" => (count($filter["sort"]) > 0) ? $filter["sort"] : [
                    ["id" => ["order" => "asc", "mode" => "avg"]]
                ],
            ];

            $data =  $this->searchIndex($fetched, $store);     
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return [
            "products" => $data,
            "current_page" => (int) $page,
            "limit" => (int) $limit
        ];
    }

    public function reIndex(int $id, ?callable $callback = null): object
    {
        try
        {
            $indexed = $this->model->findOrFail($id);
			if ($callback) $callback($indexed);
            $stores = Website::find($indexed->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);
    
            foreach($stores as $store) SingleIndexing::dispatch($indexed, $store);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $indexed;
    }

    public function bulkReIndex(object $request, ?callable $callback = null): object
    {
        try
        {
            $request->validate([
                'ids' => 'array|required',
                'ids.*' => 'required',
            ]);

            $indexed = $this->model->whereIn('id', $request->ids)->get();
			if ($callback) $callback($indexed);
            // BulkIndexing::dispatch($indexed);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $indexed;
    }
}
