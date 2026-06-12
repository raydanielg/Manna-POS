<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
class CategoryController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = ProductCategory::with("parent")->withCount("products");
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        $categories = ProductCategory::whereNull("parent_id")->get();
        return view("dashboard.inventory.product-categories", compact("categories"));
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","parent_id"=>"nullable|exists:product_categories,id","status"=>"in:active,inactive"]);
        return response()->json(["success"=>true,"category"=>ProductCategory::create($data)->load("parent")], 201);
    }
    public function show(ProductCategory $productCategory) { return response()->json($productCategory); }
    public function update(Request $req, ProductCategory $productCategory) {
        $productCategory->update($req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","parent_id"=>"nullable|exists:product_categories,id","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"category"=>$productCategory->load("parent")]);
    }
    public function destroy(ProductCategory $productCategory) { $productCategory->delete(); return response()->json(["success"=>true]); }
}
