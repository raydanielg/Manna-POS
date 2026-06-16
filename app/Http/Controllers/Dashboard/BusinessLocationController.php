<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\BusinessLocation;
use Illuminate\Http\Request;
class BusinessLocationController extends Controller {
    public function index(Request $req) {
        $q = BusinessLocation::query();
        if ($req->search) $q->where('name','like',"%{$req->search}%")->orWhere('city','like',"%{$req->search}%");
        if ($req->status) $q->where('status',$req->status);
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','phone'=>'nullable|string|max:30','status'=>'in:active,inactive']);
        return response()->json(['success'=>true,'location'=>BusinessLocation::create($data)], 201);
    }
    public function show(BusinessLocation $businessLocation) { return response()->json($businessLocation); }
    public function update(Request $req, BusinessLocation $businessLocation) {
        $businessLocation->update($req->validate(['name'=>'required|string|max:191','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','phone'=>'nullable|string|max:30','status'=>'in:active,inactive']));
        return response()->json(['success'=>true,'location'=>$businessLocation]);
    }
    public function destroy(BusinessLocation $businessLocation) { $businessLocation->delete(); return response()->json(['success'=>true]); }
}
