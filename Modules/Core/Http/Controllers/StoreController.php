<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\StoreRepository;
use Modules\Core\Transformers\StoreResource;

class StoreController extends BaseController
{
    protected $repository;

    public function __construct(Store $store, StoreRepository $storeRepository)
    {
        $this->model = $store;
        $this->model_name = "Store";
        $this->repository = $storeRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return StoreResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new StoreResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);
            $data["image"] = $this->storeImage($request, "image", strtolower($this->model_name));
            $data["slug"] = $data["slug"] ?? $this->model->createSlug($request->name);
            $created = $this->repository->create($data, function($created) use ($request) {
                $created->channels()->sync($request->channels);
            });
        }
        catch(\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->model->findOrFail($id);
        }
        catch(\Exception $exception)
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
                "slug" => "nullable|unique:stores,slug,{$id}",
                "image" => "sometimes|nullable|mimes:bmp,jpeg,jpg,png,webp"
            ]);

            if ($request->file("image")) {
                $data["image"] = $this->storeImage($request, "image", strtolower($this->model_name));
            } else {
                unset($data["image"]);
            }
            
            $updated = $this->repository->update($data, $id, function($updated) use ($request) {
                $updated->channels()->sync($request->channels);
            });
        }
        catch(\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id, function($deleted){
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
