<?php

namespace Modules\Page\Observers;

use Modules\Core\Facades\Audit;
use Modules\Page\Entities\PageAvailability;

class PageAvailabilityObserver
{
    public function created(PageAvailability $pageAvailability)
    {
        Audit::log($pageAvailability, __FUNCTION__);
    }

    public function updated(PageAvailability $pageAvailability)
    {
        Audit::log($pageAvailability, __FUNCTION__);
    }

    public function deleted(PageAvailability $pageAvailability)
    {
        Audit::log($pageAvailability, __FUNCTION__);
    }
}
