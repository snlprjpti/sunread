<?php

namespace Modules\Product\Listeners;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Jobs\VariantIndexing;

class ProductListener
{
    public function indexing($product)
    {
        $this->cacheClear($product);
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
        $this->cacheClear($product);
        $stores = Website::find($product->website_id)->channels->map(function ($channel) {
            return $channel->stores;
        })->flatten(1);
        
        $batch = Bus::batch([])->onQueue("index")->dispatch();
        foreach($stores as $store) $batch->add(new SingleIndexing(collect($product), $store, "delete"));
    }

    public function cacheClear($product)
    {
        Website::find($product->website_id)->channels->map(function ($channel) use($product) {
            return $channel->stores->map(function ($store) use($product, $channel) {
                Cache::forget("product_detail_{$product->id}_{$channel->id}_{$store->id}");
            });
        });
    }

}
