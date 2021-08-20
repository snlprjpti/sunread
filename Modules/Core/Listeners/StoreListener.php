<?php

namespace Modules\Core\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;

class StoreListener
{
    public function create($store)
    {
        CoreCache::createStoreCache($store);
    }

    public function update($store)
    {
        CoreCache::updateStoreCache($store);
    }

    public function delete($store)
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
