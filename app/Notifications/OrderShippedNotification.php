<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification implements ShouldQueue
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
            ->subject('TrackNet — Your Order #' . $order->order_number . ' Has Been Shipped')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your order has been shipped and is on its way.')
            ->line('**Order Number:** ' . $order->order_number)
            ->line('**Order Date:** ' . $order->created_at->format('F d, Y'))
            ->line('---')
            ->line('**Items Shipped:**');

        foreach ($order->items as $item) {
            $mail->line(
                '• ' . $item->product->name .
                ' × ' . $item->quantity
            );
        }

        $mail->line('---')
            ->line('**Order Total: $' . number_format($order->total, 2) . '**')
            ->line('**Shipping Address:** ' . $order->shipping_address)
            ->action('Track Your Order', route('account.orders.show', $order))
            ->line('Thank you for shopping with TrackNet!');

        return $mail;
    }
}
