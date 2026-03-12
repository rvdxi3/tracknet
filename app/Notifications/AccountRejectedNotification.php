<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccountRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('TrackNet — Account Registration Update')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('After reviewing your account registration, we were unable to approve your account at this time.');

        if ($this->reason) {
            $mail->line('**Reason:** ' . $this->reason);
        }

        return $mail
            ->line('If you believe this is a mistake, please contact our support team.')
            ->line('Thank you for your interest in TrackNet.');
    }
}
