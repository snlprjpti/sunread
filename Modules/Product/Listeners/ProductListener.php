<?php

namespace Modules\Product\Listeners;

use Modules\Core\Entities\Website;
use Modules\Product\Jobs\ElasticSearchIndexingJob;

class ProductListener
{
    public function indexing($product)
    {
        $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores;
        });

        foreach($stores as $store) ElasticSearchIndexingJob::dispatch($product, $store);
    }
}
