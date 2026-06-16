<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Auto-mark setup_completed for pre-existing users who already have subscription history
        if (!$user->setup_completed && $user->subscriptions()->exists()) {
            $user->update(['setup_completed' => true]);
        }

        // Redirect new users (registered fresh) to setup if not completed
        if (!$user->setup_completed) {
            return redirect('/setup');
        }

        // Only enforce subscription gate when there is at least one plan in the system
        $planCount = \App\Models\SubscriptionPlan::count();
        if ($planCount > 0 && !$user->hasActiveSubscription()) {
            return redirect('/subscription/plans');
        }

        return $next($request);
    }
}
