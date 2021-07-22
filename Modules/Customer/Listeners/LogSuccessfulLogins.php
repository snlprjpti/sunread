<?php

namespace Modules\Customer\Listeners;

use Illuminate\Auth\Events\Login;
use Modules\Customer\Events\LoginLog;

class LogSuccessfulLogins
{
    public function loginSuccess($customer)
    {
        $customer->last_login_at = date('Y-m-d H:i:s');
        $customer->save();
    }
}
