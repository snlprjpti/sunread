<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;

class WebsiteListener
{
    public function createCache($website)
    {
        $website = Cache::rememberForever("website_{$website->hostname}", function () use($website) {
            $website =  Website::whereHostname($website->hostname)->setEagerLoads([])->first()->toArray();
            $website["channels"] = [];
            $website["stores"] = [];
            return $website;
        });
    }

    public function updateCache($website)
    {
        $cacheWebsite = Cache::get("website_{$website->hostname}");
        unset($website->channels);
        unset($website->stores);

        foreach($cacheWebsite["channels"] as $channel) {
            $cacheChannel = Cache::get("channel_{$channel["code"]}");
            $cacheChannel["website"] = $website->toArray();
            Cache::put("channel_{$channel["code"]}", $cacheChannel);
        }

        foreach($cacheWebsite["stores"] as $store) {
            $cacheStore = Cache::get("store_{$store["code"]}");
            $cacheStore["website"] = $website->toArray();
            Cache::put("store_{$store["code"]}", $cacheStore);
        }

        $cacheWebsite = array_merge($cacheWebsite, $website->toArray());
        Cache::put("website_{$website->hostname}",  $cacheWebsite);
    }

    public function deleteCache($website)
    {
        Cache::forget("website_{$website->hostname}");

        foreach($website["channels"] as $channel) {
            $cacheStore = Cache::get("channel_{$channel->code}");
            unset($cacheStore["channels"]);
            Cache::put("channel_{$channel->code}", $cacheStore);
        }

        foreach($website["stores"] as $store) {
            $cacheStore = Cache::get("store_{$store->code}");
            unset($cacheStore["stores"]);
            Cache::put("store_{$store->code}", $cacheStore);
        }
    }
}
