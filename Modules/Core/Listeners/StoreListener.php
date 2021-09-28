<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Bus;
use Modules\Core\Entities\Store;
use Modules\Core\Jobs\CoreCacheJob;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\SingleIndexing;

class StoreListener
{
    public function create(object $store): void
    {
        CoreCacheJob::dispatch( "createStoreCache", $store )->onQueue("high");

        //indexing products in elasticsearch for new store
        $website = $store?->channel?->website;
        $products = Product::whereWebsiteId($website->id)->get();
        $batch = Bus::batch([])->onQueue("index")->dispatch();
        foreach($products as $product) $batch->add(new SingleIndexing($product, $store));
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
    }
}
