<?php

namespace Modules\Category\Http\Controllers\StoreFront;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Controllers\BaseController;
use Exception;
use Modules\Category\Repositories\StoreFront\CategoryRepository;
use Modules\Category\Transformers\StoreFront\CategoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Facades\Resolver;
use Modules\Core\Facades\SiteConfig;

class CategoryController extends BaseController
{
    protected $repository;

    public function __construct(CategoryRepository $categoryRepository, Category $category)
    {
        $this->repository = $categoryRepository;
        $this->model = $category;
        $this->model_name = "Category";

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CategoryResource::collection($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $website = Resolver::fetch($request);
            $fetched["categories"] = $this->collection($this->repository->getCategories($website));
            $fetched["logo"] = $this->repository->getLogo($website);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang("fetch-list-success"));
    }
}