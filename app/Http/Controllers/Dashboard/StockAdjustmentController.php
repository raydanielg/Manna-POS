<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class StockAdjustmentController extends Controller {
    public function index(Request $req) {
        $q = StockAdjustment::with("product")->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("reference","like","%{$req->search}%");
        if ($req->type) $q->where("type",$req->type);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["adjustment_date"=>"required|date","type"=>"required|in:addition,subtraction","product_id"=>"required|exists:products,id","quantity"=>"required|numeric|min:0.0001","unit_cost"=>"nullable|numeric|min:0","reason"=>"nullable|string|max:191","notes"=>"nullable|string"]);
        $data["reference"] = "ADJ-".strtoupper(Str::random(6));
        $data["created_by"] = $this->currentBusinessId();
        $adj = StockAdjustment::create($data);
        // Update stock
        $product = Product::find($data["product_id"]);
        if ($product) {
            if ($data["type"] === "addition") {
                $product->increment("stock_quantity", $data["quantity"]);
            } else {
                $product->decrement("stock_quantity", $data["quantity"]);
            }
        }
        return response()->json(["success"=>true,"adjustment"=>$adj->load("product")], 201);
    }
    public function show(StockAdjustment $stockAdjustment) { $this->ensureOwns($stockAdjustment); return response()->json($stockAdjustment->load("product")); }
    public function destroy(StockAdjustment $stockAdjustment) { $this->ensureOwns($stockAdjustment); $stockAdjustment->delete(); return response()->json(["success"=>true]); }
}
