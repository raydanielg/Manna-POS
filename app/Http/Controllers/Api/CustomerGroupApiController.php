<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupApiController extends Controller {
    public function index() {
        return response()->json(CustomerGroup::withCount('customers')->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191|unique:customer_groups,name','discount'=>'nullable|numeric|min:0|max:100','description'=>'nullable|string']);
        return response()->json(CustomerGroup::create($data), 201);
    }
    public function show(CustomerGroup $customerGroup) {
        return response()->json($customerGroup->loadCount('customers'));
    }
    public function update(Request $req, CustomerGroup $customerGroup) {
        $data = $req->validate(['name'=>'required|string|max:191|unique:customer_groups,name,'.$customerGroup->id,'discount'=>'nullable|numeric|min:0|max:100','description'=>'nullable|string']);
        $customerGroup->update($data);
        return response()->json($customerGroup);
    }
    public function destroy(CustomerGroup $customerGroup) {
        $customerGroup->delete();
        return response()->json(['message'=>'Customer group deleted']);
    }
}
