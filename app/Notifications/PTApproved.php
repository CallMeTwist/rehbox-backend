<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PTApproved extends Notification
{
    public function __construct(private string $activationCode) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ ReHboX — Your account has been approved!')
            ->greeting("Hi {$notifiable->name},")
            ->line('Great news! Your physiotherapist account has been verified by the ReHboX medical team.')
            ->line("Your client onboarding code is: **{$this->activationCode}**")
            ->line('Share this code with your patients when they register on the app.')
            ->action('Open ReHboX', url('/'))
            ->line('Welcome to ReHboX!');
    }
}
