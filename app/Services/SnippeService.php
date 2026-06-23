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
        $this->apiKey        = config('snippe.api_key', '');
        $this->baseUrl       = rtrim(config('snippe.base_url', 'https://api.snippe.sh'), '/');
        $this->webhookSecret = config('snippe.webhook_secret', '');
    }

    /**
     * Create a Snippe hosted checkout session.
     * Amount must be an integer in TZS (no subunits — 5000 TZS = 5000).
     */
    public function createSession(array $data): array
    {
        $amount = (int) $data['amount'];

        // Idempotency key: subscription_id + timestamp, max 30 chars
        $subId        = $data['metadata']['subscription_id'] ?? uniqid();
        $idempotencyKey = substr('sub-' . $subId . '-' . time(), 0, 30);

        $payload = [
            'amount'          => $amount,
            'currency'        => $data['currency'] ?? 'TZS',
            'allowed_methods' => ['mobile_money', 'qr'],
            'customer'        => [
                'name'  => $data['customer_name'] ?? '',
                'phone' => $data['customer_phone'] ?? '',
                'email' => $data['customer_email'] ?? '',
            ],
            'description'  => $data['description'] ?? 'Subscription Payment',
            'metadata'     => $data['metadata'] ?? [],
            'redirect_url' => $data['redirect_url'] ?? url('/subscription/plans?payment=success'),
            'webhook_url'  => $data['webhook_url'] ?? url('/webhooks/snippe'),
            'expires_in'   => 3600,
        ];

        Log::info('Snippe createSession request', [
            'amount'         => $amount,
            'description'    => $payload['description'],
            'idempotency_key'=> $idempotencyKey,
        ]);

        $response = Http::withHeaders([
            'Authorization'  => 'Bearer ' . $this->apiKey,
            'Content-Type'   => 'application/json',
            'Idempotency-Key'=> $idempotencyKey,
        ])->post($this->baseUrl . '/api/v1/sessions', $payload);

        if (!$response->successful()) {
            Log::error('Snippe session creation failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception(
                'Payment session creation failed: ' .
                ($response->json('message') ?? $response->body() ?? 'Unknown error')
            );
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
            Log::error('Snippe getSession failed', [
                'reference' => $reference,
                'status'    => $response->status(),
            ]);
            throw new \Exception('Failed to retrieve session: ' . $response->status());
        }

        return $response->json('data', []);
    }

    /**
     * Get payment status by payment reference.
     */
    public function getPayment(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/v1/payments/' . $reference);

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve payment: ' . $response->status());
        }

        return $response->json('data', []);
    }

    /**
     * Verify incoming webhook signature.
     * Uses constant-time comparison and rejects stale timestamps (>5 min).
     */
    public function verifyWebhook(string $payload, array $headers): ?array
    {
        $timestamp = $headers['x-webhook-timestamp'] ?? '';
        $signature = $headers['x-webhook-signature'] ?? '';

        if (empty($timestamp) || empty($signature)) {
            Log::warning('Snippe webhook: missing signature headers');
            return null;
        }

        if ((time() - (int) $timestamp) > 300) {
            Log::warning('Snippe webhook: timestamp too old', ['ts' => $timestamp]);
            return null;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $this->webhookSecret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Snippe webhook: invalid signature');
            return null;
        }

        return json_decode($payload, true);
    }
}
