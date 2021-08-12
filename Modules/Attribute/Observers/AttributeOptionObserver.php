<?php


namespace Modules\Attribute\Observers;

use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\PartialIndexing;

class AttributeOptionObserver
{
    public function created(AttributeOption $attribute_option)
    {
    
    }

    public function updated(AttributeOption $attribute_option)
    {
        $attribute = $attribute_option?->attribute;
        $product_attributes = $attribute->product_attributes()->with("product")->get();

        $product_attributes->map(function ($product_attribute) use($attribute, $attribute_option) {

            $product = $product_attribute->product;
            $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });

            foreach($stores as $store)
            {
                $slug_values = [];
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $attribute->id
                ];
                $value = $product->value($match);

                if (is_array($value)) {
                    if(in_array($attribute_option->id, $value)) {
                        $key = array_keys($value, $attribute_option->id);
                        $slug_values["{$attribute->slug}_{$key[0]}_value"] = $attribute_option->name;
                    } 
                }
                else $slug_values["{$attribute->slug}_value"] = $attribute_option->name;

                if(count($slug_values) > 0) PartialIndexing::dispatchSync($product, $store, $slug_values);
            } 
        });
    }

    public function deleted(AttributeOption $attribute_option)
    {
        $attribute = $attribute_option?->attribute;
        $product_attributes = $attribute->product_attributes()->with("product")->get();

        $product_attributes->map(function ($product_attribute) use($attribute, $attribute_option) {

            $product = $product_attribute->product;
            $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });

            foreach($stores as $store)
            {
                $slug_values = [];
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $attribute->id
                ];
                $value = $product->value($match);

                if (is_array($value)) {
                    if(in_array($attribute_option->id, $value)) {
                        $key = array_keys($value, $attribute_option->id);
                        $slug_values["{$attribute->slug}_{$key[0]}_value"] = $attribute_option->name;
                        unset($value[$key]);
                        $slug_values[$attribute->slug] = $value;
                    } 
                }
                else {
                    $slug_values["{$attribute->slug}_value"] = $attribute_option->name;
                    $slug_values[$attribute->slug] = null;
                } 
                
                if(count($slug_values) > 0) PartialIndexing::dispatchSync($product, $store, $slug_values);
            } 
        });
    }
}
