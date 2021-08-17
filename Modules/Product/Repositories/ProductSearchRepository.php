<?php

namespace Modules\Product\Repositories;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\BulkIndexing;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class ProductSearchRepository extends ElasticSearchRepository
{
    use HasIndexing;

    protected $model, $mainFilterKeys, $attributeFilterKeys, $searchKeys, $staticFilterKeys, $sortByKeys;

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->mainFilterKeys = [ "brand_id", "attribute_set_id", "type", "website_id" ];

        $this->attributeFilterKeys = Attribute::where('use_in_layered_navigation', 1)->pluck('slug')->toArray();
        $this->searchKeys = Attribute::where('is_searchable', 1)->pluck('slug')->toArray();

        $this->sortByKeys = [ "sort_by_id", "sort_by_name", "sort_by_price" ];

        $this->staticFilterKeys = ["color", "size", "collection"];
    }

    public function search(object $request): array
    {
        try
        {
            $search = [];
            if(isset($request->q)) {
                $search[] = $this->queryString($this->searchKeys, $request->q);
                foreach($this->searchKeys as $key) $search[] = $this->match($key, $request->q);
            }
            $query = $this->orwhereQuery($search);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $query;
    }

    public function filterAndSort(?object $request = null, ?int $category_id = null): array
    {
        try
        {
            $filter = [];
            $sort = [];
    
            if($category_id) $filter[]= $this->term("categories.id", $category_id);
    
            if ($request && count($request->all()) > 0) {
                foreach($request->all() as $key => $value) 
                {
                    if (str_starts_with($key, 'sort_by_')) $sort = $this->sort(substr($key,8), $value ?? "asc");
                    if(in_array($key, $this->attributeFilterKeys) && $value) $filter[] = $this->term($key, $value);
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

    public function getFilterProducts(object $request, int $category_id): ?array
    {
        $filter = $this->filterAndSort($request, $category_id);
        return $this->finalQuery($filter["query"], $request->page ?? 1, $request->limit ?? 10, $filter["sort"]);
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
            $final_query = $this->finalQuery($query, $request->page ?? 1, $request->limit ?? 10, $filter["sort"]);      
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
                $aggregate["{$field}_value"] = $this->aggregate("{$field}_value");
            }         
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $aggregate;   
    }

    public function getFilterOptions(int $category_id): ?array
    {
        try
        {
            $filter = [];
            $data = $this->filterAndSort(category_id:$category_id);
            $aggregate = $this->aggregation();
    
            $query = [
                "size"=> 0,
                "query"=> (count($data["query"]) > 0) ? $data["query"] : [
                    "match_all"=> (object)[]
                ],
                "aggs"=> $aggregate
            ];
    
            $fetched = $this->searchIndex($query);
    
            foreach($this->staticFilterKeys as $field) 
            {
                $filter[$field] = collect($fetched["aggregations"][$field]["buckets"])->map(function($bucket, $key) use($fetched, $field) {
                    return [
                        "label" => $fetched["aggregations"]["{$field}_value"]["buckets"][$key]["key"],
                        "value" =>  $bucket["key"]  
                    ];
                });
            } 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $filter;
    }

    public function finalQuery(array $query, ?int $page = 1, ?int $limit = 10, ?array $sort = []): ?array
    {
        try
        {
            $fetched = [
                "from"=> ($page-1) * $limit,
                "size"=> $limit,
                "query"=> (count($query) > 0) ? $query : [
                    "match_all"=> (object)[]
                ],
                "sort" => (count($sort) > 0) ? $sort : [
                    ["id" => ["order" => "asc", "mode" => "avg"]]
                ],
            ];
            $data =  $this->searchIndex($fetched);     
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function reIndex(int $id, ?callable $callback = null): object
    {
        try
        {
            $indexed = $this->model->findOrFail($id);
			if ($callback) $callback($indexed);
            $stores = Website::find($indexed->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });
    
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
            BulkIndexing::dispatch($indexed);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $indexed;
    }
}
