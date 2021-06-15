<?php

namespace Modules\Product\Repositories;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\Product;

class ProductSearchRepository extends ElasticSearchRepository
{
    protected $model, $attributeToRetrieve; 
    protected $mainFilterKeys, $nestedFilterKeys, $categoryFilterKeys, $mainSearchKeys, $nestedSearchKeys;
    protected  $allFilter = [], $nestedFilter = [], $allSearch = [], $nestedSearch = [];
    protected $nestedSearchQuery = [], $nestedFilterQuery = [];

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->attributeToRetrieve = [ "id", "parent_id", "brand_id", "attribute_group_id", "sku", "type", "created_at", "updated_at" ];
       
        $this->mainFilterKeys = [ "brand_id", "attribute_group_id", "type" ];
        $this->mainSearchKeys = [ "sku" ];

        $this->nestedFilterKeys = Attribute::where('use_in_layered_navigation', 1)->pluck('type', 'slug')->toArray();
        $this->nestedSearchKeys = Attribute::where('is_searchable', 1)->pluck('type', 'slug')->toArray();

        $this->categoryFilterKeys = [ "category_id", "category_slug" ];
    }

    public function search(object $request): array
    {
        try
        {
            if(isset($request->search))
            {
                $this->getMainSearch($request);
                $this->getNestedSearch($request);
            }
            
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
        array_push($this->allSearch, $this->queryString($this->mainSearchKeys, $request->search));
        foreach($this->mainSearchKeys as $key) array_push($this->allSearch, $this->match($key, $request->search));
    }

    public function getNestedSearch($request): void
    {
        foreach($this->nestedSearchKeys as $key => $value){
            $type = $this->getModelType($value);

            array_push($this->nestedSearch, $this->term("product_attributes.$this->scope.attribute.slug", $key));
            array_push($this->nestedSearch, $this->match("product_attributes.$this->scope.{$type}_value", $request->search));
            array_push($this->nestedSearchQuery, $this->whereQuery($this->nestedSearch));
            $this->nestedSearch = [];
        } 

        if(count($this->nestedSearchQuery) > 0) array_push($this->allSearch,
        [
            "nested" => [
              "path" => "product_attributes.$this->path",
              "score_mode" => "avg",
              "query" => $this->orwhereQuery($this->nestedSearchQuery)
            ]
        ]);
    }

    public function filter(object $request): array
    {
        try
        {
            $this->getMainFilter($request);

            $this->getNestedFilter($request);

            $this->getCategoryFilter($request);
            
            if(isset($request->max_price) && isset($request->min_price) ) array_push($this->nestedFilter, $this->range("product_attributes.$this->scope.price.value", $request->min_price, $request->max_price));

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

    public function getNestedFilter($request): void
    {
        $this->nestedFilterKeys = array_intersect_key($this->nestedFilterKeys, $request->toArray());

        foreach($this->nestedFilterKeys as $key => $value){
            $type = $this->getModelType($value);

            array_push($this->nestedFilter, $this->term("product_attributes.$this->scope.attribute.slug", $key));
            array_push($this->nestedFilter, $this->term("product_attributes.$this->scope.{$type}_value", $request->$key));
            array_push($this->nestedFilterQuery, $this->whereQuery($this->nestedFilter));
            
            $this->nestedFilter = [];
        } 
        if(count($this->nestedFilterQuery) > 0) array_push($this->allFilter, [
            "nested" => [
              "path" => "product_attributes.$this->path",
              "score_mode" => "avg",
              "query"=> $this->whereQuery($this->nestedFilterQuery)
            ]
        ]);
    }

    public function getCategoryFilter($request): void
    {
        foreach($this->categoryFilterKeys as $key) if(isset($request->$key)) array_push($this->allFilter, $this->term("categories.$this->storeORGlobal.".substr($key,9), $request->$key));
    }

    public function getProduct($request): array
    {
        $this->scope = $this->getScope($request);
        $this->path = $this->getPath($request);
        $this->storeORGlobal = $this->getStore($request);
        $data =[];

        if(count($request->all()) > 0)
        {
            array_push($data, $this->search($request));
            array_push($data, $this->filter($request));
        }

        $source = array_merge($this->attributeToRetrieve, ["product_attributes.$this->scope.attribute", "product_attributes.$this->scope.value", "categories.$this->storeORGlobal"]);

         $fetched = $this->model->searchRaw([
            "size"=> 500,
            "_source" => $source,
            "query"=> (count($data) > 0) ? $this->whereQuery($data) : [
                "match_all"=> (object)[]
            ],
            "sort" => [
                ["id" => ["order" => "asc", "mode" => "avg"]]
            ],
        ]);

        return $fetched;
    }

    public function getModelType(string $value): string
    {
        $config = config("attribute_types.{$value}");
        $model = new $config();
        return $model::$type; 
    }

    public function reIndex(int $id, ?callable $callback = null): object
    {
        try
        {
            $indexed = $this->model->findOrFail($id);
			if ($callback) $callback($indexed);
            $indexed->searchable();
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
            $indexed->map(function($item){
                $item->searchable();
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $indexed;
    }

}
