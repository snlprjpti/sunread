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

    protected $option_attribute_array = [
        'global' => [],
        'channel' => [],
        'store' => []
    ];

    protected $categoryData, $attributeData, $globalAttributes, $channelAttributes, $storeAttributes, $mainChannels, $mainStores;


    public function documentDataStructure(): array
    {
        $this->getChannels();
        $this->getStores();

        $array = $this->toArray();
        
        $array['categories'] = $this->getScopeWiseCategory();
        
        $array['channels'] = $this->channels->toArray();

        $array['product_attributes'] = $this->getScopeWiseAttribute();

        return $array;
    }

    public function getChannels(): void
    {
        $this->mainChannels = Channel::pluck('id')->toArray();
    }

    public function getStores(): void
    {
        $this->mainStores = Store::pluck('id')->toArray();
    }

    public function getChannelID(int $store_id): int
    {
        foreach($this->channels as $channel) if(in_array($store_id, $channel->stores->pluck('id')->toArray())) return $channel->id;
        return 0;
    }

    public function getScopeWiseCategory(): array
    {
        $data = [];
        
        foreach($this->categories as $category)
        {
            $data['global'][] = $category->toArray();
            foreach($this->mainStores as $store_id) $data['store'][$store_id][] = $category->firstTranslation($store_id);
        }

        return $data;
    }
}
