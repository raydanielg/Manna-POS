<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user->setup_completed) {
            return redirect('/setup');
        }

        if (!$user->hasActiveSubscription()) {
            return redirect('/subscription/plans');
        }

        return $next($request);
    }
}
