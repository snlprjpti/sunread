<?php

namespace Modules\Notification\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        SendNotification::dispatch( $event->user_id, "new_account" );
    }
}
