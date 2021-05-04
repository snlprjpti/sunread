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

        $this->attribute_types = [
            "text" => "Text",
            "textarea" => "Textarea",
            "price" => "Price",
            "boolean" => "Boolean",
            "select" => "Select",
            "multiselect" => "Multiselect",
            "datetime" => "Datetime",
            "date" => "Date",
            "image" => "Image",
            "file" => "File",
            "checkbox" => "Checkbox"
        ];
        $this->non_filterable_fields = ["select", "multiselect", "checkbox"];

        $attribute_types = implode(",", array_keys($this->attribute_types));
        $this->rules = [
            "slug" => "nullable|unique:attributes,slug",
            "name" => "required",
            "type" => "required|in:{$attribute_types}",
            "position" => "sometimes|numeric",
            "is_required" => "sometimes|boolean",
            "is_unique" => "sometimes|boolean",
            "validation" => "nullable",
            "is_visible_on_front" => "sometimes|boolean",
            "is_user_defined" => "sometimes|boolean",
            "use_in_flat" => "sometimes|boolean",
            "attribute_group_id" =>  "nullable|exists:attribute_groups,id",
            "translations" => "nullable"
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
            throw new AttributeTranslationDoesNotExist("Missing attribute translation.");
        }

        $options = $request->attribute_options;
        if (is_array($options) && in_array($request->type, $this->non_filterable_fields)) {
            foreach ($options as $option) {
                if (!isset($option["translations"]) || !$this->validateTranslationData($option["translations"])) {
                    throw new AttributeTranslationOptionDoesNotExist("Missing Attribute Option translation.");
                }
            }
        }
    }
}
