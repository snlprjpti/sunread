<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Events\ResetPassword;
use Modules\Notification\Jobs\SendNotification;

class SendPasswordChangeEmail
{
    public function __construct()
    {
        //
    }

    public function handle(ResetPassword $event): void
    {
        SendNotification::dispatch( $event->user_id, "reset_password" );
    }
}
