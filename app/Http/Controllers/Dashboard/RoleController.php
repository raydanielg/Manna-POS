<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
class RoleController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = Role::query();
            if ($req->search) $q->where("name","like","%{$req->search}%");
            return response()->json($q->latest()->get());
        }
        return view("dashboard.user-management.roles");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191|unique:roles,name","description"=>"nullable|string"]);
        return response()->json(["success"=>true,"role"=>Role::create($data)], 201);
    }
    public function show(Role $role) { return response()->json($role); }
    public function update(Request $req, Role $role) {
        $role->update($req->validate(["name"=>"required|string|max:191|unique:roles,name,{$role->id}","description"=>"nullable|string"]));
        return response()->json(["success"=>true,"role"=>$role]);
    }
    public function destroy(Role $role) { $role->delete(); return response()->json(["success"=>true]); }
}
