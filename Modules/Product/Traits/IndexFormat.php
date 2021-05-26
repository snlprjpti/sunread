<?php

namespace Modules\Product\Traits;

use Modules\Attribute\Entities\AttributeGroupTranslation;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Category\Entities\CategoryTranslation;

trait IndexFormat
{
    protected $attribute_array = [
        'global' => [],
        'channel' => [],
        'store' => []
    ];

    protected $attribute_value;


    public function documentDataStructure(): array
    {
        $array = $this->toArray();
        
        $array['categories'] = $this->getScopeWiseCategory();
        
        $array['channels'] = $this->channels;

        $array['product_attributes'] = $this->getScopeWiseAttribute();

        return $array;
    }

    public function getAttributeData($data)
    {
        if(isset($data->store_id)) $storeWiseAttribute = AttributeTranslation::where([
            [ 'attribute_id', $data->attribute->id ],
            [ 'store_id', $data->store_id ]
        ])->first();
        if(isset($data->store_id)) $storeWiseAttributeGroup = AttributeGroupTranslation::where([
            [ 'attribute_group_id', $data->attribute->attribute_group_id ],
            [ 'store_id', $data->store_id ]
        ])->first();
        return [
                $data->attribute->slug => [
                    "attribute" => [
                        "id" => $data->attribute->id,
                        "slug" => $data->attribute->slug,
                        "name" => (isset($storeWiseAttribute) ) ? $storeWiseAttribute->name : $data->attribute->name,
                        "type" => $data->attribute->type,
                        'value' => isset($data->value->value) ? $data->value->value : "",
                    ],
                    "attribute_group" => [
                        "id" => $data->attribute->attribute_group->id,
                        "name" => (isset($storeWiseAttributeGroup) ) ? $storeWiseAttributeGroup->name : $data->attribute->attribute_group->name,
                        "slug" => $data->attribute->attribute_group->slug,
                        "attribute_family" => [
                            "id" => $data->attribute->attribute_group->attribute_family->id,
                            "name" => $data->attribute->attribute_group->attribute_family->name,
                            "slug" => $data->attribute->attribute_group->attribute_family->slug,
                        ]
                    ],

                ],
        ];
    }

    public function getChannelWiseStoreID()
    {
        $stores = [];   
        foreach($this->channels as $channel)
        {
           foreach($channel->stores as $store)
           {
               array_push($stores, $store->id);
           }
        } 
       return array_unique($stores);
    }

    public function getScopeWiseAttribute()
    {
        foreach($this->product_attributes as $data){
            
            $this->attribute_value = $this->getAttributeData($data);

            if(!isset($data->store_id) && !isset($data->channel_id))  $this->getGlobalWiseAttribute();

            foreach($this->channels->pluck('id')->toArray() as $channelID)
            {
                if($data->channel_id == $channelID) $this->getChannelWiseAttribute($data);
                else $this->attribute_array['channel'] [$channelID] =  $this->attribute_array['global'];

                foreach($this->getChannelWiseStoreID() as $storeID)
                {
                    if($data->store_id == $storeID) $this->getStoreWiseAttribute($data);
                    else $this->attribute_array['store'] [$storeID] =  $this->attribute_array['channel'] [$channelID];
                }
            }
        }

        return $this->attribute_array;
    }

    public function getGlobalWiseAttribute()
    {
        $this->attribute_array['global'] = array_merge($this->attribute_array['global'], $this->attribute_value);
    }

    public function getChannelWiseAttribute($data)
    {
            if(array_key_exists ($data->channel_id, $this->attribute_array['channel']))
            $this->attribute_array['channel'][$data->channel_id] = array_merge($this->attribute_array['channel'][$data->channel_id], $this->attribute_value);
            else
            $this->attribute_array['channel'][$data->channel_id] = $this->attribute_value;
    }

    public function getStoreWiseAttribute($data)
    {
            if(array_key_exists ($data->store_id, $this->attribute_array['store']))
            $this->attribute_array['store'][$data->store_id] = array_merge($this->attribute_array['store'][$data->store_id], $this->attribute_value);
            else 
            $this->attribute_array['store'][$data->store_id] = $this->attribute_value;
    }

    public function getScopeWiseCategory()
    {
        $data = [];
        foreach($this->categories as $category)
        {
            $data[$category->id]['global'] = $this->getCategoryData($category);
            foreach($this->getChannelWiseStoreID() as $store_id)
            {
                $data[$category->id][$store_id] = $this->getCategoryData($category, $store_id);
            }
        }
        return $data;
    }

    public function getCategoryData($category, $store_id = null)
    {
        if(isset($store_id)) $storeWiseCategory = CategoryTranslation::where('category_id', $category->id)->where('store_id', $store_id)->first();

        return [
                "id" => $category->id,
                "name" => (isset($storeWiseCategory)) ? $storeWiseCategory->name : $category->name,
                "slug" => $category->slug,
                "position" => $category->position,
                "image" => $category->image_url,
                "description" => (isset($storeWiseCategory)) ? $storeWiseCategory->description : $category->description,
                "meta_title" => (isset($storeWiseCategory)) ? $storeWiseCategory->meta_title : $category->meta_title,
                "meta_description" => (isset($storeWiseCategory)) ? $storeWiseCategory->meta_description : $category->meta_description,
                "meta_keywords" => (isset($storeWiseCategory)) ? $storeWiseCategory->meta_keywords : $category->meta_keywords,
                "status" => $category->status,
                "_lft" => $category->_lft,
                "_rgt" => $category->_rgt,
                "parent" => $category->parent ?? null,
                "created_at" => $category->created_at->format('M d, Y H:i A'),            
                ];
       
    }

}
