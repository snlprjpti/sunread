<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class CoreCacheHelper
{
    protected $website, $channel, $store;

    public function __construct(Website $website, Channel $channel, Store $store)
    {
        $this->website = $website;
        $this->channel = $channel;
        $this->store = $store;
    }

    public function createWebsiteCache($website)
    {
        unset($website->channels, $website->stores);
        Cache::rememberForever("sf_website_{$website->hostname}", function () use($website) {
            return $website->toArray();
        });
        Cache::rememberForever("sf_website_{$website->id}", function () use($website) {
            return $website->toArray();
        });
    }

    public function deleteWebsiteCache($website)
    {

    }

    public function createChannelCache($channel)
    {
        $website = $channel->website;
        unset($channel->website, $channel->stores, $website->channels, $website->stores);

        Cache::rememberForever("sf_website_{$website->id}_channel_{$channel->id}", function () use($channel) {
            return $channel->toArray();
        });
        Cache::rememberForever("sf_website_{$website->id}_channel_{$channel->code}", function () use($channel) {
            return $channel->toArray();
        });
    }

    public function deleteChannelCache($channel)
    {

    }

    public function createStoreCache($store)
    {
        $channel = $store->channel;
        $website = $channel->website;

        unset($store->channel, $store->website, $channel->stores, $channel->website, $website->channels, $website->stores);

        Cache::rememberForever("sf_website_{$website->id}_channel_{$channel->id}_store_{$store->id}", function () use($store, $channel, $website) {
            $store["website_id"] = $website->id;
            return $store->toArray();
        });

        Cache::rememberForever("sf_website_{$website->id}_channel_{$channel->id}_store_{$store->code}", function () use($store, $channel, $website) {
            $store["website_id"] = $website->id;
            return $store->toArray();
        });
    }


    public function deleteStoreCache($store)
    {

    }
}
