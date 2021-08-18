<?php

namespace Modules\Product\Listeners;

use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;

class ProductListener
{
    public function indexing($product)
    {
        $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores;
        });

        SingleIndexing::dispatch($product, $stores, "product");
    }

    public function remove($product)
    {
        $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores;
        });
        
        SingleIndexing::dispatch(collect($product), $stores, "product", "delete");
    }
}
