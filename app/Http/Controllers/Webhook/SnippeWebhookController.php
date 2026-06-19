<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Services\SnippeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnippeWebhookController extends Controller
{
    public function __invoke(Request $request, SnippeService $snippe)
    {
        $payload = $request->getContent();
        $headers = [
            'x-webhook-timestamp' => $request->header('X-Webhook-Timestamp'),
            'x-webhook-signature' => $request->header('X-Webhook-Signature'),
        ];

        $event = $snippe->verifyWebhook($payload, $headers);

        if (!$event) {
            Log::warning('Snippe webhook verification failed', [
                'headers' => $request->headers->all(),
            ]);
            return response('Invalid signature', 400);
        }

        $type = $event['type'] ?? '';
        $data = $event['data'] ?? [];

        Log::info('Snippe webhook received', [
            'type' => $type,
            'reference' => $data['reference'] ?? null,
        ]);

        try {
            match ($type) {
                'payment.completed' => $this->handlePaymentCompleted($data),
                'payment.failed' => $this->handlePaymentFailed($data),
                default => Log::info('Snippe unhandled event type', ['type' => $type]),
            };
        } catch (\Throwable $e) {
            Log::error('Snippe webhook processing error', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK');
    }

    protected function handlePaymentCompleted(array $data): void
    {
        $metadata = $data['metadata'] ?? [];
        $subscriptionId = $metadata['subscription_id'] ?? null;

        if (!$subscriptionId) {
            Log::warning('Snippe webhook: no subscription_id in metadata', ['data' => $data]);
            return;
        }

        $subscription = UserSubscription::find($subscriptionId);

        if (!$subscription) {
            Log::warning('Snippe webhook: subscription not found', ['id' => $subscriptionId]);
            return;
        }

        if ($subscription->status !== 'pending') {
            Log::info('Snippe webhook: subscription already processed', [
                'id' => $subscriptionId,
                'status' => $subscription->status,
            ]);
            return;
        }

        $cycle = $subscription->billing_cycle ?? 'monthly';
        $days = $cycle === 'yearly' ? 365 : 30;

        $subscription->update([
            'status' => 'active',
            'payment_status' => 'completed',
            'paid_at' => now(),
            'transaction_ref' => $data['reference'] ?? $data['external_reference'] ?? null,
            'starts_at' => now(),
            'expires_at' => now()->addDays($days),
        ]);

        Log::info('Snippe: subscription activated', [
            'subscription_id' => $subscriptionId,
            'user_id' => $subscription->user_id,
        ]);
    }

    protected function handlePaymentFailed(array $data): void
    {
        $metadata = $data['metadata'] ?? [];
        $subscriptionId = $metadata['subscription_id'] ?? null;

        if (!$subscriptionId) return;

        $subscription = UserSubscription::find($subscriptionId);

        if ($subscription && $subscription->status === 'pending') {
            $subscription->update([
                'payment_status' => 'failed',
                'notes' => 'Payment failed: ' . ($data['failure_reason'] ?? 'Unknown reason'),
            ]);
        }
    }
}
