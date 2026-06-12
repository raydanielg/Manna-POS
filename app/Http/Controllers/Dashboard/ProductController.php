?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ProductController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Product::with(["brand","category","unit"]);
            if ($req->search) $q->where(function($q2) use($req){ $q2->where("name","like","%{$req->search}%")->orWhere("sku","like","%{$req->search}%")->orWhere("barcode","like","%{$req->search}%"); });
            if ($req->category_id) $q->where("category_id",$req->category_id);
            if ($req->brand_id) $q->where("brand_id",$req->brand_id);
            if ($req->status) $q->where("status",$req->status);
            return response()->json($q->latest()->get());
        }
        $brands = Brand::where("status","active")->get();
        $categories = ProductCategory::where("status","active")->get();
        $units = Unit::all();
        $taxRates = TaxRate::where("status","active")->get();
        return view("dashboard.inventory.list-products", compact("brands","categories","units","taxRates"));
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","sku"=>"nullable|string|max:100|unique:products,sku","barcode"=>"nullable|string|max:100","brand_id"=>"nullable|exists:brands,id","category_id"=>"nullable|exists:product_categories,id","unit_id"=>"nullable|exists:units,id","tax_rate_id"=>"nullable|exists:tax_rates,id","description"=>"nullable|string","purchase_price"=>"required|numeric|min:0","selling_price"=>"required|numeric|min:0","stock_quantity"=>"nullable|numeric|min:0","reorder_level"=>"nullable|numeric|min:0","status"=>"in:active,inactive"]);
        if (empty($data["sku"])) $data["sku"] = "SKU-".strtoupper(Str::random(8));
        $p = Product::create($data);
        return response()->json(["success"=>true,"product"=>$p->load(["brand","category","unit"])], 201);
    }
    public function show(Product $product) { return response()->json($product); }
    public function update(Request $req, Product $product) {
        $data = $req->validate(["name"=>"required|string|max:191","sku"=>"nullable|string|max:100|unique:products,sku,{$product->id}","barcode"=>"nullable|string|max:100","brand_id"=>"nullable|exists:brands,id","category_id"=>"nullable|exists:product_categories,id","unit_id"=>"nullable|exists:units,id","tax_rate_id"=>"nullable|exists:tax_rates,id","description"=>"nullable|string","purchase_price"=>"required|numeric|min:0","selling_price"=>"required|numeric|min:0","stock_quantity"=>"nullable|numeric|min:0","reorder_level"=>"nullable|numeric|min:0","status"=>"in:active,inactive"]);
        $product->update($data);
        return response()->json(["success"=>true,"product"=>$product->load(["brand","category","unit"])]);
    }
    public function destroy(Product $product) { $product->delete(); return response()->json(["success"=>true]); }
}
