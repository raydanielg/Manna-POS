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
    use UserIdTrait;
    public function stats() {
        $uid = $this->userId();
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();

        $totalSales = (float) Sale::where('created_by',$uid)->whereBetween('sale_date',[$monthStart,$today])->where('status','completed')->sum('total');
        $totalOrders = Sale::where('created_by',$uid)->whereBetween('sale_date',[$monthStart,$today])->count();
        $totalProducts = Product::where('created_by',$uid)->count();
        $totalCustomers = Customer::where('created_by',$uid)->count();

        $todaySales = (float) Sale::where('created_by',$uid)->where('sale_date',$today)->where('status','completed')->sum('total');
        $todayOrders = Sale::where('created_by',$uid)->where('sale_date',$today)->count();
        $todayPurchases = (float) Purchase::where('created_by',$uid)->where('purchase_date',$today)->sum('total');
        $weekSales = (float) Sale::where('created_by',$uid)->whereBetween('sale_date',[$weekStart,$today])->where('status','completed')->sum('total');

        $lastMonthSales = (float) Sale::where('created_by',$uid)->whereBetween('sale_date',[$lastMonthStart,$lastMonthEnd])->where('status','completed')->sum('total');
        $lastMonthOrders = Sale::where('created_by',$uid)->whereBetween('sale_date',[$lastMonthStart,$lastMonthEnd])->count();

        $salesGrowth = $lastMonthSales > 0 ? round((($totalSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0;
        $ordersGrowth = $lastMonthOrders > 0 ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) : 0;

        $lowStock = Product::where('created_by',$uid)->where('stock_quantity','>',0)->whereColumn('stock_quantity','<=','reorder_level')->count();
        $outOfStock = Product::where('created_by',$uid)->where('stock_quantity','<=',0)->count();

        $topProducts = DB::table('sale_items')
            ->join('sales','sale_items.sale_id','=','sales.id')
            ->join('products','sale_items.product_id','=','products.id')
            ->where('sales.created_by',$uid)
            ->select('products.name',DB::raw('SUM(sale_items.quantity) as total_qty'),DB::raw('SUM(sale_items.total) as total_revenue'))
            ->groupBy('products.id','products.name')
            ->orderByDesc('total_revenue')
            ->take(5)->get();

        $paymentMethods = Sale::where('created_by',$uid)->where('status','completed')
            ->select('payment_method',DB::raw('COUNT(*) as count'),DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')->get();

        $monthlySales = Sale::where('created_by',$uid)->where('status','completed')
            ->select(DB::raw('MONTH(sale_date) as month'),DB::raw('YEAR(sale_date) as year'),DB::raw('SUM(total) as total'))
            ->groupBy('year','month')->orderBy('year')->orderBy('month')->get();

        return response()->json([
            'total_sales' => $totalSales, 'total_orders' => $totalOrders, 'total_products' => $totalProducts, 'total_customers' => $totalCustomers,
            'sales_growth' => $salesGrowth, 'orders_growth' => $ordersGrowth,
            'today_sales' => $todaySales, 'today_orders' => $todayOrders, 'today_purchases' => $todayPurchases,
            'week_sales' => $weekSales, 'low_stock' => $lowStock, 'out_of_stock' => $outOfStock,
            'top_products' => $topProducts, 'payment_methods' => $paymentMethods, 'monthly_sales' => $monthlySales,
        ]);
    }
}
