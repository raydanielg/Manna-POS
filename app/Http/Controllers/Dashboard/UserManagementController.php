<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessLocation;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller {

    public function index(Request $req) {
        $q = User::with('location');
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('name','like',"%{$req->search}%")
                  ->orWhere('email','like',"%{$req->search}%");
            });
        }
        if ($req->role) $q->where('role', $req->role);
        if ($req->status === 'active')   $q->where('is_active', true);
        if ($req->status === 'inactive') $q->where('is_active', false);
        if ($req->location_id) $q->where('location_id', $req->location_id);

        return response()->json($q->latest()->get()->map(fn($u) => $this->fmt($u)));
    }

    public function store(Request $req) {
        $data = $req->validate([
            'name'        => 'required|string|max:191',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8',
            'role'        => 'in:admin,manager,cashier,user',
            'location_id' => 'nullable|exists:business_locations,id',
            'is_active'   => 'boolean',
            'notes'       => 'nullable|string',
        ]);
        $data['password']  = Hash::make($data['password']);
        $data['owner_id']  = auth()->id();
        $data['is_active'] = $data['is_active'] ?? true;
        $u = User::create($data);
        return response()->json(['success' => true, 'user' => $this->fmt($u->load('location'))], 201);
    }

    public function show(User $user) {
        return response()->json($this->fmt($user->load('location')));
    }

    public function update(Request $req, User $user) {
        $data = $req->validate([
            'name'        => 'required|string|max:191',
            'email'       => 'required|email|unique:users,email,'.$user->id,
            'role'        => 'in:admin,manager,cashier,user',
            'password'    => 'nullable|string|min:8',
            'location_id' => 'nullable|exists:business_locations,id',
            'is_active'   => 'boolean',
            'notes'       => 'nullable|string',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json(['success' => true, 'user' => $this->fmt($user->load('location'))]);
    }

    public function destroy(User $user) {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete your own account.'], 422);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function stats() {
        return response()->json([
            'total'     => User::count(),
            'active'    => User::where('is_active', true)->count(),
            'inactive'  => User::where('is_active', false)->count(),
            'admin'     => User::where('role', 'admin')->count(),
            'manager'   => User::where('role', 'manager')->count(),
            'cashier'   => User::where('role', 'cashier')->count(),
            'user'      => User::where('role', 'user')->count(),
        ]);
    }

    public function profile() {
        return view('dashboard.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $req) {
        $user = auth()->user();
        $data = $req->validate([
            'name'             => 'required|string|max:191',
            'email'            => 'required|email|unique:users,email,'.$user->id,
            'phone'            => 'nullable|string|max:30',
            'current_password' => 'required_with:new_password',
            'new_password'     => 'nullable|string|min:8|confirmed',
        ]);
        if (!empty($req->new_password)) {
            if (!Hash::check($req->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 422);
            }
            $data['password'] = Hash::make($req->new_password);
        }
        unset($data['current_password'], $data['new_password'], $data['new_password_confirmation']);
        $user->update($data);
        return response()->json(['success' => true, 'user' => $user]);
    }

    private function fmt(User $u): array {
        return [
            'id'          => $u->id,
            'name'        => $u->name,
            'email'       => $u->email,
            'role'        => $u->role ?? 'user',
            'is_active'   => (bool)($u->is_active ?? true),
            'location_id' => $u->location_id,
            'location'    => $u->location ? ['id' => $u->location->id, 'name' => $u->location->name, 'city' => $u->location->city] : null,
            'notes'       => $u->notes,
            'created_at'  => $u->created_at,
        ];
    }
}
