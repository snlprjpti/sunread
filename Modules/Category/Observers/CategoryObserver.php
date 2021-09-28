<?php


namespace Modules\Category\Observers;

use Illuminate\Support\Facades\Bus;
use Modules\Core\Facades\Audit;
use Modules\Category\Entities\Category;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Illuminate\Support\Str;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Traits\ElasticSearch\PrepareIndex;

class CategoryObserver
{
    use PrepareIndex;

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
        $this->preparingIndexData($category->products, "delete");
        Audit::log($category, __FUNCTION__);
        //UrlRewrite::handleUrlRewrite($category, __FUNCTION__);
    }
}
