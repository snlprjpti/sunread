<?php

namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;

class AttributeSetRepository extends BaseRepository
{
    public $attribute_repository, $attribute_model;

    public function __construct(AttributeSet $attribute_set, AttributeRepository $attribute_repository, Attribute $attribute_model)
    {
        $this->model = $attribute_set;
        $this->attribute_model = $attribute_model;
        $this->model_key = "catalog.attributes.attribute_set";
        $this->attribute_repository = $attribute_repository;

        $this->rules = [
            "name" => "required"
        ];
    }

    public function attributeValidation(array $data)
    {
        $attribute_ids_array = Arr::flatten(Arr::pluck($data["groups"], 'attributes'));

        if(count($attribute_ids_array) > count(array_unique($attribute_ids_array))) 
        throw ValidationException::withMessages(["attributes" => "Different attribute groups consisting of same attributes."]);

        $default_attribute_ids = Attribute::whereIsUserDefined(0)->pluck('id')->toArray();
        if(array_diff($default_attribute_ids, $attribute_ids_array)) throw ValidationException::withMessages(["attributes" => "Default attributes are missing."]);
    }

    // public function validateAttributeSetListing(object $request): array
    // {
    //     return $request->validate([
    //         "product" => ($request->get("product")) ? "required|integer|exists:products,id" : "nullable",
    //         "attribute_set" => ($request->get("product")) ? "nullable" : "required|integer"
    //     ]); 
    // }

    public function generateFormat(int $id): array
    {
        try
        {
            $data = $this->model->findOrFail($id);
    
            $groups = $data->attribute_groups->sortBy("position")->map(function ($attribute_group) { 
                return [
                    "id" => $attribute_group->id,
                    "name" => $attribute_group->name,
                    "position" => $attribute_group->position,
                    "attributes" => $attribute_group->attributes->map(function ($attribute) {
                        $attributesData = [
                            "id" => $attribute->id,
                            "name" => $attribute->name,
                            "slug" => $attribute->slug,
                            "type" => $attribute->type,
                            "scope" => $attribute->scope,
                            "position" => $attribute->position,
                            "is_required" => $attribute->is_required
                        ];
                        if(in_array($attribute->type, $this->attribute_repository->non_filterable_fields)) $attributesData["options"] = $this->getAttributeOption($attribute);
                        return $attributesData;
                    })->toArray()
                ];
            })->toArray();
        }
        catch( Exception $exception )
        {
            throw $exception;
        }
        return [
            "attribute_set_id" => $id,
            "groups" => $groups
        ];
    }

    public function getAttributeOption(object $attribute)
    {
        try
        {
            $configOptions = $attribute->getConfigOption();

            $options = $configOptions ?? $attribute->attribute_options;
            return $options->map( function ($option) {
                return [
                    "value" => $option->id,
                    "label" => $option->name
                ];
            });
        }
        catch( Exception $exception )
        {
            throw $exception;
        }
    }
}
