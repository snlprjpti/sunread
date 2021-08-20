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
        Cache::rememberForever("website_{$website->hostname}", function () use($website) {
            return $this->website->whereHostname($website->hostname)->setEagerLoads([])->first()->toArray();
        });
    }

    public function updateWebsiteCache($website)
    {
        if(!(Cache::has("website_{$website->hostname}"))) $this->createWebsiteCache($website);
        $cacheWebsite = Cache::get("website_{$website->hostname}");
       
        unset($website->channels, $website->stores);

        if(isset($cacheWebsite["channels"])) {
            foreach($cacheWebsite["channels"] as $channel) {
                if(!(Cache::has("channel_{$channel["code"]}"))) $this->createChannelCache($channel);

                $cacheChannel = Cache::get("channel_{$channel["code"]}");
                $cacheChannel["website"] = $website->toArray();
                Cache::put("channel_{$channel["code"]}", $cacheChannel);
            }
        }

        if(isset($cacheWebsite["stores"])) {
            foreach($cacheWebsite["stores"] as $store) {
                if(!(Cache::has("store_{$store["code"]}"))) $this->createStoreCache($store);

                $cacheStore = Cache::get("store_{$store["code"]}");
                $cacheStore["website"] = $website->toArray();
                Cache::put("store_{$store["code"]}", $cacheStore);
            }
        }

        $cacheWebsite = array_merge($cacheWebsite, $website->toArray());
        Cache::put("website_{$website->hostname}",  $cacheWebsite);
    }

    public function deleteWebsiteCache($website)
    {

    }

    public function createChannelCache($channel)
    {
        $website = $channel->website;
        unset($channel->website, $channel->stores, $website->channels, $website->stores);

        if(!(Cache::get("website_{$website->hostname}"))) $this->createWebsiteCache($website);
        $websiteCache = Cache::get("website_{$website->hostname}");

        $websiteCache["channels"][] = $channel->toArray();
        Cache::put("website_{$website->hostname}", $websiteCache);

        Cache::rememberForever("channel_{$channel->code}", function () use($channel, $website) {
            $channel["website"] = $website->toArray();
            return $channel->toArray();
        });
    }

    public function updateChannelCache($channel)
    {
        if(!(Cache::has("channel_{$channel->code}"))) $this->createChannelCache($channel);
        $cacheChannel = Cache::get("channel_{$channel->code}");

        $website = $channel->website;
        unset($channel->website, $channel->stores, $website->channels, $website->stores);

        if(isset($cacheChannel["stores"])) {
            foreach($cacheChannel["stores"] as $store) {
                if(!(Cache::has("store_{$store["code"]}"))) $this->createStoreCache($store);
                $cacheStore = Cache::get("store_{$store["code"]}");
                $cacheStore["channel"] = $channel->toArray();
                Cache::put("store_{$store["code"]}", $cacheStore);
            }
        }
        
        if(!(Cache::has("website_{$website->hostname}"))) $this->updateWebsiteCache($website);
        $cacheWebsite = Cache::get("website_{$website->hostname}");

        if(!isset($cacheWebsite["channels"])) $cacheWebsite["channels"][] = $channel->toArray();
        else {
            $channel_key = array_search($channel->id, array_column($cacheWebsite["channels"], 'id'));
            if($channel_key) $cacheWebsite["channels"][$channel_key] = $channel->toArray();
            else $cacheWebsite["channels"][] = $channel->toArray();
        }
        Cache::put("website_{$website->hostname}", $cacheWebsite);

        $cacheChannel["website"] = $website->toArray();
        $cacheChannel = array_merge($cacheChannel, $channel->toArray());
        Cache::put("channel_{$channel->code}", $cacheChannel);  
    }

    public function deleteChannelCache($channel)
    {

    }

    public function createStoreCache($store)
    {
        $channel = $store->channel;
        $website = $channel->website;

        if(!(Cache::has("channel_{$channel->code}"))) $this->createChannelCache($channel);
        $channelCache = Cache::get("channel_{$channel->code}");
        if(!(Cache::has("website_{$website->hostname}"))) $this->createWebsiteCache($website);
        $websiteCache = Cache::get("website_{$website->hostname}");

        unset($store->channel, $store->website, $channel->stores, $channel->website, $website->channels, $website->stores);

        $channelCache["stores"][] = $store->toArray();
        $websiteCache["stores"][] = $store->toArray();
        Cache::put("channel_{$channel->code}", $channelCache);
        Cache::put("website_{$website->hostname}", $websiteCache);

        $store = Cache::rememberForever("store_{$store->code}", function () use($store, $channel, $website) {
            $store["channel"] = $channel->toArray();
            $store["website"] = $website->toArray();
            return $store->toArray();
        });
    }

    public function updateStoreCache($store)
    {
        if(!(Cache::has("store_{$store->code}"))) $this->createStoreCache($store);
        $storeCache = Cache::get("store_{$store->code}");

        $channel = $store->channel;
        $website = $channel->website;
        unset($store->channel, $store->website, $channel->website, $channel->stores, $website->channels, $website->stores);

        if(!(Cache::has("channel_{$channel->code}"))) $this->createChannelCache($channel);
        $cacheChannel = Cache::get("channel_{$channel->code}");

        if(!(Cache::has("website_{$website->hostname}"))) $this->createWebsiteCache($website);
        $cacheWebsite = Cache::get("website_{$website->hostname}");

        $channel_key = ($cacheChannel) ? array_search($store->id, array_column($cacheChannel["stores"], 'id')) : null;
        $website_key = ($cacheWebsite) ? array_search($store->id, array_column($cacheWebsite["stores"], 'id')) : null;

        $cacheChannel["stores"][$channel_key] = $store->toArray();
        $cacheWebsite["stores"][$website_key] = $store->toArray();
        Cache::put("channel_{$channel->code}", $cacheChannel);
        Cache::put("website_{$website->hostname}", $cacheWebsite);

        $storeCache = array_merge($storeCache, $store->toArray());
        Cache::put("store_{$store->code}", $storeCache); 
    }

    public function deleteStoreCache($store)
    {

    }
}
