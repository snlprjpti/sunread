<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Category\Transformers\CategoryResource;
use Modules\Category\Repositories\CategoryRepository;

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

    public function resource(object $data): JsonResponse
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
        dd($request);
        try
        {
            $data = $this->repository->validateData($request);
            // $translation_data = $this->repository->validateTranslation($request);
            $data['image'] = $this->storeImage($request, 'image', strtolower($this->model_name));
            $data["slug"] = $data["slug"] ?? $this->model->createSlug($request->name);

            $created = $this->repository->create($data, function($created) use($request){
                $created->translations()->sync($request->translation);
            });

            dd($created);


            $created = $this->repository->create($data, $translation_data);
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
            $data = $this->repository->validateData($request, $id);
            $translation_data = $this->repository->validateTranslation($request);

            unset($data['image']);
            $updated = $this->repository->update($data, $id, $translation_data);

            if ($request->file('image')) {
                $image_data = [
                    'image' => $this->storeImage($request, 'image', 'category', $updated->image)
                ];
                $updated->fill($image_data);
                $updated->save();
            }
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse(new CategoryResource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id);
        }
        catch (\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
