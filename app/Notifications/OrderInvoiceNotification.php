<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderInvoiceNotification extends Notification implements ShouldQueue
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
            ->subject('TrackNet — Invoice for Order #' . $order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for your order! Here is your invoice summary.')
            ->line('**Order Number:** ' . $order->order_number)
            ->line('**Order Date:** ' . $order->created_at->format('F d, Y'))
            ->line('**Payment Method:** ' . ucfirst(str_replace('_', ' ', $order->payment_method)))
            ->line('---')
            ->line('**Items Ordered:**');

        foreach ($order->items as $item) {
            $mail->line(
                '• ' . $item->product->name .
                ' × ' . $item->quantity .
                ' @ $' . number_format($item->unit_price, 2) .
                ' = **$' . number_format($item->total_price, 2) . '**'
            );
        }

        $mail->line('---')
            ->line('**Subtotal:** $' . number_format($order->subtotal, 2))
            ->line('**Tax:** $' . number_format($order->tax, 2))
            ->line('**Shipping:** ' . ($order->shipping > 0 ? '$' . number_format($order->shipping, 2) : 'Free'))
            ->line('**Total: $' . number_format($order->total, 2) . '**')
            ->line('---')
            ->line('**Shipping Address:** ' . $order->shipping_address)
            ->action('View Order & Print Receipt', route('account.orders.show', $order))
            ->line('You can also download your invoice PDF using the button below.')
            ->action('Download Invoice PDF', app(PdfService::class)->getTemporaryUrl($order))
            ->line('We will notify you when your order is shipped.')
            ->line('Thank you for shopping with TrackNet!');

        return $mail;
    }
}
