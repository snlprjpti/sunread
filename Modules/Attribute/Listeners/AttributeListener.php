<?php

namespace Modules\Attribute\Listeners;

use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;

class AttributeListener
{
    public function indexing($attribute)
    {
        $product_attributes = $attribute->product_attributes()->with("product")->get();
        $product_attributes->map(function ($product_attribute) {
            
            $stores = Website::find($product_attribute->product->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });

            foreach($stores as $store) SingleIndexing::dispatch($product_attribute->product, $store);
        });
    }
}
