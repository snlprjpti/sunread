<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeController extends BaseController
{
    protected $pagination_limit, $attributeRepository;

    /**
     * Attribute constructor.
     * Admin middleware checks the admin against admins table
     * @param AttributeRepository $attributeRepository
     */

    public function __construct(AttributeRepository $attributeRepository)
    {
        parent::__construct();
        $this->middleware('admin');
        $this->attributeRepository =  $attributeRepository;
    }

    /**
     * Returns all the attribute_familys
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $payload = $this->attributeRepository->paginate($this->pagination_limit);
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

            $payload = $this->attributeRepository->findOrFail($id);
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

            //Validation
            $this->validate($request, $this->attributeRepository->rules());

            //custom validation to for translation
            $this->checkCustomCustomValidation($request);
            if (!in_array($request->get('type'), ['select', 'multiselect', 'checkbox'])) {
                $request->merge([
                    'is_filterable' => 0
                ]);
            }

            //create slug if missing
            if (!$request->get('slug')) {
                $request->merge(['slug' => $this->attributeRepository->createSlug($request->get('name'))]);
            }

            //Store Attributes
            $attribute =  $this->attributeRepository->createAttribute($request);
            return $this->successResponse($payload = $attribute, trans('core::app.response.create-success', ['name' => 'Attribute']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (SlugCouldNotBeGenerated $exception) {
            return $this->errorResponse("Slugs could not be genereated");

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Updates the attribute details
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

            //update attribute
            $this->attributeRepository->updateAttributes($request ,$id);

            return $this->successResponseWithMessage(trans('core::app.response.update-success', ['name' => 'Attribute']));

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

            $this->attributeRepository->findOrFail($id);
            $this->attributeRepository->delete($id);
            return $this->successResponseWithMessage(trans('core::app.response.delete-success', ['name' => 'Attribute ']));
        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

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
        if(empty($translations)){
            return false;
        }
        $locale_key_present = true;
        foreach ($translations as $translation) {
            if (!array_key_exists('locale', $translation)) {
                return $locale_key_present = false;
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
            throw new AttributeTranslationDoesNotExist("Missing attribute translation");
        }

        //check options translations
        $options = $request->get('attribute_options');

        if (is_array($options) && in_array($request->type, ['select', 'multiselect', 'checkbox'])) {
            foreach ($options as $option) {
                if (!isset($option['translations'])) {
                    throw new AttributeTranslationOptionDoesNotExist("Missing Attribute Option Translation");
                }
                $isOptionTranslationExist = $this->checkIfTranslationExist($translations);
                if (!$isOptionTranslationExist) {
                    throw new  AttributeTranslationOptionDoesNotExist("Missing Attribute Option Translation");
                }
            }
        }
    }

}
