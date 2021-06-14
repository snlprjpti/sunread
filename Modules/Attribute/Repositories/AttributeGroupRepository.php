<?php

namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Exceptions\AttributeTranslationDoesNotExist;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
            // "attribute_set_id" => "required|exists:attribute_sets,id",
            "attributes" => "required|array",
            "attributes.*" => "required|exists:attributes,id",
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

    public function updateOrCreate($groups, $parent, $method=null):void
    {
        if ( !is_array($groups) || count($groups) == 0 ) return;

        Event::dispatch("{$this->model_key}.sync.before");
        $attributes = [];
        try
        {
            if($method == "update") $parent->attributeGroups()->whereNotIn('id', Arr::pluck($groups, 'id'))->delete();

            foreach($groups as $group)
            {
                $this->validateData(new Request($group), isset($group["id"]) ? [
                    "id" => "exists:attribute_groups,id",
                    "slug" => "nullable|unique:attribute_groups,slug,{$group["id"]}"
                ] : []);

                $group["slug"] = $parent->slug .'_'. (!isset($group["slug"]) ? $this->model->createSlug($group["name"]) : $group["slug"]);
                $group['attribute_set_id'] = $parent->id;
                $data = !isset($group["id"]) ? $this->create($group) : $this->update($group, $group["id"]);
                
                $attributes[] = $data->attributes()->sync($group["attributes"]);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $attributes);
    }
}
