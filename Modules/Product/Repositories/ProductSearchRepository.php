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

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->attributeToRetrieve = [ "id", "parent_id", "brand_id", "attribute_group_id", "sku", "type", "created_at", "updated_at" ];
       
        $this->mainFilterKeys = [ "brand_id", "attribute_group_id", "type" ];
        $this->mainSearchKeys = [ "sku" ];

        $this->nestedFilterKeys = Attribute::where('is_filterable', 1)->pluck('slug')->toArray();
        $this->nestedSearchKeys = Attribute::where('is_searchable', 1)->pluck('slug')->toArray();;

        $this->categoryFilterKeys = [ "category_id", "category_slug" ];
    }

    public function search(object $request)
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

    public function getMainSearch($request)
    {
        array_push($this->allSearch, $this->queryString($this->mainSearchKeys, $request->search));
        foreach($this->mainSearchKeys as $key) array_push($this->allSearch, $this->match($key, $request->search));
    }

    public function getNestedSearch($request)
    {
        $this->nestedSearchKeys = array_map(function($item){
            return "product_attributes.$this->scope.$item.value" ;
        }, $this->nestedSearchKeys);
        array_push($this->nestedSearch, $this->queryString($this->nestedSearchKeys, $request->search));

        foreach($this->nestedSearchKeys as $key) array_push($this->nestedSearch, $this->match("product_attributes.$this->scope.$key.value", $request->search));

        if(count($this->nestedSearch)>0) array_push($this->allSearch,
        [
            "nested" => [
              "path" => "product_attributes.$this->path",
              "score_mode" => "avg",
              "query" => $this->orwhereQuery($this->nestedSearch)
            ]
        ]);
    }

    public function filter(object $request)
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

    public function getMainFilter($request)
    {
        foreach($this->mainFilterKeys as $key) if(isset($request->$key)) array_push($this->allFilter, $this->term($key, $request->$key));
    }

    public function getNestedFilter($request)
    {
        foreach($this->nestedFilterKeys as $key) if(isset($request->$key)) array_push($this->nestedFilter, $this->term("product_attributes.$this->scope.$key.value", $request->$key));

        if(count($this->nestedFilter)>0) array_push($this->allFilter, [
            "nested" => [
              "path" => "product_attributes.$this->path",
              "score_mode" => "avg",
              "query"=> $this->whereQuery($this->nestedFilter)
            ]
        ]);
    }

    public function getCategoryFilter($request)
    {
        foreach($this->categoryFilterKeys as $key) if(isset($request->$key)) array_push($this->allFilter, $this->term("categories.$this->storeORGlobal.".substr($key,9), $request->$key));
    }

    public function getProduct($request)
    {
        $this->scope = $this->getScope($request);
        $this->path = $this->getPath($request);
        $this->storeORGlobal = $this->getStore($request);
        $data =[];

        if(count($request->all())>0)
        {
            array_push($data, $this->search($request));
            array_push($data, $this->filter($request));
        }


        $source = array_merge($this->attributeToRetrieve, ["product_attributes.$this->scope", "categories.$this->storeORGlobal"]);

         $fetched = $this->model->searchRaw((count($data)>0) ? [
            "_source" => $source,
            "query"=> $this->whereQuery($data)
        ] : [
            "_source" => $source,
        ]);

        return $fetched;
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
