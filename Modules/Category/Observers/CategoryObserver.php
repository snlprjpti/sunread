<?php


namespace Modules\Category\Observers;

use Illuminate\Support\Facades\Bus;
use Modules\Core\Facades\Audit;
use Modules\Category\Entities\Category;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Illuminate\Support\Str;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;

class CategoryObserver
{
    public function created(Category $category)
    {
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }

    public function updated(Category $category)
    {
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }

    public function deleted(Category $category)
    {
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }

    public function deleting(Category $category)
    {
        $products = $category->products;
        $batch = Bus::batch([])->dispatch();
        foreach($products as $product)
        {
            $stores = Website::find($product->website_id)->channels->mapWithKeys(function ($channel) {
                return $channel->stores;
            });
            
            foreach($stores as $store) $batch->add(new SingleIndexing(collect($product), $store, "delete"));
        }
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }
}
