<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->load(['items.product', 'sale']);

        $mail = (new MailMessage)
            ->subject('TrackNet — Your Order #' . $order->order_number . ' Has Been Cancelled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order has been cancelled. Any applicable refund will be processed shortly.')
            ->line('**Order Number:** ' . $order->order_number)
            ->line('**Order Date:** ' . $order->created_at->format('F d, Y'))
            ->line('---')
            ->line('**Cancelled Items:**');

        foreach ($order->items as $item) {
            $mail->line(
                '• ' . $item->product->name .
                ' × ' . $item->quantity .
                ' — $' . number_format($item->total_price, 2)
            );
        }

        $mail->line('---')
            ->line('**Order Total: $' . number_format($order->total, 2) . '**')
            ->action('View Order Details', route('account.orders.show', $order))
            ->line('If you did not request this cancellation, please contact our support team.')
            ->line('Thank you for shopping with TrackNet!');

        return $mail;
    }
}
