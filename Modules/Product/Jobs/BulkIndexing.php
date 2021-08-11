<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\Website;

class BulkIndexing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function handle(): void
    {
        $client = ClientBuilder::create()->setHosts(config("elastic.client.hosts"))->build();

        foreach($this->products as $product)
        {
            $product->load("categories", "product_attributes", "catalog_inventories");
            $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });

            foreach($stores as $store)
            {
                $params["body"][] = [
                    "index" => [
                        "_index" => "sail_racing_store_{$store->id}",
                        "_id" => $product->id
                    ]
                ];
            
                $params["body"][] = $product->documentDataStructure($store);
            }
        }
        $client->bulk($params);
    }
}
