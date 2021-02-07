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
use Modules\User\Entities\Role;

class AttributeGroupController extends BaseController
{
    protected $pagination_limit;

    protected $model_name = "Attribute Group";
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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $this->validate($request, [
                'limit' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
                'sort_by' => 'sometimes',
                'sort_order' => 'sometimes|in:asc,desc',
                'q' => 'sometimes|string|min:1'
            ]);

            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';

            $attribute_groups = AttributeGroup::query();
            if ($request->has('q')) {
                $attribute_groups->whereLike(AttributeGroup::$SEARCHABLE, $request->get('q'));
            }
            $attribute_groups = $attribute_groups->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;
            $attribute_groups = $attribute_groups->paginate($limit);
            return $this->successResponse($attribute_groups, trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

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
            $attribute_group = AttributeGroup::findOrFail($id);
            return $this->successResponse($attribute_group, trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]));

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

            $this->validate($request, [
                'slug' => ['nullable', 'unique:attribute_groups,slug'],
                'name' => 'required',
                'attribute_family_id' => 'required|exists:attribute_families,id'
            ]);
            $request->merge([
                'is_user_defined' => 1
            ]);

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
            $attribute_group = AttributeGroup::findOrFail($id);
            $this->validate($request, [
                'slug' => 'sometimes|required|unique:attribute_groups,slug,'.$id,
                'name' => 'sometimes|min:2|max:200',
                'attribute_family_id' => 'sometimes|required|exists:attribute_families,id'
            ]);
            $attribute_group = $attribute_group->forceFill($request->only('name', 'slug'));
            $attribute_group->save();
            return $this->successResponse($attribute_group,trans('core::app.response.update-success', ['name' => $this->model_name]));

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
            $attributes = $attribute_group->attributes;
            if(isset($attributes) && $attributes->count()>0){
                return $this->errorResponse("Attributes present in attribute groups", 403);
            }
            $attribute_group->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Attribute Group']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        }catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}
