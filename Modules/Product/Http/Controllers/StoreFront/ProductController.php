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
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductSearchRepository;

class ProductController extends BaseController
{
    protected $repository, $search_repository, $category_repository;

    public function __construct(Product $product, ProductRepository $repository, ProductSearchRepository $search_repository, CategoryRepository $category_repository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $repository;
        $this->search_repository = $search_repository;
        $this->category_repository = $category_repository;

        $this->middleware('validate.website.host')->only(['index', 'filter']);
        $this->middleware('validate.store.code')->only(['index', 'filter']);

        parent::__construct($this->model, $this->model_name);
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
}
