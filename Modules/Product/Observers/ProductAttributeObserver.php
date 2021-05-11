<?php


namespace Modules\Product\Observers;

use Modules\Core\Facades\Audit;
use Modules\Product\Entities\ProductAttribute;

class ProductAttributeObserver
{
    public function created(ProductAttribute $product_attribute)
    {
        Audit::log($product_attribute, __FUNCTION__);
    }

    public function updated(ProductAttribute $product_attribute)
    {
        Audit::log($product_attribute, __FUNCTION__);
    }

    public function deleted(ProductAttribute $product_attribute)
    {
        Audit::log($product_attribute, __FUNCTION__);
    }
}
