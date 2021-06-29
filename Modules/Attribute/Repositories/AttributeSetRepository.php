<?php

namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Transformers\AttributeGroupResource;
use Modules\Core\Repositories\BaseRepository;

class AttributeSetRepository extends BaseRepository
{
    protected $model, $model_key;

    public function __construct(AttributeSet $attribute_set)
    {
        $this->model = $attribute_set;
        $this->model_key = "catalog.attributes.attribute_set";

        $this->rules = [
            "slug" => "nullable|unique:attribute_sets,slug",
            "name" => "required",
            "groups" => "required|array"
        ];
    }

    public function attributeValidation(array $data)
    {
        $attribute_ids_array = Arr::flatten(Arr::pluck($data["groups"], 'attributes'));

        if(count($attribute_ids_array) > count(array_unique($attribute_ids_array))) 
        throw ValidationException::withMessages(["attributes" => "Different attribute groups consisting of same attributes."]);

        $default_attribute_ids = Attribute::whereIsUserDefined(0)->whereIsRequired(0)->pluck('id')->toArray();
        if(array_diff($default_attribute_ids, $attribute_ids_array)) throw ValidationException::withMessages(["attributes" => "Default attributes are missing."]);
    }

    public function validateAttributeSetListing(object $request): array
    {
        return $request->validate([
            "product" => ($request->get("product")) ? "required|integer|exists:products,id" : "nullable",
            "attribute_set" => ($request->get("product")) ? "nullable" : "required|integer"
        ]); 
    }


}
