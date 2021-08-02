<?php

namespace Modules\Erp\Jobs\Mapper;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Traits\HasErpValueMapper;

class ErpMigrateProductAttributeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpValueMapper;

    protected $product, $erp_product_iteration, $ean_code_value;

    public function __construct(object $product, object $erp_product_iteration, bool $ean_code_value = true)
    {
        $this->product = $product;
        $this->erp_product_iteration = $erp_product_iteration;
        $this->ean_code_value = $ean_code_value;
    }

    public function handle(): void
    {
        $this->createAttributeValue($this->product, $this->erp_product_iteration, $this->ean_code_value);
    }
}
