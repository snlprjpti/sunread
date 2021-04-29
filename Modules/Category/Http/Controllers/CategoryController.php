<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryTranslation;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Category\Transformers\CategoryResource;
use Modules\Category\Repositories\CategoryRepository;

class CategoryController extends BaseController
{
    protected $repository, $translation;

    public function __construct(CategoryRepository $categoryRepository, Category $category, CategoryTranslation $categoryTranslation)
    {
        $this->repository = $categoryRepository;
        $this->translation = $categoryTranslation;
        $this->model = $category;
        $this->model_name = "Category";

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CategoryResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CategoryResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request, ["translations"]);
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);
            $data['image'] = $this->storeImage($request, 'image', strtolower($this->model_name));
            $data["slug"] = $data["slug"] ?? $this->model->createSlug($request->name);

            $created = $this->repository->create($data, function($created) use($request){
                $this->translation->updateOrCreate([
                    "store_id" => $request->translation["store_id"],
                    "category_id" => $created->id
                ], $request->translation);
            });
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->model->with(["translations"])->findOrFail($id);
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {            
            $data = $this->repository->validateData($request,[
                "slug" => "nullable|unique:categories,slug,{$id}",
                "image" => "sometimes|nullable|mimes:jpeg,jpg,bmp,png",
            ]);

            if ($request->file("image")){
                $data["image"] = $this->storeImage($request, "image", strtolower($this->model_name));
            }
            else {
                unset($data["image"]);
            }

            $updated = $this->repository->update($data, $id, function($updated) use($request){
                $this->translation->updateOrCreate([
                    "store_id" => $request->translation["store_id"],
                    "category_id" => $updated->id
                ], $request->translation);
            });
            // get latest updated translations
            $updated->translations = $updated->translations()->get();
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id, function($deleted){
                $deleted->translations()->delete();
                if($deleted->image) Storage::delete($deleted->image);
            });
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }
}
