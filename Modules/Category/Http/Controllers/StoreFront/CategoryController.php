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
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
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

        $this->middleware('validate.website.host')->only(['index']);
        $this->middleware('validate.channel.code')->only(['index']);
        $this->middleware('validate.store.code')->only(['index']);

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
            $fetched = $this->repository->getMenu($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang("fetch-list-success"));
    }
}