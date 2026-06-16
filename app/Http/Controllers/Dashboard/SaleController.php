<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class SaleController extends Controller {
    public function index(Request $req) {
        $q = Sale::with("customer");
        if ($req->search) $q->where("reference","like","%{$req->search}%");
        if ($req->status) $q->where("status",$req->status);
        if ($req->payment_status) $q->where("payment_status",$req->payment_status);
        if ($req->from) $q->whereDate("sale_date",">=",$req->from);
        if ($req->to)   $q->whereDate("sale_date","<=",$req->to);
        if ($req->customer_id) $q->where("customer_id",$req->customer_id);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["customer_id"=>"nullable|exists:customers,id","sale_date"=>"required|date","subtotal"=>"required|numeric|min:0","discount"=>"nullable|numeric|min:0","tax"=>"nullable|numeric|min:0","total"=>"required|numeric|min:0","paid"=>"nullable|numeric|min:0","payment_method"=>"in:cash,card,mobile_money,credit","status"=>"in:completed,draft,quotation,cancelled,return","notes"=>"nullable|string","items"=>"required|array|min:1","items.*.product_id"=>"nullable|exists:products,id","items.*.product_name"=>"required|string","items.*.quantity"=>"required|numeric|min:0.0001","items.*.unit_price"=>"required|numeric|min:0","items.*.discount"=>"nullable|numeric|min:0","items.*.total"=>"required|numeric|min:0"]);
        $paid = $data["paid"] ?? 0;
        $total = $data["total"];
        $data["payment_status"] = $paid >= $total ? "paid" : ($paid > 0 ? "partial" : "unpaid");
        $data["reference"] = "INV-".strtoupper(Str::random(8));
        $items = $data["items"]; unset($data["items"]);
        $sale = Sale::create($data);
        foreach ($items as $item) $sale->items()->create($item);
        // Deduct stock
        if ($data["status"] === "completed") {
            foreach ($items as $item) {
                if (!empty($item["product_id"])) {
                    Product::find($item["product_id"])?->decrement("stock_quantity", $item["quantity"]);
                }
            }
        }
        return response()->json(["success"=>true,"sale"=>$sale->load(["customer","items"])], 201);
    }
    public function show(Sale $sale) { return response()->json($sale->load(["customer","items.product"])); }
    public function update(Request $req, Sale $sale) {
        $wasCompleted = $sale->status === "completed";
        $data = $req->validate([
            "customer_id"    => "nullable|exists:customers,id",
            "sale_date"      => "sometimes|date",
            "status"         => "sometimes|in:completed,draft,quotation,cancelled,return",
            "payment_status" => "sometimes|in:paid,partial,unpaid",
            "payment_method" => "sometimes|in:cash,card,mobile_money,credit",
            "discount"       => "nullable|numeric|min:0",
            "tax"            => "nullable|numeric|min:0",
            "paid"           => "nullable|numeric|min:0",
            "notes"          => "nullable|string",
        ]);
        $sale->update($data);
        // Deduct stock when converting from draft/quotation to completed
        if (!$wasCompleted && isset($data["status"]) && $data["status"] === "completed") {
            foreach ($sale->items as $item) {
                if ($item->product_id) {
                    Product::find($item->product_id)?->decrement("stock_quantity", $item->quantity);
                }
            }
        }
        return response()->json(["success"=>true,"sale"=>$sale->load(["customer","items"])]);
    }
    public function destroy(Sale $sale) { $sale->delete(); return response()->json(["success"=>true]); }
}
