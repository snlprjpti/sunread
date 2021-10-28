<?php

namespace Modules\Notification\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword
{
    use Dispatchable, SerializesModels, InteractsWithSockets;

    public $user_id, $token;

    public function __construct(int $user_id, string $token)
    {
        $this->user_id = $user_id;
        $this->token = $token;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
