<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Product\Traits\ElasticSearch\ConfigurableProductHandler;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class ReIndexer implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing, ConfigurableProductHandler;

    public function __construct()
    {
        
    }

    public function handle(): void
    {
        try
        {
            $chunk_products = Product::with(["variants", "categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->whereParentId(null)->get()->chunk(100);
            foreach ($chunk_products as $products)
            {
                foreach ($products as $key => $product)
                {
                    $product_name = $product->name;
                    $product_name.$key = Bus::batch([])->onQueue("index")->dispatch();

                    $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                        return $channel->stores;
                    })->flatten(1);
                    
                    if ($product->type == "configurable") $chunk_variants = $product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get()->chunk(100);
                    
                    foreach ($stores as $store)
                    {
                        if ($product->type == "simple") $product_name.$key->add(new SingleIndexing($product, $store));
                        elseif ($product->type == "configurable") {
                            $product_name.$key->add(new ConfigurableIndexing($product, $store));

                            foreach ( $chunk_variants as $chunk_variant_key => $variants )
                            {
                                $key.$product_name.$chunk_variant_key = Bus::batch([])->onQueue("index")->dispatch();
                                foreach ($variants as $variant) {
                                    $key.$product_name.$chunk_variant_key->add(new VariantIndexing($product, $variants, $variant, $store));
                                }
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
