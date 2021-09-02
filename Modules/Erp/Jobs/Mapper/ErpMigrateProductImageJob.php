<?php

namespace Modules\Erp\Jobs\Mapper;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Traits\HasErpValueMapper;

class ErpMigrateProductImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpValueMapper;
    
    public $tries = 10;
    public $timeout = 1200;

    protected $product, $erp_product_iteration, $variant;

    public function __construct(object $product, object $erp_product_iteration, array $variant = [])
    {
        $this->product = $product; 
        $this->erp_product_iteration = $erp_product_iteration; 
        $this->variant = $variant;
    }

    public function handle(): void
    {
        $this->mapstoreImages($this->product, $this->erp_product_iteration, $this->variant);
    }
}
