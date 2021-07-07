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
            $next_position = 0;
    
            $attribute_groups = $data->attribute_groups->sortBy("position")->map(function ($attribute_group) use (&$groups, $product, &$next_position) {                
                $groups[] = [
                    "group_id" => $attribute_group->id,
                    "title" => $attribute_group->name,
                    "position" => $attribute_group->position,
                    "elements" => $attribute_group->attributes->sortBy("position")->map(function ($attribute) use ($product){
                        $attributes_data = [
                            "attribute_id" => $attribute->id,
                            "name" => $attribute->name,
                            "slug" => $attribute->slug,
                            "scope" => $attribute->scope,
                            "position" => $attribute->position,
                            "is_required" => $attribute->is_required,
                            "type" => $attribute->type,
                            "value" => $product ? $attribute->product_attributes->where("product_id", $product->id)->first()->value_data ?? '' : ''
                        ];

                        if(in_array($attribute->type, $this->attribute_repository->non_filterable_fields)) $attributes_data["options"] = $this->getAttributeOption($attribute);
                        
                        return $attributes_data;

                    })->toArray()
                ];
                $next_position = $attribute_group->position;
            });

            $images = [
                "title" => "Upload Images",
                "position" => ++$next_position,
                "elements" => [
                    [
                        "name" => "Base Image",
                        "slug" => "base_image",
                        "position" => 1,
                        "is_required" => 1,
                        "type" => "file",
                        "multiple" => true,
                        "value" => $product ? $product->images->pluck('main_image')->toArray() : [] 
                    ],
                    [
                        "name" => "Thumbnail Image",
                        "slug" => "thumbnail_image",
                        "position" => 2,
                        "is_required" => 1,
                        "type" => "file",
                        "multiple" => true,
                        "value" => $product ? $product->images->pluck('thumbnail')->toArray() : []  
                    ],
                    [
                        "name" => "Small Image",
                        "slug" => "small_image",
                        "position" => 3,
                        "is_required" => 1,
                        "type" => "file",
                        "multiple" => true,
                        "value" => $product ? $product->images->pluck('small_images')->toArray() : [] 
                    ]
                ]
            ];
    
            $format = [
                "general" => [
                    [
                        "title" => "General Details",
                        "position" => 1,
                        "elements" => [
                            [
                                "name" => "Type",
                                "slug" => "type",
                                "is_required" => 1,
                                "position" => 2,
                                "type" => "hidden",
                                "value" => $product->type ?? ''
                            ],
                            [
                                "name" => "Attribute Set",
                                "slug" => "attribute_set_id",
                                "is_required" => 1,
                                "position" => 3,
                                "type" => "select",
                                "value" => $product ? $product->attribute_set_id : $request->attribute_set_id
                            ]
                        ]
                    ]
                ],
                "attribute_groups" => array_merge($groups, [ $images ])
            ];
        }
        catch( \Exception $exception )
        {
            throw $exception;
        }

        return $format;
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
        catch( \Exception $exception )
        {
            throw $exception;
        }
    }


}
