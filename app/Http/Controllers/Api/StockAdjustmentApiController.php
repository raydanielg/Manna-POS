<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockAdjustmentApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = StockAdjustment::with('product')->where('created_by', $this->userId());
        if ($req->type) $q->where('type',$req->type);
        if ($req->from) $q->whereDate('adjustment_date','>=',$req->from);
        if ($req->to) $q->whereDate('adjustment_date','<=',$req->to);
        return response()->json($q->orderByDesc('created_at')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);
        $data['reference'] = 'ADJ-'.strtoupper(Str::random(8));
        $data['created_by'] = $this->userId();
        return response()->json(StockAdjustment::create($data), 201);
    }
    public function show($id) {
        return response()->json(StockAdjustment::with('product')->where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $a = StockAdjustment::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_date' => 'required|date',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);
        $a->update($data);
        return response()->json($a->fresh('product'));
    }
    public function destroy($id) {
        StockAdjustment::where('created_by', $this->userId())->where('id',$id)->delete();
        return response()->json(['message'=>'Stock adjustment deleted']);
    }
}
