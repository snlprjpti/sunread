<?php

namespace Modules\Erp\Console;

use Illuminate\Console\Command;
use Modules\Erp\Entities\ErpImport;
use Symfony\Component\Console\Input\InputOption;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductJob;
use Symfony\Component\Console\Input\InputArgument;

class ErpMigrate extends Command
{

    protected $signature = 'erp:migrate';

    protected $description = 'This command will migrate products form erp API.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): bool
    {
        if ( ErpImport::whereStatus(0)->count() > 0 ) {
            $this->error("ERP Import is not complete.");
            return false;
        }

        ErpMigrateProductJob::dispatch();
        $this->info("ERP migration job started.");
        return true;
    }

    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
