<?php

namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Transformers\AttributeGroupResource;
use Modules\Core\Repositories\BaseRepository;

class AttributeSetRepository extends BaseRepository
{
    protected $model, $model_key, $attributeGroupRepository;

    public function __construct(AttributeSet $attribute_set, AttributeGroupRepository $attributeGroupRepository)
    {
        $this->model = $attribute_set;
        $this->model_key = "catalog.attributes.attribute_set";
        $this->attributeGroupRepository = $attributeGroupRepository;

        $this->rules = [
            "slug" => "nullable|unique:attribute_sets,slug",
            "name" => "required",
            "groups" => "required|array",
            "groups.attributes" => "sometimes|array"
        ];
    }

    public function updateOrCreate($groups, $set)
    {
        $attributes = [];
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.attribute_groups.sync.before");
        try
        {
            foreach($groups as $group)
            {
                $group["slug"] = $set->slug .'_'. (!isset($group["slug"]) ? $this->model->createSlug($group["name"]) : $group["slug"]);
                $group['attribute_set_id'] = $set->id;
                $data = !isset($group["id"]) ? $this->attributeGroupRepository->create($group) : $this->attributeGroupRepository->update($group, $group["id"]);
                
                $attributes[] = $data->attributes()->sync($group["attributes"]);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.attribute_groups.sync.after", $attributes);
        DB::commit();
        return $attributes;
    }
}
