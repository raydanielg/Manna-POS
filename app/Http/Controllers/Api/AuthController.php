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
        if (!in_array($user->role, ['user', 'admin'])) {
            return response()->json(['message'=>'You do not have permission to access this system','errors'=>['email'=>['Access denied. Only users and admins can log in.']]], 403);
        }
        $user->tokens()->where('name','mannaPOS-mobile')->delete();
        $token = $user->createToken('mannaPOS-mobile')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$this->userArr($user)]);
    }

    public function register(Request $req) {
        $data = $req->validate([
            'name'              => 'required|string|max:191',
            'email'             => 'required|email|unique:users',
            'password'          => 'required|min:8',
            'phone'             => 'required|string|max:20',
            'business_name'     => 'required|string|max:191',
            'business_type'     => 'required|in:retail,wholesale,restaurant,service,other',
            'business_city'     => 'required|string|max:100',
            'business_country'  => 'nullable|string|max:100',
            'business_address'  => 'nullable|string|max:255',
            'currency'          => 'nullable|string|max:10',
            'tax_percentage'    => 'nullable|numeric|min:0|max:100',
            'fiscal_year_start' => 'nullable|string|max:20',
        ]);
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'phone'             => $data['phone'],
            'role'              => 'user',
            'business_name'     => $data['business_name'],
            'business_type'     => $data['business_type'],
            'business_city'     => $data['business_city'],
            'business_country'  => $data['business_country'] ?? 'Tanzania',
            'business_address'  => $data['business_address'] ?? null,
            'currency'          => $data['currency'] ?? 'TZS',
            'tax_percentage'    => $data['tax_percentage'] ?? 18.00,
            'fiscal_year_start' => $data['fiscal_year_start'] ?? 'January',
        ]);
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
        $data = $req->validate([
            'name'              => 'required|string|max:191',
            'email'             => 'required|email|unique:users,email,'.$user->id,
            'current_password'  => 'nullable|string',
            'password'          => 'nullable|min:8|confirmed',
            'phone'             => 'nullable|string|max:20',
            'business_name'     => 'nullable|string|max:191',
            'business_type'     => 'nullable|in:retail,wholesale,restaurant,service,other',
            'business_city'     => 'nullable|string|max:100',
            'business_country'  => 'nullable|string|max:100',
            'business_address'  => 'nullable|string|max:255',
            'currency'          => 'nullable|string|max:10',
            'tax_percentage'    => 'nullable|numeric|min:0|max:100',
            'fiscal_year_start' => 'nullable|string|max:20',
        ]);
        if (!empty($data['current_password']) && !Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message'=>'Current password is incorrect','errors'=>['current_password'=>['Incorrect password']]], 422);
        }
        $fields = ['name','email','phone','business_name','business_type','business_address','business_city','business_country','currency','tax_percentage','fiscal_year_start'];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) $user->$f = $data[$f];
        }
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);
        $user->save();
        return response()->json($this->userArr($user));
    }

    private function userArr(User $u): array {
        return [
            'id'               => $u->id,
            'name'             => $u->name,
            'email'            => $u->email,
            'phone'            => $u->phone,
            'role'             => $u->role ?? 'user',
            'business_name'    => $u->business_name,
            'business_type'    => $u->business_type,
            'business_address' => $u->business_address,
            'business_city'    => $u->business_city,
            'business_country' => $u->business_country ?? 'Tanzania',
            'currency'         => $u->currency ?? 'TZS',
            'tax_percentage'   => (float)($u->tax_percentage ?? 18.00),
            'fiscal_year_start'=> $u->fiscal_year_start ?? 'January',
            'created_at'       => $u->created_at,
        ];
    }
}