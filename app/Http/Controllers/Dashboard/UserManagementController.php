<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserManagementController extends Controller {
    public function index(Request $req) {
        $bizId = $this->currentBusinessId();
        $q = User::where(function($sq) use ($bizId) { $sq->where('id', $bizId)->orWhere('owner_id', $bizId); });
        if ($req->search) $q->where(function($sq) use($req){ $sq->where("name","like","%{$req->search}%")->orWhere("email","like","%{$req->search}%"); });
        if ($req->role) $q->where("role",$req->role);
        if ($req->status) $q->where("status",$req->status);
        return response()->json($q->latest()->get()->map(fn($u) => ["id"=>$u->id,"name"=>$u->name,"email"=>$u->email,"role"=>$u->role,"status"=>$u->status,"created_at"=>$u->created_at]));
    }
    public function store(Request $req) {
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"required|email|unique:users,email","password"=>"required|string|min:8","role"=>"in:admin,manager,cashier,user"]);
        $data["password"] = Hash::make($data["password"]);
        $data["owner_id"] = auth()->user()->owner_id ?? auth()->id();
        $u = User::create($data);
        return response()->json(["success"=>true,"user"=>["id"=>$u->id,"name"=>$u->name,"email"=>$u->email,"role"=>$u->role]], 201);
    }
    public function show(User $user) {
        $bizId = $this->currentBusinessId();
        if ($user->id != $bizId && $user->owner_id != $bizId) abort(403);
        return response()->json(["id"=>$user->id,"name"=>$user->name,"email"=>$user->email,"role"=>$user->role]);
    }
    public function update(Request $req, User $user) {
        $bizId = $this->currentBusinessId();
        if ($user->id != $bizId && $user->owner_id != $bizId) abort(403);
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"required|email|unique:users,email,{$user->id}","role"=>"in:admin,manager,cashier,user","password"=>"nullable|string|min:8"]);
        if (!empty($data["password"])) $data["password"] = Hash::make($data["password"]);
        else unset($data["password"]);
        $user->update($data);
        return response()->json(["success"=>true,"user"=>["id"=>$user->id,"name"=>$user->name,"email"=>$user->email,"role"=>$user->role]]);
    }
    public function destroy(User $user) {
        $bizId = $this->currentBusinessId();
        if ($user->id != $bizId && $user->owner_id != $bizId) abort(403);
        if ($user->id === auth()->id()) return response()->json(["success"=>false,"message"=>"Cannot delete yourself"], 422);
        $user->delete();
        return response()->json(["success"=>true]);
    }
    public function profile() {
        $user = auth()->user();
        return view("dashboard.profile", compact("user"));
    }
    public function updateProfile(Request $req) {
        $user = auth()->user();
        $data = $req->validate(["name"=>"required|string|max:191","email"=>"required|email|unique:users,email,{$user->id}","phone"=>"nullable|string|max:30","current_password"=>"required_with:new_password","new_password"=>"nullable|string|min:8|confirmed"]);
        if (!empty($req->new_password)) {
            if (!Hash::check($req->current_password, $user->password)) {
                return response()->json(["success"=>false,"message"=>"Current password is incorrect"], 422);
            }
            $data["password"] = Hash::make($req->new_password);
        }
        unset($data["current_password"], $data["new_password"], $data["new_password_confirmation"]);
        $user->update($data);
        return response()->json(["success"=>true,"user"=>$user]);
    }
}
