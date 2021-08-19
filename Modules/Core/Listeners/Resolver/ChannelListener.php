<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class ChannelListener
{
    public function createCache($channel)
    {
        $hostname = $channel->website->hostname;
        unset($channel->website);

        $website = Cache::rememberForever("website_{$hostname}", function () use($hostname) {
            return Website::whereHostname($hostname)->setEagerLoads([])->first()->toArray();
        });

        $website["channel"][] = $channel->toArray();
        Cache::put("website_{$hostname}", $website);

        $channel = Cache::rememberForever("channel_{$channel->code}", function () use($channel) {
            $channel["website"] = Website::find($channel->website_id)->setEagerLoads([])->first()->toArray();
            $channel["stores"] = [];
            return $channel->toArray();
        });
    }

    public function updateCache($channel)
    {
        $hostname = $channel->website->hostname;
        unset($channel->website);

        foreach($channel["stores"] as $store) {
            unset($channel->stores);
            $cacheStore = Cache::get("store_{$store->code}");
            unset($cacheStore["channel"]);
            $cacheStore["channel"] = $channel->toArray();
            Cache::put("store_{$store->code}", $cacheStore);
        }

        $website = Cache::rememberForever("website_{$hostname}", function () use($hostname) {
            return Website::whereHostname($hostname)->setEagerLoads([])->first()->toArray();
        });
        $website_key = array_search($channel->id, array_column($website["channels"], 'id'));
        $website["channels"][$website_key] = $channel->toArray();
        Cache::put("website_{$hostname}", $website);
        Cache::put("channel_{$channel->code}", $channel->toArray());
    }

    public function deleteCache($channel)
    {
        Cache::forget("channel_{$channel->code}");
        $hostname = $channel->website->hostname;
        unset($channel->website);

        foreach($channel["stores"] as $store) {
            unset($channel->stores);
            $cacheStore = Cache::get("store_{$store->code}");
            unset($cacheStore["channel"]);
            Cache::put("store_{$store->code}", $cacheStore);
        }

        $website = Cache::get("website_{$hostname}");
        $website_key = array_search($channel->id, array_column($website["channel"], 'id'));
        unset($website["channel"][$website_key]);
        Cache::put("website_{$hostname}", $website);
    }
}
