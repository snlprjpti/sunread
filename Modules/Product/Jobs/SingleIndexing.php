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

    public $product, $store, $method;

    public function __construct(object $product, Model $store, ?string $method = null)
    {
        $this->product = $product;
        $this->store = $store;
        $this->method = $method;
    }

    public function handle(): void
    {
        if(!$this->method) $this->singleIndexing($this->product, $this->store);
        else $this->removeIndex($this->product, $this->store);
    }
}
