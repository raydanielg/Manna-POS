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
}
