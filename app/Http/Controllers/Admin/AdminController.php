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
            'users'      => User::count(),
            'businesses' => Business::count(),
            'revenue'    => Invoice::where('status', 'paid')->sum('total'),
            'tickets'    => SupportTicket::count(),
            'plans'      => SubscriptionPlan::count(),
        ]);
    }

    public function recentActivity()
    {
        return response()->json(ActivityLog::with('user')->recent()->get());
    }
}
