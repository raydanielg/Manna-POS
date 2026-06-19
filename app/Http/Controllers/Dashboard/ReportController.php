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
use Barryvdh\DomPDF\Facade\Pdf;

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
            'total_sales' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->count(),
            'total_revenue' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->sum('total'),
            'total_paid' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->sum('paid'),
            'total_outstanding' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->selectRaw('COALESCE(SUM(total - paid),0) as balance')->value('balance'),
        ];

        $dailySales = Sale::forCurrentUser($this->currentBusinessId())->selectRaw('DATE(sale_date) as date, COUNT(*) as count, SUM(total) as revenue')
            ->whereBetween('sale_date',[$from,$to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(total) as total_revenue')
            ->whereHas('sale', fn($q) => $q->forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to]))
            ->groupBy('product_name')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        $sales = Sale::forCurrentUser($this->currentBusinessId())->with('customer')->whereBetween('sale_date',[$from,$to])->orderBy('sale_date','desc')->paginate(25);

        return view('dashboard.reports.sales-report', compact('summary','dailySales','topProducts','sales','from','to'));
    }

    public function purchaseReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $summary = [
            'total_purchases' => Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->count(),
            'total_amount' => Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->sum('total'),
            'total_paid' => Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->selectRaw('COALESCE(SUM(total),0) as total_paid')->value('total_paid'),
        ];

        $dailyPurchases = Purchase::forCurrentUser($this->currentBusinessId())->selectRaw('DATE(purchase_date) as date, COUNT(*) as count, SUM(total) as amount')
            ->whereBetween('purchase_date',[$from,$to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $purchases = Purchase::forCurrentUser($this->currentBusinessId())->with('supplier')->whereBetween('purchase_date',[$from,$to])->orderBy('purchase_date','desc')->paginate(25);

        return view('dashboard.reports.purchase-report', compact('summary','dailyPurchases','purchases','from','to'));
    }

    public function inventoryReport(Request $request)
    {
        $lowStock = Product::forCurrentUser($this->currentBusinessId())->where(function($q){ $q->whereColumn('stock_quantity','<=','reorder_level')->orWhere('stock_quantity',0); })->count();
        $totalProducts = Product::forCurrentUser($this->currentBusinessId())->count();
        $totalStockValue = Product::forCurrentUser($this->currentBusinessId())->selectRaw('SUM(stock_quantity * purchase_price) as val')->value('val') ?? 0;
        $totalRetailValue = Product::forCurrentUser($this->currentBusinessId())->selectRaw('SUM(stock_quantity * selling_price) as val')->value('val') ?? 0;

        $products = Product::forCurrentUser($this->currentBusinessId())->with('category')->orderBy('stock_quantity','asc')->paginate(25);
        $categories = Product::forCurrentUser($this->currentBusinessId())->selectRaw('product_categories.name as category, COUNT(products.id) as count, SUM(products.stock_quantity) as stock')
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
            'total_expenses' => Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$from,$to])->count(),
            'total_amount' => Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$from,$to])->sum('amount'),
        ];

        $byCategory = Expense::forCurrentUser($this->currentBusinessId())->selectRaw('expense_categories.name as category, COUNT(*) as count, SUM(expenses.amount) as total')
            ->join('expense_categories','expenses.expense_category_id','=','expense_categories.id')
            ->whereBetween('expense_date',[$from,$to])
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $dailyExpenses = Expense::forCurrentUser($this->currentBusinessId())->selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->whereBetween('expense_date',[$from,$to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenses = Expense::forCurrentUser($this->currentBusinessId())->with('category')->whereBetween('expense_date',[$from,$to])->orderBy('expense_date','desc')->paginate(25);

        return view('dashboard.reports.expense-report', compact('summary','byCategory','dailyExpenses','expenses','from','to'));
    }

    public function profitLossReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $totalRevenue = Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->sum('total');
        $totalCost = Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->sum('total');
        $totalExpenses = Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$from,$to])->sum('amount');
        $grossProfit = $totalRevenue - $totalCost;
        $netProfit = $grossProfit - $totalExpenses;

        $monthly = [];
        $current = $from->copy()->startOfMonth();
        while ($current <= $to) {
            $mStart = $current->copy();
            $mEnd = $current->copy()->endOfMonth();
            $rev = Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$mStart,$mEnd])->sum('total');
            $cost = Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$mStart,$mEnd])->sum('total');
            $exp = Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$mStart,$mEnd])->sum('amount');
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

        $suppliers = Supplier::forCurrentUser($this->currentBusinessId())->withCount(['purchases as purchases_count'])
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
        $products = Product::forCurrentUser($this->currentBusinessId())->with(['purchaseItems' => function($q) {
            $q->selectRaw('product_id, supplier_id, AVG(unit_price) as avg_price, MAX(unit_price) as max_price, MIN(unit_price) as min_price, COUNT(*) as purchases_count')
                ->groupBy('product_id','supplier_id');
        }, 'purchaseItems.supplier'])->take(50)->get();

        return view('dashboard.reports.supplier-price-comparison', compact('products'));
    }

    public function expiryReport(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];

        $expired = Product::forCurrentUser($this->currentBusinessId())->whereNotNull('expiry_date')->where('expiry_date','<',now())->count();
        $expiringSoon = Product::forCurrentUser($this->currentBusinessId())->whereNotNull('expiry_date')->whereBetween('expiry_date',[now(),now()->addDays(30)])->count();

        $products = Product::forCurrentUser($this->currentBusinessId())->whereNotNull('expiry_date')
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
            ->whereHas('sale', fn($q) => $q->forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to]))
            ->groupBy('product_name')
            ->orderByDesc('total_revenue')
            ->take(20)
            ->get();

        return view('dashboard.reports.product-trends-report', compact('trends','from','to'));
    }

    // --- PDF Download Methods ---

    public function salesReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $summary = [
            'total_sales' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->count(),
            'total_revenue' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->sum('total'),
            'total_paid' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->sum('paid'),
            'total_outstanding' => Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->selectRaw('COALESCE(SUM(total - paid),0) as balance')->value('balance'),
        ];
        $topProducts = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(total) as total_revenue')
            ->whereHas('sale', fn($q) => $q->forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to]))
            ->groupBy('product_name')->orderByDesc('total_revenue')->take(10)->get();
        $sales = Sale::forCurrentUser($this->currentBusinessId())->with('customer')
            ->whereBetween('sale_date',[$from,$to])->orderBy('sale_date','desc')->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.sales-report-pdf', compact('summary','topProducts','sales','from','to'));
        return $pdf->download('sales-report-'.$from->format('Y-m-d').'.pdf');
    }

    public function purchaseReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $summary = [
            'total_purchases' => Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->count(),
            'total_amount' => Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->sum('total_amount'),
            'total_paid' => Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->sum('paid_amount'),
        ];
        $purchases = Purchase::forCurrentUser($this->currentBusinessId())->with('supplier')
            ->whereBetween('purchase_date',[$from,$to])->orderBy('purchase_date','desc')->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.purchase-report-pdf', compact('summary','purchases','from','to'));
        return $pdf->download('purchase-report-'.$from->format('Y-m-d').'.pdf');
    }

    public function inventoryReportPdf(Request $request)
    {
        $lowStock = Product::forCurrentUser($this->currentBusinessId())->where(function($q){ $q->whereColumn('current_stock','<=','reorder_level')->orWhere('current_stock',0); })->count();
        $totalProducts = Product::forCurrentUser($this->currentBusinessId())->count();
        $totalStockValue = Product::forCurrentUser($this->currentBusinessId())->selectRaw('SUM(current_stock * purchase_price) as val')->value('val') ?? 0;
        $totalRetailValue = Product::forCurrentUser($this->currentBusinessId())->selectRaw('SUM(current_stock * selling_price) as val')->value('val') ?? 0;
        $products = Product::forCurrentUser($this->currentBusinessId())->with('category')->orderBy('current_stock','asc')->get();
        $categories = Product::forCurrentUser($this->currentBusinessId())->selectRaw('product_categories.name as category, COUNT(products.id) as count, SUM(products.current_stock) as stock')
            ->join('product_categories','products.category_id','=','product_categories.id')->groupBy('product_categories.name')->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.inventory-report-pdf', compact('lowStock','totalProducts','totalStockValue','totalRetailValue','products','categories'));
        return $pdf->download('inventory-report.pdf');
    }

    public function expenseReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $summary = [
            'total_expenses' => Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$from,$to])->count(),
            'total_amount' => Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$from,$to])->sum('amount'),
        ];
        $byCategory = Expense::forCurrentUser($this->currentBusinessId())->selectRaw('expense_categories.name as category, COUNT(*) as count, SUM(expenses.amount) as total')
            ->join('expense_categories','expenses.category_id','=','expense_categories.id')
            ->whereBetween('expense_date',[$from,$to])->groupBy('expense_categories.name')->orderByDesc('total')->get();
        $expenses = Expense::forCurrentUser($this->currentBusinessId())->with('category')
            ->whereBetween('expense_date',[$from,$to])->orderBy('expense_date','desc')->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.expense-report-pdf', compact('summary','byCategory','expenses','from','to'));
        return $pdf->download('expense-report-'.$from->format('Y-m-d').'.pdf');
    }

    public function profitLossReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $totalRevenue = Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to])->sum('total_amount');
        $totalCost = Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$from,$to])->sum('total_amount');
        $totalExpenses = Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$from,$to])->sum('amount');
        $grossProfit = $totalRevenue - $totalCost;
        $netProfit = $grossProfit - $totalExpenses;
        $monthly = [];
        $current = $from->copy()->startOfMonth();
        while ($current <= $to) {
            $mStart = $current->copy(); $mEnd = $current->copy()->endOfMonth();
            $rev = Sale::forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$mStart,$mEnd])->sum('total_amount');
            $cost = Purchase::forCurrentUser($this->currentBusinessId())->whereBetween('purchase_date',[$mStart,$mEnd])->sum('total_amount');
            $exp = Expense::forCurrentUser($this->currentBusinessId())->whereBetween('expense_date',[$mStart,$mEnd])->sum('amount');
            $monthly[] = ['month' => $current->format('M Y'), 'revenue' => $rev, 'cost' => $cost, 'expenses' => $exp, 'profit' => $rev - $cost - $exp];
            $current->addMonth();
        }
        $pdf = Pdf::loadView('dashboard.reports.pdf.profit-loss-report-pdf', compact('totalRevenue','totalCost','totalExpenses','grossProfit','netProfit','monthly','from','to'));
        return $pdf->download('profit-loss-report-'.$from->format('Y-m-d').'.pdf');
    }

    public function suppliersReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $suppliers = Supplier::forCurrentUser($this->currentBusinessId())->withCount(['purchases as purchases_count'])
            ->withSum(['purchases as purchases_total'], 'total_amount')
            ->withSum(['purchases as paid_total'], 'paid_amount')
            ->get()->map(function($s) { $s->balance = ($s->purchases_total ?? 0) - ($s->paid_total ?? 0); return $s; });
        $pdf = Pdf::loadView('dashboard.reports.pdf.suppliers-report-pdf', compact('suppliers','from','to'));
        return $pdf->download('suppliers-report-'.$from->format('Y-m-d').'.pdf');
    }

    public function supplierPriceComparisonPdf(Request $request)
    {
        $products = Product::forCurrentUser($this->currentBusinessId())->with(['purchaseItems' => function($q) {
            $q->selectRaw('product_id, supplier_id, AVG(unit_price) as avg_price, MAX(unit_price) as max_price, MIN(unit_price) as min_price, COUNT(*) as purchases_count')
                ->groupBy('product_id','supplier_id');
        }, 'purchaseItems.supplier'])->take(50)->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.supplier-price-comparison-pdf', compact('products'));
        return $pdf->download('supplier-price-comparison.pdf');
    }

    public function expiryReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $expired = Product::forCurrentUser($this->currentBusinessId())->whereNotNull('expiry_date')->where('expiry_date','<',now())->count();
        $expiringSoon = Product::forCurrentUser($this->currentBusinessId())->whereNotNull('expiry_date')->whereBetween('expiry_date',[now(),now()->addDays(30)])->count();
        $products = Product::forCurrentUser($this->currentBusinessId())->whereNotNull('expiry_date')
            ->where('expiry_date','<=',$to)->orderBy('expiry_date')->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.expiry-report-pdf', compact('expired','expiringSoon','products','from','to'));
        return $pdf->download('expiry-report-'.$from->format('Y-m-d').'.pdf');
    }

    public function productTrendsReportPdf(Request $request)
    {
        $dates = $this->resolveDates($request);
        $from = $dates['from']; $to = $dates['to'];
        $trends = SaleItem::selectRaw('product_name, SUM(quantity) as total_qty, SUM(total) as total_revenue, COUNT(DISTINCT sale_id) as sales_count')
            ->whereHas('sale', fn($q) => $q->forCurrentUser($this->currentBusinessId())->whereBetween('sale_date',[$from,$to]))
            ->groupBy('product_name')->orderByDesc('total_revenue')->take(20)->get();
        $pdf = Pdf::loadView('dashboard.reports.pdf.product-trends-report-pdf', compact('trends','from','to'));
        return $pdf->download('product-trends-report-'.$from->format('Y-m-d').'.pdf');
    }
}
