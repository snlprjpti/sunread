<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\AttributeFamily;
use Modules\Core\Repositories\BaseRepository;

class AttributeFamilyRepository extends BaseRepository
{
    protected $model, $model_key;

    public function __construct(AttributeFamily $attribute_family)
    {
        $this->model = $attribute_family;
        $this->model_key = "catalog.attribite";

        $this->rules = [
            "slug" => "nullable|unique:attribute_families,slug",
            "name" => "required"
        ];
    }
}
