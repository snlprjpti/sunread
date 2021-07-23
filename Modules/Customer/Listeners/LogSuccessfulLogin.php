<?php

namespace Modules\Customer\Listeners;

class LogSuccessfulLogin
{
    public function loginSuccess($customer)
    {
        $customer->last_login_at = date('Y-m-d H:i:s');
        $customer->save();
    }
}
