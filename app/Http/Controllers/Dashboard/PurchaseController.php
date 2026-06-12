?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PurchaseController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Purchase::with("supplier");
            if ($req->search) $q->where("reference","like","%{$req->search}%");
            if ($req->status) $q->where("payment_status",$req->status);
            return response()->json($q->latest()->get());
        }
        $suppliers = Supplier::where("status","active")->get();
        $products  = Product::where("status","active")->get();
        return view("dashboard.purchases.list-purchases", compact("suppliers","products"));
    }
    public function store(Request $req) {
        $data = $req->validate(["supplier_id"=>"nullable|exists:suppliers,id","purchase_date"=>"required|date","subtotal"=>"required|numeric|min:0","discount"=>"nullable|numeric|min:0","tax"=>"nullable|numeric|min:0","shipping"=>"nullable|numeric|min:0","total"=>"required|numeric|min:0","payment_status"=>"in:paid,partial,unpaid","status"=>"in:received,pending,cancelled","notes"=>"nullable|string","items"=>"required|array|min:1","items.*.product_id"=>"nullable|exists:products,id","items.*.product_name"=>"required|string","items.*.quantity"=>"required|numeric|min:0.0001","items.*.unit_cost"=>"required|numeric|min:0","items.*.total"=>"required|numeric|min:0"]);
        $data["reference"] = "PO-".strtoupper(Str::random(8));
        $items = $data["items"];
        unset($data["items"]);
        $purchase = Purchase::create($data);
        foreach ($items as $item) $purchase->items()->create($item);
        // Update stock
        foreach ($items as $item) {
            if (!empty($item["product_id"])) {
                Product::find($item["product_id"])?->increment("stock_quantity", $item["quantity"]);
            }
        }
        return response()->json(["success"=>true,"purchase"=>$purchase->load(["supplier","items"])], 201);
    }
    public function show(Purchase $purchase) { return response()->json($purchase->load(["supplier","items.product"])); }
    public function destroy(Purchase $purchase) { $purchase->delete(); return response()->json(["success"=>true]); }
}
