<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteNotification extends Notification
{
    use Queueable;
    protected $notification_url;
    protected $user_name;

    /**
     * Create a new notification instance.
     */
    public function __construct($user_name, $notification_url)
    {
        $this->user_name = $user_name;
        $this->notification_url = $notification_url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been invited to join a group')
            ->line('User ' . $this->user_name . ' invited you to join the group')
            ->line('Click the button below to accept the invitation.')
            ->action('Accept the Invitation', $this->notification_url)
            ->line('If you do not want to accept the invitation, you can ignore this email.');
            
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
