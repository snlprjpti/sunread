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
        // Audit::log($category, __FUNCTION__);
        UrlRewrite::create(
            Str::slug($category->slug),
            null,
            config("url-rewrite.types.$category->urlRewriteType.route"),
            $category->getUrlRewriteAttributesArray(),
            0,
            true
        );
    }

    public function updated(Category $category)
    {
        // dd($category->slug);
        // // Audit::log($category, __FUNCTION__);

        UrlRewrite::regenerateRoute($category->slug, $category->getUrlRewrite());
    }

    public function deleted(Category $category)
    {
        Audit::log($category, __FUNCTION__);
    }
}
