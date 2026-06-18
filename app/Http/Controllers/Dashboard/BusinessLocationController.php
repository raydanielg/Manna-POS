<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\BusinessLocation;
use Illuminate\Http\Request;
class BusinessLocationController extends Controller {
    public function index(Request $req) {
        $q = BusinessLocation::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where(function($sq) use($req){ $sq->where('name','like',"%{$req->search}%")->orWhere('city','like',"%{$req->search}%"); });
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','phone'=>'nullable|string|max:30','status'=>'in:active,inactive']);
        $data['created_by'] = $this->currentBusinessId();
        return response()->json(['success'=>true,'location'=>BusinessLocation::create($data)], 201);
    }
    public function show(BusinessLocation $businessLocation) { $this->ensureOwns($businessLocation); return response()->json($businessLocation); }
    public function update(Request $req, BusinessLocation $businessLocation) {
        $this->ensureOwns($businessLocation);
        $businessLocation->update($req->validate(['name'=>'required|string|max:191','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','phone'=>'nullable|string|max:30','status'=>'in:active,inactive']));
        return response()->json(['success'=>true,'location'=>$businessLocation]);
    }
    public function destroy(BusinessLocation $businessLocation) { $this->ensureOwns($businessLocation); $businessLocation->delete(); return response()->json(['success'=>true]); }
}
