<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserManagementController extends Controller {
    public function index(Request $req) {
        if ($req->ajax()) {
            $q = User::query();
            if ($req->search) $q->where("name","like","%{$req->search}%")->orWhere("email","like","%{$req->search}%");
            return response()->json($q->latest()->get()->map(fn($u) => ["id"=>$u->id,"name"=>$u->name,"email"=>$u->email,"role"=>$u->role,"created_at"=>$u->created_at]));
        }
        return view("dashboard.user-management.users");
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"required|email|unique:users,email","password"=>"required|string|min:8","role"=>"in:admin,manager,cashier,user"]);
        $data["password"] = Hash::make($data["password"]);
        $u = User::create($data);
        return response()->json(["success"=>true,"user"=>["id"=>$u->id,"name"=>$u->name,"email"=>$u->email,"role"=>$u->role]], 201);
    }
    public function show(User $user) { return response()->json(["id"=>$user->id,"name"=>$user->name,"email"=>$user->email,"role"=>$user->role]); }
    public function update(Request $req, User $user) {
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"required|email|unique:users,email,{$user->id}","role"=>"in:admin,manager,cashier,user","password"=>"nullable|string|min:8"]);
        if (!empty($data["password"])) $data["password"] = Hash::make($data["password"]);
        else unset($data["password"]);
        $user->update($data);
        return response()->json(["success"=>true,"user"=>["id"=>$user->id,"name"=>$user->name,"email"=>$user->email,"role"=>$user->role]]);
    }
    public function destroy(User $user) {
        if ($user->id === auth()->id()) return response()->json(["success"=>false,"message"=>"Cannot delete yourself"], 422);
        $user->delete();
        return response()->json(["success"=>true]);
    }
}
