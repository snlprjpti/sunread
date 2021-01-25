<?php


namespace Modules\Customer\Observers;


use Modules\Core\Facades\Audit;
use Modules\Customer\Entities\CustomerGroup;

class CustomerGroupObserver
{
    public function created(CustomerGroup $customerGroup)
    {
        Audit::log($customerGroup, __FUNCTION__);
    }
}
