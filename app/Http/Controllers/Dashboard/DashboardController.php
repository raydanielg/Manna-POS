<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $bizId = $this->currentBusinessId();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // ── KPIs ────────────────────────────────────────
        $salesToday = Sale::forCurrentUser($bizId)->whereDate('sale_date', $today)->sum('total') ?? 0;
        $ordersToday = Sale::forCurrentUser($bizId)->whereDate('sale_date', $today)->count();
        $totalCustomers = Customer::forCurrentUser($bizId)->count();
        $newCustomers = Customer::forCurrentUser($bizId)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $totalProducts = Product::forCurrentUser($bizId)->count();
        $lowStock = Product::forCurrentUser($bizId)->where(function ($q) {
            $q->whereColumn('stock_quantity', '<=', 'reorder_level')->orWhere('stock_quantity', 0);
        })->count();
        $monthlyRevenue = Sale::forCurrentUser($bizId)->whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('total') ?? 0;
        $paymentsMtd = Sale::forCurrentUser($bizId)->whereBetween('sale_date', [$startOfMonth, $endOfMonth])->sum('paid') ?? 0;
        $activeUsers = User::where(function ($sq) use ($bizId) {
            $sq->where('id', $bizId)->orWhere('owner_id', $bizId);
        })->count();

        $avgTransaction = Sale::forCurrentUser($bizId)->whereBetween('sale_date', [$startOfMonth, $endOfMonth])->avg('total') ?? 0;

        // ── Trend (last 14 days) ─────────────────────────
        $trend = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trend[] = [
                'date' => $date->format('M d'),
                'sales' => (float) Sale::forCurrentUser($bizId)->whereDate('sale_date', $date)->sum('total'),
                'orders' => (int) Sale::forCurrentUser($bizId)->whereDate('sale_date', $date)->count(),
                'customers' => (int) Customer::forCurrentUser($bizId)->whereDate('created_at', $date)->count(),
            ];
        }

        // ── Payment distribution (this month) ────────────
        $paymentDistribution = Sale::forCurrentUser($bizId)
            ->whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => ['label' => ucfirst(str_replace('_', ' ', $row->payment_method)), 'value' => (float) $row->total]);

        // ── Recent lists ─────────────────────────────────
        $recentSales = Sale::forCurrentUser($bizId)->with('customer:id,name')->latest('sale_date')->take(5)->get(['id', 'reference', 'customer_id', 'total', 'sale_date']);
        $recentCustomers = Customer::forCurrentUser($bizId)->latest()->take(5)->get(['id', 'name', 'email', 'phone', 'created_at']);
        $lowStockProducts = Product::forCurrentUser($bizId)->where(function ($q) {
            $q->whereColumn('stock_quantity', '<=', 'reorder_level')->orWhere('stock_quantity', 0);
        })->take(5)->get(['id', 'name', 'sku', 'stock_quantity', 'reorder_level']);

        return response()->json([
            'kpis' => [
                'sales_today' => $salesToday,
                'orders_today' => $ordersToday,
                'total_customers' => $totalCustomers,
                'new_customers' => $newCustomers,
                'total_products' => $totalProducts,
                'low_stock' => $lowStock,
                'monthly_revenue' => $monthlyRevenue,
                'payments_mtd' => $paymentsMtd,
                'active_users' => $activeUsers,
                'avg_transaction' => $avgTransaction,
            ],
            'trend' => $trend,
            'payment_distribution' => $paymentDistribution,
            'recent_sales' => $recentSales,
            'recent_customers' => $recentCustomers,
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    public function crmStats()
    {
        $bizId = $this->currentBusinessId();
        $today = Carbon::today();

        $totalActivities = \App\Models\CrmActivity::forCurrentUser($bizId)->count();
        $pendingFollowups = \App\Models\CrmActivity::forCurrentUser($bizId)->where('status', 'pending')->whereDate('follow_up_date', '>=', $today)->count();
        $overdueTasks = \App\Models\CrmActivity::forCurrentUser($bizId)->where('status', 'pending')->whereDate('follow_up_date', '<', $today)->count();
        $recentInteractions = \App\Models\CrmActivity::forCurrentUser($bizId)->whereDate('created_at', '>=', $today->copy()->subDays(7))->count();

        $upcomingFollowups = \App\Models\CrmActivity::forCurrentUser($bizId)
            ->with('customer:id,name,phone')
            ->where('status', 'pending')
            ->whereNotNull('follow_up_date')
            ->orderBy('follow_up_date')
            ->take(10)
            ->get();

        $activitiesByType = \App\Models\CrmActivity::forCurrentUser($bizId)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'total_activities' => $totalActivities,
            'pending_followups' => $pendingFollowups,
            'overdue_tasks' => $overdueTasks,
            'recent_interactions' => $recentInteractions,
            'upcoming_followups' => $upcomingFollowups,
            'activities_by_type' => $activitiesByType,
        ]);
    }
}
