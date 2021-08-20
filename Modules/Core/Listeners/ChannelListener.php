<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;

class ChannelListener
{
    public function create($channel)
    {
        CoreCache::createChannelCache($channel);
    }

    public function beforeUpdate($id)
    {
        $updated = Channel::findOrFail($id);
        Cache::rememberForever("channel_mapper_$id", function () use($updated) {
            return $updated->toArray();
        });
    }
    public function update($channel)
    {
        CoreCache::updateChannelCache($channel);
    }

    public function delete($channel)
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
