<?php

namespace Modules\Erp\Jobs\Mapper;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Erp\Traits\HasErpValueMapper;
use Modules\Product\Entities\AttributeConfigurableProduct;
use Modules\Product\Entities\AttributeOptionsChildProduct;
use Modules\Product\Entities\Product;

class ErpMigrateVariantJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasErpValueMapper;

    protected $product, $variant, $erp_product_iteration;

    public $tries = 10;
    public $timeout = 90000;

    public function __construct(object $product, mixed $variant, object $erp_product_iteration)
    {
        $this->product = $product;
        $this->variant = $variant;
        $this->erp_product_iteration = $erp_product_iteration;
    }

    public function handle(): void
    {
        try 
        {
            $product_data = [
                "parent_id" => $this->product->id,
                "attribute_set_id" => 1,
                "website_id" => 1,
                "sku" => "{$this->product->sku}_{$this->variant['pfVerticalComponentCode']}_{$this->variant['pfHorizontalComponentCode']}",
                "type" => "simple",
            ];

            $match = [
                "website_id" => 1,
                "sku" => "{$this->product->sku}_{$this->variant['pfVerticalComponentCode']}_{$this->variant['pfHorizontalComponentCode']}",
            ];

            $variant_product = Product::updateOrCreate($match, $product_data);
            $variant_product->categories()->sync(1);
            $ean_codes = $this->getDetailCollection("eanCodes", $this->erp_product_iteration->sku);
            $ean_code = $this->getValue($ean_codes)->where("variantCode", $this->variant["code"])->first()["crossReferenceNo"] ?? "" ;

            $this->createAttributeValue($variant_product, $this->erp_product_iteration, $ean_code, 8, $this->variant);
            $attribute_options = AttributeOption::get()->filter(function ($attribute_option) {
                return $attribute_option->code == $this->variant["pfVerticalComponentCode"] || $attribute_option->name == $this->variant["pfHorizontalComponentCode"];
            });

            if ($attribute_options->count() > 1)
            {
                foreach ( $attribute_options as $attribute_option )
                {
                    $configurable_product_attributes["product_id"] = $this->product->id;
                    $configurable_product_attributes["attribute_id"] = $attribute_option?->attribute_id;

                    $configurable_product_attributes["used_in_grouping"] = ($attribute_option?->attribute_id == $this->getAttributeId("color")) ? 1 : 0;
                    AttributeConfigurableProduct::updateOrCreate($configurable_product_attributes);
                    AttributeOptionsChildProduct::updateOrCreate([
                        "attribute_option_id" => $attribute_option?->id,
                        "product_id" => $variant_product->id 
                    ]);
                }
            }
            $this->mapstoreImages($variant_product, $this->erp_product_iteration, $this->variant);
            $this->createInventory($variant_product, $this->erp_product_iteration, $this->variant);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

    }
}
