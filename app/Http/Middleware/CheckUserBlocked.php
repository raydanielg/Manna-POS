<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Check if user is blocked
        if ($user && $user->status === 'blocked') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account has been blocked. Please contact support.',
                    'reason' => $user->block_reason ?? 'No reason provided'
                ], 403);
            }
            return redirect('/blocked');
        }

        return $next($request);
    }
}
