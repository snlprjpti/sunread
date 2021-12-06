<?php

namespace Modules\Product\Observers;

use Modules\Core\Facades\Audit;
use Modules\Product\Entities\Feature;

class FeatureObserver
{
    public function created(Feature $feature)
    {
        Audit::log($feature, __FUNCTION__);
    }

    public function updated(Feature $feature)
    {
        Audit::log($feature, __FUNCTION__);
    }

    public function deleted(Feature $feature)
    {
        Audit::log($feature, __FUNCTION__);
    }
}
