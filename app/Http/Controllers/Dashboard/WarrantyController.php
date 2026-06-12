?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Warranty;
use Illuminate\Http\Request;
class WarrantyController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Warranty::query();
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.inventory.warranties");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","duration"=>"required|integer|min:1","duration_unit"=>"in:days,months,years","description"=>"nullable|string"]);
        return response()->json(["success"=>true,"warranty"=>Warranty::create($data)], 201);
    }
    public function show(Warranty $warranty) { return response()->json($warranty); }
    public function update(Request $req, Warranty $warranty) {
        $warranty->update($req->validate(["name"=>"required|string|max:191","duration"=>"required|integer|min:1","duration_unit"=>"in:days,months,years","description"=>"nullable|string"]));
        return response()->json(["success"=>true,"warranty"=>$warranty]);
    }
    public function destroy(Warranty $warranty) { $warranty->delete(); return response()->json(["success"=>true]); }
}
