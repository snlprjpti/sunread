<?php

namespace Modules\EmailTemplate\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SampleTemplate extends Mailable
{
    use Queueable, SerializesModels;

    private $theSubject;
    private $htmlBody;
    private $fromAddress;

    public function __construct($subject, $htmlBody, $fromAddress)
    {
        $this->theSubject = $subject;
        $this->htmlBody = $htmlBody;
        $this->fromAddress = $fromAddress;
    }

    public function getBody()
    {
        return $this->htmlBody;
    }

    public function build()
    {
        $this->subject($this->theSubject);
        $this->from($this->fromAddress);
        $this->html($this->htmlBody);
        return $this;
    }
}
