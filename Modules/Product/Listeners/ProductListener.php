<?php

namespace Modules\Product\Listeners;

use Illuminate\Support\Facades\Bus;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Jobs\VariantIndexing;

class ProductListener
{
    public function indexing($product)
    {
        if($product->type == "simple") {
            $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);
    
            $batch = Bus::batch([])->onQueue("index")->dispatch();

            if (!$product->parent_id) {
                foreach($stores as $store) $batch->add(new SingleIndexing($product, $store)); 
            }
            else {
                $variants = $product->parent->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();
                foreach($stores as $store) $batch->add(new VariantIndexing($product->parent, $variants, $product, $store));
            }

        }
    }

    public function remove($product)
    {
        $stores = Website::find($product->website_id)->channels->map(function ($channel) {
            return $channel->stores;
        })->flatten(1);
        
        $batch = Bus::batch([])->onQueue("index")->dispatch();
        foreach($stores as $store) $batch->add(new SingleIndexing(collect($product), $store, "delete"));
    }
}
