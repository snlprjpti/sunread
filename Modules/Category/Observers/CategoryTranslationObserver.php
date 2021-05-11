<?php


namespace Modules\Category\Observers;

use Modules\UrlRewrite\Facades\UrlRewrite;
use Modules\Category\Entities\CategoryTranslation;

class CategoryTranslationObserver
{
    public function created(CategoryTranslation $category_translation)
    {
        UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__, $category_translation->createUrlRewrite());
    }

    public function updated(CategoryTranslation $category_translation)
    {
        UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__, $category_translation->createUrlRewrite());
    }

    public function deleted(CategoryTranslation $category_translation)
    {
        UrlRewrite::handleUrlRewrite($category_translation, __FUNCTION__, $category_translation->createUrlRewrite());
    }
}
