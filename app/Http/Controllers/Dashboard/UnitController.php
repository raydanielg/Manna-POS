?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
class UnitController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Unit::withCount("products");
            if ($req->search) $q->where("name","like","%{$req->search}%")->orWhere("short_name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.inventory.units");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","short_name"=>"required|string|max:20","allow_decimal"=>"boolean"]);
        return response()->json(["success"=>true,"unit"=>Unit::create($data)], 201);
    }
    public function show(Unit $unit) { return response()->json($unit); }
    public function update(Request $req, Unit $unit) {
        $unit->update($req->validate(["name"=>"required|string|max:191","short_name"=>"required|string|max:20","allow_decimal"=>"boolean"]));
        return response()->json(["success"=>true,"unit"=>$unit]);
    }
    public function destroy(Unit $unit) { $unit->delete(); return response()->json(["success"=>true]); }
}
