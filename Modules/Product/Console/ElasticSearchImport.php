<?php

namespace Modules\Product\Console;

use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\BulkIndexing;
use Modules\Product\Jobs\ConfigurableIndexing;
use Modules\Product\Jobs\ElasticSearchIndexingJob;
use Modules\Product\Jobs\ReIndexer;
use Modules\Product\Jobs\ReIndexing;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Jobs\VariantIndexing;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Bus\Batch;

class ElasticSearchImport extends Command
{
    protected $signature = 'reindexer:reindex';

    protected $description = 'Import all the data to the elasticsearch';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $batch = Bus::batch([])->onQueue("index")->dispatch();

        $products = Product::whereParentId(null)->get();
        foreach ($products as $product)
        {
            $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);
            if ($product->type == "configurable") $variants = $product->variants()->with(["categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->get();
            
            foreach ($stores as $store) {
                if ($product->type == "simple") $batch->add(new SingleIndexing($product, $store));
                elseif ($product->type == "configurable") {
                    Bus::batch([
                        new ConfigurableIndexing($product, $store)
                    ])->then(function (Batch $variant_batch) use ($variants, $product, $store) {
                        $variant_batch = Bus::batch([])->allowFailures()->onQueue('index')->dispatch();
                        foreach ($variants as $variant) $variant_batch->add(new VariantIndexing($product, $variants, $variant, $store));
                    })->allowFailures()->onQueue('index')->dispatch();
                }
            } 
        }
        $this->info("All data imported successfully");
    }
}
