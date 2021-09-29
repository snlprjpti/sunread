<?php

namespace Modules\Notification\Services;

use Modules\Notification\Entities\EmailNotification;
use Exception;

class NotificationLogHelper
{
    private $emailNotification;

    public function __construct(EmailNotification $emailNotification)
    {
        $this->emailNotification = $emailNotification;
    }

    public function log($name, $subject, $html_content, $recipient_email_address, $email_template_id, $email_template_code)
    {
        try
        {
            $recipient_detail = $this->getRecipientUser();

            $data = [
                "name" => $name,
                "subject" => $subject,
                "html_content" => $html_content,
                "recipient_email_address" => $recipient_email_address,
                "email_template_id" => $email_template_id,
                "email_template_code" => $email_template_code,
                "is_sent" => true,
                "created_at" => now(),
            ];

            $this->emailNotification->create(array_merge($data, $recipient_detail));
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function getRecipientUser(): array
    {
        if(auth()->user()) {
            $user_id = auth()->user()->id;
            $type = "admin";
        }
        elseif (auth()->guard('customer')->user()) {
            $user_id = auth()->guard('customer')->user()->id;
            $type = "customer";
        }
        else {
            $user_id = null;
            $type = "guest";
        }

        $data = [
            "recipient_user_type" => $type,
            "recipient_user_id" => $user_id,
        ];

        return $data;
    }
}
