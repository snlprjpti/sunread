<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Core\Entities\Store;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
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

        return $this->successResponse(StoreResource::collection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);
            $data['image'] = $this->storeImage($request, 'image', 'store');
            $created = $this->repository->create($data);
        }
        catch(\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse(new StoreResource($created), $this->lang('create-success'), 201);
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
        return $this->successResponse(new StoreResource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, $id);
            unset($data['image']);
            $updated = $this->repository->update($data, $id);

            if ($request->file('image')) {
                $image_data = [
                    'image' => $this->storeImage($request, 'image', 'category', $updated->image)
                ];
                $updated->fill($image_data);
                $updated->save();
            }
        }
        catch(\Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse(new StoreResource($updated), $this->lang("update-success"));
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
