<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $newUser) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('TrackNet — New User Pending Approval')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new customer account has been registered and is awaiting your approval.')
            ->line('**Name:** ' . $this->newUser->name)
            ->line('**Email:** ' . $this->newUser->email)
            ->line('**MFA Method:** ' . strtoupper($this->newUser->mfa_method ?? 'email'))
            ->action('Review Pending Users', url('/admin/pending-users'))
            ->line('Please log in to the admin panel to approve or reject this account.');
    }
}
