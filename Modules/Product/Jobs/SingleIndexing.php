<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class SingleIndexing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public $product, $store;

    public function __construct(Model $product, Model $store)
    {
        $this->product = $product;
        $this->store = $store;
    }

    public function handle(): void
    {
        $this->singleIndexing($this->product, $this->store);
    }
}
