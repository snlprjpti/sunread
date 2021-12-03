<?php

namespace Modules\Product\Listeners;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\ProductObserverIndexer;

class ProductListener
{
    public function indexing($product)
    {
        $this->cacheClear($product);

        $batch = Bus::batch([])->onQueue("index")->dispatch();
        $batch->add(new ProductObserverIndexer($product)); 
    }

    public function remove($product)
    {
        $this->cacheClear($product);

        $batch = Bus::batch([])->onQueue("index")->dispatch();
        $batch->add(new ProductObserverIndexer(collect($product), "delete")); 
    }

    public function cacheClear($product)
    {
        Website::find($product->website_id)->channels->map(function ($channel) use($product) {
            $channel->stores->map(function ($store) use($product, $channel) {
                $cache_name = "product_details_{$product->id}_{$channel->id}_{$store->id}";
                Redis::del($cache_name);
            });
        });
    }

}
