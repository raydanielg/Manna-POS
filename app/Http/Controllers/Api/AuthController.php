<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function login(Request $req) {
        $req->validate(['email'=>'required|email','password'=>'required']);
        $user = User::where('email',$req->email)->first();
        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json(['message'=>'Credentials do not match our records','errors'=>['email'=>['Invalid email or password.']]], 422);
        }
        $user->tokens()->where('name','mannaPOS-mobile')->delete();
        $token = $user->createToken('mannaPOS-mobile')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$this->userArr($user)]);
    }
    public function register(Request $req) {
        $data = $req->validate(['name'=>'required|string|max:191','email'=>'required|email|unique:users','password'=>'required|min:8']);
        $user = User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password']),'role'=>'user']);
        $token = $user->createToken('mannaPOS-mobile')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$this->userArr($user)], 201);
    }
    public function logout(Request $req) {
        $req->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out successfully']);
    }
    public function user(Request $req) {
        return response()->json($this->userArr($req->user()));
    }
    public function updateProfile(Request $req) {
        $user = $req->user();
        $data = $req->validate(['name'=>'required|string|max:191','email'=>'required|email|unique:users,email,'.$user->id,'password'=>'nullable|min:8']);
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);
        $user->save();
        return response()->json($this->userArr($user));
    }
    private function userArr(User $u): array {
        return ['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role??'user','created_at'=>$u->created_at];
    }
}