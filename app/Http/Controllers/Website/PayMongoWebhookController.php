<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request, PayMongoService $payMongoService)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Paymongo-Signature', '');

        // Verify webhook signature
        if (!$payMongoService->verifyWebhookSignature($payload, $sigHeader)) {
            Log::warning('PayMongo webhook: signature verification failed');
            return response('Invalid signature', 401);
        }

        $event        = $request->input('data.attributes.type');
        $resourceData = $request->input('data.attributes.data');

        Log::info('PayMongo webhook received', ['type' => $event]);

        switch ($event) {
            case 'checkout_session.payment.paid':
                $this->handlePaymentPaid($resourceData);
                break;

            case 'payment.failed':
                $this->handlePaymentFailed($resourceData);
                break;

            default:
                Log::info('Unhandled PayMongo webhook event', ['type' => $event]);
        }

        return response('OK', 200);
    }

    private function handlePaymentPaid(array $resourceData): void
    {
        $sessionId = $resourceData['id'] ?? null;

        // Extract payment reference
        $paymentIntentId = $resourceData['attributes']['payment_intent']['id']
            ?? $resourceData['attributes']['payments'][0]['id']
            ?? null;

        // Find order by checkout session ID
        $order = Order::where('paymongo_checkout_session_id', $sessionId)->first();

        // Fallback: try metadata
        if (!$order) {
            $metadata = $resourceData['attributes']['metadata'] ?? [];
            $orderId  = $metadata['order_id'] ?? null;
            $order    = $orderId ? Order::find($orderId) : null;
        }

        if (!$order) {
            Log::error('PayMongo webhook: order not found', ['session_id' => $sessionId]);
            return;
        }

        // Idempotent — skip if already paid
        if ($order->payment_status === 'paid') {
            Log::info('PayMongo webhook: order already paid', ['order_number' => $order->order_number]);
            return;
        }

        $order->update([
            'payment_status'            => 'paid',
            'paymongo_payment_intent_id' => $paymentIntentId,
        ]);

        if ($order->sale) {
            $order->sale->update(['payment_status' => 'paid']);
        }

        Log::info('PayMongo payment confirmed', ['order_number' => $order->order_number]);
    }

    private function handlePaymentFailed(array $resourceData): void
    {
        $metadata = $resourceData['attributes']['metadata'] ?? [];
        $orderId  = $metadata['order_id'] ?? null;

        $order = $orderId ? Order::find($orderId) : null;

        if (!$order || $order->payment_status === 'paid') {
            return;
        }

        $order->update(['payment_status' => 'failed']);

        if ($order->sale) {
            $order->sale->update([
                'payment_status'     => 'failed',
                'fulfillment_status' => 'cancelled',
            ]);
        }

        // Restore inventory
        $order->load('items.product.inventory');
        foreach ($order->items as $item) {
            if ($item->product && $item->product->inventory) {
                $item->product->inventory->increment('quantity', $item->quantity);
            }
        }

        Log::warning('PayMongo payment failed, inventory restored', [
            'order_number' => $order->order_number,
        ]);
    }
}
