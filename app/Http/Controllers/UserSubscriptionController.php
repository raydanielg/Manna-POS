<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
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

    public function choosePlan(Request $req)
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

        $status = $amount == 0 ? 'trial' : 'active';
        $days   = $cycle === 'yearly' ? 365 : 30;

        UserSubscription::create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $plan->id,
            'billing_cycle'        => $cycle,
            'amount_paid'          => $amount,
            'currency'             => $user->currency ?? 'TZS',
            'status'               => $status,
            'starts_at'            => now(),
            'expires_at'           => $amount == 0 ? now()->addDays(14) : now()->addDays($days),
        ]);

        $message = 'Welcome to ' . $plan->name . '!';

        if ($req->expectsJson() || $req->ajax()) {
            return response()->json(['message' => $message, 'plan' => $plan->name]);
        }

        return redirect('/dashboard')->with('subscribed', $message);
    }
}
