<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller {
    public function index(Request $req) {
        $q = CustomerGroup::withCount("customers")->forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","discount"=>"nullable|numeric|min:0|max:100","description"=>"nullable|string"]);
        $data["created_by"] = $this->currentBusinessId();
        $g = CustomerGroup::create($data);
        return response()->json(["success"=>true,"group"=>$g], 201);
    }
    public function show(CustomerGroup $customerGroup) { $this->ensureOwns($customerGroup); return response()->json($customerGroup); }
    public function update(Request $req, CustomerGroup $customerGroup) {
        $this->ensureOwns($customerGroup);
        $data = $req->validate(["name"=>"required|string|max:191","discount"=>"nullable|numeric|min:0|max:100","description"=>"nullable|string"]);
        $customerGroup->update($data);
        return response()->json(["success"=>true,"group"=>$customerGroup]);
    }
    public function destroy(CustomerGroup $customerGroup) { $this->ensureOwns($customerGroup); $customerGroup->delete(); return response()->json(["success"=>true]); }
}
