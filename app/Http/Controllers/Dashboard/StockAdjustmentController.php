<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class StockAdjustmentController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = StockAdjustment::with("product");
            if ($req->search) $q->where("reference","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        $products = Product::where("status","active")->get();
        return view("dashboard.stock-adjustment.list-stock-adjustment", compact("products"));
    }
    public function store(Request $req) {
        $data = $req->validate(["adjustment_date"=>"required|date","type"=>"required|in:addition,subtraction","product_id"=>"required|exists:products,id","quantity"=>"required|numeric|min:0.0001","unit_cost"=>"nullable|numeric|min:0","reason"=>"nullable|string|max:191","notes"=>"nullable|string"]);
        $data["reference"] = "ADJ-".strtoupper(Str::random(6));
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
    public function show(StockAdjustment $stockAdjustment) { return response()->json($stockAdjustment->load("product")); }
    public function destroy(StockAdjustment $stockAdjustment) { $stockAdjustment->delete(); return response()->json(["success"=>true]); }
}
