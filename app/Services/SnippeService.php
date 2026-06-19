<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SnippeService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->apiKey = config('snippe.api_key');
        $this->baseUrl = config('snippe.base_url');
        $this->webhookSecret = config('snippe.webhook_secret');
    }

    /**
     * Create a payment session for subscription checkout.
     */
    public function createSession(array $data): array
    {
        $payload = [
            'amount' => (int) $data['amount'],
            'currency' => $data['currency'] ?? 'TZS',
            'allowed_methods' => ['mobile_money', 'qr', 'card'],
            'customer' => [
                'name' => $data['customer_name'] ?? '',
                'phone' => $data['customer_phone'] ?? '',
                'email' => $data['customer_email'] ?? '',
            ],
            'description' => $data['description'] ?? 'Subscription Payment',
            'metadata' => $data['metadata'] ?? [],
            'redirect_url' => $data['redirect_url'] ?? url('/dashboard'),
            'webhook_url' => $data['webhook_url'] ?? url('/webhooks/snippe'),
            'expires_in' => 86400, // 24 hours
            'display' => [
                'show_line_items' => true,
                'line_items_style' => 'compact',
                'show_description' => true,
                'show_merchant_logo' => true,
                'theme' => 'light',
                'button_text' => 'Pay Now',
            ],
        ];

        if (!empty($data['line_items'])) {
            $payload['line_items'] = $data['line_items'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/api/v1/sessions', $payload);

        if (!$response->successful()) {
            Log::error('Snippe session creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Payment session creation failed: ' . ($response->json('message') ?? 'Unknown error'));
        }

        return $response->json('data', []);
    }

    /**
     * Get session details by reference.
     */
    public function getSession(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/api/v1/sessions/' . $reference);

        if (!$response->successful()) {
            Log::error('Snippe get session failed', [
                'reference' => $reference,
                'status' => $response->status(),
            ]);
            throw new \Exception('Failed to retrieve session');
        }

        return $response->json('data', []);
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhook(string $payload, array $headers): ?array
    {
        $timestamp = $headers['x-webhook-timestamp'] ?? '';
        $signature = $headers['x-webhook-signature'] ?? '';

        if (empty($timestamp) || empty($signature)) {
            Log::warning('Snippe webhook missing headers');
            return null;
        }

        // Prevent replay attacks (reject if > 5 minutes old)
        $eventTime = (int) $timestamp;
        $currentTime = time();
        if ($currentTime - $eventTime > 300) {
            Log::warning('Snippe webhook timestamp too old');
            return null;
        }

        // Compute expected signature
        $message = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $message, $this->webhookSecret);

        // Constant-time comparison
        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Snippe webhook invalid signature');
            return null;
        }

        return json_decode($payload, true);
    }
}
