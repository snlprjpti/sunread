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
    }

    public function deleteCache($store)
    {
        Cache::forget("store_{$store->code}");
    }
}
