<?php

namespace Modules\Notification\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\Notification\Emails\NotificationMail;
use Modules\Notification\Facades\NotificationLog;
use Modules\Notification\Traits\EmailNotification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EmailNotification;

    public $event, $entity_id, $append_data;

    public function __construct( int $entity_id, string $event, string $append_data = "")
    {
        $this->event = $event;
        $this->entity_id = $entity_id;
        $this->append_data = $append_data;
    }

    /**
     * send email through follwing events
     */
    public function handle(): void
    {
        /** get data from various content of email templates */
        $data = $this->getData( $this->entity_id, $this->event, $this->append_data);

        /** Send Email  */
        Mail::to($data->to_email)->send(new NotificationMail($data->content, $data->subject, $data->style, $data->sender_name, $data->sender_email));

        /** save email notification logs */
        $logs = [
            "name" => $this->event,
            "subject" => $data->subject,
            "html_content" => $data->content,
            "recipient_email_address" => $data->to_email,
            "email_template_id" => $data->template_id,
            "email_template_code" => $this->event,
        ];

        if( count(Mail::failures()) > 0 ) {
            NotificationLog::log($logs, false);
        }
        else {
            NotificationLog::log($logs, true);
        }
    }
}
