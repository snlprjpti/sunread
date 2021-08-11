<?php

namespace Modules\Product\Console;

use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;
use Modules\Core\Entities\Website;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\BulkIndexing;
use Modules\Product\Jobs\ElasticSearchIndexingJob;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ElasticSearchImport extends Command
{
    protected $signature = 'elasticsearch:import';

    protected $description = 'Import all the data to the elasticsearch';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $products = Product::get();
        BulkIndexing::dispatch($products);
        $this->info("All data imported successfully");
    }
}
