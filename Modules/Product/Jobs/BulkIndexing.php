<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\Website;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class BulkIndexing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function handle(): void
    {
       $this->bulkIndexing($this->products);
    }
}
