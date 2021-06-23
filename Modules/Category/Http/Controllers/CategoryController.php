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
use Modules\Category\Repositories\CategoryValueRepository;
use Modules\Category\Rules\SlugUniqueRule;
use Modules\Category\Rules\WebsiteRule;
use Modules\Core\Rules\ScopeRule;

class CategoryController extends BaseController
{
    protected $repository, $categoryValueRepository, $is_super_admin, $main_root_id;

    public function __construct(CategoryRepository $categoryRepository, Category $category, CategoryValueRepository $categoryValueRepository)
    {
        $this->repository = $categoryRepository;
        $this->categoryValueRepository = $categoryValueRepository;
        $this->model = $category;
        $this->model_name = "Category";
        $this->is_super_admin = auth()->guard("admin")->check() ? auth()->guard("admin")->user()->hasRole("super-admin") : false;
        $this->main_root_id = $this->model::oldest("id")->first()->id;

        $exception_statuses = [
            CategoryAuthorizationException::class => 403
        ];

        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function collection(object $data): ResourceCollection
    {
        return CategoryResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CategoryResource($data);
    }

    private function blockCategoryAuthority(?int $parent_id, ?int $main_root_id = null): bool
    {
        $parent_id_authority = (!$this->is_super_admin && $parent_id == $this->main_root_id);
        $main_root_id_authority = $main_root_id ? $main_root_id == $this->main_root_id : false;

        if ( $parent_id_authority || $main_root_id_authority ) throw new CategoryAuthorizationException("Action not authorized.");

        return false;
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = [];

            $data = $request->validate([
                "scope" => "required|in:website,channel,store",
                "scope_id" => [ "required", "integer", "min:1", new ScopeRule($request->scope)]
            ]);

            foreach($this->model->get()->toTree() as $category)
            {
                $fetched[] = $this->repository->treeWiseList($data, $category);
            }

            // Dont fetch root category for other admin
            //if (!$this->is_super_admin) $fetched = $fetched->where("parent_id", "<>", null);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            // $this->blockCategoryAuthority($request->parent_id);

            $rules = [];

            $data = $this->repository->validateData($request, [
                "slug" => [ "nullable", new SlugUniqueRule($request) ],
                "scope" => "sometimes|in:website",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope)]
            ], function() use ($request) {
                return [
                    "slug" => $request->slug ?? $this->model->createSlug($request->name),
                    "scope" => "website",
                    "scope_id" => $request->website_id
                ];
            });
            if(isset($data["parent_id"])) if(!strcmp(strval($this->model->find($data["parent_id"])->website_id), $data["website_id"]))
            throw ValidationException::withMessages(["website_id" => "Patent Category does not belong to this website"]);

            $data["image"] = $this->storeImage($request, "image", strtolower($this->model_name));

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

    public function show(int $id): JsonResponse
    {
        try
        {
            $category = $this->model->findOrFail($id);
            $this->blockCategoryAuthority($category->parent_id, $id);

            $fetched = $this->model->with(["translations"])->findOrFail($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang("fetch-success"));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            // $this->blockCategoryAuthority($request->parent_id, $id);

            $rules = [];

            $data = $this->repository->validateData($request, array_merge($rules, [
                "slug" => [ "nullable", new SlugUniqueRule($request) ],
                "image" =>  "sometimes|nullable|mimes:jpeg,jpg,bmp,png",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope)]
            ]), function() use ($request) {
                return [
                    "slug" => $request->slug ?? $this->model->createSlug($request->name),
                    "scope" => $request->scope ?? "website",
                    "scope_id" => $request->scope_id ?? $request->website_id
                ];
            });

            if(isset($data["parent_id"])) if(strcmp(strval($this->model->find($data["parent_id"])->website_id), $data["website_id"]))
            throw ValidationException::withMessages(["website_id" => "Patent Category does not belong to this website"]);

            if ($request->file("image")) $data["image"] = $this->storeImage($request, "image", strtolower($this->model_name));
            else  unset($data["image"]);

            $updated = $this->repository->update($data, $id, function($updated) use($data){
                $this->categoryValueRepository->createOrUpdate($data, $updated);
                if(isset($data["channels"])) $updated->channels()->sync($data["channels"]);
            });
            // get latest updated translations
            // $updated->translations = $updated->translations()->get();
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
            $this->blockCategoryAuthority($category->parent_id, $id);

            $this->repository->delete($id, function($deleted){
                $deleted->translations()->each(function($translation){
                    $translation->delete();
                });
                if($deleted->image) Storage::delete($deleted->image);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang("delete-success"), 204);
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
