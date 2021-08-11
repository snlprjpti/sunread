<?php

namespace Modules\Product\Listeners;

use Elasticsearch\ClientBuilder;
use Modules\Core\Entities\Website;

class ProductListener
{
    public function indexing($product)
    {
        $client = ClientBuilder::create()->setHosts(config("elastic.client.hosts"))->build();

        $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores;
        });

        foreach($stores as $store)
        {
            $data = $product->documentDataStructure($store);
            $params = [
                "index" => "sail_racing_store_{$store->id}",
                "id" => $product->id,
                "body" => $data
            ];
            $client->index($params);
        }
    }
}
