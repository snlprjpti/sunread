<?php

namespace Modules\Notification\Services;

use Modules\Notification\Entities\EmailNotification;

class NotificationLogHelper
{
    private $emailNotification;

    public function __construct(EmailNotification $emailNotification)
    {
        $this->emailNotification = $emailNotification;
    }

    public function log($name, $subject, $html_content, $recipient_email_address, $email_template_id, $email_template_code, $recipient_user_type = null, $recipient_user_id = null)
    {
        $data = [
            "name" => $name,
            "subject" => $subject,
            "html_content" => $html_content,
            "recipient_email_address" => $recipient_email_address,
            "recipient_user_type" => $recipient_user_type,
            "recipient_user_id" => $recipient_user_id,
            "email_template_id" => $email_template_id,
            "email_template_code" => $email_template_code,
            "is_sent" => true,
            "created_at" => now(),
        ];
        $this->emailNotification->create($data);
    }
}
