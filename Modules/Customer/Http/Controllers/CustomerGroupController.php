<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Modules\Customer\Entities\CustomerGroup;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Customer\Exceptions\CustomersPresentInGroup;
use Modules\Customer\Transformers\CustomerGroupResource;
use Modules\Customer\Repositories\CustomerGroupRepository;

class CustomerGroupController extends BaseController
{
    protected $repository;

    public function __construct(CustomerGroupRepository $customerGroupRepository, CustomerGroup $customer_group)
    {
        $this->repository = $customerGroupRepository;
        $this->model = $customer_group;
        $this->model_name = "Customer Group";

        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(CustomerGroupResource::collection($fetched), $this->lang('fetch-list-success'));
    }

    /**
     * Store a new resource
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $data = $this->repository->validateData($request);
            $created = $this->repository->create($data);
        }
        catch (SlugCouldNotBeGenerated $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerGroupResource($created), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular resource
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $fetched = $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerGroupResource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try
        {
            $data = $this->repository->validateData($request, $id);
            $updated = $this->repository->update($data, $id);
        }
        catch (SlugCouldNotBeGenerated $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new CustomerGroupResource($updated), $this->lang('update-success'));
    }

    /**
     * Remove the specified resource
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            $this->repository->delete($id);
        }
        catch (CustomersPresentInGroup $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
