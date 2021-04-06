<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Attribute\Exceptions\AttributeGroupsPresent;
use Modules\Attribute\Transformers\AttributeFamilyResource;
use Modules\Attribute\Repositories\AttributeFamilyRepository;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;

class AttributeFamilyController extends BaseController
{
    protected $repository;

    public function __construct(AttributeFamilyRepository $attributeFamilyRepository, AttributeFamily $attribute_family)
    {
        $this->repository = $attributeFamilyRepository;
        $this->model = $attribute_family;
        $this->model_name = "Attribute Family";

        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Returns all the attribute family
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

        return $this->successResponse(AttributeFamilyResource::collection($fetched), $this->lang('fetch-list-success'));
    }

    /**
     * Stores new attribute family
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

        return $this->successResponse(new AttributeFamilyResource($created), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular attribute family
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

        return $this->successResponse(new AttributeFamilyResource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Updates the attribute family details
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

        return $this->successResponse(new AttributeFamilyResource($updated), $this->lang('update-success'));
    }

    /**
     * Destroys the particular attribute family
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
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (DefaultFamilyCanNotBeDeleted $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (AttributeGroupsPresent $exception)
        {
            return $this->errorResponse($exception->getMessage(), 403);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
