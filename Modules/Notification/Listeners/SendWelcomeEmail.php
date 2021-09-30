<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Events\RegistrationSuccess;
use Modules\Notification\Jobs\SendNotification;

class SendWelcomeEmail
{
    public function __construct()
    {
        //
    }

    public function handle(RegistrationSuccess $event): void
    {
        SendNotification::dispatch( $event->user_id, "default_welcome_email" );
    }
}
