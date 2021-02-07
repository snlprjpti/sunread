<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Attribute\Exceptions\DefaultFamilyCanNotBeDeleted;
use Modules\Attribute\Exceptions\DefaultFamilySlugCanNotBeModified;
use Modules\Attribute\Repositories\AttributeFamilyRepository;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeFamilyController extends BaseController
{
    //set custom pagination list
    protected $pagination_limit;

    protected $model_name = 'Attribute Family';

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

            $attribute_families = AttributeFamily::query();
            if ($request->has('q')) {
                $attribute_families->whereLike(AttributeFamily::$SEARCHABLE, $request->get('q'));
            }
            $attribute_families = $attribute_families->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;
            $attribute_families = $attribute_families->paginate($limit);
            return $this->successResponse($attribute_families, trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

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
            $attribute_family = $this->attributeFamilyRepository->findOrFail($id);
            return $this->successResponse($attribute_family, trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponseForMissingModel(trans('core::app.response.not-found', ['name' => $this->model_name]));

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

            if($request->has('slug')){
                $request->merge(['slug' => Str::slug($request->get('slug')),]);
            }

            //validation
            $this->validate($request, [
                'slug' => 'required|unique:attribute_families',
                'name' => 'required'
            ]);

            $attribute_family = AttributeFamily::create($request->only('name', 'slug'));
            return $this->successResponse($payload = $attribute_family, trans('core::app.response.create-success', ['name' => $this->model_name]), 201);

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
            $this->validate($request, [
                'slug' => 'required|unique:attribute_families,slug,'.$id,
                'name' => 'required'
            ]);

            $attribute_family =  AttributeFamily::findOrFail($id);
            $attribute_family->fill($request->only('name', 'slug'),$id);
            $attribute_family->save();
            return $this->successResponse($attribute_family, trans('core::app.response.update-success', ['name' => 'Attribute Family']));

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

            $attribute_family = AttributeFamily::findOrFail($id);
            if ($attribute_family->slug == 'default') {
                throw  new DefaultFamilyCanNotBeDeleted;
            }
            $attribute_groups = $attribute_family->attributeGroups;

            if($attribute_groups && count($attribute_groups)>0){
                return $this->errorResponse("Attribute Groups present in family.", 403);
            }
            $attribute_family->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Attribute Family']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (DefaultFamilyCanNotBeDeleted $exception) {
            return $this->errorResponse("default family cannot be deleted", 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}
