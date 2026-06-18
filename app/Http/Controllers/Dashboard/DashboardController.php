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
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalCustomers = Customer::forCurrentUser($bizId)->count();
        $newCustomers = Customer::forCurrentUser($bizId)->whereBetween('created_at', [$startOfMonth, Carbon::now()->endOfMonth()])->count();
        $activeCustomers = Customer::forCurrentUser($bizId)->where('status', 'active')->count();
        $totalActivities = \App\Models\CrmActivity::forCurrentUser($bizId)->count();
        $pendingFollowUps = \App\Models\CrmActivity::forCurrentUser($bizId)->where('status', 'pending')->whereDate('follow_up_date', '>=', $today)->count();
        $overdueFollowUps = \App\Models\CrmActivity::forCurrentUser($bizId)->where('status', 'pending')->whereDate('follow_up_date', '<', $today)->count();

        $recentActivities = \App\Models\CrmActivity::forCurrentUser($bizId)
            ->with('customer:id,name')
            ->latest()
            ->take(10)
            ->get();

        $activityByType = \App\Models\CrmActivity::forCurrentUser($bizId)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->map(fn($row) => ['type' => $row->type, 'count' => (int) $row->count]);

        return response()->json([
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'active_customers' => $activeCustomers,
            'total_activities' => $totalActivities,
            'pending_follow_ups' => $pendingFollowUps,
            'overdue_follow_ups' => $overdueFollowUps,
            'recent_activities' => $recentActivities,
            'activity_by_type' => $activityByType,
        ]);
    }
}
