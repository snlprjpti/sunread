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

class PartialIndexing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public $product, $store, $slug_values;

    public function __construct(object $product, Model $store, array $slug_values)
    {
        $this->product = $product;
        $this->store = $store;
        $this->slug_values = $slug_values;
    }

    public function handle(): void
    {
        $this->partialIndexing($this->product, $this->store, $this->slug_values);
    }
}
