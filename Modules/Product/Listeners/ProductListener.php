<?php

namespace Modules\Product\Listeners;

use Elasticsearch\ClientBuilder;
use Modules\Core\Entities\Website;
use Modules\Product\Traits\ElasticSearch\ElasticSearchFormat;

class ProductListener
{
    use ElasticSearchFormat;
    
    public function indexing($product)
    {
        $client = ClientBuilder::create()->setHosts(config("elastic.client.hosts"))->build();

        dd($client);

        $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
            return $channel->stores->pluck("id");
        })->toArray();

        $data = $product->toArray();
        foreach($product->product_attributes()->with("attribute")->get() as $product_attribute)
        {
            $data[$product_attribute->attribute->slug] = $product_attribute->value?->value;
        }
        $data["categories"] = $product->categories()->pluck("id")->toArray();
        $data["catalog_inventories"] = $product->catalog_inventories;

        foreach($stores as $store)
        {
            $params = [
                "index" => "sail_racing_store_$store",
                "id" => $product->id,
                "body" => $data
            ];
            $client->index($params);
        }
    }
}
