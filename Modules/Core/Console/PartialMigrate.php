<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Modules\Core\Jobs\PartialMigrateJob;

class PartialMigrate extends Command
{ 
    protected $signature = 'partial:migrate';

    protected $description = 'This command will run all updated seeders.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): bool
    {
        PartialMigrateJob::dispatch();
        $this->info("Partial migrate initiated.");
        return false;
    }

}
