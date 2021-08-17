<?php

namespace Modules\Product\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Transformers\StoreFront\PageResource;
use Modules\Product\Entities\Product;
use Modules\Product\Repositories\ProductSearchRepository;

class ProductFilterController extends BaseController
{
    protected $repository;

    public function __construct(Product $product, ProductSearchRepository $repository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $repository;

        parent::__construct($this->model, $this->model_name);
    }

    public function index($category_id, Request $request): JsonResponse
    {
        try
        {
            Cache::forget('product_filter_list');

            $fetched = Cache::rememberForever("product_filter_list", function() use($request, $category_id){
                $data = $this->repository->getFilterProduct($request, $category_id);
                return collect($data["hits"]["hits"])->pluck("_source")->toArray();
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function filter(Request $request): JsonResponse
    {
        try
        {
            $products = Cache::forget('product_filter_list');
            dd($products);
            // $fetched = $this->repository->getFilter($products);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }
}
