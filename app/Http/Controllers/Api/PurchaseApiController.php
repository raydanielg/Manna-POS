<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = Purchase::with('supplier','items.product')->where('created_by', $this->userId());
        if ($req->status) $q->where('status', $req->status);
        if ($req->from) $q->whereDate('purchase_date', '>=', $req->from);
        if ($req->to) $q->whereDate('purchase_date', '<=', $req->to);
        if ($req->search) $q->where('reference','like','%'.$req->search.'%');
        return response()->json($q->orderByDesc('created_at')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_status' => 'nullable|in:paid,partial,due',
            'status' => 'nullable|in:received,pending,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);
        $data['reference'] = 'PO-'.strtoupper(Str::random(8));
        $data['created_by'] = $this->userId();
        $purchase = Purchase::create($data);
        foreach ($data['items'] as $item) { $item['purchase_id'] = $purchase->id; PurchaseItem::create($item); }
        return response()->json(Purchase::with('supplier','items.product')->find($purchase->id), 201);
    }
    public function show($id) {
        return response()->json(Purchase::with('supplier','items.product')->where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $p = Purchase::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_status' => 'nullable|in:paid,partial,due',
            'status' => 'nullable|in:received,pending,cancelled',
            'notes' => 'nullable|string',
        ]);
        $p->update($data);
        return response()->json($p->fresh('supplier','items.product'));
    }
    public function destroy($id) {
        $p = Purchase::where('created_by', $this->userId())->findOrFail($id);
        $p->items()->delete(); $p->delete();
        return response()->json(['message'=>'Purchase deleted']);
    }
}
