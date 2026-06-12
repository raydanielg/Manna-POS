<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductApiController extends Controller {
    public function index(Request $req) {
        $q = Product::with(['category:id,name','brand:id,name','unit:id,name,short_name']);
        if ($req->search) $q->where('name','like',"%{$req->search}%")->orWhere('sku','like',"%{$req->search}%");
        if ($req->category_id) $q->where('category_id',$req->category_id);
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191','category_id'=>'nullable|exists:product_categories,id','brand_id'=>'nullable|exists:brands,id','unit_id'=>'nullable|exists:units,id','selling_price'=>'required|numeric|min:0','purchase_price'=>'nullable|numeric|min:0','stock_quantity'=>'nullable|numeric','reorder_level'=>'nullable|numeric','status'=>'in:active,inactive','description'=>'nullable|string']);
        if (empty($data['sku'])) $data['sku'] = 'SKU-'.strtoupper(Str::random(6));
        return response()->json(Product::create($data)->load(['category:id,name','brand:id,name','unit:id,name,short_name']),201);
    }
    public function show(Product $product) {
        return response()->json($product->load(['category:id,name','brand:id,name','unit:id,name,short_name']));
    }
    public function update(Request $req, Product $product) {
        $data = $req->validate(['name'=>'required|string|max:191','category_id'=>'nullable|exists:product_categories,id','brand_id'=>'nullable|exists:brands,id','unit_id'=>'nullable|exists:units,id','selling_price'=>'required|numeric|min:0','purchase_price'=>'nullable|numeric|min:0','stock_quantity'=>'nullable|numeric','reorder_level'=>'nullable|numeric','status'=>'in:active,inactive','description'=>'nullable|string']);
        $product->update($data);
        return response()->json($product->load(['category:id,name','brand:id,name','unit:id,name,short_name']));
    }
    public function destroy(Product $product) {
        $product->delete();
        return response()->json(['message'=>'Product deleted']);
    }
    public function formData() {
        return response()->json(['categories'=>ProductCategory::select('id','name')->orderBy('name')->get(),'brands'=>Brand::select('id','name')->orderBy('name')->get(),'units'=>Unit::select('id','name','short_name')->orderBy('name')->get()]);
    }
}