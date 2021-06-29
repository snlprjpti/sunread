<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Category\Exceptions\CategoryAuthorizationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Category\Transformers\CategoryResource;
use Modules\Category\Repositories\CategoryRepository;
use Exception;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Transformers\List\CategoryResource as ListCategoryResource;
use Modules\Category\Repositories\CategoryValueRepository;
use Modules\Category\Rules\SlugUniqueRule;
use Modules\Category\Rules\WebsiteRule;
use Modules\Category\Transformers\CategoryValueResource;
use Modules\Category\Rules\ScopeRule;

class CategoryController extends BaseController
{
    protected $repository, $categoryValueRepository, $is_super_admin, $main_root_id;

    public function __construct(CategoryRepository $categoryRepository, Category $category, CategoryValueRepository $categoryValueRepository)
    {
        $this->repository = $categoryRepository;
        $this->categoryValueRepository = $categoryValueRepository;
        $this->model = $category;
        $this->model_name = "Category";

        $exception_statuses = [
            CategoryAuthorizationException::class => 403
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
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
            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request)],
                "website_id" => "sometimes|exists:websites,id"
            ]);
            $fetched = $this->repository->fetchAll(request: $request, callback: function() use($request) {
                return $this->model->whereWebsiteId($request->website_id);
            })->toTree();
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->listCollection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, array_merge([
                "slug" => [ "nullable", new SlugUniqueRule($request) ]
            ], $this->repository->getValidationRules($request)), function() use ($request) {
                return [
                    "slug" => $request->slug ?? $this->model->createSlug($request->name),
                    "scope" => "website",
                    "scope_id" => $request->website_id
                ];
            });

            if(isset($data["parent_id"])) if(strcmp(strval($this->model->find($data["parent_id"])->website_id), $data["website_id"]))
            throw ValidationException::withMessages(["website_id" => "Patent Category does not belong to this website"]);

            $created = $this->repository->create($data, function($created) use($data){
                $this->categoryValueRepository->createOrUpdate($data, $created);
                if(isset($data["channels"])) $created->channels()->sync($data["channels"]);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    public function attributes(Request $request): JsonResponse
    {
        try
        {
            $data = $request->validate([
                "category_id" => "required_with:scope,scope_id|exists:categories,id",
                "scope" => "required_with:category_id,scope_id|in:website,channel,store",
                "scope_id" => [ "required_with:scope,category_id", "integer", "min:1", new ScopeRule($request)]
            ]);

            $fetched = $this->repository->show($data);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang("fetch-success"));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, array_merge([
                "slug" => [ "nullable", new SlugUniqueRule($request, $id) ],
                "scope" => "required|in:website,channel,store",
                "scope_id" => [ "required", "integer", "min:1", new ScopeRule($request)]
            ], $this->repository->getValidationRules($request, "updated")), function() use ($request, $id) {
                return [
                    "slug" => $request->slug ?? $this->model->createSlug($request->name)
                ];
            });

            unset($data["website_id"], $data["parent_id"]);

            $updated = $this->repository->update($data, $id, function($updated) use($data){
                $this->categoryValueRepository->createOrUpdate($data, $updated);
                if(isset($data["channels"])) $updated->channels()->sync($data["channels"]);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $category = $this->model->findOrFail($id);

            $this->repository->delete($id, function($deleted){
                $deleted->values()->each(function($value){
                    $value->delete();
                });
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try
        {
            $updated = $this->repository->updateStatus($request, $id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("status-updated"));
    }
}
