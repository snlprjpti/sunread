<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\Website;

class ElasticSearchIndexingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product, $store;

    public function __construct(Model $product, Model $store)
    {
        $this->product = $product;
        $this->store = $store;
    }

    public function handle(): void
    {
        $client = ClientBuilder::create()->setHosts(config("elastic.client.hosts"))->build();
        $data = $this->product->documentDataStructure($this->store);
        $params = [
            "index" => "sail_racing_store_{$this->store->id}",
            "id" => $this->product->id,
            "body" => $data
        ];
        $client->index($params);
    }
}
