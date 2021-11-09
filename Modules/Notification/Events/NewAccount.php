<?php

namespace Modules\Notification\Events;

use Illuminate\Queue\SerializesModels;

class NewAccount
{
    use SerializesModels;

    public $user_id, $verification_token;

    public function __construct(int $user_id, string $verification_token)
    {
        $this->user_id = $user_id;
        $this->verification_token = $verification_token;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
