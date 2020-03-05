<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Exceptions\DefaultFamilySlugCanNotBeModified;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeFamilyController extends BaseController
{
    protected $pagination_limit;

    /**
     * AtttributeFamily constructor.
     * Admin middleware checks the admin against admins table
     */

    public function __construct()
    {
        $this->middleware('admin');
        parent::__construct();
    }

    /**
     * Returns all the attribute_familys
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {

            $payload = AttributeFamily::paginate($this->pagination_limit);
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

            $payload = AttributeFamily::findOrFail($id);
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

            $this->validate($request, AttributeFamily::rules());

            if (!$request->get('slug')) {
                $request->merge(['slug' => AttributeFamily::createSlug($request->get('name'))]);
            }
            $attribute_family = AttributeFamily::create(
                $request->only('name', 'slug')
            );

            return $this->successResponse($payload = $attribute_family, trans('core::app.response.create-success', ['name' => 'Attribute Family']), 201);

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
            $this->validate($request, AttributeFamily::rules($id));
            $attribute_family = AttributeFamily::findOrFail($id);

            //custom exception
            if ($request->slug == 'default') {
                throw new DefaultFamilySlugCanNotBeModified();
            }
            $attribute_family = $attribute_family->update(
                $request->only('name', 'slug')
            );

            return $this->successResponse($attribute_family, trans('core::app.response.update-success', ['name' => 'Attribute Family']));
        } catch (DefaultFamilySlugCanNotBeModified $exception) {
            return $this->errorResponse($exception->errors(), 400);

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
            $attribute_family = AttributeFamily::findOrFail($id);
            if ($attribute_family->slug == 'default') {
                throw  new DefaultFamilyCanNotBeDeleted;
            }
            $attribute_family->delete();
            return $this->successResponseWithMessage(trans('core::app.response.delete-success', ['name' => 'Attribute Family']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (DefaultFamilyCanNotBeDeleted $exception) {
            return $this->errorResponse("default family cannot be deleted", 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}
