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
            "attributes" => "sometimes|array",
            "attributes.*" => "sometimes|exists:attributes,id",
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

    public function multipleUpdateOrCreate(array $groups, object $parent):void
    {
        if ( !is_array($groups) || count($groups) == 0 ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");
        $attributes = [];
        try
        {
            $parent->attribute_groups()->whereNotIn('id', array_filter(Arr::pluck($groups, 'id')))->delete();

            foreach($groups as $group)
            {  
                $attributes[] = $this->singleUpdateOrCreate($group, $parent);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $attributes);
        DB::commit();
    }

    public function singleUpdateOrCreate(array $group, object $parent): array
    {
        try
        {
            $this->validateData(new Request($group), isset($group["id"]) ? [
                "id" => "exists:attribute_groups,id",
                "slug" => "nullable|unique:attribute_groups,slug,{$group["id"]}"
            ] : []);

            $group["slug"] = $parent->slug .'_'. (!isset($group["slug"]) ? $this->model->createSlug($group["name"]) : $group["slug"]);
            $group['attribute_set_id'] = $parent->id;
            $data = !isset($group["id"]) ? $this->create($group) : $this->update($group, $group["id"]);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
        
        return $data->attributes()->sync($group["attributes"]);
    }
}
