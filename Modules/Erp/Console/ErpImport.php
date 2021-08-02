<?php

namespace Modules\Erp\Console;

use Exception;
use Illuminate\Console\Command;
use Modules\Erp\Jobs\EanCodes;
use Modules\Erp\Jobs\ErpAttributeGroups;
use Modules\Erp\Jobs\ListProducts;
use Modules\Erp\Jobs\ProductImages;
use Modules\Erp\Jobs\ProductVariants;
use Modules\Erp\Jobs\SalePrices;
use Modules\Erp\Jobs\WebInventories;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ErpImport extends Command
{
    public $signature = 'erp:import';

    protected $description = 'This command will import all data from Erp API.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        ListProducts::dispatch();
        ErpAttributeGroups::dispatch();
        EanCodes::dispatch();
        SalePrices::dispatch();
        WebInventories::dispatch();
        ProductVariants::dispatch();
        ProductImages::dispatch();
        $this->info("All jobs dispatched.");
    }

    protected function getArguments(): array
    {
        return [
            ['erp:import', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['erp:import', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
