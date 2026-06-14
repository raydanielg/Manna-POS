<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     * Admin bypass for is_active check.
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        
        // Admin can always login regardless of is_active status
        $user = \App\Models\User::where($this->username(), $credentials[$this->username()])->first();
        if ($user && $user->role === 'admin') {
            return $credentials;
        }
        
        // For non-admin users, check is_active
        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                $this->username() => ['Your account is inactive. Please contact the administrator.'],
            ]);
        }
        
        return $credentials;
    }

    /**
     * Redirect after login based on user role.
     */
    protected function redirectTo()
    {
        $role = auth()->user()->role ?? 'user';

        return match ($role) {
            'admin'            => '/dashboard',
            'manager'          => '/dashboard',
            'cashier'          => '/dashboard/sell/pos',
            'staff'            => '/dashboard/sell/pos',
            default            => '/dashboard',
        };
    }

    /**
     * Flash success toast message after login.
     */
    protected function authenticated(Request $request, $user)
    {
        $roleLabel = match($user->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'cashier' => 'Cashier',
            'staff' => 'Staff',
            default => 'User',
        };
        
        $request->session()->flash('toast_success', "Welcome back, {$user->name}! Logged in as {$roleLabel}.");
    }

    /**
     * The user has logged out of the application.
     */
    protected function loggedOut(Request $request)
    {
        $request->session()->flash('toast_success', 'You have been logged out successfully.');
    }
}
