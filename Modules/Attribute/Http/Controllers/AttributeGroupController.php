<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Exceptions\DefaultFamilySlugCanNotBeModified;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeGroupController extends BaseController
{
    protected $pagination_limit;

    /**
     * AttributeGroup Controller constructor.
     * Admin middleware checks the admin against admins table
     */

    public function __construct()
    {
        $this->middleware('admin');
        parent::__construct();
    }

    /**
     * Returns all the attribute_group
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {

            $payload = AttributeGroup::paginate($this->pagination_limit);
            return $this->successResponse($payload);
        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular attribute_family
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {

            $payload = AttributeGroup::findOrFail($id);
            return $this->successResponse($payload);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new attribute_family
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, AttributeGroup::rules());

            if (!$request->get('slug')) {
                $request->merge(['slug' => AttributeGroup::createSlug($request->get('name'))]);
            }

            $attribute_group = AttributeGroup::create(
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
     * Updates the attribute_family details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, AttributeGroup::rules($id));
            $attribute_group = AttributeGroup::findOrFail($id);
            $attribute_group = $attribute_group->update(
                $request->only('name', 'slug', 'attribute_family_id')
            );

            return $this->successResponse($attribute_group, trans('core::app.response.update-success', ['name' => 'Attribute Group']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular attribute_family
     * @param $id
     * @return \Illuminate\Http\JsonResponse
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
