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
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class ReIndexer implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public function __construct()
    {
    }

    public function handle(): void
    {
        try
        {
            $count = 0;
            $chunk_products = Product::with(["variants", "categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->whereParentId(null)->get()->chunk(100);
            
            foreach ($chunk_products as $products)
            {
                foreach ($products as $product)
                {
                    if ($count == 3) break;

                    $product_batch = Bus::batch([])->onQueue("index")->dispatch();
                    $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                        return $channel->stores;
                    })->flatten(1);
        
                    if ($product->type == "configurable") {
                        $all_variants = $product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();
                        $chunk_variants = $all_variants->chunk(100);
                    }
        
                    foreach ($stores as $store)
                    {
                        if ($product->type == "simple") $product_batch->add(new SingleIndexing($product, $store));
                        elseif ($product->type == "configurable") {
                            $product_batch->add(new ConfigurableIndexing($product, $store));
                            // foreach ( $chunk_variants as $variants )
                            // {
                            //     $variant_batch = Bus::batch([])->onQueue("index")->dispatch();
                            //     foreach ($variants as $variant) {
                            //         $variant_batch->add(new VariantIndexing($product, $all_variants, $variant, $store));
                            //     }
                            // }
                        }
                    }
    
                    $count++;
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
