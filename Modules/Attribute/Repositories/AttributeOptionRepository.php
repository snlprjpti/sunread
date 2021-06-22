<?php


namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Attribute\Entities\AttributeOptionTranslation;
use Modules\Core\Entities\Channel;

class AttributeOptionRepository extends BaseRepository
{
    protected $model, $model_key, $translation, $translation_model;

    public function __construct(AttributeOption $attribute_option, AttributeOptionTranslation $attribute_option_translation, AttributeOptionTranslationRepository $attributeOptionTranslationRepository)
    {
        $this->model = $attribute_option;
        $this->translation = $attributeOptionTranslationRepository;
        $this->translation_model = $attribute_option_translation;
        $this->model_key = "catalog.attribute.options";
        $this->rules = [
            "name" => "required",
            "position" => "sometimes|numeric",
            "translations" => "nullable|array"
        ];
    }

    public function updateOrCreate(?array $data, object $parent, $method=null): void
    {
        if ( count($data) == 0 ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");
        $items = [];
        try
        {
            if($method == "update") $parent->attribute_options()->whereNotIn('id', array_filter(Arr::pluck($data, 'id')))->delete();

            foreach ($data as $row){
                $this->validateData(new Request($row), isset($row["id"]) ? [
                    "id" => "exists:attribute_options,id"
                ] : []);
                
                $row['attribute_id'] = $parent->id;
                $created = !isset($row["id"]) ? $this->create($row) : $this->update($row, $row["id"]);

                $this->translation->updateOrCreate($row["translations"], $created);
                $items[] = $created;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $items);
        DB::commit();
    }

    public function show(int $id): array
    {
        $all_data = [];
        $input = [];
        $attribute_options = $this->model->whereAttributeId($id)->get();

        if($attribute_options->count() == 0) return $all_data;

        foreach($attribute_options as $attribute_option)
        {
            $data = [];
            $data = [
                "id" => $attribute_option->id,
                "name" => $attribute_option->name,
                "position" => $attribute_option->position,
                "is_default" => $attribute_option->is_default
            ];
            foreach(Channel::get() as $channel)
            {
                $item = [];
                $item = [
                    "id" =>  $channel->id,
                    "name" => $channel->name
                ];
            
                foreach($channel->stores as $store)
                {
                    if(!isset($store)) continue;
    
                    $item["stores"][] = [
                       "id" => $store->id,
                       "name" => $store->name,
                       "value" => $this->translation_model->whereStoreId($store->id)->first()->name ?? null
                    ];
                }
                $data["translations"][] = $item;
            }
            $input[] = $data;
        }
        $all_data[] = $input;
        return $all_data;
    }
}
