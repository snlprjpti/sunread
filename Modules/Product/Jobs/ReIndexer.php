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

    public $product, $store, $method;

    public function __construct()
    {
        
    }

    public function handle(): void
    {
        try
        {
            $this->connectElasticSearch(); 
  
            $batch = Bus::batch([])->onQueue("index")->dispatch();

            $products = Product::with(["variants", "categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->whereParentId(null)->get();
            foreach ($products as $product)
            {
                $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                    return $channel->stores;
                })->flatten(1);
                if ($product->type == "configurable") $variants = $product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();
                $store_batch = Bus::batch([])->onQueue("index")->dispatch();
                foreach ($stores as $store) {
                    
                    // if ($product->type == "simple") $batch->add(new SingleIndexing($product, $store));
                    if ($product->type == "simple") $this->singleIndexing($product, $store);
                    
                    elseif ($product->type == "configurable") {
                        $this->createProduct($product, $store, $variants);
                        // $configurable_batch->add(new ConfigurableIndexing($product, $store));

                        // $variant_batch = Bus::batch([])->allowFailures()->onQueue('index')->dispatch();
                        foreach ($variants as $variant) {
                            $this->createVariantProduct($product, $variants, $variant, $store);
                            // $configurable_batch->add(new VariantIndexing($product, $variants, $variant, $store));
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

    public function singleIndexing($product, $store)
    {
        try
        {
            if(!null) {
                $is_visibility = $product->value([
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_slug" => "visibility"
                ]);
                
                if($is_visibility?->name != "Not Visible Individually") $this->singleIndexing($product, $store);
                else $this->removeIndex(collect($product), $store);
            }
            else $this->removeIndex($product, $store);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
