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
            $simple_batch = Bus::batch([])->onQueue("index")->dispatch();
            $configurable_batch = Bus::batch([])->onQueue("index")->dispatch();
            $variant_batch = Bus::batch([])->onQueue("index")->dispatch();

            $products = Product::whereParentId(null)->get();
            foreach ($products as $product)
            {
                $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                    return $channel->stores;
                })->flatten(1);

                if ($product->type == "configurable") $variants = $product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();

                foreach ($stores as $store) {
                    if ($product->type == "simple") $simple_batch->add(new SingleIndexing($product, $store));

                    elseif ($product->type == "configurable") {
                        $configurable_batch->add(new ConfigurableIndexing($product, $store));
                        // $variant_batch = Bus::batch([])->allowFailures()->onQueue('index')->dispatch();
                        foreach ($variants as $variant) $variant_batch->add(new VariantIndexing($product, $variants, $variant, $store));
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
