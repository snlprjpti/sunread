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

    protected $product;

    public function __construct(object $product)
    {
        $this->product = $product;
    }

    public function handle(): void
    {
        try
        {
            $product_sku = $this->product->sku;
            $product_sku = Bus::batch([])->onQueue("index")->dispatch();
            $stores = Website::find($this->product->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);

            if ($this->product->type == "configurable") $chunk_variants = $this->product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get()->chunk(100);

            foreach ($stores as $store)
            {
                if ($this->product->type == "simple") $product_sku->add(new SingleIndexing($this->product, $store));
                elseif ($this->product->type == "configurable") {
                    $product_sku->add(new ConfigurableIndexing($this->product, $store));
                    foreach ( $chunk_variants as $chunk_variant_key => $variants )
                    {
                        $chunk_variant_key = Bus::batch([])->onQueue("index")->dispatch();
                        foreach ($variants as $variant) {
                            $chunk_variant_key->add(new VariantIndexing($this->product, $variants, $variant, $store));
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
