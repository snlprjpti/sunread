<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Events\ForgotPassword;
use Modules\Notification\Jobs\SendNotification;

class SendPasswordResetLink
{
    public function __construct()
    {
        //
    }

    public function handle(ForgotPassword $event): void
    {
        SendNotification::dispatch( $event->user_id, "forgot_password", $event->token );
    }
}
