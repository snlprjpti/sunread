<?php

namespace Modules\Product\Traits\ElasticSearch;

use Modules\Attribute\Entities\AttributeGroupTranslation;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Category\Entities\CategoryTranslation;

trait AttributeFormat
{
    public function getScopeWiseAttribute()
    {
        foreach($this->product_attributes as $data){
            
            $this->getAttributeData($data);

            if(!isset($data->store_id) && !isset($data->channel_id))  $this->getGlobalWiseAttribute();

            foreach($this->channels->pluck('id')->toArray() as $channelID)
            {
                $this->getChannelWiseAttribute($data, $channelID);

                foreach($this->getChannelWiseStoreID() as $storeID)
                {
                    $this->getStoreWiseAttribute($data, $channelID, $storeID);
                }
            }
        }
        
        return $this->attribute_array;
    }

    public function getGlobalWiseAttribute()
    {
        $this->attribute_array['global'] = array_merge($this->attribute_array['global'], $this->attributeData);
    }

    public function getChannelWiseAttribute($data, $channelID)
    {
        if($data->channel_id == $channelID)
        $this->attribute_array['channel'][$channelID] = (array_key_exists ($data->channel_id, $this->attribute_array['channel'])) ? array_merge($this->attribute_array['channel'][$data->channel_id], $this->attributeData) : $this->attributeData;
        else 
        $this->attribute_array['channel'] [$channelID] = $this->attribute_array['global'];
           
    }

    public function getStoreWiseAttribute($data, $channelID, $storeID)
    {
        if($data->store_id == $storeID)
        $this->attribute_array['store'] [$storeID] = array_key_exists ($data->store_id, $this->attribute_array['store']) ? array_merge($this->attribute_array['store'][$data->store_id], $this->attributeData) : $this->attributeData; 
        else 
        $this->attribute_array['store'] [$storeID] = $this->attribute_array['channel'] [$channelID];
    }

    public function getAttributeData($data)
    {
        $attribute = $data->attribute;
        $attribute_group = $attribute->attribute_group;
        $attribute_family = $attribute_group->attribute_family;

        return $this->attributeData = [
            $attribute->slug => [
                "attribute" => [
                    "id" => $attribute->id,
                    "slug" => $attribute->slug,
                    "name" => $attribute->name,
                    "type" => $attribute->type,
                    "value" => isset($data->value->value) ?? $data->value->value,
                    "attribute_group" => [
                        "id" => $attribute_group->id,
                        "name" => $attribute_group->name,
                        "slug" => $attribute_group->slug,
                        "attribute_family" => [
                            "id" => $attribute_family->id,
                            "name" => $attribute_family->name,
                            "slug" => $attribute_family->slug
                        ],
                    ]
                ]
            ]
        ];
    }

    public function getAttributeTranslationData($data)
    {
        if(isset($data->store_id)) $storeWiseAttribute = AttributeTranslation::where([
            [ 'attribute_id', $data->attribute->id ],
            [ 'store_id', $data->store_id ]
        ])->first();
        if(isset($data->store_id)) $storeWiseAttributeGroup = AttributeGroupTranslation::where([
            [ 'attribute_group_id', $data->attribute->attribute_group_id ],
            [ 'store_id', $data->store_id ]
        ])->first();

        return (!isset($storeWiseAttribute) && !isset($storeWiseAttributeGroup)) ? $this->attributeData :
        array_merge($this->categoryData, [
            $data->attribute->slug => [
                "attribute" => [
                    "name" => (isset($storeWiseAttribute) ) ? $storeWiseAttribute->name : $data->attribute->name,
                    "attribute_group" => [
                        "name" => (isset($storeWiseAttributeGroup) ) ? $storeWiseAttributeGroup->name : $data->attribute->attribute_group->name
                    ]
                ]
            ]
        ]);
    }

}