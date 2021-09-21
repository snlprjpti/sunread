<?php

namespace Modules\EmailTemplate\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SampleTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function getBody()
    {
        return $this->details;
    }

    public function build()
    {
        return $this->markdown('emailtemplate::mailTemplate');
//
//        $this->subject($this->theSubject);
//        $this->from($this->fromAddress);
//        $this->html($this->htmlBody);
//        return $this;
    }
}
