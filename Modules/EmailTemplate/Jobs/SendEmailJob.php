<?php

namespace Modules\EmailTemplate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\EmailTemplate\Mail\SampleTemplate;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $content, $subject;

    public function __construct(string $content, string $subject)
    {
        $this->content = $content;
        $this->subject = $subject;
    }

    public function handle(): void
    {
        Mail::to("sl.prjpti@gmail.com")->send(new SampleTemplate($this->content, $this->subject));
    }
}
