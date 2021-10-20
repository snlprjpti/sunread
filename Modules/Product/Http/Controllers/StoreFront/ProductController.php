<?php

namespace Modules\Product\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Exceptions\CategoryNotFoundException;
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

        $this->middleware('validate.website.host');
        $this->middleware('validate.store.code');
        $this->middleware('validate.channel.code');

        $exception_statuses = [
            ProductNotFoundIndividuallyException::class => 404,
            CategoryNotFoundException::class => 404
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function index(Request $request, string $category_slug): JsonResponse
    {
        try
        {
            $request->validate([
                "page" => "numeric|min:1",
                "color" => "sometimes|array",
                "color.*" => "sometimes|exists:attribute_options,id",
                "size" => "sometimes|array",
                "size.*" => "sometimes|exists:attribute_options,id",
                "collection" => "sometimes|array",
                "collection.*" => "sometimes|exists:attribute_options,id",
            ]);

            $fetched = $this->store_fornt_repository->categoryWiseProduct($request, $category_slug);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function category(Request $request, string $category_slug): JsonResponse
    {
        try
        {
            $fetched = $this->store_fornt_repository->getCategoryData($request, $category_slug);
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
            $fetched = $this->store_fornt_repository->getOptions($request, $category_slug);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function show(Request $request, mixed $identifier): JsonResponse
    {
        try
        {
            $data = $this->store_fornt_repository->productDetail($request, $identifier);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($data, $this->lang('fetch-success'));
    }

    public function variantShow(Request $request, int $parent_identifier, int $identifier): JsonResponse
    {
        try
        {
            $data = $this->store_fornt_repository->productDetail($request, $identifier, $parent_identifier);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($data, $this->lang('fetch-success'));
    }

    public function search(Request $request): JsonResponse
    {
        try
        {
            $data = $this->store_fornt_repository->searchWiseProduct($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($data, $this->lang('fetch-success'));
    }
}
