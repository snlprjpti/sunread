<?php

namespace Modules\Core\Listeners;

use Modules\Product\Entities\Product;
use Modules\Product\Jobs\SingleIndexing;

class StoreListener
{
    public function indexing($store)
    {
        $website = $store?->channel?->website;
        $products = Product::whereWebsiteId($website->id)->get();
        foreach($products as $product) SingleIndexing::dispatch($product, $store);
    }
}
