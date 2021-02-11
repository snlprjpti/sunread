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
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeController extends BaseController
{
    protected $pagination_limit, $attributeRepository;
    protected $model_name = 'Attribute';

    /**
     * Attribute constructor.
     * Admin middleware checks the admin against admins table
     * @param AttributeRepository $attributeRepository
     */

    public function __construct(AttributeRepository $attributeRepository)
    {
        parent::__construct();
        $this->middleware('admin');
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Returns all the attributes
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
            $attributes = Attribute::with('translations');
            if ($request->has('q')) {
                $attributes->whereLike(Attribute::$SEARCHABLE, $request->get('q'));
            }
            $attributes = $attributes->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;
            $attributes = $attributes->paginate($limit);
            return $this->successResponse($attributes, trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular attribute
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $attribute = Attribute::with(['attributeOptions' ,'translations'])->findOrFail($id);
            return $this->successResponse($attribute, trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]));

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new attribute
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            //Validation
            $this->validate($request, $this->attributeRepository->rules());

            //custom validation to for translation
            $this->checkCustomCustomValidation($request);
            if (!in_array($request->get('type'), ['select', 'multiselect', 'checkbox'])) {
                $request->merge(['is_filterable' => 0]);
            }

            //create slug if missing
            if (!$request->get('slug')) {
                $request->merge(['slug' => $this->attributeRepository->createSlug($request->get('name'))]);
            }

            //TODO::future ,available for development only
            $request->merge(["use_in_flat" => 0]);

            //Store Attributes
            $attribute = $this->attributeRepository->createAttribute($request);
            return $this->successResponse($attribute, trans('core::app.response.create-success', ['name' => $this->model_name]), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (SlugCouldNotBeGenerated $exception) {
            return $this->errorResponse("Slugs could not be generated");

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Updates the attribute
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            //validation
            $this->validate($request, $this->attributeRepository->rules($id));

            // custom validation for translation and options
            $this->checkCustomCustomValidation($request);

            //TODO::future , available for development only
            $request->merge(["use_in_flat" => 0]);

            //update attribute
            $this->attributeRepository->updateAttributes($request, $id);

            return $this->successResponseWithMessage(trans('core::app.response.update-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular attribute
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->attributeRepository->delete($id);
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]));

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


    /** Checks if locale is present in translation
     * @param $translations
     * @return bool
     */
    public function checkIfTranslationExist($translations)
    {
        if (empty($translations)) {
            return false;
        }
        $locale_key_present = true;
        foreach ($translations as $translation) {
            if (!array_key_exists('locale', $translation) || !array_key_exists('name', $translation)) {
                return false;
            }
        }
        return $locale_key_present;

    }

    /**
     * Checks if translation exist for attributes and attribute options
     * At least one translation is needed
     * @param $request
     * @throws AttributeTranslationDoesNotExist
     * @throws AttributeTranslationOptionDoesNotExist
     */
    public function checkCustomCustomValidation($request)
    {
        $translations = $request->get('translations');

        //check attribute translation
        $isAttributeTranslationExist = $this->checkIfTranslationExist($translations);
        if (!$isAttributeTranslationExist) {
            throw new AttributeTranslationDoesNotExist("Missing attribute translation.");
        }

        //check options translations
        $options = $request->get('attribute_options');

        if (is_array($options) && in_array($request->type, ['select', 'multiselect', 'checkbox'])) {
            foreach ($options as $option) {
                if (!isset($option['translations'])) {
                    throw new AttributeTranslationOptionDoesNotExist("Missing Attribute Option translation.");
                }
                $isOptionTranslationExist = $this->checkIfTranslationExist($translations);
                if (!$isOptionTranslationExist) {
                    throw new  AttributeTranslationOptionDoesNotExist("Missing Attribute Option translation.");
                }
            }
        }
    }

}
