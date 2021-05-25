<?php


namespace Modules\Product\Observers;

use Modules\Product\Entities\ProductAttributeDecimal;

class ProductAttributeDecimalObserver
{
    public function updated(ProductAttributeDecimal $product_attribute_decimal)
    {
        $product_attribute_decimal->product_attribute->product->searchable();
    }
}
