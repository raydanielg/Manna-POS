<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierApiController extends Controller {
    public function index(Request $req) {
        $q = Supplier::query();
        if ($req->search) $q->where(function($qq) use ($req) {
            $qq->where('name','like',"%{$req->search}%")->orWhere('company','like',"%{$req->search}%")->orWhere('email','like',"%{$req->search}%")->orWhere('phone','like',"%{$req->search}%");
        });
        return response()->json($q->orderBy('name')->get());
    }
    public function store(Request $req) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'company' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'pay_term' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric',
            'status' => 'in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        return response()->json(Supplier::create($data), 201);
    }
    public function show(Supplier $supplier) {
        return response()->json($supplier);
    }
    public function update(Request $req, Supplier $supplier) {
        $data = $req->validate([
            'name' => 'required|string|max:191',
            'company' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'pay_term' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric',
            'status' => 'in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        $supplier->update($data);
        return response()->json($supplier);
    }
    public function destroy(Supplier $supplier) {
        $supplier->delete();
        return response()->json(['message'=>'Supplier deleted']);
    }
}
