<?php

namespace Modules\Notification\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;


    public $details, $subject;

    public function __construct(string $details, string $subject)
    {
        $this->details = $details;
        $this->subject = $subject;
    }

    public function getBody(): string
    {
        return $this->details;
    }

    public function build(): NotificationMail
    {
        $this->subject($this->subject);
        return $this->markdown('notification::emailNotification');
    }
}
