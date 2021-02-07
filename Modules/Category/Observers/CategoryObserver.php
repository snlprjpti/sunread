<?php


namespace Modules\Category\Observers;

use Modules\Core\Facades\Audit;
use Modules\Category\Entities\Category;

class CategoryObserver
{
    public function created(Category $category)
    {
        Audit::log($category, __FUNCTION__);
    }

    public function updated(Category $category)
    {

        Audit::log($category, __FUNCTION__);
    }

    public function deleted(Category $category)
    {
        Audit::log($category, __FUNCTION__);
    }
}
