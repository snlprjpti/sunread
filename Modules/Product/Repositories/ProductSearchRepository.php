<?php

namespace Modules\Product\Repositories;

use Exception;
use Modules\Product\Entities\Product;

class ProductSearchRepository extends ElasticSearchRepository
{
    protected $model, $attributeToRetrieve;

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->attributeToRetrieve = [
            "id", "parent_id", "brand_id", "attribute_group_id", "sku", "type", "created_at", "updated_at", "categories", "channels"
          ];
    }

    public function search(object $request)
    {
        try
        {
            $nested_filter = isset($request->search) ? [
                $this->queryString(["product_attributes.$this->scope.slug.value"], $request->search),
                $this->match("product_attributes.$this->scope.slug.value", $request->search)
            ] : [];
            $all_filter = isset($request->search) ? [
                $this->queryString([ "sku" ], $request->search),
                $this->match("sku", $request->search),
                [
                    "nested" => [
                      "path" => "product_attributes.$this->path",
                      "score_mode" => "avg",
                      "query"=> $this->orwhereQuery($nested_filter)
                    ]
                ],
            ] : [];

            $query = $this->orwhereQuery($all_filter);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $query;
    }

    public function filter(object $request)
    {
        try
        {
            $all_filter = [];
            $nested_filter = [];

            if(isset($request->attribute_group_id)) array_push($all_filter, $this->term("attribute_group_id", $request->attribute_group_id));
            
            if(isset($request->category_id)) array_push($all_filter, $this->term("categories.$request->category_id.$this->storeORGlobal.id", $request->category_id));

            if(isset($request->max_price) && isset($request->min_price) ) array_push($nested_filter, $this->range("product_attributes.$this->scope.price.value", $request->min_price, $request->max_price));

            if(isset($nested_filter)) array_push($all_filter, [
                    "nested" => [
                      "path" => "product_attributes.$this->path",
                      "score_mode" => "avg",
                      "query"=> $this->whereQuery($nested_filter)
                    ]
                ]);

            $query = $this->whereQuery($all_filter);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    
        return $query;
    }

    public function getProduct($request)
    {
        $this->scope = $this->getScope($request);
        $this->path = $this->getPath($request);
        $this->storeORGlobal = $this->getStore($request);
        $data =[];

        array_push($data, $this->search($request));
        array_push($data, $this->filter($request));

         $fetched = $this->model->searchRaw([
            "_source" => array_merge($this->attributeToRetrieve, ["product_attributes.$this->scope"]),
            "query"=> $this->whereQuery($data)
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
