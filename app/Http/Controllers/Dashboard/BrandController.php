?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
class BrandController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Brand::withCount("products");
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.inventory.brands");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","status"=>"in:active,inactive"]);
        return response()->json(["success"=>true,"brand"=>Brand::create($data)], 201);
    }
    public function show(Brand $brand) { return response()->json($brand); }
    public function update(Request $req, Brand $brand) {
        $brand->update($req->validate(["name"=>"required|string|max:191","description"=>"nullable|string","status"=>"in:active,inactive"]));
        return response()->json(["success"=>true,"brand"=>$brand]);
    }
    public function destroy(Brand $brand) { $brand->delete(); return response()->json(["success"=>true]); }
}
