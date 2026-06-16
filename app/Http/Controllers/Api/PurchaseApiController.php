<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PurchaseApiController extends Controller {
    public function index(Request $req) {
        $q = Purchase::with('supplier:id,name,company');
        if ($req->search) $q->where('reference','like',"%{$req->search}%");
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->take(100)->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_status' => 'in:pending,partial,paid',
            'status' => 'in:received,pending,draft,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);
        return DB::transaction(function() use ($data) {
            $reference = 'PUR-'.strtoupper(Str::random(8));
            $subtotal = collect($data['items'])->sum(fn($i)=>$i['quantity']*$i['unit_cost']);
            $purchase = Purchase::create([
                'reference' => $reference,
                'supplier_id' => $data['supplier_id'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'subtotal' => $subtotal,
                'discount' => 0, 'tax' => 0, 'shipping' => 0,
                'total' => $subtotal,
                'payment_status' => $data['payment_status'] ?? 'pending',
                'status' => $data['status'] ?? 'received',
                'notes' => $data['notes'] ?? null,
            ]);
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product ? $product->name : 'Product',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $item['quantity'] * $item['unit_cost'],
                ]);
                if (($data['status'] ?? 'received') === 'received' && $product) {
                    $product->increment('stock_quantity', $item['quantity']);
                }
            }
            return response()->json($purchase->load(['supplier:id,name,company','items']), 201);
        });
    }
    public function show(Purchase $purchase) {
        return response()->json($purchase->load(['supplier:id,name,company','items']));
    }
    public function update(Request $req, Purchase $purchase) {
        $data = $req->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'payment_status' => 'in:pending,partial,paid',
            'status' => 'in:received,pending,draft,cancelled',
            'notes' => 'nullable|string',
        ]);
        $purchase->update($data);
        return response()->json($purchase->load(['supplier:id,name,company','items']));
    }
    public function destroy(Purchase $purchase) {
        $purchase->items()->delete();
        $purchase->delete();
        return response()->json(['message'=>'Purchase deleted']);
    }
}
