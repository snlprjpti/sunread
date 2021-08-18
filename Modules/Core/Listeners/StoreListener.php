<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Bus;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\SingleIndexing;

class StoreListener
{
    public function indexing($store)
    {
        $website = $store?->channel?->website;
        $products = Product::whereWebsiteId($website->id)->get();

        $batch = Bus::batch([])->dispatch();
        foreach($products as $product) $batch->add(new SingleIndexing($product, $store));
    }
}
