<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
class CategoryController extends Controller {
    public function index(Request $req) {
        $q = ProductCategory::with("parent")->withCount("products")->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        if ($req->status) $q->where("status",$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","parent_id"=>"nullable|exists:product_categories,id","status"=>"in:active,inactive"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"category"=>ProductCategory::create($data)->load("parent")], 201);
    }
    public function show(ProductCategory $productCategory) { $this->ensureOwns($productCategory); return response()->json($productCategory); }
    public function update(Request $req, ProductCategory $productCategory) {
        $this->ensureOwns($productCategory);
        $productCategory->update($req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","parent_id"=>"nullable|exists:product_categories,id","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"category"=>$productCategory->load("parent")]);
    }
    public function destroy(ProductCategory $productCategory) { $this->ensureOwns($productCategory); $productCategory->delete(); return response()->json(["success"=>true]); }

    public function importLibrary()
    {
        $library = [
            ['name'=>'Electronics','description'=>'Electronic devices and gadgets','status'=>'active'],
            ['name'=>'Clothing','description'=>'Apparel and fashion items','status'=>'active'],
            ['name'=>'Food & Beverages','description'=>'Edible products and drinks','status'=>'active'],
            ['name'=>'Furniture','description'=>'Home and office furniture','status'=>'active'],
            ['name'=>'Health & Beauty','description'=>'Personal care and wellness','status'=>'active'],
            ['name'=>'Sports & Outdoors','description'=>'Sporting equipment and outdoor gear','status'=>'active'],
            ['name'=>'Toys & Games','description'=>'Children toys and games','status'=>'active'],
            ['name'=>'Automotive','description'=>'Vehicle parts and accessories','status'=>'active'],
            ['name'=>'Books & Stationery','description'=>'Books, pens, and office supplies','status'=>'active'],
            ['name'=>'Home Appliances','description'=>'Kitchen and home appliances','status'=>'active'],
            ['name'=>'Jewelry & Watches','description'=>'Watches, rings, necklaces','status'=>'active'],
            ['name'=>'Pet Supplies','description'=>'Pet food, toys, and accessories','status'=>'active'],
            ['name'=>'Hardware & Tools','description'=>'Construction and DIY tools','status'=>'active'],
            ['name'=>'Software','description'=>'Digital products and software licenses','status'=>'active'],
            ['name'=>'Services','description'=>'Service-based offerings','status'=>'active'],
        ];
        return response()->json($library);
    }

    public function import(Request $req)
    {
        $data = $req->validate(['items'=>'required|array|min:1','items.*.name'=>'required|string|max:191']);
        $businessId = $this->currentBusinessId();
        $imported = 0; $skipped = 0;
        foreach ($data['items'] as $item) {
            $exists = ProductCategory::where('created_by', $businessId)->where('name', $item['name'])->exists();
            if ($exists) { $skipped++; continue; }
            ProductCategory::create([
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'status' => $item['status'] ?? 'active',
                'created_by' => $businessId,
            ]);
            $imported++;
        }
        return response()->json(['success'=>true,'imported'=>$imported,'skipped'=>$skipped,'message'=>"Imported {$imported} categories, skipped {$skipped} duplicates."]);
    }
}
