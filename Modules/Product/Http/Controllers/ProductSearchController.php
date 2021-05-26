<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\Product;

class ProductSearchController extends BaseController
{
    protected $repository;

    public function __construct(Product $product)
    {
        $this->model = $product;
        $this->model_name = "Product";

        parent::__construct($this->model, $this->model_name);
    }

    public function search(Request $request): JsonResponse
    {
        $fetched = $this->model->searchRaw([
            'query' => [
            'bool' => [
                'must' => [
                    ['match' => ['sku' => $request->search ]]
                ]
            ]
            ]
        ]); 
        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function productWithFilters(Request $request): JsonResponse
    {
        $filter = isset($request->store_id) ? "store.$request->store_id" : (isset($request->channel_id) ? "channel.$request->channel_id" : "global"); 

        $fetched = $this->model->searchRaw([
           "_source" => ["id","parent_id","brand_id","attribute_group_id","sku","type","created_at","updated_at", "categories","channels", "product_attributes.$filter"],
           'query' => [
                'bool' => [
                    'must' => [
                        [
                            "match" => [
                                "sku" => isset($request->sku) ? $request->sku : "",
                            ]
                        ]
                    ]
                ],
                "nested" => [
                    "path" => "product_attributes",
                    "query" => [
                        'bool' => [
                            'must' => [
                                [
                                    "match" => [
                                        "product_attributes.$filter.slug.attribute.value" => $request->slug,
                                        // "product_attributes.$filter.name" => $request->name,
                                        // "product_attributes.$filter.price" => $request->price,
                                    ]
                                ]
                            ]
                        ],
                    ]
                ] 
            ]
        ]);
        $fetched = collect($fetched["hits"]["hits"]);
        
        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function reIndex($id)
    {
        try
        {
            $this->repository->reIndex($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse([], "", 200);
    }

    public function bulkReIndex(Request $request)
    {
        try
        {
            $this->repository->bulkReIndex($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse([], "", 200);
    }
}
