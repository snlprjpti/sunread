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

    protected $model; 
    protected $mainFilterKeys, $attributeFilterKeys, $categoryFilterKeys, $searchKeys, $staticFilterKeys;
    protected  $allFilter = [], $allSearch = [];

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->mainFilterKeys = [ "brand_id", "attribute_set_id", "type", "website_id" ];

        $this->attributeFilterKeys = Attribute::where('use_in_layered_navigation', 1)->pluck('slug')->toArray();
        $this->searchKeys = Attribute::where('is_searchable', 1)->pluck('slug')->toArray();

        $this->categoryFilterKeys = [ "category_id", "category_slug" ];

        $this->staticFilterKeys = ["colouruy", "visibility"];
    }

    public function search(object $request): array
    {
        try
        {
            if(isset($request->search)) $this->getMainSearch($request);   
            $query = $this->orwhereQuery($this->allSearch);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $query;
    }

    public function getMainSearch($request): void
    {
        array_push($this->allSearch, $this->queryString($this->searchKeys, $request->search));
        foreach($this->searchKeys as $key) array_push($this->allSearch, $this->match($key, $request->search));
    }

    public function filter(object $request, ?int $category_id = null): array
    {
        try
        {
            $this->getMainFilter($request);

            $this->getAttributeFilter($request);

            if($category_id) $this->getCategoryFilter($category_id);

            $query = $this->whereQuery($this->allFilter);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    
        return $query;
    }

    public function getMainFilter($request): void
    {
        foreach($this->mainFilterKeys as $key) if(isset($request->$key)) array_push($this->allFilter, $this->term($key, $request->$key));
    }

    public function getAttributeFilter($request): void
    {
        foreach($this->attributeFilterKeys as $key) {
            if(isset($request->$key)) array_push($this->allFilter, $this->term($key, $request->$key));
        }
    }

    public function getCategoryFilter(int $category_id): void
    {
        array_push($this->allFilter, $this->term("categories.id", $category_id));
    }

    public function getProduct($request): ?array
    {
        $data =[];

        if(count($request->all()) > 0)
        {
            array_push($data, $this->search($request));
            array_push($data, $this->filter($request));
        }

        return $this->finalQuery($data);
    }

    public function getFilterProduct(object $request, int $category_id): ?array
    {
        $data =[];

        if(count($request->all()) > 0 || $category_id) array_push($data, $this->filter($request, $category_id));

        return $this->finalQuery($data);
    }

    public function finalQuery(array $data): ?array
    {
        $fetched = [
            "size"=> 500,
            "query"=> (count($data) > 0) ? $this->whereQuery($data) : [
                "match_all"=> (object)[]
            ],
            "sort" => [
                ["id" => ["order" => "asc", "mode" => "avg"]]
            ],
        ];

        return $this->searchIndex($fetched);
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

    public function getFilter(array $products): array
    {
        $filter = [];
        foreach($this->staticFilterKeys as $key) {           
            $options = [];
            foreach($products as $product) {
                $array_options = [];
                if(!isset($product[$key])) continue;

                if(is_array($product[$key])) {
                    $array_options[] = collect($product[$key])->map(function($value, $i) use($key, $product) {
                        $option_key = "{$key}_{$i}_value";
                        if(isset($product[$option_key])) return $this->getOption($value, $product[$option_key]);
                    })->toArray();
                    
                    $options = array_merge($options, collect($array_options)->flatten(1)->toArray());
                }
                else  {
                    $option_key = "{$key}_value";
                    if(isset($product[$option_key])) $options[] = $this->getOption($product[$key], $product[$option_key]);
                }
            }   
            $filter[$key] = array_unique($options, SORT_REGULAR);
        }
        return $filter;
    }

    public function getOption(mixed $value, string $label): array
    {
        return [
            "value" => $value,
            "label" => $label
        ];
    }

}
