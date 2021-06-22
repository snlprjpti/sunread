<?php


namespace Modules\Attribute\Repositories;

use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Core\Entities\Channel;

class AttributeTranslationRepository
{
    protected $model, $model_key;

    public function __construct(AttributeTranslation $attribute_translation)
    {
        $this->model = $attribute_translation;
        $this->model_key = "catalog.attribute.translations";
    }

    public function updateOrCreate(?array $data, object $parent): void
    {
        if ( count($data) == 0 ) return;

        Event::dispatch("{$this->model_key}.create.before");

        try
        {
            foreach ($data as $row){
                if(!$row["name"]) continue;
                
                $check = [
                    "store_id" => $row["store_id"],
                    "attribute_id" => $parent->id
                ];
    
                $created = $this->model->firstorNew($check);
                $created->fill($row);
                $created->save();
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.create.after", $created);
    }

    public function show(int $id): array
    {
        $data = [];
        foreach(Channel::get() as $channel)
        {
            $item = [];
            $item["id"] = $channel->id;
            $item["name"] = $channel->name;
        
            foreach($channel->stores as $store)
            {
                if(!isset($store)) continue;

                $item["stores"][] = [
                   "id" => $store->id,
                   "name" => $store->name,
                   "value" => $this->model->whereAttributeId($id)->whereStoreId($store->id)->first()->name ?? null
                ];
            }
            $data[] = $item;
        }
        return $data;
    }
}
