<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardApiController extends Controller {
    public function stats() {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();

        // Current month stats
        $totalSales = (float) Sale::whereBetween('sale_date',[$monthStart,$today])->where('status','completed')->sum('total');
        $totalOrders = Sale::whereBetween('sale_date',[$monthStart,$today])->count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        // Last month stats for growth calculation
        $lastMonthSales = (float) Sale::whereBetween('sale_date',[$lastMonthStart,$lastMonthEnd])->where('status','completed')->sum('total');
        $lastMonthOrders = Sale::whereBetween('sale_date',[$lastMonthStart,$lastMonthEnd])->count();

        // Calculate growth percentages
        $salesGrowth = $lastMonthSales > 0 ? round((($totalSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0;
        $ordersGrowth = $lastMonthOrders > 0 ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) : 0;

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'total_customers' => $totalCustomers,
            'sales_growth' => $salesGrowth,
            'orders_growth' => $ordersGrowth,
        ]);
    }
}