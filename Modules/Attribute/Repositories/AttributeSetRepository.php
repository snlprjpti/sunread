<?php

namespace Modules\Attribute\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Repositories\BaseRepository;

class AttributeSetRepository extends BaseRepository
{
    public function __construct(AttributeSet $attribute_set)
    {
        $this->model = $attribute_set;
        $this->model_key = "catalog.attributes.attribute_set";

        $this->rules = [
            "slug" => "nullable|unique:attribute_sets,slug",
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

    public function validateAttributeSetListing(object $request): array
    {
        return $request->validate([
            "product" => ($request->get("product")) ? "required|integer|exists:products,id" : "nullable",
            "attribute_set" => ($request->get("product")) ? "nullable" : "required|integer"
        ]); 
    }

    public function generateFormat(object $request): array
    {
        try
        {
            $product = Product::find($request->product);
            $data = $this->model->findOrFail($product ? $product->attribute_set_id : $request->attribute_set);
            $groups = [];
    
            $attribute_groups = $data->attribute_groups->sortBy("position")->map(function ($attribute_group) use (&$groups, $product) {                
                $groups[] = [
                    "group_id" => $attribute_group->id,
                    "title" => $attribute_group->name,
                    "position" => $attribute_group->position,
                    "elements" => $attribute_group->attributes->sortBy("position")->map(function ($attribute) use ($product){
                        return [
                            "attribute_id" => $attribute->id,
                            "name" => $attribute->name,
                            "scope" => $attribute->scope,
                            "position" => $attribute->position,
                            "is_required" => $attribute->is_required,
                            "type" => $attribute->type,
                            "value" => $product ? $attribute->product_attributes->where("product_id", $product->id)->first()->value_data ?? '' : '' 
                        ];
                    })->toArray()
                ];
            });
    
            $format = [
                "general" => [
                    [
                        "title" => "General Details",
                        "position" => 1,
                        "elements" => [
                            [
                                "title" => "SKU",
                                "name" => "sku",
                                "is_required" => 1,
                                "position" => 1,
                                "type" => "text",
                                "value" => $product->sku ?? '' 
                            ],
                            [
                                "title" => "Type",
                                "name" => "type",
                                "is_required" => 1,
                                "position" => 2,
                                "type" => "hidden",
                                "value" => $product->type ?? ''
                            ],
                            [
                                "title" => "Attribute Set",
                                "name" => "attribute_set_id",
                                "is_required" => 1,
                                "position" => 3,
                                "type" => "select",
                                "value" => $product ? $product->attribute_set_id : $request->attribute_set_id
                            ]
                        ]
                    ]
                ],
                "attribute_groups" => $groups,
            ];
        }
        catch( \Exception $exception )
        {
            throw $exception;
        }

        return $format;
    }


}
