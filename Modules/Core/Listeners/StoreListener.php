<?php

namespace Modules\Core\Listeners;

use Elasticsearch\ClientBuilder;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\ElasticSearchIndexingJob;

class StoreListener
{
    public function indexing($store)
    {
        $website = $store?->channel?->website;
        $products = Product::whereWebsiteId($website->id)->get();
        foreach($products as $product) ElasticSearchIndexingJob::dispatch($product, $store);
    }
}
