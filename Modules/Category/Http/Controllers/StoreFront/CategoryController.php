<?php

namespace Modules\Category\Http\Controllers\StoreFront;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Category\Transformers\CategoryResource;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Category\Transformers\List\CategoryResource as ListCategoryResource;
use Modules\Category\Repositories\CategoryValueRepository;
use Modules\Category\Repositories\StoreFront\CategoryRepository;
use Modules\Category\Rules\CategoryScopeRule;
use Modules\Category\Rules\SlugUniqueRule;
use Modules\Core\Entities\Website;
use Modules\Core\Rules\ScopeRule;
use Modules\Product\Entities\Product;

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

    public function listCollection(object $data): ResourceCollection
    {
        return ListCategoryResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CategoryResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->getData($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse([], $this->lang("fetch-list-success"));
    }
}