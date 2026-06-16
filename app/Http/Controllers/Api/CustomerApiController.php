<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller {
    use UserIdTrait;
    public function index(Request $req) {
        $q = Customer::with('group')->where('created_by', $this->userId());
        if ($req->search) $q->where(function($qq) use ($req) { $qq->where('name','like','%'.$req->search.'%')->orWhere('phone','like','%'.$req->search.'%')->orWhere('email','like','%'.$req->search.'%'); });
        return response()->json($q->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        $data['created_by'] = $this->userId();
        return response()->json(Customer::create($data), 201);
    }
    public function show($id) {
        return response()->json(Customer::with('group')->where('created_by', $this->userId())->findOrFail($id));
    }
    public function update(Request $req, $id) {
        $c = Customer::where('created_by', $this->userId())->findOrFail($id);
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        $c->update($data);
        return response()->json($c->fresh('group'));
    }
    public function destroy($id) {
        Customer::where('created_by', $this->userId())->where('id',$id)->delete();
        return response()->json(['message'=>'Customer deleted']);
    }
}
