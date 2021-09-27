<?php

namespace Modules\Notification\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\Notification\Emails\NotificationMail;
use Modules\Notification\Traits\EmailNotification;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EmailNotification;

    public $event, $entity_id;

    public function __construct(string $event, string $entity_id)
    {
        $this->event = $event;
        $this->entity_id = $entity_id;
    }

    public function handle(): void
    {
        $data = $this->getData($this->event, $this->entity_id);
        Mail::to($data["to_email"])->send(new NotificationMail($data["content"], $data["subject"]));
    }
}
