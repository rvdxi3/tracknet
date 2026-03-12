<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private string $baseUrl;
    private string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('paymongo.base_url');
        $this->secretKey = config('paymongo.secret_key');
    }

    /**
     * Create a PayMongo Checkout Session.
     *
     * @param  array  $lineItems           [{name, quantity, amount (centavos), currency, description}]
     * @param  array  $paymentMethodTypes  e.g. ['gcash'], ['paymaya'], ['card'], ['grab_pay']
     * @param  string $description
     * @param  array  $metadata            ['order_id' => ..., 'order_number' => ...]
     * @param  string $successUrl
     * @param  string $cancelUrl
     * @return array  ['checkout_session_id' => ..., 'checkout_url' => ...]
     */
    public function createCheckoutSession(
        array $lineItems,
        array $paymentMethodTypes,
        string $description,
        array $metadata,
        string $successUrl,
        string $cancelUrl
    ): array {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/checkout_sessions", [
                'data' => [
                    'attributes' => [
                        'line_items'           => $lineItems,
                        'payment_method_types' => $paymentMethodTypes,
                        'description'          => $description,
                        'send_email_receipt'   => false,
                        'show_description'     => true,
                        'show_line_items'      => true,
                        'metadata'             => $metadata,
                        'success_url'          => $successUrl,
                        'cancel_url'           => $cancelUrl,
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::error('PayMongo checkout session creation failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            throw new \RuntimeException('Failed to create PayMongo checkout session: ' . ($response->json('errors.0.detail') ?? 'Unknown error'));
        }

        $data = $response->json('data');

        return [
            'checkout_session_id' => $data['id'],
            'checkout_url'        => $data['attributes']['checkout_url'],
        ];
    }

    /**
     * Retrieve a checkout session to check its payment status.
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/checkout_sessions/{$sessionId}");

        if ($response->failed()) {
            Log::error('PayMongo retrieve session failed', [
                'session_id' => $sessionId,
                'status'     => $response->status(),
            ]);
            throw new \RuntimeException('Failed to retrieve PayMongo checkout session.');
        }

        return $response->json('data');
    }

    /**
     * Verify PayMongo webhook signature.
     *
     * Signature header format: t=timestamp,te=test_signature,li=live_signature
     */
    public function verifyWebhookSignature(string $payload, string $sigHeader): bool
    {
        $webhookSecret = config('paymongo.webhook_secret');

        if (empty($webhookSecret) || empty($sigHeader)) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $sigHeader) as $item) {
            $pair = explode('=', $item, 2);
            if (count($pair) === 2) {
                $parts[$pair[0]] = $pair[1];
            }
        }

        $timestamp = $parts['t'] ?? '';
        // Use 'te' for test mode, 'li' for live mode
        $signature = $parts['te'] ?? $parts['li'] ?? '';

        if (empty($timestamp) || empty($signature)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', "{$timestamp}.{$payload}", $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }
}
