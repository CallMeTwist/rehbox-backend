<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpired extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your ReHboX subscription has expired')
            ->greeting("Hello {$notifiable->name},")
            ->line('Your ReHboX subscription has expired and your access to personalized exercises has been paused.')
            ->action('Renew Subscription', config('app.frontend_url') . '/subscription')
            ->line('Your progress and plan are saved — renew anytime to pick up where you left off.');
    }
}
