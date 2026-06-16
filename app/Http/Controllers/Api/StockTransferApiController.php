<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockTransferApiController extends Controller {
    public function index(Request $req) {
        $q = StockTransfer::query();
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->take(100)->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'from_location' => 'required|string|max:191',
            'to_location' => 'required|string|max:191',
            'transfer_date' => 'required|date',
            'status' => 'in:draft,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        $data['reference'] = 'TRF-'.strtoupper(Str::random(8));
        $data['status'] = $data['status'] ?? 'draft';
        return response()->json(StockTransfer::create($data), 201);
    }
    public function show(StockTransfer $stockTransfer) {
        return response()->json($stockTransfer);
    }
    public function update(Request $req, StockTransfer $stockTransfer) {
        $data = $req->validate([
            'from_location' => 'required|string|max:191',
            'to_location' => 'required|string|max:191',
            'transfer_date' => 'required|date',
            'status' => 'in:draft,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        $stockTransfer->update($data);
        return response()->json($stockTransfer);
    }
    public function destroy(StockTransfer $stockTransfer) {
        $stockTransfer->delete();
        return response()->json(['message'=>'Stock transfer deleted']);
    }
}
