<?php

namespace Modules\Notification\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notification\Events\NewAccount;
use Modules\Notification\Jobs\SendNotificationJob;

class SendNewAccount
{
    public function __construct()
    {
        //
    }

    public function handle(NewAccount $event): void
    {
        SendNotificationJob::dispatch( $event->user_id, "new_account", $event->verification_token );
    }
}
