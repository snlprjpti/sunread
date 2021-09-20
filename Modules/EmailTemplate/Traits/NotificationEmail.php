<?php

namespace Modules\EmailTemplate\Traits;

use Modules\EmailTemplate\Entities\EmailNotification;

class NotificationEmail
{
    public static function insert(string $name, string $subject, string $html_content, string $recipient_email_address, string $recipient_user_type = null, int $recipient_user_id = null, int $email_template_id, string $email_template_code): bool
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
        EmailNotification::insert($data);
        return true;
    }
}
