<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller {

    const DEFAULT_ROLES = [
        'admin' => [
            'description' => 'Full system access — can manage everything including users, roles, and system settings.',
            'permissions'  => [
                'dashboard.view',
                'sales.view','sales.create','sales.edit','sales.delete','sales.pos',
                'purchases.view','purchases.create','purchases.edit','purchases.delete',
                'inventory.view','inventory.create','inventory.edit','inventory.delete',
                'customers.view','customers.create','customers.manage',
                'suppliers.view','suppliers.manage',
                'reports.view','reports.export',
                'expenses.view','expenses.create','expenses.manage',
                'stock.transfer','stock.adjust',
                'settings.view','settings.manage',
                'users.view','users.manage',
                'roles.manage',
                'plans.view','plans.manage',
            ],
        ],
        'manager' => [
            'description' => 'Manages day-to-day operations — sales, inventory, staff, and reports. Cannot change system settings or roles.',
            'permissions'  => [
                'dashboard.view',
                'sales.view','sales.create','sales.edit','sales.delete','sales.pos',
                'purchases.view','purchases.create','purchases.edit','purchases.delete',
                'inventory.view','inventory.create','inventory.edit','inventory.delete',
                'customers.view','customers.create','customers.manage',
                'suppliers.view','suppliers.manage',
                'reports.view','reports.export',
                'expenses.view','expenses.create','expenses.manage',
                'stock.transfer','stock.adjust',
                'settings.view',
                'users.view',
            ],
        ],
        'cashier' => [
            'description' => 'Operates the POS terminal, processes sales, and handles customers. Limited to their assigned store location.',
            'permissions'  => [
                'dashboard.view',
                'sales.view','sales.create','sales.pos',
                'customers.view','customers.create',
                'inventory.view',
            ],
        ],
        'user' => [
            'description' => 'Basic access — can view the dashboard only. No operational permissions.',
            'permissions'  => ['dashboard.view'],
        ],
    ];

    public function index(Request $req) {
        $roles = Role::query();
        if ($req->search) $roles->where('name','like',"%{$req->search}%");
        $list = $roles->latest()->get()->map(function($r) {
            $r->user_count = User::where('role', $r->name)->count();
            return $r;
        });
        return response()->json($list);
    }

    public function store(Request $req) {
        $data = $req->validate([
            'name'        => 'required|string|max:191|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);
        $data['permissions'] = $data['permissions'] ?? [];
        $role = Role::create($data);
        $role->user_count = 0;
        return response()->json(['success' => true, 'role' => $role], 201);
    }

    public function show(Role $role) {
        $role->user_count = User::where('role', $role->name)->count();
        return response()->json($role);
    }

    public function update(Request $req, Role $role) {
        $data = $req->validate([
            'name'        => 'required|string|max:191|unique:roles,name,'.$role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);
        $data['permissions'] = $data['permissions'] ?? [];
        $role->update($data);
        $role->user_count = User::where('role', $role->name)->count();
        return response()->json(['success' => true, 'role' => $role]);
    }

    public function destroy(Role $role) {
        $systemRoles = array_keys(self::DEFAULT_ROLES);
        if (in_array(strtolower($role->name), $systemRoles)) {
            return response()->json(['success' => false, 'message' => 'Cannot delete a system default role.'], 422);
        }
        if (User::where('role', $role->name)->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete role that has assigned users.'], 422);
        }
        $role->delete();
        return response()->json(['success' => true]);
    }

    public function seedDefaults() {
        $created = 0;
        foreach (self::DEFAULT_ROLES as $name => $data) {
            $role = Role::firstOrNew(['name' => $name]);
            $role->description = $data['description'];
            $role->permissions = $data['permissions'];
            $role->save();
            $created++;
        }
        return response()->json(['success' => true, 'message' => "Default roles synced ({$created} roles)."]);
    }

    public function permissionList() {
        $groups = [
            'Dashboard'  => ['dashboard.view'],
            'Sales'      => ['sales.view','sales.create','sales.edit','sales.delete','sales.pos'],
            'Purchases'  => ['purchases.view','purchases.create','purchases.edit','purchases.delete'],
            'Inventory'  => ['inventory.view','inventory.create','inventory.edit','inventory.delete'],
            'Customers'  => ['customers.view','customers.create','customers.manage'],
            'Suppliers'  => ['suppliers.view','suppliers.manage'],
            'Reports'    => ['reports.view','reports.export'],
            'Expenses'   => ['expenses.view','expenses.create','expenses.manage'],
            'Stock'      => ['stock.transfer','stock.adjust'],
            'Settings'   => ['settings.view','settings.manage'],
            'Users'      => ['users.view','users.manage'],
            'Roles'      => ['roles.manage'],
            'Plans'      => ['plans.view','plans.manage'],
        ];
        return response()->json($groups);
    }
}
