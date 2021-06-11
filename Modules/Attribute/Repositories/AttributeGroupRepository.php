<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Core\Repositories\BaseRepository;

class AttributeGroupRepository extends BaseRepository
{
    protected $model, $model_key;

    public function __construct(AttributeGroup $attribute_group)
    {
        $this->model = $attribute_group;
        $this->model_key = "catalog.attributes.attribute_group";
        $this->rules = [
            "slug" => "nullable|unique:attribute_groups,slug",
            "name" => "required",
            "attribute_set_id" => "required|exists:attribute_sets,id"
        ];
    }


    public function validateTranslationData(array $translations): bool
    {
        if (empty($translations)) return false;

        foreach ($translations as $translation) {
            if (!array_key_exists('store_id', $translation) || !array_key_exists('name', $translation)) return false;
        }

        return true;
    }

    public function validateTranslation(object $request): void
    {
        $translations = $request->translations;
        if (!$this->validateTranslationData($translations)) {
            throw new AttributeTranslationDoesNotExist("Missing attribute translation.");
        }
    }
}
