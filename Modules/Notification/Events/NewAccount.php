<?php

namespace Modules\Notification\Events;

use Illuminate\Queue\SerializesModels;

class NewAccount
{
    use SerializesModels;

    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
