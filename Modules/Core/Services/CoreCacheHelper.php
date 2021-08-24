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
            return $website;
        });
    }

    public function createChannelCache($channel)
    {
        unset($channel->website, $channel->stores);

        Cache::rememberForever("sf_channel_{$channel->code}", function () use($channel) {
            return $channel;
        });
    }

    public function createStoreCache($store)
    {
        $website = $store->channel->website;

        unset($store->channel, $store->website);

        Cache::rememberForever("sf_store_{$store->id}", function () use($store, $website) {
            $store["website_id"] = $website->id;
            return $store->toArray();
        });
    }

    public function getWebsiteCache(string $website_hostname): ?object
    {
        return Cache::rememberForever("sf_website_{$website_hostname}", function () use($website_hostname) {
            return $this->website->whereHostname($website_hostname)->setEagerLoads([])->firstOrFail();
        });
    }

    public function getChannelCache(?object $website, string $channel_code): ?object
    {
        if(!$website) return null;
        return Cache::rememberForever("sf_channel_{$channel_code}", function () use($channel_code, $website) {
            return $this->channel->whereWebsiteId($website?->id)->whereCode($channel_code)->setEagerLoads([])->firstOrFail();
        });
    }

    public function getStoreCache(?object $website, ?object $channel, string $store_code): ?object
    {
        if(!$website) return null;
        return Cache::rememberForever("sf_store_{$store_code}", function () use($store_code, $channel, $website) {
            $store = $this->store->whereChannelId($channel?->id)->whereCode($store_code)->setEagerLoads([])->firstOrFail();
            $store["website_id"] = $website?->id;
            return $store;
        });
    }
}
