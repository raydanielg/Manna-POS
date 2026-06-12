<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerApiController extends Controller {
    public function index(Request $req) {
        $q = Customer::with('group:id,name');
        if ($req->search) $q->where(function($qq) use ($req){ $qq->where('name','like',"%{$req->search}%")->orWhere('email','like',"%{$req->search}%")->orWhere('phone','like',"%{$req->search}%"); });
        return response()->json($q->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191','email'=>'nullable|email','phone'=>'nullable|string|max:30','address'=>'nullable|string','city'=>'nullable|string|max:100','customer_group_id'=>'nullable|exists:customer_groups,id','credit_limit'=>'nullable|numeric','status'=>'in:active,inactive','notes'=>'nullable|string']);
        return response()->json(Customer::create($data)->load('group:id,name'),201);
    }
    public function show(Customer $customer) {
        return response()->json($customer->load('group:id,name'));
    }
    public function update(Request $req, Customer $customer) {
        $data = $req->validate(['name'=>'required|string|max:191','email'=>'nullable|email','phone'=>'nullable|string|max:30','address'=>'nullable|string','city'=>'nullable|string|max:100','customer_group_id'=>'nullable|exists:customer_groups,id','credit_limit'=>'nullable|numeric','status'=>'in:active,inactive','notes'=>'nullable|string']);
        $customer->update($data);
        return response()->json($customer->load('group:id,name'));
    }
    public function destroy(Customer $customer) {
        $customer->delete();
        return response()->json(['message'=>'Customer deleted']);
    }
}