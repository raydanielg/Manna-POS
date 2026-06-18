<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
class RoleController extends Controller {
    public function index(Request $req) {
        $q = Role::forCurrentUser($this->currentBusinessId());
        if ($req->search) $q->where("name","like","%{$req->search}%");
        return response()->json($q->latest()->get());
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191|unique:roles,name","description"=>"nullable|string"]);
        $data["created_by"] = $this->currentBusinessId();
        return response()->json(["success"=>true,"role"=>Role::create($data)], 201);
    }
    public function show(Role $role) { $this->ensureOwns($role); return response()->json($role); }
    public function update(Request $req, Role $role) {
        $this->ensureOwns($role);
        $role->update($req->validate(["name"=>"required|string|max:191|unique:roles,name,{$role->id}","description"=>"nullable|string"]));
        return response()->json(["success"=>true,"role"=>$role]);
    }
    public function destroy(Role $role) { $this->ensureOwns($role); $role->delete(); return response()->json(["success"=>true]); }
}
