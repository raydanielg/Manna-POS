<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Invoice;
use App\Models\SupportTicket;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct() { $this->middleware('auth')->except(['login']); }

    public function dashboard()
    {
        $stats = [
            'total_users'        => User::count(),
            'total_businesses'   => Business::count(),
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'total_revenue'      => Invoice::where('status', 'paid')->sum('total'),
            'pending_tickets'    => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'new_users_month'    => User::whereMonth('created_at', now()->month)->count(),
            'total_staff'        => \App\Models\Staff::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function stats()
    {
        return response()->json([
            'total_users'           => User::count(),
            'total_businesses'      => Business::count(),
            'total_revenue'         => number_format(Invoice::where('status', 'paid')->sum('total'), 0),
            'active_subscriptions'  => UserSubscription::where('status', 'active')->count(),
            'pending_tickets'       => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'new_users_month'       => User::whereMonth('created_at', now()->month)->count(),
            'new_biz_month'         => Business::whereMonth('created_at', now()->month)->count(),
            'pending_verifications' => \App\Models\BusinessVerification::where('status', 'pending')->count(),
            'total_staff'           => \App\Models\Staff::count(),
        ]);
    }

    public function recentActivity()
    {
        return response()->json(ActivityLog::with('user')->recent()->get());
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:50',
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if ($request->filled('password')) {
            if (!\Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password is incorrect', 'errors' => ['current_password' => ['Current password is incorrect']]], 422);
            }
            $user->password = bcrypt($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone ?? '';
        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
}
