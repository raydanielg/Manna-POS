<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockAdjustmentApiController extends Controller {
    public function index(Request $req) {
        $q = StockAdjustment::with('product:id,name,sku');
        if ($req->type) $q->where('type',$req->type);
        return response()->json($q->latest()->take(100)->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $reference = 'ADJ-'.strtoupper(Str::random(8));
        $adjustment = StockAdjustment::create(array_merge($data, ['reference' => $reference]));
        $product = Product::find($data['product_id']);
        if ($product) {
            if ($data['type'] === 'addition') {
                $product->increment('stock_quantity', $data['quantity']);
            } else {
                $product->decrement('stock_quantity', $data['quantity']);
            }
        }
        return response()->json($adjustment->load('product:id,name,sku'), 201);
    }
    public function show(StockAdjustment $stockAdjustment) {
        return response()->json($stockAdjustment->load('product:id,name,sku'));
    }
    public function update(Request $req, StockAdjustment $stockAdjustment) {
        $data = $req->validate([
            'adjustment_date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $stockAdjustment->update($data);
        return response()->json($stockAdjustment->load('product:id,name,sku'));
    }
    public function destroy(StockAdjustment $stockAdjustment) {
        $product = $stockAdjustment->product;
        if ($product) {
            if ($stockAdjustment->type === 'addition') {
                $product->decrement('stock_quantity', $stockAdjustment->quantity);
            } else {
                $product->increment('stock_quantity', $stockAdjustment->quantity);
            }
        }
        $stockAdjustment->delete();
        return response()->json(['message'=>'Stock adjustment deleted']);
    }
}
