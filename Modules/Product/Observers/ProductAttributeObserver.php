<?php


namespace Modules\Product\Observers;

use Modules\Core\Facades\Audit;
use Modules\Product\Entities\ProductAttribute;
use Modules\UrlRewrite\Facades\UrlRewrite;

class ProductAttributeObserver
{
    // public function created(ProductAttribute $product_attribute)
    // {
    //     if($product_attribute->attribute->name === 'SKU' && isset($product_attribute->value_id)) UrlRewrite::handleUrlRewrite($product_attribute, __FUNCTION__, $product_attribute->createUrlRewrite());
    // }

    // public function updated(ProductAttribute $product_attribute)
    // {
    //     if($product_attribute->attribute->name === 'SKU' && isset($product_attribute->value_id))
    //     {
    //         if($product_attribute->getUrlRewrite()) UrlRewrite::handleUrlRewrite($product_attribute, __FUNCTION__, $product_attribute->createUrlRewrite());
    //         else UrlRewrite::handleUrlRewrite($product_attribute, "created", $product_attribute->createUrlRewrite());
    //     }
    // }

    // public function deleted(ProductAttribute $product_attribute)
    // {
    //     if($product_attribute->attribute->name === 'SKU') UrlRewrite::handleUrlRewrite($product_attribute, __FUNCTION__, $product_attribute->createUrlRewrite());
    // }
}
