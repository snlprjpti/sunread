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
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductSearchRepository;

class ProductController extends BaseController
{
    protected $repository, $search_repository;

    public function __construct(Product $product, ProductRepository $repository, ProductSearchRepository $search_repository)
    {
        $this->model = $product;
        $this->model_name = "Product";
        $this->repository = $repository;
        $this->search_repository = $search_repository;

        $this->middleware('validate.website.host', ['only' => ['index']]);
        $this->middleware('validate.store.code', ['only' => ['index']]);

        parent::__construct($this->model, $this->model_name);
    }

    public function index(Request $request, int $category_id): JsonResponse
    {
        try
        {
            $request->validate([
                "page" => "numeric|min:1"
            ]);
            
            $data = $this->search_repository->getFilterProducts($request, $category_id);
            $fetched = collect($data["hits"]["hits"])->pluck("_source")->toArray();
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }

    public function filter(int $category_id): JsonResponse
    {
        try
        {
            $fetched = $this->search_repository->getFilterOptions($category_id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched,  $this->lang('fetch-list-success'));
    }
}
