<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function list(Request $req)
    {
        try {
            $q = User::query();
            if ($req->search) {
                $q->where(function($q) use ($req) {
                    $q->where('name', 'like', "%{$req->search}%")
                      ->orWhere('email', 'like', "%{$req->search}%")
                      ->orWhere('phone', 'like', "%{$req->search}%");
                });
            }
            if ($req->role) $q->where('role', $req->role);
            if ($req->status) $q->where('status', $req->status);
            return response()->json($q->latest()->paginate(20)->through(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $u->role,
                'status' => $u->status,
                'email_verified_at' => $u->email_verified_at?->format('Y-m-d H:i:s'),
                'created_at' => $u->created_at->format('Y-m-d H:i:s'),
            ]));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $req)
    {
        try {
            $data = $req->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'role' => 'nullable|in:user,admin',
                'status' => 'nullable|in:active,inactive,blocked',
            ]);
            $data['password'] = bcrypt($data['password']);
            $data['role'] = $data['role'] ?? 'user';
            $data['status'] = $data['status'] ?? 'active';
            $user = User::create($data);
            return response()->json(['success' => true, 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(User $user)
    {
        try {
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $req, User $user)
    {
        try {
            $data = $req->validate([
                'name' => 'nullable|string|max:255',
                'email' => "nullable|email|max:255|unique:users,email,{$user->id}",
                'password' => 'nullable|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'role' => 'nullable|in:user,admin',
                'status' => 'nullable|in:active,inactive,blocked',
            ]);
            if (isset($data['password'])) $data['password'] = bcrypt($data['password']);
            $user->update($data);
            return response()->json(['success' => true, 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function blocked()
    {
        return view('admin.users.blocked');
    }

    public function block(User $user)
    {
        try {
            $user->update(['status' => 'blocked']);
            return response()->json(['success' => true, 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function unblock(User $user)
    {
        try {
            $user->update(['status' => 'active']);
            return response()->json(['success' => true, 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
