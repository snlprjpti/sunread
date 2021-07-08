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
    public function __construct(AttributeGroup $attribute_group)
    {
        $this->model = $attribute_group;
        $this->model_key = "catalog.attributes.attribute_group";
        $this->rules = [
            "name" => "required",
            "attributes" => "sometimes|array",
            "attributes.*" => "sometimes|exists:attributes,id",
            "position" => "required|numeric"
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
            $item = $this->validateData(new Request($group), isset($group["id"]) ? [
                "id" => "exists:attribute_groups,id"
            ] : []);

            $item['attribute_set_id'] = $parent->id;
            $data = !isset($item["id"]) ? $this->create($item) : $this->update($item, $item["id"]);
        }
        catch(Exception $exception)
        {
            throw $exception;
        }

        return $data->attributes()->sync($item["attributes"]);
    }
}
