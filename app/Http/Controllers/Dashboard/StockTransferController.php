?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class StockTransferController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = StockTransfer::query();
            if ($req->search) $q->where("reference","like","%{$req->search}%")->orWhere("from_location","like","%{$req->search}%")->orWhere("to_location","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.stock-transfer.list-stock-transfer");
    }
    public function store(Request $req) {
        $data = $req->validate(["from_location"=>"required|string|max:191","to_location"=>"required|string|max:191","transfer_date"=>"required|date","status"=>"in:pending,completed,cancelled","notes"=>"nullable|string"]);
        $data["reference"] = "TRF-".strtoupper(Str::random(6));
        $t = StockTransfer::create($data);
        return response()->json(["success"=>true,"transfer"=>$t], 201);
    }
    public function show(StockTransfer $stockTransfer) { return response()->json($stockTransfer); }
    public function update(Request $req, StockTransfer $stockTransfer) {
        $stockTransfer->update($req->validate(["from_location"=>"required|string|max:191","to_location"=>"required|string|max:191","transfer_date"=>"required|date","status"=>"in:pending,completed,cancelled","notes"=>"nullable|string"]));
        return response()->json(["success"=>true,"transfer"=>$stockTransfer]);
    }
    public function destroy(StockTransfer $stockTransfer) { $stockTransfer->delete(); return response()->json(["success"=>true]); }
}
