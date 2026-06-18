<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ProductVariationController extends Controller {
    public function index(Request $req) {
        $q = ProductVariation::with('product')->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($q2) use($req){ $q2->where('attribute_value','like',"%{$req->search}%")->orWhere('sku','like',"%{$req->search}%")->orWhereHas('product',fn($q3)=>$q3->where('name','like',"%{$req->search}%")); });
        if ($req->product_id) $q->where('product_id',$req->product_id);
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['product_id'=>'required|exists:products,id','attribute_name'=>'required|string|max:100','attribute_value'=>'required|string|max:191','additional_price'=>'nullable|numeric|min:0','sku'=>'nullable|string|max:100','stock_quantity'=>'nullable|integer|min:0','status'=>'in:active,inactive']);
        if (empty($data['sku'])) $data['sku'] = 'VAR-'.strtoupper(Str::random(6));
        $data['created_by'] = $this->currentBusinessId();
        return response()->json(['success'=>true,'variation'=>ProductVariation::create($data)->load('product')], 201);
    }
    public function show(ProductVariation $productVariation) { $this->ensureOwns($productVariation); return response()->json($productVariation->load('product')); }
    public function update(Request $req, ProductVariation $productVariation) {
        $this->ensureOwns($productVariation);
        $productVariation->update($req->validate(['product_id'=>'required|exists:products,id','attribute_name'=>'required|string|max:100','attribute_value'=>'required|string|max:191','additional_price'=>'nullable|numeric|min:0','sku'=>'nullable|string|max:100','stock_quantity'=>'nullable|integer|min:0','status'=>'in:active,inactive']));
        return response()->json(['success'=>true,'variation'=>$productVariation->load('product')]);
    }
    public function destroy(ProductVariation $productVariation) { $this->ensureOwns($productVariation); $productVariation->delete(); return response()->json(['success'=>true]); }
}
