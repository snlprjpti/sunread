<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;

class StoreListener
{
    public function createCache($store)
    {
        $channel_code = $store->channel->code;
        $hostname = $store->channel->website->hostname;
        unset($store->channel);

        $channel = Cache::rememberForever("channel_{$channel_code}", function () use($channel_code) {
            return Channel::whereCode($channel_code)->setEagerLoads([])->first()->toArray();
        });
        $website = Cache::rememberForever("website_{$hostname}", function () use($hostname) {
            return Website::whereHostname($hostname)->setEagerLoads([])->first()->toArray();
        });

        $channel["stores"][] = $store->toArray();
        $website["stores"][] = $store->toArray();
        Cache::put("channel_{$channel_code}", $channel);
        Cache::put("website_{$hostname}", $website);
        $store = Cache::rememberForever("store_{$store->code}", function () use($store, $hostname, $channel_code) {
            $store["channel"] = Channel::whereCode($channel_code)->setEagerLoads([])->first()->toArray();
            $store["website"] = Website::whereHostname($hostname)->setEagerLoads([])->first()->toArray();
            return $store->toArray();
        });
    }

    public function updateCache($store)
    {
        $channel_code = $store->channel->code;
        $hostname = $store->channel->website->hostname;
        unset($store->channel);

        $store["channel"] = Channel::whereCode($channel_code)->setEagerLoads([])->first()->toArray();
        $store["website"] = Website::whereHostname($hostname)->setEagerLoads([])->first()->toArray();
        Cache::put("store_{$store->code}", $store->toArray());

        $channel = Cache::get("channel_{$channel_code}");
        $website = Cache::get("website_{$hostname}");

        $channel_key = ($channel) ? array_search($store->id, array_column($channel["stores"], 'id')) : null;
        $website_key = ($website) ? array_search($store->id, array_column($website["stores"], 'id')) : null;
        unset($store->website);
        unset($store->channel);
        $channel["stores"][$channel_key] = $store->toArray();
        $website["stores"][$website_key] = $store->toArray();
        Cache::put("channel_{$channel_code}", $channel);
        Cache::put("website_{$hostname}", $website);
    }

    public function deleteCache($store)
    {
        Cache::forget("store_{$store->code}");
        $channel_code = $store->channel->code;
        $hostname = $store->channel->website->hostname;
        unset($store->channel);

        $channel = Cache::get("channel_{$channel_code}");
        $website = Cache::get("website_{$hostname}");
        $channel_key = array_search($store->id, array_column($channel["stores"], 'id'));
        $website_key = array_search($store->id, array_column($channel["stores"], 'id'));
        unset($channel["stores"][$channel_key]);
        unset($website["stores"][$website_key]);
        Cache::put("channel_{$channel_code}", $channel);
        Cache::put("website_{$hostname}", $website);
    }
}
