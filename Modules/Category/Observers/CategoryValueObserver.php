<?php


namespace Modules\Category\Observers;

use Modules\UrlRewrite\Facades\UrlRewrite;
use Modules\Category\Entities\CategoryValue;

class CategoryValueObserver
{
    public function created(CategoryValue $category_translation)
    {
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function updated(CategoryValue $category_translation)
    {
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }

    public function deleted(CategoryValue $category_translation)
    {
        // UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__);
    }
}
