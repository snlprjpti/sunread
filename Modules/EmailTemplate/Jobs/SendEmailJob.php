<?php

namespace Modules\EmailTemplate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\EmailTemplate\Mail\SendEmail;
use Modules\EmailTemplate\Traits\SendEmailTrait;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SendEmailTrait;

    public $event, $entity_id;

    public function __construct(string $event, string $entity_id)
    {
        $this->event = $event;
        $this->entity_id = $entity_id;
    }

    public function handle(): void
    {
        $data = $this->newEvent($this->event, $this->entity_id);
        Mail::to($data["to_email"])->send(new SendEmail($data["content"], $data["subject"]));
    }
}
