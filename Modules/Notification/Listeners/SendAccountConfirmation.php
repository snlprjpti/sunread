<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Events\ConfirmEmail;
use Modules\Notification\Jobs\SendNotification;

class SendAccountConfirmation
{
    public function __construct()
    {
        //
    }

    public function handle(ConfirmEmail $event): void
    {
        SendNotification::dispatch( $event->user_id, "welcome_email" );
    }
}
