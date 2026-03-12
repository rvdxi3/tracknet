<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('TrackNet — Your Account Has Been Approved!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your TrackNet account has been reviewed and approved by our team.')
            ->line('You can now log in and start shopping.')
            ->action('Log In Now', route('login'))
            ->line('Thank you for joining TrackNet!');
    }
}
