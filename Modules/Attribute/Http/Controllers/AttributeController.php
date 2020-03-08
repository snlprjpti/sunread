<?php

namespace Modules\Attribute\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Attribute\Entities\AttributeOptionTranslation;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Attribute\Exceptions\DefaultFamilySlugCanNotBeModified;
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

            DB::beginTransaction();

            //validate request
            $this->validate($request, Attribute::rules());

            //create  slug if missing
            if (!$request->get('slug')) {
                $request->merge(['slug' => Attribute::createSlug($request->get('name'))]);
            }

            //store attribute
            $attribute = Attribute::create(
                $request->only(
                    ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined', 'use_in_flat']
                )
            );

            //store translation
            $this->createOrUpdateTranslation($attribute, $request);


            //store attribute-option and translation
            $options = $request->get('options');
            if (is_array($options) && in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
                foreach ($options as $optionInputs) {
                    $attributeOption = AttributeOption::create($optionInputs);
                    $this->createOrUpdateOptionTranslation($attributeOption, $optionInputs);
                }
            }

            return $this->successResponse($payload = $attribute, trans('core::app.response.create-success', ['name' => 'Attribute']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (SlugCouldNotBeGenerated $exception) {
            return $this->errorResponse("Slugs could not be genereated");

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    private function createOrUpdateTranslation(Attribute $attribute, Request $request)
    {
        $check_attributes = ['locale' => $this->locale, 'attribute_id' => $attribute->id];
        $request->merge($check_attributes);
        $attribute_translation = AttributeTranslation::firstorNew($check_attributes);
        $attribute_translation->fill(
            $request->only(['name', 'locale', 'attribute_id'])
        );
        $attribute_translation->save();

    }

    private function createOrUpdateOptionTranslation(AttributeOption $attribute_option, Array $optionInputs)
    {
        $check_attributes = ['locale' => $this->locale, 'attribute_option_id' => $attribute_option->id];
        $optionInputs = array_merge($optionInputs,$check_attributes);
        $option_translation = AttributeOptionTranslation::firstorNew($check_attributes);
        $option_translation->fill($optionInputs);
        $option_translation->save();

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
            $this->validate($request, Attribute::rules($id));

            $attribute = Attribute::findOrFail($id);
            $attribute = $attribute->update(
                $request->only(
                    ['slug', 'name', 'type', 'position', 'is_required', 'is_unique', 'validation', 'is_filterable', 'is_visible_on_front', 'is_user_defined', 'swatch_type', 'use_in_flat']
                )
            );
            $this->createOrUpdateTranslation($attribute, $request);

            //store attribute-option and translation
            $options = $request->get('options');
            if (is_array($options) && in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
                foreach ($options as $optionInputs) {
                    if(isset($optionInputs['attribute_option_id'])){
                        $attribute_option = AttributeOption::find($optionInputs['attribute_option_id']);
                    }else{

                        $attribute_option =  new AttributeOption();
                    }
                    $attribute_option->fill($optionInputs);
                    $attribute_option->save();
                    $this->createOrUpdateOptionTranslation($attribute_option, $optionInputs);

                }
            }


            return $this->successResponse($attribute, trans('core::app.response.update-success', ['name' => 'Attribute Family']));
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


}
