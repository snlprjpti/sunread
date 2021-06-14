<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Attribute\Exceptions\AttributeTranslationOptionDoesNotExist;
use Modules\Core\Repositories\BaseRepository;

class AttributeRepository extends BaseRepository
{
    public $attribute_types, $non_filterable_fields;

    public function __construct(Attribute $attribute)
    {
        $this->model = $attribute;
        $this->model_key = "catalog.attribute";

        $this->non_filterable_fields = ["select", "multiselect", "checkbox"];

        $attribute_types = implode(",", array_keys(config("attribute_types")));
        $this->rules = [
            "slug" => "nullable|unique:attributes,slug",
            "name" => "required",
            "type" => "required|in:{$attribute_types}",
            "scope" => "required|in:global,website,channel",
            "is_required" => "sometimes|boolean",
            "comparable_on_storefront" => "sometimes|boolean",
            "validation" => "nullable",
            "is_visible_on_storefront" => "sometimes|boolean",
            "is_user_defined" => "sometimes|boolean",
            "use_in_layered_navigation" => "sometimes|boolean",
            "position" => "sometimes|numeric",
            "is_searchable" => "sometimes|boolean",
            "translations" => "nullable|array"
        ];
    }

    public function validateTranslationData(?array $translations): bool
    {
        if (empty($translations)) return false;

        foreach ($translations as $translation) {
            if (!array_key_exists("store_id", $translation) || !array_key_exists("name", $translation)) return false;
        }

        return true;
    }

    public function validateTranslation(object $request): void
    {
        $translations = $request->translations;
        if (!$this->validateTranslationData($translations)) {
            throw new AttributeTranslationDoesNotExist(__("core.app.response.missing-data", ["name" => "Attribute"]));
        }

        $options = $request->attribute_options;
        if (is_array($options) && in_array($request->type, $this->non_filterable_fields)) {
            foreach ($options as $option) {
                if (!isset($option["translations"]) || !$this->validateTranslationData($option["translations"])) {
                    throw new AttributeTranslationOptionDoesNotExist(__("core.app.response.missing-data", ["name" => "Attribute Option"]));
                }
            }
        }
    }
}
