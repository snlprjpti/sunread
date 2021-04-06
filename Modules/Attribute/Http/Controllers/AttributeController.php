<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Attribute\Transformers\AttributeResource;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeController extends BaseController
{
    protected $repository;

    public function __construct(AttributeRepository $attributeRepository, Attribute $attribute)
    {
        $this->repository = $attributeRepository;
        $this->model = $attribute;
        $this->model_name = "Attribute";

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
            $fetched = $this->getFilteredList($request, ["translations", "attribute_group"]);
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

        return $this->successResponse(AttributeResource::collection($fetched), $this->lang('fetch-list-success'));
    }

    /**
     * Stores new attribute
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $data = $this->repository->validateData($request);
            $this->repository->validateTranslation($request);

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
        catch (AttributeTranslationDoesNotExist $exception)
        {
            return $this->errorResponse($exception->getMessage(), 422);
        }
        catch (AttributeTranslationOptionDoesNotExist $exception)
        {
            return $this->errorResponse($exception->getMessage(), 422);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new AttributeResource($created), $this->lang('create-success'), 201);
    }

    /**
     * Get the particular attribute
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $fetched = $this->model->with(["attribute_options", "translations"])->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new AttributeResource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Updates the attribute
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
            $this->repository->validateTranslation($request);
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

        return $this->successResponse(new AttributeResource($updated), $this->lang('update-success'));
    }

    /**
     * Destroys the particular attribute
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
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    /**
     * Remove the specified resource in bullk.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        try
        {
            $this->repository->bulkDelete($request);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}
