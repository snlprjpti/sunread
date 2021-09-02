<?php

namespace Modules\Erp\Jobs\Mapper;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Product\Entities\AttributeConfigurableProduct;

class ErpMigrateAttributeConfigurableProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    public function __construct(mixed $product, mixed $data)
    {
        $this->product = $product;
        $this->data = $data;
    }

    public function handle(): void
    {
        try
        {
            $configurable_product_attributes = [];
            $attribute_option = AttributeOption::whereCode($this->data['pfVerticalComponentCode'])->first();
            if ($attribute_option)
            {
                $configurable_product_attributes["product_id"] = $this->product->id;
                $configurable_product_attributes["attribute_id"] = $attribute_option->attribute_id;
                $configurable_product_attributes["attribute_option_id"] = $attribute_option->id;
                AttributeConfigurableProduct::updateOrCreate($configurable_product_attributes);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
