<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function index()   { return view('admin.users.index'); }
    public function create()  { return view('admin.users.create'); }
    public function blocked() { return view('admin.users.blocked'); }

    public function list(Request $req)
    {
        $q = User::query();
        if ($req->search) {
            $q->where(function ($q2) use ($req) {
                $q2->where('name', 'like', "%{$req->search}%")
                   ->orWhere('email', 'like', "%{$req->search}%")
                   ->orWhere('phone', 'like', "%{$req->search}%")
                   ->orWhere('business_name', 'like', "%{$req->search}%");
            });
        }
        if ($req->role)   $q->where('role', $req->role);
        if ($req->status) $q->where('status', $req->status);
        $perPage = (int)($req->per_page ?? 20);
        return response()->json(
            $q->latest()->paginate($perPage)->through(function ($u) {
                $sub = $u->subscriptions()
                    ->with('plan:id,name')
                    ->whereIn('status', ['active', 'trial'])
                    ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
                    ->latest()->first();
                return [
                    'id'               => $u->id,
                    'name'             => $u->name,
                    'email'            => $u->email,
                    'phone'            => $u->phone,
                    'role'             => $u->role,
                    'status'           => $u->status ?? 'active',
                    'business_name'    => $u->business_name,
                    'business_type'    => $u->business_type,
                    'business_country' => $u->business_country,
                    'currency'         => $u->currency,
                    'block_reason'     => $u->block_reason,
                    'blocked_at'       => $u->blocked_at,
                    'created_at'       => $u->created_at,
                    'subscription'     => $sub ? [
                        'status'     => $sub->status,
                        'plan_name'  => $sub->plan?->name,
                        'expires_at' => $sub->expires_at,
                    ] : null,
                ];
            })
        );
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'             => 'required|string|max:191',
            'email'            => 'required|email|max:191|unique:users,email',
            'password'         => 'required|string|min:8',
            'phone'            => 'required|string|max:30',
            'role'             => 'nullable|in:user,admin',
            'status'           => 'nullable|in:active,inactive,blocked',
            'business_name'    => 'nullable|string|max:191',
            'business_type'    => 'nullable|string|max:100',
            'business_country' => 'nullable|string|max:100',
            'currency'         => 'nullable|string|max:10',
        ]);
        $data['password']        = bcrypt($data['password']);
        $data['role']            = $data['role'] ?? 'user';
        $data['status']          = $data['status'] ?? 'active';
        $data['setup_completed'] = true;
        return response()->json(['success' => true, 'user' => User::create($data)], 201);
    }

    public function show(User $user)
    {
        $sub = $user->subscriptions()
            ->with('plan:id,name')
            ->whereIn('status', ['active', 'trial'])
            ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->latest()->first();
        return response()->json(array_merge($user->toArray(), [
            'subscription' => $sub ? [
                'status'     => $sub->status,
                'plan_name'  => $sub->plan?->name,
                'expires_at' => $sub->expires_at,
            ] : null,
        ]));
    }

    public function update(Request $req, User $user)
    {
        $data = $req->validate([
            'name'             => 'nullable|string|max:191',
            'email'            => "nullable|email|max:191|unique:users,email,{$user->id}",
            'password'         => 'nullable|string|min:8',
            'phone'            => 'nullable|string|max:30',
            'role'             => 'nullable|in:user,admin',
            'status'           => 'nullable|in:active,inactive,blocked',
            'business_name'    => 'nullable|string|max:191',
            'business_type'    => 'nullable|string|max:100',
            'business_country' => 'nullable|string|max:100',
            'currency'         => 'nullable|string|max:10',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json(['success' => true, 'user' => $user->fresh()]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function block(Request $req, User $user)
    {
        $user->update([
            'status'       => 'blocked',
            'block_reason' => $req->reason ?? 'No reason provided',
            'blocked_at'   => now(),
        ]);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function unblock(User $user)
    {
        $user->update([
            'status'       => 'active',
            'block_reason' => null,
            'blocked_at'   => null,
        ]);
        return response()->json(['success' => true, 'user' => $user]);
    }
}
