<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller {
    public function stats() {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();

        // Current month stats
        $totalSales = (float) Sale::whereBetween('sale_date',[$monthStart,$today])->where('status','completed')->sum('total');
        $totalOrders = Sale::whereBetween('sale_date',[$monthStart,$today])->count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();

        // Today stats
        $todaySales = (float) Sale::where('sale_date',$today)->where('status','completed')->sum('total');
        $todayOrders = Sale::where('sale_date',$today)->count();
        $todayPurchases = (float) Purchase::where('purchase_date',$today)->sum('total');

        // Week stats
        $weekSales = (float) Sale::whereBetween('sale_date',[$weekStart,$today])->where('status','completed')->sum('total');

        // Last month stats for growth
        $lastMonthSales = (float) Sale::whereBetween('sale_date',[$lastMonthStart,$lastMonthEnd])->where('status','completed')->sum('total');
        $lastMonthOrders = Sale::whereBetween('sale_date',[$lastMonthStart,$lastMonthEnd])->count();

        // Growth percentages
        $salesGrowth = $lastMonthSales > 0 ? round((($totalSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0;
        $ordersGrowth = $lastMonthOrders > 0 ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) : 0;

        // Low stock & out of stock
        $lowStock = Product::where('stock_quantity','>',0)->whereColumn('stock_quantity','<=','reorder_level')->count();
        $outOfStock = Product::where('stock_quantity','<=',0)->count();

        // Top products
        $topProducts = DB::table('sale_items')
            ->join('products','sale_items.product_id','=','products.id')
            ->select('products.name',DB::raw('SUM(sale_items.quantity) as total_qty'),DB::raw('SUM(sale_items.total) as total_revenue'))
            ->groupBy('products.id','products.name')
            ->orderByDesc('total_revenue')
            ->take(5)->get();

        // Payment method breakdown
        $paymentMethods = Sale::where('status','completed')
            ->select('payment_method',DB::raw('COUNT(*) as count'),DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')->get();

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'total_customers' => $totalCustomers,
            'sales_growth' => $salesGrowth,
            'orders_growth' => $ordersGrowth,
            'today_sales' => $todaySales,
            'today_orders' => $todayOrders,
            'today_purchases' => $todayPurchases,
            'week_sales' => $weekSales,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'top_products' => $topProducts,
            'payment_methods' => $paymentMethods,
        ]);
    }
}