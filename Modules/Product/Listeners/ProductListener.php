<?php

namespace Modules\Product\Listeners;

use Illuminate\Support\Facades\Bus;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;

class ProductListener
{
    public function indexing($product)
    {
        if (!$product->parent_id && $product->type == "simple") {
            $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);
    
            $batch = Bus::batch([])->onQueue("index")->dispatch();
            foreach($stores as $store) $batch->add(new SingleIndexing($product, $store));
        }
    }

    public function remove($product)
    {
        $stores = Website::find($product->website_id)->channels->map(function ($channel) {
            return $channel->stores;
        })->flatten(1);
        
        $batch = Bus::batch([])->onQueue("index")->dispatch();
        foreach($stores as $store) $batch->add(new SingleIndexing(collect($product), $store, "delete"));
    }
}
