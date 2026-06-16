<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockTransferApiController extends Controller {
    use UserIdTrait;
    public function index() {
        return response()->json(StockTransfer::where('created_by', $this->userId())->orderByDesc('created_at')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'from_location' => 'required|string|max:191',
            'to_location' => 'required|string|max:191',
            'transfer_date' => 'required|date',
            'status' => 'nullable|in:draft,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        $data['reference'] = 'TRF-'.strtoupper(Str::random(8));
        $data['created_by'] = $this->userId();
        return response()->json(StockTransfer::create($data), 201);
    }
    public function show($id) {
        return response()->json(StockTransfer::where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $t = StockTransfer::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'from_location' => 'required|string|max:191',
            'to_location' => 'required|string|max:191',
            'transfer_date' => 'required|date',
            'status' => 'nullable|in:draft,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        $t->update($data);
        return response()->json($t->fresh());
    }
    public function destroy($id) {
        StockTransfer::where('created_by', $this->userId())->where('id',$id)->delete();
        return response()->json(['message'=>'Stock transfer deleted']);
    }
}
