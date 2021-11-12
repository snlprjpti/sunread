<?php

namespace Modules\Product\Console;

use Illuminate\Console\Command;
use Modules\Product\Jobs\ReindexMigrator;

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
        ReindexMigrator::dispatch()->onQueue("index");
        $this->info("All data imported successfully");
    }
}
