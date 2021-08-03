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
        // Import from API
        ListProducts::dispatch();
        ErpAttributeGroups::dispatch();
        EanCodes::dispatch();
        SalePrices::dispatch();
        WebInventories::dispatch();
        ProductVariants::dispatch();

        // Import from FTP
        ProductImages::dispatch();

        $this->info("All jobs dispatched.");
    }
}
