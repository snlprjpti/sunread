<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationNotification extends Notification
{
    use Queueable;
    private $token;
    private $role;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $role)
    {
        $this->token = $token;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Invitation')
            ->line('You are receiving this email because you are invited to '.config('name','Sunread').' '.$this->role.' role')
            ->action('Accept', route('admin.accept-invitation', $this->token));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
