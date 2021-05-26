<?php

namespace Modules\Product\Traits\ElasticSearch;

trait ElasticSearchFormat
{
    use CategoryFormat, AttributeFormat;

    protected $attribute_array = [
        'global' => [],
        'channel' => [],
        'store' => []
    ];

    protected $categoryData, $attributeData;


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
        foreach($this->channels as $channel)
        {
           foreach($channel->stores as $store)
           {
               array_push($stores, $store->id);
           }
        } 
       return array_unique($stores);
    }
}
