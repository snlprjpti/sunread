<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Store;
use Modules\Core\Jobs\CoreCacheJob;

class StoreListener
{
    public function create(object $store): void
    {
        CoreCacheJob::dispatch( "createStoreCache", $store );
    }

    public function beforeUpdate(int $store_id): void
    {
        $store = Store::findOrFail($store_id);
        CoreCacheJob::dispatch( "deleteStoreCache", collect($store) );
    }

    public function update(object $store): void
    {
        CoreCacheJob::dispatch( "createStoreCache", $store );
    }

    public function delete(object $store): void
    {
        CoreCacheJob::dispatch( "deleteStoreCache", collect($store) );
    }
}
