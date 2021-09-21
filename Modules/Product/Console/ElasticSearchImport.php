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
use Modules\Product\Jobs\SingleIndexing;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
        $batch = Bus::batch([])->dispatch();
        
        $products = Product::whereType("simple")->whereParentId(null)->get();
        foreach($products as $product)
        {
            $stores = Website::find($product->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);
            
            foreach($stores as $store) $batch->add(new SingleIndexing($product, $store));
        }

        $variants = Product::whereType("configurable")->get();
        foreach($variants as $variant)
        {
            $stores = Website::find($variant->website_id)->channels->map(function ($channel) {
                return $channel->stores;
            })->flatten(1);
    
            foreach( $stores as $store) $batch->add(new ConfigurableIndexing($variant, $store));
        }
        $this->info("All data imported successfully");
    }
}
