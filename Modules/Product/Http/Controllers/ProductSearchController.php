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
        return $this->successResponse([],  $this->lang('fetch-list-success'));
    }

    public function productWithFilters(Request $request): JsonResponse
    {
        $filter = isset($request->channel_id) ? "channel.$request->channel_id" : (isset($requeststore_id) ? "store.$requeststore_id" : "global"); 
        $fetched = $this->model->searchRaw([
           "_source" => ["type", "product_attributes.$filter", "sku", "brand_id", "attribute_group_id"]
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
