<?php

namespace Modules\Customer\Listeners;

class LogSuccessfulLogin
{
    public function loginSuccess($customer)
    {
        $customer->last_login_at = now();
        $customer->save();
    }
}
