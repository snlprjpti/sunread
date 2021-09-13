<?php

namespace Modules\Product\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Repositories\StoreFront\CategoryRepository;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Transformers\StoreFront\PageResource;
use Modules\Product\Entities\Product;
use Modules\Product\Exceptions\ProductNotFoundIndividuallyException;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductSearchRepository;
use Modules\Product\Repositories\StoreFront\ProductRepository as StoreFrontProductRepository;

class ProductController extends BaseController
{
    protected $repository, $search_repository, $category_repository, $store_fornt_repository;

    public function __construct(Product $product, ProductRepository $repository, ProductSearchRepository $search_repository, CategoryRepository $category_repository, StoreFrontProductRepository $store_fornt_repository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $repository;
        $this->search_repository = $search_repository;
        $this->category_repository = $category_repository;
        $this->store_fornt_repository = $store_fornt_repository;

        $this->middleware('validate.website.host')->only(['index', 'filter', 'show']);
        $this->middleware('validate.store.code')->only(['index', 'filter', 'show']);
        $this->middleware('validate.channel.code')->only(['show']);

        $exception_statuses = [
            ProductNotFoundIndividuallyException::class => 404
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function index(Request $request, string $category_slug): JsonResponse
    {
        try
        {
            $fetched = [];
            $request->validate([
                "page" => "numeric|min:1",
                "color" => "sometimes|array",
                "color.*" => "sometimes|exists:attribute_options,id",
                "size" => "sometimes|array",
                "size.*" => "sometimes|exists:attribute_options,id",
                "collection" => "sometimes|array",
                "collection.*" => "sometimes|exists:attribute_options,id",
            ]);
            
            $store = $this->search_repository->getStore($request);
            $category = CategoryValue::whereAttribute("slug")->whereValue($category_slug)->firstOrFail();
            $fetched = $this->search_repository->getFilterProducts($request, $category->category_id, $store);
            $fetched["categories"] = $this->category_repository->getPages($category->category_id, $store);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function filter(Request $request, string $category_slug): JsonResponse
    {
        try
        {
            $store = $this->search_repository->getStore($request);
            $category = CategoryValue::whereAttribute("slug")->whereValue($category_slug)->firstOrFail();
            $fetched = $this->search_repository->getFilterOptions($category->category_id, $store);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function show(Request $request, string $sku): JsonResponse
    {
        try
        {
            $data = $this->store_fornt_repository->productDetail($request, $sku);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($data, $this->lang('fetch-success'));
    }
}
