<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect after login based on user role.
     */
    protected function redirectTo()
    {
        $role = auth()->user()->role ?? 'user';

        return match ($role) {
            'admin', 'manager' => '/dashboard',
            'cashier'          => '/dashboard/sell/pos',
            default            => '/dashboard',
        };
    }

    /**
     * Flash success toast message after login.
     */
    protected function authenticated(Request $request, $user)
    {
        $request->session()->flash('toast_success', "Welcome back, {$user->name}! You are now signed in.");
    }
}
