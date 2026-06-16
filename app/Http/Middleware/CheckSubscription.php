<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Grace: if user has any subscription history, they are "pre-existing" — mark setup complete
        if (!$user->setup_completed && $user->subscriptions()->exists()) {
            $user->update(['setup_completed' => true]);
        }

        if (!$user->setup_completed) {
            return redirect('/setup');
        }

        // Check for active subscription (allow through if they have no subscription system yet)
        if (!$user->hasActiveSubscription()) {
            return redirect('/subscription/plans');
        }

        return $next($request);
    }
}
