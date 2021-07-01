<?php

namespace Modules\Attribute\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
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
}