<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;

class WebsiteListener
{
    public function create($website)
    {
        CoreCache::createWebsiteCache($website);
    }

    public function update($website)
    {
        CoreCache::updateWebsiteCache($website);
    }

    public function delete($website)
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
