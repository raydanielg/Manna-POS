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
        $todaySales = (float) Sale::whereDate('sale_date',$today)->where('status','completed')->sum('total');
        $todayOrders = Sale::whereDate('sale_date',$today)->count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $monthRevenue = (float) Sale::whereBetween('sale_date',[$monthStart,$today])->where('status','completed')->sum('total');
        $monthExpenses = (float) Expense::whereBetween('expense_date',[$monthStart,$today])->sum('amount');
        $lowStock = Product::whereColumn('stock_quantity','<=','reorder_level')->where('stock_quantity','>',0)->count();
        $outOfStock = Product::where('stock_quantity','<=',0)->count();
        $recentSales = Sale::with('customer:id,name')->select('id','reference','customer_id','total','payment_status','status','sale_date')->latest()->take(8)->get();
        $chart = [];
        for ($i=6;$i>=0;$i--) {
            $d = now()->subDays($i)->toDateString();
            $chart[] = ['date'=>$d,'label'=>now()->subDays($i)->format('D'),'total'=>(float)Sale::whereDate('sale_date',$d)->where('status','completed')->sum('total')];
        }
        return response()->json(['today_sales'=>$todaySales,'today_orders'=>$todayOrders,'total_products'=>$totalProducts,'total_customers'=>$totalCustomers,'month_revenue'=>$monthRevenue,'month_expenses'=>$monthExpenses,'low_stock'=>$lowStock,'out_of_stock'=>$outOfStock,'recent_sales'=>$recentSales,'sales_chart'=>$chart]);
    }
}