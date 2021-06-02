<?php

namespace Modules\Product\Traits\ElasticSearch;

use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;

trait ElasticSearchFormat
{
    use AttributeFormat;

    protected $attribute_array = [
        'global' => [],
        'channel' => [],
        'store' => []
    ];

    protected $categoryData, $attributeData, $globalAttributes, $channelAttributes, $storeAttributes;


    public function documentDataStructure(): array
    {
        $array = $this->toArray();
        
        $array['categories'] = $this->getScopeWiseCategory();
        
        $array['channels'] = $this->channels;

        $array['product_attributes'] = $this->getScopeWiseAttribute();

        return $array;
    }

    public function getChannelWiseStoreID()
    {
        $stores = [];   
        foreach($this->channels as $channel) foreach($channel->stores as $store) array_push($stores, $store->id);
        return array_unique($stores);
    }

    public function getChannelID($store_id)
    {
        foreach($this->channels as $channel) if(in_array($store_id, $channel->stores->pluck('id')->toArray())) return $channel->id;
    }

    public function getScopeWiseCategory()
    {
        $data = [];
        
        foreach($this->categories as $category)
        {
            $data['global'][] = $category;
            foreach($this->getChannelWiseStoreID() as $store_id) $data['store'][$store_id][] = $category->firstTranslation($store_id);
        }

        return $data;
    }
}
