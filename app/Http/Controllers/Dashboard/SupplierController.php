<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller {
    public function index(Request $req) {
        $q = Supplier::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($sq) use($req){ $sq->where('name','like',"%{$req->search}%")->orWhere('email','like',"%{$req->search}%")->orWhere('phone','like',"%{$req->search}%"); });
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191','company'=>'nullable|string|max:191','email'=>'nullable|email|max:191','phone'=>'nullable|string|max:30','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','tax_number'=>'nullable|string|max:50','pay_term'=>'nullable|string|max:100','credit_limit'=>'nullable|numeric','status'=>'in:active,inactive','notes'=>'nullable|string']);
        $data['created_by'] = $this->currentBusinessId();
        $s = Supplier::create($data);
        return response()->json(['success'=>true,'supplier'=>$s], 201);
    }
    public function show(Supplier $supplier) {
        $this->ensureOwns($supplier);
        return response()->json($supplier);
    }
    public function update(Request $req, Supplier $supplier) {
        $this->ensureOwns($supplier);
        $data = $req->validate(['name'=>'required|string|max:191','company'=>'nullable|string|max:191','email'=>'nullable|email|max:191','phone'=>'nullable|string|max:30','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','tax_number'=>'nullable|string|max:50','pay_term'=>'nullable|string|max:100','credit_limit'=>'nullable|numeric','status'=>'in:active,inactive','notes'=>'nullable|string']);
        $supplier->update($data);
        return response()->json(['success'=>true,'supplier'=>$supplier]);
    }
    public function destroy(Supplier $supplier) {
        $this->ensureOwns($supplier);
        $supplier->delete();
        return response()->json(['success'=>true]);
    }
}
