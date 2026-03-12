<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification implements ShouldQueue
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
            ->subject('TrackNet — Your Order #' . $order->order_number . ' Has Been Delivered')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order has been delivered! We hope you enjoy your purchase.')
            ->line('**Order Number:** ' . $order->order_number)
            ->line('**Order Date:** ' . $order->created_at->format('F d, Y'))
            ->line('---')
            ->line('**Items Delivered:**');

        foreach ($order->items as $item) {
            $mail->line(
                '• ' . $item->product->name .
                ' × ' . $item->quantity .
                ' @ $' . number_format($item->unit_price, 2) .
                ' = **$' . number_format($item->total_price, 2) . '**'
            );
        }

        $mail->line('---')
            ->line('**Total Paid: $' . number_format($order->total, 2) . '**')
            ->action('View Order & Print Receipt', route('account.orders.receipt', $order))
            ->line('Thank you for shopping with TrackNet!');

        return $mail;
    }
}
