<?php

namespace Modules\Product\Traits\ElasticSearch;

use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeGroupTranslation;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Category\Entities\CategoryTranslation;

trait AttributeFormat
{
    public function getScopeWiseAttribute()
    {
        $uniqueAttributeIDS= array_unique($this->product_attributes->pluck('attribute_id')->toArray());
        $available_attributes = Attribute::whereIn('id', $uniqueAttributeIDS)->get();

        foreach($available_attributes as $attribute)
        {
            $this->getSingleAttributeData($attribute);

            if(isset($this->globalAttributes)) $this->getGlobalWiseAttribute();

            if(isset($this->channelAttributes)) $this->getChannelWiseAttribute();

          
            if(isset($this->storeAttributes)) $this->getStoreWiseAttribute();

        }
        return $this->attribute_array;
    }

    public function getSingleAttributeData($attribute)
    {
        $attributes = $this->product_attributes()->where('attribute_id', $attribute->id);

        $this->globalAttributes = (clone $attributes)->where('store_id', null)->where('channel_id', null)->first();
        $this->channelAttributes = (clone $attributes)->where('channel_id', '!=', null)->get();
        $this->storeAttributes = $attributes->where('store_id', '!=', null)->get();

    }

    public function getGlobalWiseAttribute()
    {
        $data = $this->globalAttributes;
        $this->getAttributeData($data);
        $this->attribute_array['global'][$data->attribute->slug] = $this->attributeData;
    }

    public function getChannelWiseAttribute()
    {
        foreach($this->channels->pluck('id')->toArray() as $channelID){
            foreach($this->channelAttributes as $data){
                if(isset($data)){
                    $this->getAttributeData($data);

                    if($data->channel_id == $channelID)
                    $item = $this->attributeData;

                    if(!in_array($channelID, $this->channelAttributes->pluck('channel_id')->toArray()))
                    $item = $this->attribute_array['global'][$data->attribute->slug] ?? [];

                    $this->attribute_array['channel'] [$channelID][$data->attribute->slug] = $item;
                }
            }
        }
    }

    public function getStoreWiseAttribute()
    {
        foreach($this->getChannelWiseStoreID() as $storeID){
            foreach($this->storeAttributes as $data){
                if(isset($data)){
                    $this->getAttributeData($data);

                    if($data->store_id == $storeID)
                    $item =  $this->attributeData; 

                    if(!in_array($storeID, $this->storeAttributes->pluck('store_id')->toArray())){
                    $input = $this->attribute_array['channel'] [$this->getChannelID($storeID)] [$data->attribute->slug] ?? 
                    $this->attribute_array['global'][$data->attribute->slug] ?? [] ;
                    $item = $this->mergeAttributeData($input, $storeID, $data);
                    }

                    $this->attribute_array['store'] [$storeID][$data->attribute->slug] = $item;
                }
            }
        }
    }

    public function getAttributeData($data)
    {
        $attribute = (!$data->store_id) ? $data->attribute : $data->attribute->firstTranslation($data->store_id);

        $this->attributeData = [
            "attribute" => $attribute,
            "value" => isset($data->value->value) ? $data->value->value : ""
        ];
    }

    public function mergeAttributeData($input, $storeID, $data)
    {
        $input["attribute"] = (!$storeID) ? $data->attribute : $data->attribute->firstTranslation($storeID);
        return $input;
    }
}