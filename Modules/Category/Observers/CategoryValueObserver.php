<?php


namespace Modules\Category\Observers;

use Illuminate\Support\Facades\Bus;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Modules\Category\Entities\CategoryValue;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;

class CategoryValueObserver
{
    public function created(CategoryValue $category_value)
    {
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function updated(CategoryValue $category_value)
    {
        if($category_value->attribute == "name" || $category_value->attribute == "slug") {
            $products = $category_value->category->products;
            $batch = Bus::batch([])->dispatch();
            foreach($products as $product)
            {
                $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                    return $channel->stores;
                });
                
                foreach($stores as $store) $batch->add(new SingleIndexing($product, $store));
            }
        }
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function deleted(CategoryValue $category_value)
    {
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }
}
