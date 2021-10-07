<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class ReIndexing implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public function __construct()
    {
    }

    public function handle(): void
    {
        try
        {
            $batch = Bus::batch([])->onQueue("index")->dispatch();
        
            $products = Product::whereType("simple")->whereParentId(null)->get();
            foreach($products as $product)
            {
                $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                    return $channel->stores;
                })->flatten(1);
                
                foreach($stores as $store) $batch->add(new SingleIndexing($product, $store));
            }
    
            $configurable_products = Product::whereType("configurable")->get();
            foreach($configurable_products as $configurable_product)
            {
                $stores = Website::find($configurable_product->website_id)->channels->map(function ($channel) {
                    return $channel->stores;
                })->flatten(1);
                $variants = $configurable_product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();
        
                foreach( $stores as $store) {
                    $batch->add(new ConfigurableIndexing($configurable_product, $store));
                    foreach($variants as $variant) $batch->add(new VariantIndexing($configurable_product, $variants, $variant, $store));
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
