<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\Product;
use Modules\Product\Repositories\ProductSearchRepository;

class ProductSearchController extends BaseController
{
    protected $repository, $attributeToRetrieve;

    public function __construct(Product $product, ProductSearchRepository $repository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $repository;

        parent::__construct($this->model, $this->model_name);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->getProduct($request);
            $fetched["attribute_key"] = $this->repository->getScope($request);
            $fetched["category_key"] = $this->repository->getStore($request);
            $fetched["data"] = collect($data["hits"]["hits"])->pluck("_source");
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function reIndex(int $id): JsonResponse
    {
        try
        {
            $this->repository->reIndex($id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage("Product reindex successfully", 200);
    }

    public function bulkReIndex(Request $request): JsonResponse
    {
        try
        {
            $this->repository->bulkReIndex($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage("All products reindex successfully", 200);
    }
}
