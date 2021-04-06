<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Attribute\Transformers\AttributeGroupResource;
use Modules\Attribute\Repositories\AttributeGroupRepository;

class AttributeGroupController extends BaseController
{
    protected $repository, $non_filterable_fields;

    public function __construct(AttributeGroupRepository $attributeGroupRepository, AttributeGroup $attribute_group)
    {
        $this->repository = $attributeGroupRepository;
        $this->model = $attribute_group;
        $this->model_name = "Attribute Group";
        
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Returns all the attribute_group
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request, ["attribute_family"]);
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

        return $this->successResponse(AttributeGroupResource::collection($fetched), $this->lang('fetch-list-success'));
    }

    /**
     * Stores new attribute group
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

        return $this->successResponse(new AttributeGroupResource($created), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular attribute group
     * 
     * @param $id
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

        return $this->successResponse(new AttributeGroupResource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Updates the attribute group details
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

        return $this->successResponse(new AttributeGroupResource($updated), $this->lang('update-success'));
    }

    /**
     * Destroys the particular attribute group
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
        catch (AttributesPresent $exception)
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
