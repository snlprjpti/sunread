<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Exceptions\DefaultFamilySlugCanNotBeModified;
use Modules\Attribute\Repositories\AttributeFamilyRepository;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeFamilyController extends BaseController
{
    //set custom pagination list
    protected $pagination_limit;

    private $attributeFamilyRepository;
    /**
     * Attribute Family constructor.
     * @param AttributeFamilyRepository $attributeFamilyRepository
     */

    public function __construct(AttributeFamilyRepository $attributeFamilyRepository)
    {
        $this->middleware('admin');
        parent::__construct();
        $this->attributeFamilyRepository = $attributeFamilyRepository;
    }

    /**
     * Returns all the attribute family
     * @return JsonResponse
     */
    public function index()
    {
        try {

            $payload = $this->attributeFamilyRepository->paginate($this->pagination_limit);
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
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $payload = $this->attributeFamilyRepository->findOrFail($id);
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
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            //validation
            $this->validate($request, $this->attributeFamilyRepository->rules());

            //create slug
            if (!$request->get('slug')) {
                $request->merge(['slug' => $this->attributeFamilyRepository->createSlug($request->get('name'))]);
            }

            $attribute_family = $this->attributeFamilyRepository->create($request->only('name', 'slug'));

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
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, $this->attributeFamilyRepository->rules($id));

            //custom exception default attribute family slug not allowed
            if ($request->slug == 'default') {
                throw new DefaultFamilySlugCanNotBeModified();
            }

            $this->attributeFamilyRepository->update($request->only('name', 'slug'),$id);
            return $this->successResponseWithMessage(trans('core::app.response.update-success', ['name' => 'Attribute Family']));

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
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {

            $attribute_family = $this->attributeFamilyRepository->findOrFail($id);
            if ($attribute_family->slug == 'default') {
                throw  new DefaultFamilyCanNotBeDeleted;
            }
            $this->attributeFamilyRepository->delete($attribute_family->id);
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
