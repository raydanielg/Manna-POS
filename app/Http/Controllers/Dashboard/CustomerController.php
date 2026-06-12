?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Customer::with("group");
            if ($req->search) $q->where(function($q2) use($req){ $q2->where("name","like","%{$req->search}%")->orWhere("email","like","%{$req->search}%")->orWhere("phone","like","%{$req->search}%"); });
            return response()->json($q->latest()->get());
        }
        $groups = CustomerGroup::all();
        return view("dashboard.contacts.customers", compact("groups"));
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"nullable|email|max:191","phone"=>"nullable|string|max:30","address"=>"nullable|string","city"=>"nullable|string|max:100","country"=>"nullable|string|max:100","customer_group_id"=>"nullable|exists:customer_groups,id","credit_limit"=>"nullable|numeric","status"=>"in:active,inactive","notes"=>"nullable|string"]);
        $c = Customer::create($data);
        return response()->json(["success"=>true,"customer"=>$c->load("group")], 201);
    }
    public function show(Customer $customer) { return response()->json($customer); }
    public function update(Request $req, Customer $customer) {
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"nullable|email|max:191","phone"=>"nullable|string|max:30","address"=>"nullable|string","city"=>"nullable|string|max:100","country"=>"nullable|string|max:100","customer_group_id"=>"nullable|exists:customer_groups,id","credit_limit"=>"nullable|numeric","status"=>"in:active,inactive","notes"=>"nullable|string"]);
        $customer->update($data);
        return response()->json(["success"=>true,"customer"=>$customer->load("group")]);
    }
    public function destroy(Customer $customer) { $customer->delete(); return response()->json(["success"=>true]); }
}
