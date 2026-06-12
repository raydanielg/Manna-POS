<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller {
    public function index(Request $req) {
        $q = Sale::with('customer:id,name');
        if ($req->search) $q->where('reference','like',"%{$req->search}%");
        if ($req->status) $q->where('status',$req->status);
        if ($req->from) $q->whereDate('sale_date','>=',$req->from);
        if ($req->to) $q->whereDate('sale_date','<=',$req->to);
        return response()->json($q->latest()->take(100)->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['customer_id'=>'nullable|exists:customers,id','sale_date'=>'required|date','status'=>'in:completed,draft,quotation,cancelled','payment_method'=>'in:cash,card,mobile_money,credit','discount'=>'nullable|numeric','paid'=>'nullable|numeric','subtotal'=>'nullable|numeric','total'=>'nullable|numeric','notes'=>'nullable|string','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|numeric|min:0.01','items.*.unit_price'=>'required|numeric|min:0']);
        return DB::transaction(function() use ($data, $req) {
            $reference = 'INV-'.strtoupper(Str::random(8));
            $subtotal = collect($data['items'])->sum(fn($i)=>$i['quantity']*$i['unit_price']);
            $discount = $data['discount']??0;
            $total = $subtotal - $discount;
            $paid = $data['paid']??0;
            $paymentStatus = $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            $sale = Sale::create(['reference'=>$reference,'customer_id'=>$data['customer_id']??null,'sale_date'=>$data['sale_date'],'subtotal'=>$subtotal,'discount'=>$discount,'tax'=>0,'total'=>$total,'paid'=>$paid,'payment_status'=>$paymentStatus,'payment_method'=>$data['payment_method']??'cash','status'=>$data['status']??'completed','notes'=>$data['notes']??null]);
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                SaleItem::create(['sale_id'=>$sale->id,'product_id'=>$item['product_id'],'product_name'=>$product?$product->name:'Product','quantity'=>$item['quantity'],'unit_price'=>$item['unit_price'],'discount'=>$item['discount']??0,'total'=>$item['quantity']*$item['unit_price']]);
                if (($data['status']??'completed') === 'completed' && $product) {
                    $product->decrement('stock_quantity',$item['quantity']);
                }
            }
            return response()->json($sale->load(['customer:id,name','items']),201);
        });
    }
    public function show(Sale $sale) {
        return response()->json($sale->load(['customer:id,name','items']));
    }
    public function update(Request $req, Sale $sale) {
        $data = $req->validate(['status'=>'in:completed,draft,quotation,cancelled','payment_method'=>'in:cash,card,mobile_money,credit','paid'=>'nullable|numeric','notes'=>'nullable|string']);
        $sale->update($data);
        return response()->json($sale->load(['customer:id,name','items']));
    }
    public function destroy(Sale $sale) {
        $sale->items()->delete();
        $sale->delete();
        return response()->json(['message'=>'Sale deleted']);
    }
}