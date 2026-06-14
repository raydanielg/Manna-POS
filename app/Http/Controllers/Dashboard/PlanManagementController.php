<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanManagementController extends Controller
{
    // ── Subscription Plans CRUD ────────────────────────────────────────

    public function indexPlans(Request $request)
    {
        $q = SubscriptionPlan::withCount(['subscriptions', 'activeSubscriptions'])
            ->orderBy('sort_order')
            ->orderBy('price_monthly');

        if ($s = $request->search) {
            $q->where('name', 'like', "%{$s}%");
        }

        return response()->json($q->get());
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string',
            'price_monthly'  => 'required|numeric|min:0',
            'price_yearly'   => 'nullable|numeric|min:0',
            'currency'       => 'nullable|string|max:3',
            'max_users'      => 'nullable|integer|min:1',
            'max_products'   => 'nullable|integer|min:1',
            'max_locations'  => 'nullable|integer|min:1',
            'features'       => 'nullable|array',
            'is_active'      => 'nullable|boolean',
            'is_featured'    => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
            'badge_color'    => 'nullable|string|max:20',
        ]);

        $data['slug']         = Str::slug($data['name']) . '-' . time();
        $data['price_yearly'] = $data['price_yearly'] ?? round($data['price_monthly'] * 10, 2);
        $data['currency']     = $data['currency'] ?? 'TZS';
        $data['is_active']    = $data['is_active'] ?? true;
        $data['is_featured']  = $data['is_featured'] ?? false;

        $plan = SubscriptionPlan::create($data);
        $plan->loadCount(['subscriptions', 'activeSubscriptions']);

        return response()->json($plan, 201);
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string',
            'price_monthly'  => 'required|numeric|min:0',
            'price_yearly'   => 'nullable|numeric|min:0',
            'currency'       => 'nullable|string|max:3',
            'max_users'      => 'nullable|integer|min:1',
            'max_products'   => 'nullable|integer|min:1',
            'max_locations'  => 'nullable|integer|min:1',
            'features'       => 'nullable|array',
            'is_active'      => 'nullable|boolean',
            'is_featured'    => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
            'badge_color'    => 'nullable|string|max:20',
        ]);

        $plan->update($data);
        $plan->loadCount(['subscriptions', 'activeSubscriptions']);

        return response()->json($plan);
    }

    public function destroyPlan(SubscriptionPlan $plan)
    {
        if ($plan->activeSubscriptions()->count() > 0) {
            return response()->json(['message' => 'Cannot delete a plan with active subscriptions.'], 422);
        }
        $plan->delete();
        return response()->json(['message' => 'Plan deleted.']);
    }

    // ── Subscriptions CRUD ─────────────────────────────────────────────

    public function indexSubscriptions(Request $request)
    {
        $q = UserSubscription::with(['user:id,name,email', 'plan:id,name,badge_color'])
            ->latest();

        if ($s = $request->search) {
            $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        if ($status = $request->status) {
            $q->where('status', $status);
        }
        if ($plan = $request->plan_id) {
            $q->where('subscription_plan_id', $plan);
        }

        return response()->json($q->paginate(20));
    }

    public function storeSubscription(Request $request)
    {
        $data = $request->validate([
            'user_id'              => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle'        => 'nullable|in:monthly,yearly',
            'amount_paid'          => 'nullable|numeric|min:0',
            'status'               => 'nullable|in:active,expired,cancelled,pending,trial',
            'starts_at'            => 'nullable|date',
            'expires_at'           => 'nullable|date|after:starts_at',
            'transaction_ref'      => 'nullable|string|max:100',
            'notes'                => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::findOrFail($data['subscription_plan_id']);
        $data['billing_cycle'] = $data['billing_cycle'] ?? 'monthly';
        $data['currency']      = $plan->currency;
        $data['amount_paid']   = $data['amount_paid'] ?? (
            $data['billing_cycle'] === 'yearly' ? $plan->price_yearly : $plan->price_monthly
        );
        $data['starts_at'] = $data['starts_at'] ?? now();

        $sub = UserSubscription::create($data);
        $sub->load(['user:id,name,email', 'plan:id,name,badge_color']);

        return response()->json($sub, 201);
    }

    public function updateSubscription(Request $request, UserSubscription $subscription)
    {
        $data = $request->validate([
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'billing_cycle'        => 'nullable|in:monthly,yearly',
            'amount_paid'          => 'nullable|numeric|min:0',
            'status'               => 'nullable|in:active,expired,cancelled,pending,trial',
            'starts_at'            => 'nullable|date',
            'expires_at'           => 'nullable|date',
            'transaction_ref'      => 'nullable|string|max:100',
            'notes'                => 'nullable|string',
        ]);

        $subscription->update(array_filter($data, fn($v) => $v !== null));
        $subscription->load(['user:id,name,email', 'plan:id,name,badge_color']);

        return response()->json($subscription);
    }

    public function destroySubscription(UserSubscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Subscription removed.']);
    }

    public function statsPlans()
    {
        return response()->json([
            'total_plans'        => SubscriptionPlan::count(),
            'active_plans'       => SubscriptionPlan::where('is_active', true)->count(),
            'total_subscribers'  => UserSubscription::count(),
            'active_subscribers' => UserSubscription::where('status', 'active')->count(),
            'monthly_revenue'    => UserSubscription::where('status', 'active')
                ->where('billing_cycle', 'monthly')
                ->join('subscription_plans', 'subscription_plans.id', '=', 'user_subscriptions.subscription_plan_id')
                ->sum('subscription_plans.price_monthly'),
        ]);
    }
}
