<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Store;
use Modules\Core\Jobs\CoreCacheJob;

class StoreListener
{
    public function create($store)
    {
        CoreCacheJob::dispatch( "createStoreCache", $store );
    }

    public function beforeUpdate($store_id)
    {
        $store = Store::findOrFail($store_id);
        CoreCacheJob::dispatch( "deleteStoreCache", collect($store) );
    }

    public function update($store)
    {
        CoreCacheJob::dispatch( "createStoreCache", $store );
    }

    public function delete($store)
    {
        CoreCacheJob::dispatch( "deleteStoreCache", collect($store) );
    }
}
