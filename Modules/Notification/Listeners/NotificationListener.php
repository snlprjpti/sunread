<?php

namespace Modules\Notification\Listeners;

use Modules\Notification\Jobs\SendNotification;

class NotificationListener
{
    /**
     * @param string $event
     * @param int $entity_id
     * send email using job
     */
    public function sendEmail(string $event, int $entity_id)
    {
        SendNotification::dispatch( $event, $entity_id );
    }
}
