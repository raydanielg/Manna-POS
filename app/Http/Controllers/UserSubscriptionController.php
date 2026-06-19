<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\SnippeService;
use Illuminate\Http\Request;

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
            'billing_cycle' => 'in:monthly,yearly',
        ]);

        $user   = auth()->user();
        $plan   = SubscriptionPlan::findOrFail($req->plan_id);
        $cycle  = $req->billing_cycle ?? 'monthly';
        $amount = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        // Cancel existing active subscriptions
        UserSubscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled']);

        if ($amount == 0) {
            // Free / Trial plan — activate immediately
            $subscription = UserSubscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle'        => $cycle,
                'amount_paid'          => 0,
                'currency'             => $user->currency ?? 'TZS',
                'status'               => 'trial',
                'starts_at'            => now(),
                'expires_at'           => now()->addDays(14),
            ]);

            $message = 'Welcome to ' . $plan->name . '!';

            if ($req->expectsJson() || $req->ajax()) {
                return response()->json(['message' => $message, 'plan' => $plan->name, 'redirect' => '/dashboard']);
            }

            return redirect('/dashboard')->with('subscribed', $message);
        }

        // Paid plan — create pending subscription + Snippe session
        $subscription = UserSubscription::create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_cycle'        => $cycle,
            'amount_paid'          => $amount,
            'currency'             => $user->currency ?? 'TZS',
            'status'               => 'pending',
            'starts_at'            => null,
            'expires_at'           => null,
        ]);

        try {
            $session = $snippe->createSession([
                'amount'        => (int) ($amount * 100), // convert to smallest unit
                'currency'      => $user->currency ?? 'TZS',
                'customer_name' => $user->name,
                'customer_phone'=> $user->phone ?? '',
                'customer_email'=> $user->email,
                'description'   => $plan->name . ' (' . ucfirst($cycle) . ')',
                'metadata'      => [
                    'subscription_id' => (string) $subscription->id,
                    'plan_id'         => (string) $plan->id,
                    'user_id'         => (string) $user->id,
                    'billing_cycle'   => $cycle,
                ],
                'redirect_url'  => route('dashboard') . '?payment=success',
                'line_items'    => [
                    [
                        'name'       => $plan->name . ' — ' . ucfirst($cycle),
                        'quantity'   => 1,
                        'unit_price' => (int) ($amount * 100),
                    ],
                ],
            ]);

            $subscription->update([
                'snippe_session_ref' => $session['reference'] ?? null,
            ]);

            $checkoutUrl = $session['checkout_url'] ?? null;

            if ($req->expectsJson() || $req->ajax()) {
                return response()->json([
                    'message'      => 'Redirecting to payment...',
                    'plan'         => $plan->name,
                    'checkout_url' => $checkoutUrl,
                    'session_ref'  => $session['reference'] ?? null,
                ]);
            }

            if ($checkoutUrl) {
                return redirect()->away($checkoutUrl);
            }

            throw new \Exception('No checkout URL returned');
        } catch (\Throwable $e) {
            $subscription->update(['notes' => 'Payment failed: ' . $e->getMessage()]);

            if ($req->expectsJson() || $req->ajax()) {
                return response()->json([
                    'message' => 'Could not initiate payment. Please try again.',
                ], 422);
            }

            return back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }
}
