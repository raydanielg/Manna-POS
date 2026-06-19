<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        // Register gates for every permission
        $permissions = [
            'dashboard.view',
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.approve',
            'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete', 'purchases.approve',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete', 'inventory.approve',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.approve',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'reports.view', 'reports.export',
            'settings.view', 'settings.edit',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'banking.view', 'banking.create', 'banking.edit', 'banking.delete',
            'microfinance.view', 'microfinance.create', 'microfinance.edit', 'microfinance.delete',
            'payroll.view', 'payroll.create', 'payroll.edit', 'payroll.delete',
            'manufacturing.view', 'manufacturing.create', 'manufacturing.edit', 'manufacturing.delete',
            'crm.view', 'crm.create', 'crm.edit', 'crm.delete',
            'approvals.view', 'approvals.approve',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        // Wildcard for owners
        Gate::before(function ($user) {
            if ($user->isOwner()) {
                return true;
            }
        });
    }
}
