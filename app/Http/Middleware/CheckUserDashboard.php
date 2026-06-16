<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserDashboard
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role === 'admin') {
            return redirect('/admin');
        }
        return $next($request);
    }
}