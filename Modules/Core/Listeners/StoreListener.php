<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Store;
use Modules\Core\Jobs\CoreCacheJob;
use Modules\Product\Jobs\ReindexMigrator;
use Modules\Product\Jobs\RemoveIndex;

class StoreListener
{
    public function create(object $store): void
    {
        CoreCacheJob::dispatch( "createStoreCache", $store )->onQueue("high");

        //indexing products in elasticsearch for new store
        ReindexMigrator::dispatch($store)->onQueue("index");
    }

    public function beforeUpdate(int $store_id): void
    {
        $store = Store::findOrFail($store_id);
        CoreCacheJob::dispatch( "deleteStoreCache", collect($store) )->onQueue("high");
    }

    public function update(object $store): void
    {
        CoreCacheJob::dispatch( "createStoreCache", $store )->onQueue("high");
    }

    public function delete(object $store): void
    {
        CoreCacheJob::dispatch( "deleteStoreCache", collect($store) )->onQueue("high");

        //remove index
        RemoveIndex::dispatch(collect($store))->onQueue("index");
    }
}
