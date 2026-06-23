<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\SnippeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function plans()
    {
        return view('subscription.plans');
    }

    public function choosePlan(Request $req, SnippeService $snippe)
    {
        $req->validate([
            'plan_id'       => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'nullable|in:monthly,yearly',
        ]);

        $user   = auth()->user();
        $plan   = SubscriptionPlan::findOrFail($req->plan_id);
        $cycle  = $req->billing_cycle ?? 'monthly';
        $amount = (float) ($cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly);

        // Cancel existing active/trial subscriptions before starting a new one
        UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled']);

        // ── Free / Trial plan ────────────────────────────────────────────
        if ($amount == 0) {
            UserSubscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle'        => $cycle,
                'amount_paid'          => 0,
                'currency'             => 'TZS',
                'status'               => 'trial',
                'starts_at'            => now(),
                'expires_at'           => now()->addDays(14),
            ]);

            $message = 'Welcome to ' . $plan->name . '! Your 14-day free trial has started.';

            if ($req->expectsJson() || $req->ajax()) {
                return response()->json([
                    'message'  => $message,
                    'plan'     => $plan->name,
                    'redirect' => '/dashboard',
                ]);
            }

            return redirect('/dashboard')->with('subscribed', $message);
        }

        // ── Paid plan — create pending subscription + Snippe checkout ────
        $subscription = UserSubscription::create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_cycle'        => $cycle,
            'amount_paid'          => $amount,
            'currency'             => 'TZS',
            'status'               => 'pending',
            'starts_at'            => null,
            'expires_at'           => null,
        ]);

        try {
            $session = $snippe->createSession([
                // TZS is zero-decimal: send the integer as-is (e.g. 5000 TZS → 5000)
                'amount'         => (int) $amount,
                'currency'       => 'TZS',
                'customer_name'  => $user->name,
                'customer_phone' => $user->phone ?? '',
                'customer_email' => $user->email,
                'description'    => $plan->name . ' — ' . ucfirst($cycle) . ' subscription',
                'metadata'       => [
                    'subscription_id' => (string) $subscription->id,
                    'plan_id'         => (string) $plan->id,
                    'user_id'         => (string) $user->id,
                    'billing_cycle'   => $cycle,
                ],
                'redirect_url' => url('/subscription/plans?payment=success'),
            ]);

            $subscription->update([
                'snippe_session_ref' => $session['reference'] ?? null,
            ]);

            $checkoutUrl   = $session['checkout_url'] ?? null;
            $paymentLinkUrl = $session['payment_link_url'] ?? null;
            $sessionRef    = $session['reference'] ?? null;

            if ($req->expectsJson() || $req->ajax()) {
                return response()->json([
                    'message'          => 'Redirecting to payment...',
                    'plan'             => $plan->name,
                    'checkout_url'     => $checkoutUrl,
                    'payment_link_url' => $paymentLinkUrl,
                    'session_ref'      => $sessionRef,
                ]);
            }

            if ($checkoutUrl) {
                return redirect()->away($checkoutUrl);
            }

            throw new \Exception('No checkout URL returned from Snippe');
        } catch (\Throwable $e) {
            Log::error('Snippe subscription initiation failed', [
                'user_id'         => $user->id,
                'plan_id'         => $plan->id,
                'subscription_id' => $subscription->id,
                'error'           => $e->getMessage(),
            ]);

            $subscription->update([
                'status' => 'pending',
                'notes'  => 'Payment init failed: ' . $e->getMessage(),
            ]);

            if ($req->expectsJson() || $req->ajax()) {
                return response()->json([
                    'message' => 'Could not initiate payment. ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }

    /**
     * Poll Snippe for the session/payment status.
     * Called by JS after the user returns from checkout.
     */
    public function checkPaymentStatus(Request $req, SnippeService $snippe)
    {
        $user = auth()->user();

        // Find the latest pending subscription for this user
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereNotNull('snippe_session_ref')
            ->latest()
            ->first();

        if (!$subscription) {
            // Check if already activated (webhook already fired)
            $active = $user->activeSubscription();
            if ($active) {
                return response()->json([
                    'status' => 'completed',
                    'plan'   => $active->plan->name ?? 'Your plan',
                ]);
            }
            return response()->json(['status' => 'not_found'], 404);
        }

        try {
            $session = $snippe->getSession($subscription->snippe_session_ref);
            $sessionStatus = $session['status'] ?? 'pending';

            if ($sessionStatus === 'completed') {
                // Activate if webhook hasn't already done it
                if ($subscription->status === 'pending') {
                    $cycle = $subscription->billing_cycle ?? 'monthly';
                    $days  = $cycle === 'yearly' ? 365 : 30;
                    $subscription->update([
                        'status'         => 'active',
                        'payment_status' => 'completed',
                        'paid_at'        => now(),
                        'starts_at'      => now(),
                        'expires_at'     => now()->addDays($days),
                        'transaction_ref'=> $session['reference'] ?? null,
                    ]);
                }
                return response()->json([
                    'status' => 'completed',
                    'plan'   => $subscription->plan->name ?? 'Your plan',
                ]);
            }

            if (in_array($sessionStatus, ['expired', 'cancelled'])) {
                $subscription->update([
                    'status'         => 'cancelled',
                    'payment_status' => $sessionStatus,
                ]);
                return response()->json(['status' => $sessionStatus]);
            }

            return response()->json(['status' => 'pending']);
        } catch (\Throwable $e) {
            Log::warning('Snippe status check failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'pending']);
        }
    }
}
