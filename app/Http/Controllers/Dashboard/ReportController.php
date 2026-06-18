<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function resolveDates(Request $request)
    {
        $from = $request->filled('from_date') ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $to = $request->filled('to_date') ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();
        return compact('from','to');
    }

    public function salesReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $summary = [
            'total_sales' => Sale::whereBetween('sale_date',[$from,$to])->count(),
            'total_revenue' => Sale::whereBetween('sale_date',[$from,$to])->sum('total_amount'),
            'total_paid' => Sale::whereBetween('sale_date',[$from,$to])->sum('paid_amount'),
            'total_outstanding' => Sale::whereBetween('sale_date',[$from,$to])->sum('balance'),
        ];

        $dailySales = Sale::selectRaw('DATE(sale_date) as date, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('sale_date',[$from,$to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(total) as total_revenue')
            ->whereHas('sale', fn($q) => $q->whereBetween('sale_date',[$from,$to]))
            ->groupBy('product_name')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        $sales = Sale::with('customer')->whereBetween('sale_date',[$from,$to])->orderBy('sale_date','desc')->paginate(25);

        return view('dashboard.reports.sales-report', compact('summary','dailySales','topProducts','sales','from','to'));
    }

    public function purchaseReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $summary = [
            'total_purchases' => Purchase::whereBetween('purchase_date',[$from,$to])->count(),
            'total_amount' => Purchase::whereBetween('purchase_date',[$from,$to])->sum('total_amount'),
            'total_paid' => Purchase::whereBetween('purchase_date',[$from,$to])->sum('paid_amount'),
        ];

        $dailyPurchases = Purchase::selectRaw('DATE(purchase_date) as date, COUNT(*) as count, SUM(total_amount) as amount')
            ->whereBetween('purchase_date',[$from,$to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $purchases = Purchase::with('supplier')->whereBetween('purchase_date',[$from,$to])->orderBy('purchase_date','desc')->paginate(25);

        return view('dashboard.reports.purchase-report', compact('summary','dailyPurchases','purchases','from','to'));
    }

    public function inventoryReport(Request $request)
    {
        $lowStock = Product::whereColumn('current_stock','<=','reorder_level')->orWhere('current_stock',0)->count();
        $totalProducts = Product::count();
        $totalStockValue = Product::selectRaw('SUM(current_stock * purchase_price) as val')->value('val') ?? 0;
        $totalRetailValue = Product::selectRaw('SUM(current_stock * selling_price) as val')->value('val') ?? 0;

        $products = Product::with('category')->orderBy('current_stock','asc')->paginate(25);
        $categories = Product::selectRaw('product_categories.name as category, COUNT(products.id) as count, SUM(products.current_stock) as stock')
            ->join('product_categories','products.category_id','=','product_categories.id')
            ->groupBy('product_categories.name')
            ->get();

        return view('dashboard.reports.inventory-report', compact('lowStock','totalProducts','totalStockValue','totalRetailValue','products','categories'));
    }

    public function expenseReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $summary = [
            'total_expenses' => Expense::whereBetween('expense_date',[$from,$to])->count(),
            'total_amount' => Expense::whereBetween('expense_date',[$from,$to])->sum('amount'),
        ];

        $byCategory = Expense::selectRaw('expense_categories.name as category, COUNT(*) as count, SUM(expenses.amount) as total')
            ->join('expense_categories','expenses.category_id','=','expense_categories.id')
            ->whereBetween('expense_date',[$from,$to])
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $dailyExpenses = Expense::selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->whereBetween('expense_date',[$from,$to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenses = Expense::with('category')->whereBetween('expense_date',[$from,$to])->orderBy('expense_date','desc')->paginate(25);

        return view('dashboard.reports.expense-report', compact('summary','byCategory','dailyExpenses','expenses','from','to'));
    }

    public function profitLossReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $totalRevenue = Sale::whereBetween('sale_date',[$from,$to])->sum('total_amount');
        $totalCost = Purchase::whereBetween('purchase_date',[$from,$to])->sum('total_amount');
        $totalExpenses = Expense::whereBetween('expense_date',[$from,$to])->sum('amount');
        $grossProfit = $totalRevenue - $totalCost;
        $netProfit = $grossProfit - $totalExpenses;

        $monthly = [];
        $current = $from->copy()->startOfMonth();
        while ($current <= $to) {
            $mStart = $current->copy();
            $mEnd = $current->copy()->endOfMonth();
            $rev = Sale::whereBetween('sale_date',[$mStart,$mEnd])->sum('total_amount');
            $cost = Purchase::whereBetween('purchase_date',[$mStart,$mEnd])->sum('total_amount');
            $exp = Expense::whereBetween('expense_date',[$mStart,$mEnd])->sum('amount');
            $monthly[] = [
                'month' => $current->format('M Y'),
                'revenue' => $rev,
                'cost' => $cost,
                'expenses' => $exp,
                'profit' => $rev - $cost - $exp,
            ];
            $current->addMonth();
        }

        return view('dashboard.reports.profit-loss-report', compact('totalRevenue','totalCost','totalExpenses','grossProfit','netProfit','monthly','from','to'));
    }

    public function suppliersReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $suppliers = Supplier::withCount(['purchases as purchases_count'])
            ->withSum(['purchases as purchases_total'], 'total_amount')
            ->withSum(['purchases as paid_total'], 'paid_amount')
            ->get()
            ->map(function($s) {
                $s->balance = ($s->purchases_total ?? 0) - ($s->paid_total ?? 0);
                return $s;
            });

        return view('dashboard.reports.suppliers-report', compact('suppliers','from','to'));
    }

    public function supplierPriceComparison(Request $request)
    {
        $products = Product::with(['purchaseItems' => function($q) {
            $q->selectRaw('product_id, supplier_id, AVG(unit_price) as avg_price, MAX(unit_price) as max_price, MIN(unit_price) as min_price, COUNT(*) as purchases_count')
                ->groupBy('product_id','supplier_id');
        }, 'purchaseItems.supplier'])->take(50)->get();

        return view('dashboard.reports.supplier-price-comparison', compact('products'));
    }

    public function expiryReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $expired = Product::whereNotNull('expiry_date')->where('expiry_date','<',now())->count();
        $expiringSoon = Product::whereNotNull('expiry_date')->whereBetween('expiry_date',[now(),now()->addDays(30)])->count();

        $products = Product::whereNotNull('expiry_date')
            ->where('expiry_date','<=',$to)
            ->orderBy('expiry_date')
            ->paginate(25);

        return view('dashboard.reports.expiry-report', compact('expired','expiringSoon','products','from','to'));
    }

    public function productTrendsReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $trends = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(total) as total_revenue, COUNT(DISTINCT sale_id) as sales_count')
            ->whereHas('sale', fn($q) => $q->whereBetween('sale_date',[$from,$to]))
            ->groupBy('product_name')
            ->orderByDesc('total_revenue')
            ->take(20)
            ->get();

        return view('dashboard.reports.product-trends-report', compact('trends','from','to'));
    }
}
