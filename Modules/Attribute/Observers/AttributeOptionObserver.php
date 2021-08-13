<?php


namespace Modules\Attribute\Observers;

use Modules\Attribute\Entities\AttributeOption;
use Modules\Product\Jobs\PartialModifyAttributeOption;

class AttributeOptionObserver
{
    public function created(AttributeOption $attribute_option)
    {

    }

    public function updated(AttributeOption $attribute_option)
    {
        $attribute = $attribute_option?->attribute;
        $product_attributes = $attribute->product_attributes()->with("product")->get();
        PartialModifyAttributeOption::dispatch($product_attributes, $attribute, $attribute_option, "update");
    }

    public function deleted(AttributeOption $attribute_option)
    {
        $attribute = $attribute_option?->attribute;
        $product_attributes = $attribute->product_attributes()->with("product")->get();
        PartialModifyAttributeOption::dispatchSync($product_attributes, $attribute, collect($attribute_option), "delete");
    }
}
