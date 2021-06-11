<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\AttributeSet;
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
            "name" => "required"
        ];
    }
}
