<?php

namespace Modules\Erp\Jobs\Mapper;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Erp\Traits\HasErpValueMapper;
use Modules\Product\Entities\Product;

class ErpMigratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpValueMapper;
    
    public $tries = 10;
    public $timeout = 1200;

    protected $detail;

    public function __construct(object $detail)
    {
        $this->detail = $detail;
    }

    public function handle(): void
    {
        try
        {
            $check_variants = ($this->getDetailCollection("productVariants", $this->detail->sku)->count() > 1);
            $type = ($check_variants) ? "configurable" : "simple";

            $match = [
                "website_id" => 1,
                "sku" => $this->detail->sku
            ];
            $product_data = array_merge($match, [
                "attribute_set_id" => 1,
                "type" => $type,
            ]);

            $product = Product::updateOrCreate($match, $product_data);
            ErpMigrateProductImageJob::dispatchSync($product, $this->detail);

            if ($check_variants) ErpGenerateVariantProductJob::dispatchSync($product, $this->detail);

            //visibility attribute value
            $visibility = ($check_variants) ? 5 : 8;
            
            ErpMigrateProductAttributeJob::dispatchSync($product, $this->detail, false, $visibility);
            ErpMigrateProductInventoryJob::dispatchSync($product, $this->detail);
            ErpDetailStatusUpdate::dispatchSync($this->detail->id);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
