<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaleApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = Sale::with('customer','items.product')->where('created_by', $this->userId());
        if ($req->status) $q->where('status', $req->status);
        if ($req->from) $q->whereDate('sale_date', '>=', $req->from);
        if ($req->to) $q->whereDate('sale_date', '<=', $req->to);
        if ($req->search) $q->where(function($qq) use ($req) { $qq->where('reference','like','%'.$req->search.'%')->orWhereHas('customer', fn($cq) => $cq->where('name','like','%'.$req->search.'%')); });
        return response()->json($q->orderByDesc('created_at')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'payment_status' => 'nullable|in:paid,partial,due',
            'payment_method' => 'nullable|string|max:50',
            'status' => 'nullable|in:completed,draft,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);
        $data['reference'] = 'INV-'.strtoupper(Str::random(8));
        $data['created_by'] = $this->userId();
        if (!$data['payment_status']) $data['payment_status'] = $data['paid'] >= $data['total'] ? 'paid' : ($data['paid'] > 0 ? 'partial' : 'due');
        if (!$data['status']) $data['status'] = 'completed';
        if (!$data['discount']) $data['discount'] = 0;
        if (!$data['tax']) $data['tax'] = 0;

        $sale = Sale::create($data);
        foreach ($data['items'] as $item) {
            $item['sale_id'] = $sale->id;
            SaleItem::create($item);
        }
        return response()->json(Sale::with('customer','items.product')->find($sale->id), 201);
    }
    public function show($id) {
        return response()->json(Sale::with('customer','items.product')->where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $s = Sale::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'payment_status' => 'nullable|in:paid,partial,due',
            'payment_method' => 'nullable|string|max:50',
            'status' => 'nullable|in:completed,draft,cancelled',
            'notes' => 'nullable|string',
        ]);
        $s->update($data);
        return response()->json($s->fresh('customer','items.product'));
    }
    public function destroy($id) {
        $s = Sale::where('created_by', $this->userId())->findOrFail($id);
        $s->items()->delete();
        $s->delete();
        return response()->json(['message'=>'Sale deleted']);
    }
    public function receipt($id) {
        $sale = Sale::with('customer','items.product')->where('created_by', $this->userId())->findOrFail($id);
        $items = $sale->items->map(fn($i) => [
            'product' => $i->product->name ?? 'N/A',
            'quantity' => $i->quantity,
            'unit_price' => $i->unit_price,
            'subtotal' => $i->subtotal,
        ]);
        return response()->json([
            'receipt_number' => $sale->reference,
            'customer_name' => $sale->customer->name ?? 'Walk-in',
            'sale_date' => $sale->sale_date,
            'items' => $items,
            'subtotal' => $sale->subtotal,
            'discount' => $sale->discount,
            'tax' => $sale->tax,
            'total' => $sale->total,
            'paid' => $sale->paid,
            'status' => $sale->status,
        ]);
    }
}
