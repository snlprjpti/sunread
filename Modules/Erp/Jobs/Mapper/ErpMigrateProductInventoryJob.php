<?php

namespace Modules\Erp\Jobs\Mapper;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Traits\HasErpValueMapper;

class ErpMigrateProductInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpValueMapper;
    
    public $tries = 10;
    public $timeout = 1200;

    protected $product, $erp_product_iteration;

    public function __construct(object $product, object $erp_product_iteration)
    {
        $this->product = $product;
        $this->erp_product_iteration = $erp_product_iteration;
    }

    public function handle()
    {
        $this->createInventory($this->product, $this->erp_product_iteration);
    }
}
