<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Notifications\OrderInvoiceNotification;
use App\Services\ActivityLogService;
use App\Services\PayMongoService;
use App\Services\PdfService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private const ONLINE_METHODS = ['gcash', 'maya', 'card', 'grabpay'];

    public function index()
    {
        $cart = Auth::user()->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $cartItems = $cart->items()->with('product.inventory')->get();

        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} doesn't have enough stock");
            }
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);
        $taxRate  = config('cart.tax_rate', 0);
        $tax      = $subtotal * ($taxRate / 100);
        $shipping = 0;
        $total    = $subtotal + $tax + $shipping;

        return view('website.checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'billing_address'  => 'nullable|string',
            'payment_method'   => 'required|in:cod,gcash,maya,card,grabpay',
            'notes'            => 'nullable|string',
        ]);

        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $cartItems = $cart->items()->with('product.inventory')->get();

        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')->with('error', "{$item->product->name} doesn't have enough stock");
            }
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);
        $taxRate  = config('cart.tax_rate', 0);
        $tax      = $subtotal * ($taxRate / 100);
        $shipping = 0;
        $total    = $subtotal + $tax + $shipping;

        $isOnlinePayment = in_array($request->payment_method, self::ONLINE_METHODS);

        // Create order in a DB transaction
        $order = DB::transaction(function () use ($request, $user, $cart, $cartItems, $subtotal, $tax, $shipping, $total, $isOnlinePayment) {

            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
                'subtotal'         => $subtotal,
                'tax'              => $tax,
                'shipping'         => $shipping,
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'payment_status'   => $isOnlinePayment ? 'awaiting_payment' : 'pending',
                'shipping_address' => $request->shipping_address,
                'billing_address'  => $request->same_billing
                                        ? $request->shipping_address
                                        : $request->billing_address,
                'notes'            => $request->notes,
            ]);

            $stockService = app(StockService::class);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $cartItem->product_id,
                    'quantity'    => $cartItem->quantity,
                    'unit_price'  => $cartItem->product->price,
                    'total_price' => $cartItem->product->price * $cartItem->quantity,
                ]);

                $stockService->decrease(
                    $cartItem->product,
                    $cartItem->quantity,
                    StockMovement::REASON_CUSTOMER_ORDER,
                    "Order #{$order->order_number}",
                    $order,
                    $user->id
                );
            }

            // Clear cart
            $cart->items()->delete();

            // Create sale record for the sales department
            Sale::create([
                'user_id'            => $user->id,
                'order_id'           => $order->id,
                'total_amount'       => $total,
                'payment_status'     => 'pending',
                'fulfillment_status' => 'pending',
            ]);

            return $order;
        });

        ActivityLogService::orderPlaced($user->id, $order->order_number, $total, $request);

        // --- Online payment: redirect to PayMongo ---
        if ($isOnlinePayment) {
            return $this->redirectToPayMongo($order, $cartItems, $request->payment_method);
        }

        // --- COD: generate PDF then send invoice ---
        try {
            app(PdfService::class)->generateAndStore($order->load(['user', 'sale', 'items.product.category']));
        } catch (\Exception $e) {
            // PDF generation failure should not block order confirmation
        }

        try {
            $user->notify(new OrderInvoiceNotification($order->load('items.product')));
        } catch (\Exception $e) {
            // Invoice email failure should not block order confirmation
        }

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Order placed successfully! An invoice has been sent to your email.');
    }

    /**
     * Create PayMongo checkout session and redirect customer to hosted payment page.
     */
    private function redirectToPayMongo(Order $order, $cartItems, string $paymentMethod)
    {
        $payMongoService = app(PayMongoService::class);

        // Map our method names to PayMongo's payment_method_types
        $methodMap = [
            'gcash'   => ['gcash'],
            'maya'    => ['paymaya'],
            'card'    => ['card'],
            'grabpay' => ['grab_pay'],
        ];

        // Build line items — PayMongo expects amounts in centavos
        $lineItems = [];
        foreach ($cartItems as $item) {
            $lineItems[] = [
                'name'        => $item->product->name,
                'quantity'    => (int) $item->quantity,
                'amount'      => (int) round($item->product->price * 100),
                'currency'    => 'PHP',
                'description' => Str::limit($item->product->description ?? $item->product->name, 200),
            ];
        }

        // Add tax as a line item if > 0
        if ($order->tax > 0) {
            $lineItems[] = [
                'name'     => 'Tax (' . config('cart.tax_rate', 10) . '%)',
                'quantity' => 1,
                'amount'   => (int) round($order->tax * 100),
                'currency' => 'PHP',
            ];
        }

        try {
            $session = $payMongoService->createCheckoutSession(
                lineItems: $lineItems,
                paymentMethodTypes: $methodMap[$paymentMethod] ?? ['card'],
                description: "TrackNet Order {$order->order_number}",
                metadata: [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ],
                successUrl: route('checkout.success', ['order' => $order->id]),
                cancelUrl: route('checkout.cancel', ['order' => $order->id]),
            );

            $order->update([
                'paymongo_checkout_session_id' => $session['checkout_session_id'],
            ]);

            return redirect()->away($session['checkout_url']);

        } catch (\Exception $e) {
            Log::error('PayMongo session creation failed', [
                'order'   => $order->order_number,
                'error'   => $e->getMessage(),
            ]);

            // Restore inventory and mark order as failed
            $this->restoreInventoryAndFail($order);

            return redirect()->route('checkout.index')
                ->with('error', 'Payment gateway error. Please try again or choose Cash on Delivery.');
        }
    }

    /**
     * PayMongo redirects here after successful payment.
     * The webhook is the source of truth; this just provides immediate UX.
     */
    public function success(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Poll PayMongo to update status immediately if webhook hasn't arrived yet
        if ($order->isAwaitingPayment() && $order->paymongo_checkout_session_id) {
            try {
                $payMongoService = app(PayMongoService::class);
                $session = $payMongoService->retrieveCheckoutSession($order->paymongo_checkout_session_id);
                $payments = $session['attributes']['payments'] ?? [];

                if (!empty($payments)) {
                    $paymentStatus = $payments[0]['attributes']['status'] ?? '';
                    if ($paymentStatus === 'paid') {
                        $order->update([
                            'payment_status'            => 'paid',
                            'paymongo_payment_intent_id' => $payments[0]['id'] ?? null,
                        ]);
                        if ($order->sale) {
                            $order->sale->update(['payment_status' => 'paid']);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Webhook will handle it — no problem
            }
        }

        // Generate PDF then send invoice email
        try {
            app(PdfService::class)->generateAndStore($order->load(['user', 'sale', 'items.product.category']));
        } catch (\Exception $e) {
            // Non-critical
        }

        try {
            $order->user->notify(new OrderInvoiceNotification($order->load('items.product')));
        } catch (\Exception $e) {
            // Non-critical
        }

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Payment successful! Your order has been confirmed.');
    }

    /**
     * PayMongo redirects here if customer cancels payment.
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Only cancel if still awaiting payment
        if ($order->isAwaitingPayment()) {
            $this->restoreInventoryAndFail($order);
        }

        return redirect()->route('cart.index')
            ->with('error', 'Payment was cancelled. You can try again by re-adding items to your cart.');
    }

    /**
     * Restore inventory for a failed/cancelled online payment order.
     */
    private function restoreInventoryAndFail(Order $order): void
    {
        $order->load('items.product.inventory');
        $stockService = app(StockService::class);

        foreach ($order->items as $item) {
            if ($item->product) {
                $stockService->increase(
                    $item->product,
                    $item->quantity,
                    StockMovement::REASON_PAYMENT_FAILED,
                    "Payment failed for Order #{$order->order_number}",
                    $order
                );
            }
        }

        $order->update(['payment_status' => 'failed']);

        if ($order->sale) {
            $order->sale->update([
                'payment_status'     => 'failed',
                'fulfillment_status' => 'cancelled',
            ]);
        }
    }
}
