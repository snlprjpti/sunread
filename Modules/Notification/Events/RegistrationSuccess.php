<?php

namespace Modules\Notification\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccess
{
    use Dispatchable, SerializesModels, InteractsWithSockets;

    public $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function broadcastOn(): array
    {
        return [];
    }
}
