<?php


namespace Modules\Category\Observers;

use Modules\Core\Facades\Audit;
use Modules\Category\Entities\Category;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function created(Category $category)
    {
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }

    public function updated(Category $category)
    {
        $category->products->map(function ($product) {
            $product->searchable();
        });
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }

    public function deleted(Category $category)
    {
        $category->products->map(function ($product) {
            $product->searchable();
        });
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }
}
