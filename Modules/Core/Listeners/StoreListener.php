<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Store;
use Modules\Core\Facades\CoreCache;

class StoreListener
{
    public function create($store)
    {
        CoreCache::createStoreCache($store);
    }

    public function beforeUpdate($store)
    {
        CoreCache::updateBeforeStoreCache($store);
    }

    public function update($store)
    {
        CoreCache::createStoreCache($store);
    }

    public function delete($store)
    {
        CoreCache::deleteStoreCache($store);
    }
}
