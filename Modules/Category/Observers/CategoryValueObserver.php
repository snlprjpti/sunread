<?php


namespace Modules\Category\Observers;

use Illuminate\Support\Facades\Bus;
use Modules\UrlRewrite\Facades\UrlRewrite;
use Modules\Category\Entities\CategoryValue;
use Modules\Core\Entities\Website;
use Modules\Product\Jobs\SingleIndexing;
use Modules\Product\Traits\ElasticSearch\PrepareIndex;

class CategoryValueObserver
{
    use PrepareIndex;

    public function created(CategoryValue $category_value)
    {
        if(($category_value->attribute == "name" || $category_value->attribute == "slug") && $category_value->scope != "website") 
        $this->preparingIndexData($category_value->category->products);
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function updated(CategoryValue $category_value)
    {
        if($category_value->attribute == "name" || $category_value->attribute == "slug") 
        $this->preparingIndexData($category_value->category->products);
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function deleted(CategoryValue $category_value)
    {
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function deleting(CategoryValue $category_value)
    {
        if($category_value->attribute == "name" || $category_value->attribute == "slug") 
        $this->preparingIndexData($category_value->category->products);
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }
}
