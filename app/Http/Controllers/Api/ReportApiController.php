<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportApiController extends Controller {
    use UserIdTrait;
    public function sales(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->startOfMonth()->toDateString();
        $to = $req->to ?? now()->toDateString();
        $sales = Sale::with('customer:id,name')->where('created_by',$uid)->whereBetween('sale_date',[$from,$to])->get();
        return response()->json(['from'=>$from,'to'=>$to,'count'=>$sales->count(),'total'=>(float)$sales->sum('total'),'paid'=>(float)$sales->sum('paid'),'outstanding'=>(float)($sales->sum('total')-$sales->sum('paid')),'items'=>$sales]);
    }
    public function profitLoss(Request $req) {
        $uid = $this->userId();
        $from = $req->from ?? now()->startOfMonth()->toDateString();
        $to = $req->to ?? now()->toDateString();
        $revenue = (float) Sale::where('created_by',$uid)->whereBetween('sale_date',[$from,$to])->where('status','completed')->sum('total');
        $cost = (float) Purchase::where('created_by',$uid)->whereBetween('purchase_date',[$from,$to])->where('status','received')->sum('total');
        $expenses = (float) Expense::where('created_by',$uid)->whereBetween('expense_date',[$from,$to])->sum('amount');
        return response()->json(['from'=>$from,'to'=>$to,'revenue'=>$revenue,'cost'=>$cost,'expenses'=>$expenses,'gross_profit'=>$revenue-$cost,'net_profit'=>$revenue-$cost-$expenses]);
    }
    public function inventory() {
        $uid = $this->userId();
        $products = Product::with('category:id,name')->where('created_by',$uid)->orderBy('name')->get();
        $stockValue = $products->sum(fn($p)=>$p->stock_quantity * $p->cost_price);
        $lowStock = $products->filter(fn($p)=>$p->stock_quantity <= $p->reorder_level && $p->stock_quantity > 0);
        $outOfStock = $products->filter(fn($p)=>$p->stock_quantity <= 0);
        return response()->json(['total_products'=>$products->count(),'stock_value'=>(float)$stockValue,'low_stock_count'=>$lowStock->count(),'out_of_stock_count'=>$outOfStock->count(),'products'=>$products]);
    }
}
