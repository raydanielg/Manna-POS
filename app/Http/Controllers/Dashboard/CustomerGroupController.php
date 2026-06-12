<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = CustomerGroup::withCount("customers");
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.contacts.customer-groups");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","discount"=>"nullable|numeric|min:0|max:100","description"=>"nullable|string"]);
        $g = CustomerGroup::create($data);
        return response()->json(["success"=>true,"group"=>$g], 201);
    }
    public function show(CustomerGroup $customerGroup) { return response()->json($customerGroup); }
    public function update(Request $req, CustomerGroup $customerGroup) {
        $data = $req->validate(["name"=>"required|string|max:191","discount"=>"nullable|numeric|min:0|max:100","description"=>"nullable|string"]);
        $customerGroup->update($data);
        return response()->json(["success"=>true,"group"=>$customerGroup]);
    }
    public function destroy(CustomerGroup $customerGroup) { $customerGroup->delete(); return response()->json(["success"=>true]); }
}
