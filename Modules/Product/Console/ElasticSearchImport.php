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
use Modules\Product\Jobs\ReindexMigrator;
use Modules\Product\Jobs\Tester;

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
        $chunk_products = Product::with(["variants", "categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->whereParentId(null)->get()->chunk(100);
        foreach ($chunk_products as $products)
        {
            foreach ($products as $product)
            {
                $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                    return $channel->stores;
                })->flatten(1);
                
                foreach ($stores as $store)
                {
                    if ($product->type == "simple") Tester::dispatch($product, $store)->onQueue("index");
                }
            }
        }
        $this->info("All data imported successfully");
    }
}
