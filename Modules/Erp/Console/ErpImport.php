<?php

namespace Modules\Erp\Console;

use Illuminate\Console\Command;
use Modules\Erp\Jobs\EanCodes;
use Modules\Erp\Jobs\ErpAttributeGroups;
use Modules\Erp\Jobs\ListProducts;
use Modules\Erp\Jobs\ProductImages;
use Modules\Erp\Jobs\ProductVariants;
use Modules\Erp\Jobs\SalePrices;
use Modules\Erp\Jobs\WebAssortments;
use Modules\Erp\Jobs\WebInventories;

class ErpImport extends Command
{
    public $signature = 'erp:import';

    protected $description = 'This command will import all data from Erp API.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): bool
    {
        // Import from API
        ListProducts::dispatch()->onQueue('erp');
        ErpAttributeGroups::dispatch()->onQueue('erp');
        EanCodes::dispatch()->onQueue('erp');
        SalePrices::dispatch()->onQueue('erp');
        WebInventories::dispatch()->onQueue('erp');
        WebAssortments::dispatch()->onQueue('erp');
        ProductVariants::dispatch()->onQueue('erp');

        // Import from FTP
        ProductImages::dispatch()->onQueue('erp');

        $this->info("All jobs dispatched.");
        return true;
    }
}
