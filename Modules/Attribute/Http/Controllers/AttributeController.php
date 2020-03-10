<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;

use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Modules\Core\Http\Controllers\BaseController;

class AttributeController extends BaseController
{
    protected $pagination_limit;

    /**
     * Attribute constructor.
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
            $payload = Attribute::paginate($this->pagination_limit);
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

            $payload = Attribute::findOrFail($id);
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

            //begin transaction
            DB::beginTransaction();

            //Validation
            $this->validate($request, Attribute::rules());
            if (!in_array($request->get('type'), ['select', 'multiselect', 'checkbox'])) {
                $request->merge([
                    'is_filterable' => 0
                ]);
            }

            //create slug if missing
            if (!$request->get('slug')) {
                $request->merge(['slug' => Attribute::createSlug($request->get('name'))]);
            }

            $this->checkCustomCustomValidation($request);

            //store attribute
            $attribute = Attribute::create(
                $request->only(
                    ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined', 'use_in_flat']
                )
            );

            //store attribute translation
            if (is_array($request->get('translations'))) {
                $attribute->createUpdateTranslation($request->get('translations'));
            }

            //store attribute-option and translation
            $options = $request->get('attribute_options');
            if (is_array($options) && in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
                foreach ($options as $optionInputs) {
                    $attribute_option = AttributeOption::create(
                        array_merge(
                            $optionInputs,
                            ['attribute_id' => $attribute->id]
                        )
                    );
                    $attribute_option->createUpdateOptionTranslation($optionInputs);
                }
            }

            DB::commit();
            return $this->successResponse($payload = $attribute, trans('core::app.response.create-success', ['name' => 'Attribute']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (SlugCouldNotBeGenerated $exception) {
            return $this->errorResponse("Slugs could not be genereated");

        } catch (\Exception $exception) {
            dd($exception);
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Updates the attribute details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        try {

            //validation
            $this->validate($request, Attribute::rules($id));

            // custom validation
            $this->checkCustomCustomValidation($request);

            //update attribute
            $attribute = Attribute::findOrFail($id);
            $attribute->update(
                $request->only(
                    ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined', 'swatch_type', 'use_in_flat']
                )
            );

            //update attribute translation
            $attribute->createUpdateTranslation($request->get('translations'));

            //store attribute-option and translation
            $options = $request->get('attribute_options');

            if (is_array($options) && in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
                foreach ($options as $optionInputs) {

                    if (isset($optionInputs['translation'])) {
                        $attribute_option = AttributeOption::find($optionInputs['attribute_option_id']);
                    } else {
                        $attribute_option = new AttributeOption();
                    }
                    $optionInputs = array_merge($optionInputs, ['attribute_id' => $attribute->id]);
                    $attribute_option->fill($optionInputs);
                    $attribute_option->save();
                    $attribute_option->createUpdateOptionTranslation($optionInputs);
                }
            }

            return $this->successResponse($attribute, trans('core::app.response.update-success', ['name' => 'Attribute Family']));
        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            dd($exception);
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular attribute
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {

            $attribute = Attribute::findOrFail($id);
            $attribute->delete();
            return $this->successResponseWithMessage(trans('core::app.response.delete-success', ['name' => 'Attribute Family']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    public function checkCustomCustomValidation($request)
    {
        $translations = $request->get('translations');

        //check attribute translation
        $isAttributeTranslationExist = $this->checkIfTranslationExist($translations);
        if (!$isAttributeTranslationExist) {
            throw new AttributeTranslationDoesNotExist();
        }

        //check options translations
        $options = $request->get('attribute_options');

        if (is_array($options) && in_array($request->type, ['select', 'multiselect', 'checkbox'])) {
            foreach ($options as $option) {
                if (!isset($option['translations'])) {
                    throw new AttributeTranslationOptionDoesNotExist();
                }
                $isOptionTranslationExist = $this->checkIfTranslationExist($translations);
                if (!$isOptionTranslationExist) {
                    throw new  AttributeTranslationOptionDoesNotExist();
                }
            }
        }
    }


    public function checkIfTranslationExist($translations)
    {
        $locale_variable_present = true;
        foreach ($translations as $translation) {
            if (!array_key_exists('locale', $translation)) {
                return $locale_variable_present = false;
            }
        }
        return $locale_variable_present;

    }


}
