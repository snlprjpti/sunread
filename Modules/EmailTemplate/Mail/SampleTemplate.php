<?php

namespace Modules\EmailTemplate\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SampleTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $details, $subject;

    public function __construct(string $details, string $subject)
    {
        $this->details = $details;
        $this->subject = $subject;
    }

    public function getBody()
    {
        return $this->details;
    }

    public function build()
    {
        $this->subject($this->subject);
        return $this->markdown('emailtemplate::mailTemplate');
//        $this->from($this->fromAddress);
//        $this->html($this->htmlBody);
//        return $this;
    }
}
