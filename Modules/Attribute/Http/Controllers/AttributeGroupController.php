<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Repositories\AttributeGroupRepository;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeGroupController extends BaseController
{
    protected $pagination_limit;

    /**
     * AttributeGroup Controller constructor.
     * Admin middleware checks the admin against admins table
     */
    private $attributeGroupRepository;

    public function __construct(AttributeGroupRepository $attributeGroupRepository)
    {
        parent::__construct();
        $this->middleware('admin');
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * Returns all the attribute_group
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $payload = $this->attributeGroupRepository->all();
            return $this->successResponse($payload);
        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular attribute group
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $payload = $this->attributeGroupRepository->findOrFail($id);
            return $this->successResponse($payload);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new attribute group
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, $this->attributeGroupRepository->rules());

            if (!$request->get('slug')) {
                $request->merge(['slug' => $this->attributeGroupRepository->createSlug($request->get('name'))]);
            }

            $attribute_group = $this->attributeGroupRepository->create(
                $request->only('name', 'slug' ,'is_user_defined','attribute_family_id')
            );

            return $this->successResponse($payload = $attribute_group, trans('core::app.response.create-success', ['name' => 'Attribute Group']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (SlugCouldNotBeGenerated $exception) {
            return $this->errorResponse("Slugs could not be genereated");

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Updates the attribute group details
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, $this->attributeGroupRepository->rules($id));
            $this->attributeGroupRepository->update($request->only('name', 'slug'),$id);
            return $this->successResponseWithMessage(trans('core::app.response.update-success', ['name' => 'Attribute Group']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular attribute group
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $attribute_group = AttributeGroup::findOrFail($id);
            $attribute_group->delete();
            return $this->successResponseWithMessage(trans('core::app.response.delete-success', ['name' => 'Attribute Group']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        }catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}
