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
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete', 'expenses.approve',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'crm.view', 'crm.create', 'crm.edit', 'crm.delete',
            'stock_transfers.view', 'stock_transfers.create', 'stock_transfers.edit', 'stock_transfers.delete',
            'stock_adjustments.view', 'stock_adjustments.create', 'stock_adjustments.edit', 'stock_adjustments.delete',
            'banking.view', 'banking.create', 'banking.edit', 'banking.delete',
            'microfinance.view', 'microfinance.create', 'microfinance.edit', 'microfinance.delete',
            'sms.view', 'sms.create', 'sms.edit', 'sms.delete',
            'files.view', 'files.create', 'files.edit', 'files.delete',
            'payroll.view', 'payroll.create', 'payroll.edit', 'payroll.delete',
            'manufacturing.view', 'manufacturing.create', 'manufacturing.edit', 'manufacturing.delete',
            'reports.view', 'reports.export',
            'settings.view', 'settings.edit',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
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
