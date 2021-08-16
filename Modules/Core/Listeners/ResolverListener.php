<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;

class ResolverListener
{
    public function updateWebsite($website)
    {
        Cache::put("website_{$website->hostname}", $website);
    }

    public function deleteWebsite($website)
    {
        Cache::forget("website_{$website->hostname}");
    }

    public function updateChannel($channel)
    {
        Cache::put("channel_{$channel->code}", $channel);
    }

    public function deleteChannel($channel)
    {
        Cache::forget("channel_{$channel->code}");
    }

    public function updatestore($store)
    {
        Cache::put("store_{$store->code}", $store);
    }

    public function deleteStore($store)
    {
        Cache::forget("store_{$store->code}");
    }

}
