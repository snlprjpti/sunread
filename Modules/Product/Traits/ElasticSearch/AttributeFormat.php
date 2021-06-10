<?php

namespace Modules\Product\Traits\ElasticSearch;

use Modules\Attribute\Entities\Attribute;

trait AttributeFormat
{
    public function getScopeWiseAttribute(): array
    {
        $uniqueAttributeIDS= array_unique($this->product_attributes->pluck('attribute_id')->toArray());
        $available_attributes = Attribute::whereIn('id', $uniqueAttributeIDS)->get();

        foreach($available_attributes as $attribute)
        {
            $this->getSingleAttributeData($attribute);

            if(isset($this->globalAttributes)) $this->getGlobalWiseAttribute();

            if(isset($this->channelAttributes)) $this->getChannelWiseAttribute();

          
            if(isset($this->storeAttributes)) $this->getStoreWiseAttribute();

            
            $channel_diffs = array_diff($this->mainChannels, $this->channelAttributes->pluck('channel_id')->toArray());
            if(count($channel_diffs) > 0) $this->getOtherChannelsAttribute($attribute, $channel_diffs);

            $store_diffs = array_diff($this->mainStores, $this->storeAttributes->pluck('store_id')->toArray());
            if(count($store_diffs) > 0) $this->getOtherStoresAttribute($attribute, $store_diffs);

        }
        return $this->attribute_array;
    }

    public function getSingleAttributeData($attribute): void
    {
        $attributes = $this->product_attributes()->where('attribute_id', $attribute->id);

        $this->globalAttributes = (clone $attributes)->where('store_id', null)->where('channel_id', null)->first();
        $this->channelAttributes = (clone $attributes)->where('channel_id', '!=', null)->get();
        $this->storeAttributes = $attributes->where('store_id', '!=', null)->get();
    }

    public function getGlobalWiseAttribute(): void
    {
        $this->getAttributeData($this->globalAttributes);
        $this->attribute_array['global'][] = $this->attributeData;
        $this->option_attribute_array['global'][$this->globalAttributes->attribute->slug] = $this->attributeData;
    }

    public function getChannelWiseAttribute(): void
    {
        foreach($this->channelAttributes as $data){
            $this->getAttributeData($data);
            $this->attribute_array['channel'][$data->channel_id][] = $this->attributeData;
            $this->option_attribute_array['channel'][$data->channel_id][$data->attribute->slug] = $this->attributeData;
        }
    }

    public function getStoreWiseAttribute(): void
    {
        foreach($this->storeAttributes as $data){
            $this->getAttributeData($data);
            $this->attribute_array['store'][$data->store_id][] = $this->attributeData; 
            $this->option_attribute_array['store'][$data->store_id][$data->attribute->slug] = $this->attributeData; 
        }
    }

    public function getAttributeData($data): void
    {
        $model_type = new $data->value_type();
        $type = $model_type::$type;

        $attribute = (!$data->store_id) ? $data->attribute->toArray() : $data->attribute->firstTranslation($data->store_id);

        $this->attributeData = [
            "attribute" => $this->getData($attribute),
            "value" => isset($data->value->value) ? $data->value->value : "",
            "{$type}_value" => isset($data->value->value) ? $data->value->value : ""
        ];
    }

    public function getData(array $attribute): array
    {
        return [
            "id" => $attribute["id"],
            "slug" => $attribute["slug"],
            "name" => $attribute["name"],
            "position" => $attribute["position"],
            "attribute_group_id" => $attribute["attribute_group_id"]
        ];
    }

    public function mergeAttributeData($input, $storeID, $attribute): array
    {
        $input["attribute"] = (!$storeID) ? $attribute->toArray() : $attribute->firstTranslation($storeID);
        return $this->getData($input["attribute"]);
    }

    public function getOtherChannelsAttribute($attribute, $channel_diffs): void
    {
        foreach($channel_diffs as $channel_id) $this->attribute_array['channel'] [$channel_id][] = $this->option_attribute_array['global'][$attribute->slug] ?? [];
    }

    public function getOtherStoresAttribute($attribute, $store_diffs): void
    {
        foreach($store_diffs as $store_id)
        {
            $input = $this->option_attribute_array['channel'] [$this->getChannelID($store_id)] [$attribute->slug] ?? 
            $this->option_attribute_array['global'][$attribute->slug] ?? [] ;
            $this->attribute_array['store'] [$store_id][] = $this->mergeAttributeData($input, $store_id, $attribute);
        }
    }
}