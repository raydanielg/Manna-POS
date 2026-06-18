<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller {
    use UserIdTrait;

    // ── 1. Sales Report (enhanced) ──────────────────────────
    public function sales(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->startOfMonth()->toDateString();
        $to = $req->to ?? now()->toDateString();
        $sales = Sale::with('customer:id,name')->where('created_by',$uid)->whereBetween('sale_date',[$from,$to])->get();
        $items = SaleItem::whereHas('sale', fn($q) => $q->where('created_by', $uid)->whereBetween('sale_date',[$from,$to]))->get();
        return response()->json([
            'from'=>$from,'to'=>$to,
            'count'=>$sales->count(),
            'total'=>(float)$sales->sum('total'),
            'paid'=>(float)$sales->sum('paid'),
            'outstanding'=>(float)($sales->sum('total')-$sales->sum('paid')),
            'total_items_sold'=>(float)$items->sum('quantity'),
            'items'=>$sales
        ]);
    }

    // ── 2. Purchase Report ──────────────────────────────────
    public function purchases(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->startOfMonth()->toDateString();
        $to = $req->to ?? now()->toDateString();
        $purchases = Purchase::with('supplier:id,name')->where('created_by',$uid)->whereBetween('purchase_date',[$from,$to])->get();
        $items = PurchaseItem::whereHas('purchase', fn($q) => $q->where('created_by', $uid)->whereBetween('purchase_date',[$from,$to]))->get();
        return response()->json([
            'from'=>$from,'to'=>$to,
            'count'=>$purchases->count(),
            'total'=>(float)$purchases->sum('total'),
            'paid'=>(float)$purchases->where('payment_status','paid')->sum('total'),
            'unpaid'=>(float)$purchases->whereIn('payment_status',['partial','unpaid'])->sum('total'),
            'total_items_purchased'=>(float)$items->sum('quantity'),
            'items'=>$purchases
        ]);
    }

    // ── 3. Profit & Loss ────────────────────────────────────
    public function profitLoss(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->startOfMonth()->toDateString();
        $to = $req->to ?? now()->toDateString();
        $revenue = (float) Sale::where('created_by',$uid)->whereBetween('sale_date',[$from,$to])->where('status','completed')->sum('total');
        $cost = (float) Purchase::where('created_by',$uid)->whereBetween('purchase_date',[$from,$to])->where('status','received')->sum('total');
        $expenses = (float) Expense::where('created_by',$uid)->whereBetween('expense_date',[$from,$to])->sum('amount');
        return response()->json(['from'=>$from,'to'=>$to,'revenue'=>$revenue,'cost'=>$cost,'expenses'=>$expenses,'gross_profit'=>$revenue-$cost,'net_profit'=>$revenue-$cost-$expenses]);
    }

    // ── 4. Inventory ────────────────────────────────────────
    public function inventory() {
        $uid = $this->userId();
        $products = Product::with('category:id,name')->where('created_by',$uid)->orderBy('name')->get();
        $stockValue = $products->sum(fn($p)=>$p->stock_quantity * $p->cost_price);
        $lowStock = $products->filter(fn($p)=>$p->stock_quantity <= $p->reorder_level && $p->stock_quantity > 0);
        $outOfStock = $products->filter(fn($p)=>$p->stock_quantity <= 0);
        return response()->json(['total_products'=>$products->count(),'stock_value'=>(float)$stockValue,'low_stock_count'=>$lowStock->count(),'out_of_stock_count'=>$outOfStock->count(),'products'=>$products]);
    }

    // ── 5. Supplier Reports ─────────────────────────────────
    public function suppliers(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->startOfMonth()->subMonths(3)->toDateString();
        $to = $req->to ?? now()->toDateString();

        $supplierData = Supplier::where('created_by', $uid)
            ->withCount(['purchases' => fn($q) => $q->whereBetween('purchase_date',[$from,$to])])
            ->withSum(['purchases' => fn($q) => $q->whereBetween('purchase_date',[$from,$to])], 'total')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'company' => $s->company,
                'total_purchases' => $s->purchases_count ?? 0,
                'total_amount' => (float)($s->purchases_sum_total ?? 0),
                'balance' => (float)$s->balance,
                'credit_limit' => (float)$s->credit_limit,
                'status' => $s->status,
            ]);

        $topSuppliers = $supplierData->sortByDesc('total_amount')->values()->take(5);

        return response()->json([
            'from' => $from, 'to' => $to,
            'total_suppliers' => $supplierData->count(),
            'active_suppliers' => $supplierData->where('status','active')->count(),
            'total_purchase_value' => $supplierData->sum('total_amount'),
            'top_suppliers' => $topSuppliers,
            'suppliers' => $supplierData,
        ]);
    }

    // ── 6. Supplier Price Comparison ──────────────────────
    public function supplierPriceComparison(Request $req) {
        $uid = $this->userId();
        $productId = $req->product_id;
        $from = $req->from ?? now()->subMonths(6)->toDateString();
        $to = $req->to ?? now()->toDateString();

        $query = PurchaseItem::with(['purchase.supplier:id,name', 'product:id,name,sku'])
            ->whereHas('purchase', fn($q) => $q->where('created_by', $uid)->whereBetween('purchase_date',[$from,$to]));
        if ($productId) $query->where('product_id', $productId);

        $items = $query->orderBy('created_at', 'desc')->get();

        // Group by product → supplier → avg price
        $comparison = $items->groupBy('product_id')->map(function($productItems) {
            $product = $productItems->first()->product;
            $bySupplier = $productItems->groupBy(fn($i) => $i->purchase->supplier_id ?? 0)->map(function($si) {
                return [
                    'supplier_id' => $si->first()->purchase->supplier_id,
                    'supplier_name' => $si->first()->purchase->supplier?->name ?? 'Unknown',
                    'avg_unit_cost' => round($si->avg('unit_cost'), 2),
                    'lowest_cost' => round($si->min('unit_cost'), 2),
                    'highest_cost' => round($si->max('unit_cost'), 2),
                    'total_qty_purchased' => round($si->sum('quantity'), 2),
                    'last_purchase_date' => $si->max('created_at'),
                ];
            })->values();
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'suppliers' => $bySupplier,
            ];
        })->values();

        return response()->json([
            'from' => $from, 'to' => $to,
            'product_count' => $comparison->count(),
            'comparison' => $comparison,
        ]);
    }

    // ── 7. Expiry Date Reports ──────────────────────────────
    public function expiry(Request $req) {
        $uid = $this->userId();
        $days = $req->days ?? 30;
        $status = $req->status; // 'expiring', 'expired', 'all'

        $q = ProductBatch::with(['product:id,name,sku', 'supplier:id,name'])
            ->whereHas('product', fn($pq) => $pq->where('created_by', $uid));

        if ($status === 'expired') {
            $q->where('expiry_date', '<', now())->where('status', 'active');
        } elseif ($status === 'expiring') {
            $q->where('expiry_date', '>=', now())
              ->where('expiry_date', '<=', now()->addDays($days))
              ->where('status', 'active');
        } else {
            $q->whereNotNull('expiry_date');
        }

        $batches = $q->orderBy('expiry_date')->get();

        $summary = [
            'expired_count' => ProductBatch::whereHas('product', fn($pq) => $pq->where('created_by', $uid))
                ->where('expiry_date', '<', now())->where('status', 'active')->count(),
            'expiring_7_days' => ProductBatch::whereHas('product', fn($pq) => $pq->where('created_by', $uid))
                ->whereBetween('expiry_date', [now(), now()->addDays(7)])->where('status', 'active')->count(),
            'expiring_30_days' => ProductBatch::whereHas('product', fn($pq) => $pq->where('created_by', $uid))
                ->whereBetween('expiry_date', [now(), now()->addDays(30)])->where('status', 'active')->count(),
            'expiring_90_days' => ProductBatch::whereHas('product', fn($pq) => $pq->where('created_by', $uid))
                ->whereBetween('expiry_date', [now(), now()->addDays(90)])->where('status', 'active')->count(),
        ];

        return response()->json([
            'days_threshold' => $days,
            'summary' => $summary,
            'batches' => $batches,
        ]);
    }

    // ── 8. Product Trends (Fast / Slow Moving) ──────────────
    public function productTrends(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->subMonths(3)->toDateString();
        $to = $req->to ?? now()->toDateString();
        $limit = $req->limit ?? 20;

        $salesByProduct = SaleItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_qty_sold'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_id) as sale_count'),
                DB::raw('AVG(unit_price) as avg_selling_price')
            )
            ->whereHas('sale', fn($q) => $q->where('created_by', $uid)->where('status','completed')->whereBetween('sale_date',[$from,$to]))
            ->whereNotNull('product_id')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty_sold')
            ->get();

        // Merge with purchase data for margin analysis
        $purchaseByProduct = PurchaseItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty_purchased'),
                DB::raw('AVG(unit_cost) as avg_purchase_cost')
            )
            ->whereHas('purchase', fn($q) => $q->where('created_by', $uid)->whereBetween('purchase_date',[$from,$to]))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $products = $salesByProduct->map(function($s) use ($purchaseByProduct) {
            $p = $purchaseByProduct->get($s->product_id);
            $margin = $p ? ($s->avg_selling_price - $p->avg_purchase_cost) : 0;
            return [
                'product_id' => $s->product_id,
                'product_name' => $s->product_name,
                'total_qty_sold' => round((float)$s->total_qty_sold, 2),
                'total_revenue' => round((float)$s->total_revenue, 2),
                'sale_count' => (int)$s->sale_count,
                'avg_selling_price' => round((float)$s->avg_selling_price, 2),
                'avg_purchase_cost' => $p ? round((float)$p->avg_purchase_cost, 2) : null,
                'margin_per_unit' => round($margin, 2),
                'total_qty_purchased' => $p ? round((float)$p->total_qty_purchased, 2) : null,
            ];
        });

        $fastMoving = $products->take($limit);
        $slowMoving = $products->sortBy('total_qty_sold')->values()->take($limit);

        return response()->json([
            'from' => $from, 'to' => $to,
            'total_products_sold' => $products->count(),
            'total_revenue' => round($products->sum('total_revenue'), 2),
            'total_quantity_sold' => round($products->sum('total_qty_sold'), 2),
            'fast_moving' => $fastMoving,
            'slow_moving' => $slowMoving,
            'all_products' => $products->values(),
        ]);
    }
}
