<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Allow access to verify-otp, logout, and activation routes
        $allowed = ['verify-otp', 'verify.otp.post', 'verify.otp.resend', 'activate', 'logout', 'login'];
        $current = $request->route()->getName();

        if (in_array($current, $allowed) || $request->is('verify-otp', 'activate/*', 'logout', 'login')) {
            return $next($request);
        }

        if (!$user->email_verified_at) {
            return redirect('/verify-otp');
        }

        return $next($request);
    }
}
