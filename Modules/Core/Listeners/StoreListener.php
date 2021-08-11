<?php

namespace Modules\Core\Listeners;

use Elasticsearch\ClientBuilder;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;

class StoreListener
{
    public function indexing($store)
    {
        // $hosts = [
        //     "localhost:9200" 
        // ];
        
        // $client = ClientBuilder::create()->setHosts($hosts)->build();

        // $website = $store?->channel?->website;
        // $products = Product::whereWebsiteId($website->id)->get();
        // foreach($products as $product)
        // {
        //     $data = $product->toArray();
        //     foreach($product->product_attributes()->with("attribute")->get() as $product_attribute)
        //     {
        //         $data[$product_attribute->attribute->slug] = $product_attribute->value?->value;
        //     }
        //     $data["categories"] = $product->categories()->pluck("id")->toArray();
        //     $data["catalog_inventories"] = $product->catalog_inventories()->get();

        //     $params = [
        //         "index" => "sail_racing_store_{$store->id}",
        //         "id" => $product->id,
        //         "body" => $data
        //     ];
        //     $client->index($params);
        // }
    }
}
