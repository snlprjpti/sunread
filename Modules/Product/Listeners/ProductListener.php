<?php

namespace Modules\Product\Listeners;

use Elasticsearch\ClientBuilder;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\ElasticSearchIndexingJob;

class ProductListener
{
    public function indexing($product)
    {
        ElasticSearchIndexingJob::dispatch($product);
    }
}
