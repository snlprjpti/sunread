<?php

namespace Modules\Core\Listeners\Resolver;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class StoreListener
{
    public function createCache($store)
    {
        Cache::put("store_{$store->code}", $store);
        $channel_code = $store->channel->code;
        $channel = Cache::get("channel_{$channel_code}");
        unset($store->channel);
        $channel["stores"][($store->id - 1)] = $store;
        Cache::put("channel_{$channel_code}", $channel);
    }

    public function updateCache($store)
    {
        Cache::put("store_{$store->code}", $store);
        $channel_code = $store->channel->code;
        $channel = Cache::get("channel_{$channel_code}");
        $key = array_search($store->id, array_column($channel["stores"]->toArray(), 'id'));
        unset($store->channel);
        $channel["stores"][$key] = $store;
        Cache::put("channel_{$channel_code}", $channel);
    }

    public function deleteCache($store)
    {
        Cache::forget("store_{$store->code}");
        $channel_code = $store->channel->code;
        $channel = Cache::get("channel_{$channel_code}");
        $key = array_search($store->id, array_column($channel["stores"]->toArray(), 'id'));
        unset($store->channel);
        unset($channel["stores"][$key]);
    }
}
