<?php


namespace Modules\Attribute\Observers;

use Modules\Core\Facades\Audit;
use Modules\Attribute\Entities\Attribute;

class AttributeObserver
{
    public function created(Attribute $attribute)
    {
        Audit::log($attribute, __FUNCTION__);
    }

    public function updated(Attribute $attribute)
    {
        $attribute->product_attributes->map(function ($product_attribute) {
            $product_attribute->product->searchable();
        });
        Audit::log($attribute, __FUNCTION__);
    }

    public function deleted(Attribute $attribute)
    {
        $attribute->product_attributes->map(function ($product_attribute) {
            $product_attribute->product->searchable();
        });
        Audit::log($attribute, __FUNCTION__);
    }
}
