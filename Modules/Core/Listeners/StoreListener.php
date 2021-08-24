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
        CoreCache::createStoreCache($store);
    }

    public function delete($store)
    {
        Cache::forget("sf_website_{$website?->hostname}_channel_{$channel?->code}_store_{$store_code}");
    }
}
