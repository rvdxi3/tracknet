<?php

// app/Http/Controllers/Sales/OrderController.php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderShippedNotification;
use App\Services\PdfService;
use App\Services\StockService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'sale', 'items.product.category'])->latest();

        // Filter by fulfillment status
        if ($request->filled('status')) {
            $query->whereHas('sale', fn ($q) => $q->where('fulfillment_status', $request->status));
        }

        // Filter by payment status
        if ($request->filled('payment')) {
            $query->whereHas('sale', fn ($q) => $q->where('payment_status', $request->payment));
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('sales.orders.index', compact('orders'));
    }

    public function create()
    {
        return redirect()->route('sales.orders.index')
            ->with('info', 'Orders are created through the customer checkout process.');
    }

    public function store(Request $request)
    {
        return redirect()->route('sales.orders.index')
            ->with('info', 'Orders are created through the customer checkout process.');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'sale', 'items.product.category']);
        return view('sales.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if ($order->sale && $order->sale->fulfillment_status === 'delivered') {
            return redirect()->route('sales.orders.show', $order)
                ->with('error', 'Delivered orders cannot be edited.');
        }

        $order->load(['user', 'sale', 'items.product']);
        return view('sales.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->sale && $order->sale->fulfillment_status === 'delivered') {
            return redirect()->route('sales.orders.show', $order)
                ->with('error', 'Delivered orders cannot be updated.');
        }

        $request->validate([
            'payment_status'     => ['required', 'in:pending,paid,failed,refunded'],
            'fulfillment_status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
            'notes'              => ['nullable', 'string', 'max:1000'],
        ]);

        $oldFulfillment = $order->sale?->fulfillment_status;

        if ($order->sale) {
            $order->sale->update([
                'payment_status'     => $request->payment_status,
                'fulfillment_status' => $request->fulfillment_status,
            ]);
        } else {
            Sale::create([
                'user_id'            => $order->user_id,
                'order_id'           => $order->id,
                'total_amount'       => $order->total,
                'payment_status'     => $request->payment_status,
                'fulfillment_status' => $request->fulfillment_status,
            ]);
        }

        $order->update(['notes' => $request->notes]);

        $newFulfillment = $request->fulfillment_status;
        if ($newFulfillment !== $oldFulfillment) {
            $order->load('user');
            try {
                if ($newFulfillment === 'shipped') {
                    $order->user->notify(new OrderShippedNotification($order));
                } elseif ($newFulfillment === 'delivered') {
                    $order->user->notify(new OrderDeliveredNotification($order));
                } elseif ($newFulfillment === 'cancelled') {
                    $order->user->notify(new OrderCancelledNotification($order));
                }
            } catch (\Exception $e) {
                // Notification failure should not block the update
            }
        }

        return redirect()->route('sales.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        if ($order->sale && $order->sale->fulfillment_status === 'delivered') {
            return redirect()->route('sales.orders.index')
                ->with('error', 'Delivered orders cannot be deleted.');
        }

        $order->load('items.product');
        $stockService = app(StockService::class);

        foreach ($order->items as $item) {
            if ($item->product) {
                $stockService->increase(
                    $item->product,
                    $item->quantity,
                    StockMovement::REASON_ORDER_CANCELLED,
                    "Order #{$order->order_number} deleted",
                    $order
                );
            }
        }

        $order->delete();

        return redirect()->route('sales.orders.index')
            ->with('success', 'Order deleted and inventory restored.');
    }

    public function fulfill(Order $order)
    {
        if (!$order->sale) {
            return back()->with('error', 'Order does not have a sale record.');
        }

        if ($order->sale->fulfillment_status === 'delivered') {
            return back()->with('error', 'Order is already delivered.');
        }

        $order->sale->update([
            'fulfillment_status' => 'delivered',
            'payment_status'     => 'paid',
        ]);

        try {
            $order->user->notify(new OrderDeliveredNotification($order));
        } catch (\Exception $e) {}

        return back()->with('success', 'Order marked as delivered and paid.');
    }

    public function cancel(Order $order)
    {
        if ($order->sale && $order->sale->fulfillment_status === 'delivered') {
            return back()->with('error', 'Delivered orders cannot be cancelled.');
        }

        $order->load('items.product');
        $stockService = app(StockService::class);

        foreach ($order->items as $item) {
            if ($item->product) {
                $stockService->increase(
                    $item->product,
                    $item->quantity,
                    StockMovement::REASON_ORDER_CANCELLED,
                    "Order #{$order->order_number} cancelled",
                    $order
                );
            }
        }

        if ($order->sale) {
            $order->sale->update([
                'payment_status'     => 'refunded',
                'fulfillment_status' => 'cancelled',
            ]);
        } else {
            Sale::create([
                'user_id'            => $order->user_id,
                'order_id'           => $order->id,
                'total_amount'       => $order->total,
                'payment_status'     => 'refunded',
                'fulfillment_status' => 'cancelled',
            ]);
        }

        try {
            $order->user->notify(new OrderCancelledNotification($order));
        } catch (\Exception $e) {}

        return back()->with('success', 'Order cancelled and inventory restored.');
    }

    public function refund(Order $order)
    {
        if (!$order->sale) {
            return back()->with('error', 'Order does not have a sale record.');
        }

        if ($order->sale->payment_status === 'refunded') {
            return back()->with('error', 'Order has already been refunded.');
        }

        $order->load('items.product');
        $stockService = app(StockService::class);

        foreach ($order->items as $item) {
            if ($item->product) {
                $stockService->increase(
                    $item->product,
                    $item->quantity,
                    StockMovement::REASON_ORDER_REFUNDED,
                    "Order #{$order->order_number} refunded",
                    $order
                );
            }
        }

        $order->sale->update(['payment_status' => 'refunded']);

        return back()->with('success', 'Order refunded and inventory restored.');
    }

    public function receipt(Order $order)
    {
        $order->load(['user', 'sale.user', 'items.product.category']);
        return view('sales.orders.receipt', compact('order'));
    }

    public function receiptPdf(Order $order)
    {
        $order->load(['user', 'sale.user', 'items.product.category']);
        $url = app(PdfService::class)->getTemporaryUrl($order);
        return redirect()->away($url);
    }
}
