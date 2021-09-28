<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Jobs\SendNotification;

class NotificationListener
{
    public function welcomeEmail(int $entity_id)
    {
        SendNotification::dispatch( $entity_id, "welcome_email" );
    }

    public function forgotPassword(int $entity_id, string $token)
    {
        SendNotification::dispatch( $entity_id, "forgot_password", $token );
    }
}
