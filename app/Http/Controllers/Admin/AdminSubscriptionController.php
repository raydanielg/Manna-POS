<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    public function index()
    {
        return view('admin.subscriptions.index');
    }

    public function list(Request $req)
    {
        try {
            $q = UserSubscription::with(['user:id,name,email', 'plan:id,name,slug']);
            if ($req->status) $q->where('status', $req->status);
            if ($req->is_trial !== null) $q->where('is_trial', $req->is_trial);
            if ($req->search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$req->search}%"));
            }
            return response()->json($q->latest()->paginate(20)->through(fn($s) => [
                'id' => $s->id,
                'user_name' => $s->user?->name,
                'user_id' => $s->user_id,
                'plan_name' => $s->plan?->name,
                'plan_slug' => $s->plan?->slug,
                'billing_cycle' => $s->billing_cycle,
                'amount_paid' => $s->amount_paid,
                'currency' => $s->currency,
                'status' => $s->status,
                'starts_at' => $s->starts_at?->format('Y-m-d'),
                'expires_at' => $s->expires_at?->format('Y-m-d'),
                'created_at' => $s->created_at->format('Y-m-d H:i:s'),
            ]));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(UserSubscription $subscription)
    {
        try {
            return response()->json($subscription->load(['user', 'plan']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $req, UserSubscription $subscription)
    {
        try {
            $data = $req->validate([
                'status' => 'nullable|string|max:20',
                'expires_at' => 'nullable|date',
                'billing_cycle' => 'nullable|string|max:20',
                'amount_paid' => 'nullable|numeric|min:0',
            ]);
            $subscription->update($data);
            return response()->json(['success' => true, 'subscription' => $subscription]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(UserSubscription $subscription)
    {
        try {
            $subscription->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
