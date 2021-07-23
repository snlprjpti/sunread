<?php

namespace Modules\Customer\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Facades\Audit;

class SessionListener
{
    public function customerLogin($customer)
    {
        Audit::log($customer, "login", "Customer Login", "{$customer->full_name} logged in.");
        $customer->last_login_at = now();
        $customer->save();
    }

    public function customerLogOut($customer)
    {
        Audit::log($customer, "login", "Customer Logout", "{$customer->full_name} logged out.");
    }
}
