<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffApiController extends Controller {
    public function index(Request $req) {
        $staff = User::where('owner_id', $req->user()->id)
            ->orderBy('name')
            ->get();
        return response()->json($staff->map(fn($u) => $this->userArr($u)));
    }

    public function store(Request $req) {
        $data = $req->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:users',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,manager,cashier,viewer',
            'password' => 'required|string|min:6',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['owner_id'] = $req->user()->id;
        // Copy business info from owner
        $owner = $req->user();
        $data['business_name'] = $owner->business_name;
        $data['business_type'] = $owner->business_type;
        $data['business_city'] = $owner->business_city;
        $data['business_country'] = $owner->business_country;
        $data['currency'] = $owner->currency ?? 'TZS';
        $data['tax_percentage'] = $owner->tax_percentage ?? 18;
        $user = User::create($data);
        return response()->json($this->userArr($user), 201);
    }

    public function show(Request $req, $id) {
        $staff = User::where('id', $id)->where('owner_id', $req->user()->id)->firstOrFail();
        return response()->json($this->userArr($staff));
    }

    public function update(Request $req, $id) {
        $staff = User::where('id', $id)->where('owner_id', $req->user()->id)->firstOrFail();
        $data = $req->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,'.$staff->id,
            'phone' => 'nullable|string|max:20',
            'role'  => 'required|in:admin,manager,cashier,viewer',
        ]);
        if ($req->filled('password')) {
            $req->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($req->password);
        }
        $staff->update($data);
        return response()->json($this->userArr($staff->fresh()));
    }

    public function destroy(Request $req, $id) {
        $staff = User::where('id', $id)->where('owner_id', $req->user()->id)->firstOrFail();
        $staff->delete();
        return response()->json(['message' => 'Staff member removed']);
    }

    private function userArr(User $u): array {
        return [
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'role'  => $u->role ?? 'cashier',
            'business_name' => $u->business_name,
            'created_at' => $u->created_at?->toDateString(),
        ];
    }
}