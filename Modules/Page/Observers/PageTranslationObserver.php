<?php

namespace Modules\Page\Observers;

use Modules\Core\Facades\Audit;
use Modules\Page\Entities\PageTranslation;

class PageTranslationObserver
{
    public function created(PageTranslation $pageTranslation)
    {
        Audit::log($pageTranslation, __FUNCTION__);
    }

    public function updated(PageTranslation $pageTranslation)
    {
        Audit::log($pageTranslation, __FUNCTION__);
    }

    public function deleted(PageTranslation $pageTranslation)
    {
        Audit::log($pageTranslation, __FUNCTION__);
    }
}
