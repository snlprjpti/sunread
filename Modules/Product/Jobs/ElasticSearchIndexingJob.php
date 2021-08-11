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

    public $product, $client;

    public function __construct(Model $product)
    {
        // $this->client = ClientBuilder::create()->setHosts(config("elastic.client.hosts"))->build();
        $this->product = $product;
    }

    public function handle(): void
    {
        $stores = Website::find($this->product->website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores;
        });

        foreach($stores as $store)
        {
            $data = $this->product->documentDataStructure($store);
            $params = [
                "index" => "sail_racing_store_{$store->id}",
                "id" => $this->product->id,
                "body" => $data
            ];
            $this->client->index($params);
        }
    }
}
