<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';
    protected $description = 'Expire orders that have been awaiting payment for more than 24 hours and restore their inventory';

    public function handle(): int
    {
        $staleOrders = Order::where('payment_status', 'awaiting_payment')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        if ($staleOrders->isEmpty()) {
            $this->info('No stale orders found.');
            return self::SUCCESS;
        }

        foreach ($staleOrders as $order) {
            $order->load('items.product.inventory');

            // Restore inventory
            foreach ($order->items as $item) {
                if ($item->product && $item->product->inventory) {
                    $item->product->inventory->increment('quantity', $item->quantity);
                }
            }

            $order->update(['payment_status' => 'expired']);

            if ($order->sale) {
                $order->sale->update([
                    'payment_status'     => 'failed',
                    'fulfillment_status' => 'cancelled',
                ]);
            }

            $this->line("  Expired: {$order->order_number}");
        }

        $this->info("Done. Expired {$staleOrders->count()} order(s).");
        return self::SUCCESS;
    }
}
