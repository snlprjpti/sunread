<?php

namespace Modules\Product\Traits;

trait IndexFormat
{
    public function documentDataStructure(): array
    {
        $array = $this->toArray();

        $array['categories'] = $this->categories;

        $array['channels'] = $this->channels;

        $stores = $this->channels->map(function($channel){
            $channel->stores->map(function($store){
                return [
                    $store->id
                ];
            });
        })->toArray();

        $array['product_attributes'] = [
            'global' => [],
            'channel' => [],
            'store' => []
        ];

        foreach($this->product_attributes as $data){
            $attribute_value = [
                $data->attribute->slug => isset($data->value->value) ? $data->value->value : ""
            ];

            if(!isset($data->store_id) && !isset($data->channel_id)) $array['product_attributes']['global'] = array_merge($array['product_attributes']['global'], $attribute_value);
            
            if(!isset($data->store_id) && isset($data->channel_id))
            {
                if(in_array($data->channel_id, $this->channels->pluck('id')->toArray())) $array['product_attributes']['channel'][$data->channel_id] = (array_key_exists ($data->channel_id, $array['product_attributes']['channel'])) ? array_merge($array['product_attributes'] ['channel'][$data->channel_id], $attribute_value) : $attribute_value;
            }

            if(isset($data->store_id) && !isset($data->channel_id)) 
            {
                if(in_array($data->store_id, $stores)) $array['product_attributes'] ['store'][$data->store_id] = (array_key_exists ($data->store_id, $array['product_attributes']['store'])) ? array_merge($array['product_attributes'] ['store'][$data->store_id], $attribute_value) : $attribute_value;
            }
        }

        return $array;
    }
}
