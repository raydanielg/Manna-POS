<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $latestBlogs = \App\Models\Blog::where('is_published', true)
        ->orderByDesc('published_at')
        ->take(3)
        ->get();
    return view('landing', compact('latestBlogs'));
});

// Product pages
Route::get('/features', function () {
    return view('product.features');
});
Route::get('/pricing', function () {
    return view('product.pricing');
});
Route::get('/integrations', function () {
    return view('product.integrations');
});
Route::get('/updates', function () {
    return view('product.updates');
});

// Company pages
Route::get('/about', function () {
    return view('company.about');
});
Route::get('/contact', function () {
    return view('company.contact');
});
Route::get('/careers', function () {
    return view('company.careers');
});

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{blog}/comment', [BlogController::class, 'storeComment'])->name('blog.comment');

// Support pages
Route::get('/help', function () {
    return view('support.help');
});
Route::get('/documentation', function () {
    return view('support.documentation');
});
Route::get('/api', function () {
    return view('support.api');
});
Route::get('/status', function () {
    return view('support.status');
});

// Legal pages
Route::get('/privacy', function () {
    return view('legal.privacy');
});
Route::get('/terms', function () {
    return view('legal.terms');
});
Route::get('/cookies', function () {
    return view('legal.cookies');
});
Route::get('/gdpr', function () {
    return view('legal.gdpr');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'user.dashboard'])->name('home');

Route::prefix('dashboard')->middleware(['auth', 'user.dashboard'])->group(function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Sales Routes
    Route::get('/sales/pos', function () { return view('dashboard.sales.pos'); })->name('dashboard.sales.pos');
    Route::get('/sales/transactions', function () { return view('dashboard.sales.transactions'); })->name('dashboard.sales.transactions');
    Route::get('/sales/receipts', function () { return view('dashboard.sales.receipts'); })->name('dashboard.sales.receipts');

    // Inventory Routes
    Route::get('/inventory/products', function () { return view('dashboard.inventory.products'); })->name('dashboard.inventory.products');
    Route::get('/inventory/categories', function () { return view('dashboard.inventory.categories'); })->name('dashboard.inventory.categories');
    Route::get('/inventory/stock', function () { return view('dashboard.inventory.stock'); })->name('dashboard.inventory.stock');
    Route::get('/inventory/suppliers', function () { return view('dashboard.inventory.suppliers'); })->name('dashboard.inventory.suppliers');

    // Customers Routes
    Route::get('/customers/all', function () { return view('dashboard.customers.all'); })->name('dashboard.customers.all');
    Route::get('/customers/loyalty', function () { return view('dashboard.customers.loyalty'); })->name('dashboard.customers.loyalty');

    // Reports Routes
    Route::get('/reports/sales', function () { return view('dashboard.reports.sales'); })->name('dashboard.reports.sales');
    Route::get('/reports/inventory', function () { return view('dashboard.reports.inventory'); })->name('dashboard.reports.inventory');

    // Settings Route
    Route::get('/settings', function () { return view('dashboard.settings'); })->name('dashboard.settings');

    // User Management Routes
    Route::get('/user-management/users', function () { return view('dashboard.user-management.users'); })->name('dashboard.user-management.users');
    Route::get('/user-management/roles', function () { return view('dashboard.user-management.roles'); })->name('dashboard.user-management.roles');
    Route::get('/user-management/sales-commission-agents', function () { return view('dashboard.user-management.sales-commission-agents'); })->name('dashboard.user-management.sales-commission-agents');

    // Profile Routes (controllers)
    Route::get('/profile', [App\Http\Controllers\Dashboard\UserManagementController::class, 'profile'])->name('dashboard.profile');
    Route::put('/profile', [App\Http\Controllers\Dashboard\UserManagementController::class, 'updateProfile'])->name('dashboard.profile.update');

    // Contacts Routes
    Route::get('/contacts/suppliers', function () { return view('dashboard.contacts.suppliers'); })->name('dashboard.contacts.suppliers');
    Route::get('/contacts/customers', [App\Http\Controllers\Dashboard\CustomerController::class, 'index'])->name('dashboard.contacts.customers');
    Route::get('/contacts/customer-groups', function () { return view('dashboard.contacts.customer-groups'); })->name('dashboard.contacts.customer-groups');
    Route::get('/contacts/import-contacts', function () { return view('dashboard.contacts.import-contacts'); })->name('dashboard.contacts.import-contacts');

    // Products Routes
    Route::get('/inventory/list-products', function () { return view('dashboard.inventory.list-products'); })->name('dashboard.inventory.list-products');
    Route::get('/inventory/add-product', function () { return view('dashboard.inventory.add-product'); })->name('dashboard.inventory.add-product');
    Route::get('/inventory/update-price', function () { return view('dashboard.inventory.update-price'); })->name('dashboard.inventory.update-price');
    Route::get('/inventory/print-labels', function () { return view('dashboard.inventory.print-labels'); })->name('dashboard.inventory.print-labels');
    Route::get('/inventory/variations', function () { return view('dashboard.inventory.variations'); })->name('dashboard.inventory.variations');
    Route::get('/inventory/import-products', function () { return view('dashboard.inventory.import-products'); })->name('dashboard.inventory.import-products');
    Route::get('/inventory/import-opening-stock', function () { return view('dashboard.inventory.import-opening-stock'); })->name('dashboard.inventory.import-opening-stock');
    Route::get('/inventory/selling-price-group', function () { return view('dashboard.inventory.selling-price-group'); })->name('dashboard.inventory.selling-price-group');
    Route::get('/inventory/units', function () { return view('dashboard.inventory.units'); })->name('dashboard.inventory.units');
    Route::get('/inventory/product-categories', function () { return view('dashboard.inventory.product-categories'); })->name('dashboard.inventory.product-categories');
    Route::get('/inventory/brands', function () { return view('dashboard.inventory.brands'); })->name('dashboard.inventory.brands');
    Route::get('/inventory/warranties', function () { return view('dashboard.inventory.warranties'); })->name('dashboard.inventory.warranties');

    // Purchases Routes
    Route::get('/purchases/list-purchases', function () { return view('dashboard.purchases.list-purchases'); })->name('dashboard.purchases.list-purchases');
    Route::get('/purchases/add-purchase', function () { return view('dashboard.purchases.add-purchase'); })->name('dashboard.purchases.add-purchase');
    Route::get('/purchases/list-purchase-return', function () { return view('dashboard.purchases.list-purchase-return'); })->name('dashboard.purchases.list-purchase-return');

    // Sell Routes
    Route::get('/sell/all-sales', function () { return view('dashboard.sell.all-sales'); })->name('dashboard.sell.all-sales');
    Route::get('/sell/add-sale', function () { return view('dashboard.sell.add-sale'); })->name('dashboard.sell.add-sale');
    Route::get('/sell/list-pos', function () { return view('dashboard.sell.list-pos'); })->name('dashboard.sell.list-pos');
    Route::get('/sell/pos', function () { return view('dashboard.sell.pos'); })->name('dashboard.sell.pos');
    Route::get('/sell/add-draft', function () { return view('dashboard.sell.add-draft'); })->name('dashboard.sell.add-draft');
    Route::get('/sell/list-drafts', function () { return view('dashboard.sell.list-drafts'); })->name('dashboard.sell.list-drafts');
    Route::get('/sell/add-quotation', function () { return view('dashboard.sell.add-quotation'); })->name('dashboard.sell.add-quotation');
    Route::get('/sell/list-quotations', function () { return view('dashboard.sell.list-quotations'); })->name('dashboard.sell.list-quotations');
    Route::get('/sell/list-sell-return', function () { return view('dashboard.sell.list-sell-return'); })->name('dashboard.sell.list-sell-return');
    Route::get('/sell/shipments', function () { return view('dashboard.sell.shipments'); })->name('dashboard.sell.shipments');
    Route::get('/sell/discounts', function () { return view('dashboard.sell.discounts'); })->name('dashboard.sell.discounts');
    Route::get('/sell/import-sales', function () { return view('dashboard.sell.import-sales'); })->name('dashboard.sell.import-sales');

    // Stock Transfer Routes
    Route::get('/stock-transfer/list-stock-transfer', function () { return view('dashboard.stock-transfer.list-stock-transfer'); })->name('dashboard.stock-transfer.list-stock-transfer');
    Route::get('/stock-transfer/add-stock-transfer', function () { return view('dashboard.stock-transfer.add-stock-transfer'); })->name('dashboard.stock-transfer.add-stock-transfer');

    // Stock Adjustment Routes
    Route::get('/stock-adjustment/list-stock-adjustment', function () { return view('dashboard.stock-adjustment.list-stock-adjustment'); })->name('dashboard.stock-adjustment.list-stock-adjustment');
    Route::get('/stock-adjustment/add-stock-adjustment', function () { return view('dashboard.stock-adjustment.add-stock-adjustment'); })->name('dashboard.stock-adjustment.add-stock-adjustment');

    // Expenses Routes
    Route::get('/expenses/list-expenses', function () { return view('dashboard.expenses.list-expenses'); })->name('dashboard.expenses.list-expenses');
    Route::get('/expenses/add-expense', function () { return view('dashboard.expenses.add-expense'); })->name('dashboard.expenses.add-expense');
    Route::get('/expenses/expense-categories', function () { return view('dashboard.expenses.expense-categories'); })->name('dashboard.expenses.expense-categories');

    // Reports Routes
    Route::get('/reports/sales-report', function () { return view('dashboard.reports.sales-report'); })->name('dashboard.reports.sales-report');
    Route::get('/reports/purchase-report', function () { return view('dashboard.reports.purchase-report'); })->name('dashboard.reports.purchase-report');
    Route::get('/reports/inventory-report', function () { return view('dashboard.reports.inventory-report'); })->name('dashboard.reports.inventory-report');
    Route::get('/reports/expense-report', function () { return view('dashboard.reports.expense-report'); })->name('dashboard.reports.expense-report');
    Route::get('/reports/profit-loss-report', function () { return view('dashboard.reports.profit-loss-report'); })->name('dashboard.reports.profit-loss-report');

    // Notification Templates Routes
    Route::get('/notification-templates', function () { return view('dashboard.notification-templates.index'); })->name('dashboard.notification-templates');

    // Settings Routes
    Route::get('/settings/tax-rates', function () { return view('dashboard.settings.tax-rates'); })->name('dashboard.settings.tax-rates');

    // Plan Management Routes
    Route::get('/plan-management/plans', function () { return view('dashboard.plan-management.plans'); })->name('dashboard.plan-management.plans');
    Route::get('/plan-management/subscriptions', function () { return view('dashboard.plan-management.subscriptions'); })->name('dashboard.plan-management.subscriptions');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // User Management
    Route::get('/users', function () {
        return view('dashboard.user-management.users');
    })->name('admin.users');

    Route::get('/roles', function () {
        return view('dashboard.user-management.roles');
    })->name('admin.roles');

    Route::get('/sales-commission-agents', function () {
        return view('dashboard.user-management.sales-commission-agents');
    })->name('admin.sales-commission-agents');

    // Plan Management
    Route::get('/plans', function () {
        return view('dashboard.plan-management.plans');
    })->name('admin.plans');

    Route::get('/subscriptions', function () {
        return view('dashboard.plan-management.subscriptions');
    })->name('admin.subscriptions');

    // Notification Templates
    Route::get('/notification-templates', function () {
        return view('dashboard.notification-templates.index');
    })->name('admin.notification-templates');

    // Settings
    Route::get('/settings/general', function () {
        return view('dashboard.settings.general');
    })->name('admin.settings.general');

    Route::get('/settings/business-location', function () {
        return view('dashboard.settings.business-location');
    })->name('admin.settings.business-location');

    Route::get('/settings/invoice-settings', function () {
        return view('dashboard.settings.invoice-settings');
    })->name('admin.settings.invoice-settings');

    Route::get('/settings/barcode-settings', function () {
        return view('dashboard.settings.barcode-settings');
    })->name('admin.settings.barcode-settings');

    Route::get('/settings/tax-rates', function () {
        return view('dashboard.settings.tax-rates');
    })->name('admin.settings.tax-rates');

    // Reports Dashboard
    Route::get('/reports', function () {
        return view('dashboard.reports.sales-report');
    })->name('admin.reports');

    // ── Staff ──
    Route::get('/staff', [\App\Http\Controllers\Admin\AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/staff/attendance', [\App\Http\Controllers\Admin\AdminStaffController::class, 'attendance'])->name('admin.staff.attendance');
    Route::get('/staff/schedules', [\App\Http\Controllers\Admin\AdminStaffController::class, 'schedules'])->name('admin.staff.schedules');

    // ── Business ──
    Route::get('/business', [\App\Http\Controllers\Admin\AdminBusinessController::class, 'index'])->name('admin.business.index');
    Route::get('/business/categories', [\App\Http\Controllers\Admin\AdminBusinessController::class, 'categories'])->name('admin.business.categories');
    Route::get('/business/verifications', [\App\Http\Controllers\Admin\AdminBusinessController::class, 'verifications'])->name('admin.business.verifications');

    // ── Billing ──
    Route::get('/billing/invoices', [\App\Http\Controllers\Admin\AdminBillingController::class, 'invoices'])->name('admin.billing.invoices');
    Route::get('/billing/payments', [\App\Http\Controllers\Admin\AdminBillingController::class, 'payments'])->name('admin.billing.payments');
    Route::get('/billing/gateways', [\App\Http\Controllers\Admin\AdminBillingController::class, 'gateways'])->name('admin.billing.gateways');

    // ── Communication ──
    Route::get('/communication/email-templates', [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'emailTemplates'])->name('admin.communication.email-templates');
    Route::get('/communication/sms-templates', [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'smsTemplates'])->name('admin.communication.sms-templates');
    Route::get('/communication/announcements', [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'announcements'])->name('admin.communication.announcements');

    // ── Support ──
    Route::get('/support/tickets', [\App\Http\Controllers\Admin\AdminSupportController::class, 'tickets'])->name('admin.support.tickets');

    // ── System ──
    Route::get('/system/config', [\App\Http\Controllers\Admin\AdminSystemController::class, 'config'])->name('admin.system.config');
    Route::get('/system/activity-logs', [\App\Http\Controllers\Admin\AdminSystemController::class, 'activityLogs'])->name('admin.system.activity-logs');
    Route::get('/system/backups', [\App\Http\Controllers\Admin\AdminSystemController::class, 'backups'])->name('admin.system.backups');
    Route::get('/system/health', [\App\Http\Controllers\Admin\AdminSystemController::class, 'health'])->name('admin.system.health');
    Route::get('/system/login-history', [\App\Http\Controllers\Admin\AdminSystemController::class, 'loginHistory'])->name('admin.system.login-history');
});

// ─── API / AJAX Resource Routes ────────────────────────────────────────────
use App\Http\Controllers\Dashboard\SupplierController;
use App\Http\Controllers\Dashboard\CustomerGroupController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\UnitController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ExpenseCategoryController;
use App\Http\Controllers\Dashboard\ExpenseController;
use App\Http\Controllers\Dashboard\TaxRateController;
use App\Http\Controllers\Dashboard\WarrantyController;
use App\Http\Controllers\Dashboard\DiscountController;
use App\Http\Controllers\Dashboard\NotificationTemplateController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\StockAdjustmentController;
use App\Http\Controllers\Dashboard\StockTransferController;
use App\Http\Controllers\Dashboard\PurchaseController;
use App\Http\Controllers\Dashboard\SaleController;
use App\Http\Controllers\Dashboard\UserManagementController;
use App\Http\Controllers\Dashboard\PlanManagementController;

Route::middleware('auth')->prefix('api/dashboard')->group(function () {
    Route::apiResource('suppliers',              SupplierController::class);
    Route::apiResource('customer-groups',        CustomerGroupController::class);
    Route::apiResource('customers',              CustomerController::class);
    Route::apiResource('brands',                 BrandController::class);
    Route::apiResource('units',                  UnitController::class);
    Route::apiResource('categories',             CategoryController::class);
    Route::apiResource('products',               ProductController::class);
    Route::apiResource('expense-categories',     ExpenseCategoryController::class);
    Route::apiResource('expenses',               ExpenseController::class);
    Route::apiResource('tax-rates',              TaxRateController::class);
    Route::apiResource('warranties',             WarrantyController::class);
    Route::apiResource('discounts',              DiscountController::class);
    Route::apiResource('notification-templates', NotificationTemplateController::class);
    Route::apiResource('roles',                  RoleController::class);
    Route::apiResource('stock-adjustments',      StockAdjustmentController::class);
    Route::apiResource('stock-transfers',        StockTransferController::class);
    Route::apiResource('purchases',              PurchaseController::class);
    Route::apiResource('sales',                  SaleController::class);
    Route::apiResource('users',                  UserManagementController::class);

    // Plan Management API
    Route::get('plans/stats',             [PlanManagementController::class, 'statsPlans']);
    Route::get('plans',                   [PlanManagementController::class, 'indexPlans']);
    Route::post('plans',                  [PlanManagementController::class, 'storePlan']);
    Route::put('plans/{plan}',            [PlanManagementController::class, 'updatePlan']);
    Route::delete('plans/{plan}',         [PlanManagementController::class, 'destroyPlan']);

    Route::get('subscriptions',           [PlanManagementController::class, 'indexSubscriptions']);
    Route::post('subscriptions',          [PlanManagementController::class, 'storeSubscription']);
    Route::put('subscriptions/{subscription}', [PlanManagementController::class, 'updateSubscription']);
    Route::delete('subscriptions/{subscription}', [PlanManagementController::class, 'destroySubscription']);
});

// ─── Admin API Routes ──────────────────────────────────────────────────────
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminBillingController;
use App\Http\Controllers\Admin\AdminCommunicationController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminSystemController;

Route::middleware(['auth', 'admin'])->prefix('api/admin')->name('admin.api.')->group(function () {

    // Staff
    Route::get('staff',                [AdminStaffController::class, 'list'])->name('staff.list');
    Route::post('staff',               [AdminStaffController::class, 'store'])->name('staff.store');
    Route::get('staff/{staff}',        [AdminStaffController::class, 'show'])->name('staff.show');
    Route::put('staff/{staff}',        [AdminStaffController::class, 'update'])->name('staff.update');
    Route::delete('staff/{staff}',     [AdminStaffController::class, 'destroy'])->name('staff.destroy');
    Route::get('staff/attendance',     [AdminStaffController::class, 'attendanceList'])->name('staff.attendance');
    Route::post('staff/attendance',    [AdminStaffController::class, 'attendanceStore'])->name('staff.attendance.store');
    Route::delete('staff/attendance/{attendance}', [AdminStaffController::class, 'attendanceDestroy'])->name('staff.attendance.destroy');
    Route::get('staff/schedules',      [AdminStaffController::class, 'schedulesList'])->name('staff.schedules');

    // Business
    Route::get('business',             [AdminBusinessController::class, 'list'])->name('business.list');
    Route::post('business',            [AdminBusinessController::class, 'store'])->name('business.store');
    Route::get('business/{business}',  [AdminBusinessController::class, 'show'])->name('business.show');
    Route::put('business/{business}',  [AdminBusinessController::class, 'update'])->name('business.update');
    Route::delete('business/{business}',[AdminBusinessController::class, 'destroy'])->name('business.destroy');
    Route::get('business/categories',  [AdminBusinessController::class, 'categoriesList'])->name('business.categories');
    Route::post('business/categories', [AdminBusinessController::class, 'categoriesStore'])->name('business.categories.store');
    Route::get('business/verifications',[AdminBusinessController::class, 'verificationsList'])->name('business.verifications');

    // Billing
    Route::get('billing/users',        [AdminBillingController::class, 'users'])->name('billing.users');
    Route::get('billing/invoices',     [AdminBillingController::class, 'invoicesList'])->name('billing.invoices');
    Route::post('billing/invoices',    [AdminBillingController::class, 'invoicesStore'])->name('billing.invoices.store');
    Route::get('billing/payments',     [AdminBillingController::class, 'paymentsList'])->name('billing.payments');
    Route::post('billing/payments',    [AdminBillingController::class, 'paymentsStore'])->name('billing.payments.store');
    Route::get('billing/gateways',     [AdminBillingController::class, 'gatewaysList'])->name('billing.gateways');
    Route::post('billing/gateways',    [AdminBillingController::class, 'gatewaysStore'])->name('billing.gateways.store');
    Route::put('billing/gateways/{gateway}', [AdminBillingController::class, 'gatewaysUpdate'])->name('billing.gateways.update');
    Route::delete('billing/gateways/{gateway}', [AdminBillingController::class, 'gatewaysDestroy'])->name('billing.gateways.destroy');

    // Communication
    Route::get('communication/email-templates',      [AdminCommunicationController::class, 'emailTemplatesList'])->name('communication.email-templates.list');
    Route::post('communication/email-templates',     [AdminCommunicationController::class, 'emailTemplatesStore'])->name('communication.email-templates.store');
    Route::get('communication/email-templates/{template}', [AdminCommunicationController::class, 'emailTemplatesShow'])->name('communication.email-templates.show');
    Route::put('communication/email-templates/{template}', [AdminCommunicationController::class, 'emailTemplatesUpdate'])->name('communication.email-templates.update');
    Route::delete('communication/email-templates/{template}', [AdminCommunicationController::class, 'emailTemplatesDestroy'])->name('communication.email-templates.destroy');
    Route::get('communication/sms-templates',        [AdminCommunicationController::class, 'smsTemplatesList'])->name('communication.sms-templates.list');
    Route::post('communication/sms-templates',       [AdminCommunicationController::class, 'smsTemplatesStore'])->name('communication.sms-templates.store');
    Route::put('communication/sms-templates/{template}', [AdminCommunicationController::class, 'smsTemplatesUpdate'])->name('communication.sms-templates.update');
    Route::delete('communication/sms-templates/{template}', [AdminCommunicationController::class, 'smsTemplatesDestroy'])->name('communication.sms-templates.destroy');
    Route::get('communication/announcements',        [AdminCommunicationController::class, 'announcementsList'])->name('communication.announcements.list');
    Route::post('communication/announcements',       [AdminCommunicationController::class, 'announcementsStore'])->name('communication.announcements.store');
    Route::get('communication/announcements/{announcement}', [AdminCommunicationController::class, 'announcementsShow'])->name('communication.announcements.show');
    Route::put('communication/announcements/{announcement}', [AdminCommunicationController::class, 'announcementsUpdate'])->name('communication.announcements.update');
    Route::delete('communication/announcements/{announcement}', [AdminCommunicationController::class, 'announcementsDestroy'])->name('communication.announcements.destroy');

    // Support
    Route::get('support/tickets',        [AdminSupportController::class, 'ticketsList'])->name('support.tickets.list');
    Route::get('support/tickets/{ticket}', [AdminSupportController::class, 'ticketsShow'])->name('support.tickets.show');
    Route::put('support/tickets/{ticket}', [AdminSupportController::class, 'ticketsUpdate'])->name('support.tickets.update');
    Route::delete('support/tickets/{ticket}', [AdminSupportController::class, 'ticketsDestroy'])->name('support.tickets.destroy');

    // System
    Route::get('system/config',          [AdminSystemController::class, 'configList'])->name('system.config.list');
    Route::post('system/config',         [AdminSystemController::class, 'configStore'])->name('system.config.store');
    Route::get('system/config/{config}', [AdminSystemController::class, 'configShow'])->name('system.config.show');
    Route::put('system/config/{config}', [AdminSystemController::class, 'configUpdate'])->name('system.config.update');
    Route::delete('system/config/{config}', [AdminSystemController::class, 'configDestroy'])->name('system.config.destroy');
    Route::get('system/activity-logs',   [AdminSystemController::class, 'activityLogsList'])->name('system.activity-logs.list');
    Route::delete('system/activity-logs',[AdminSystemController::class, 'activityLogsClear'])->name('system.activity-logs.clear');
    Route::get('system/backups',         [AdminSystemController::class, 'backupsList'])->name('system.backups.list');
    Route::post('system/backups',        [AdminSystemController::class, 'backupsCreate'])->name('system.backups.create');
    Route::get('system/backups/{backup}/download', [AdminSystemController::class, 'backupsDownload'])->name('system.backups.download');
    Route::delete('system/backups/{backup}', [AdminSystemController::class, 'backupsDestroy'])->name('system.backups.destroy');
    Route::get('system/health',          [AdminSystemController::class, 'healthData'])->name('system.health');
    Route::get('system/login-history',   [AdminSystemController::class, 'loginHistoryList'])->name('system.login-history.list');
    Route::delete('system/login-history',[AdminSystemController::class, 'loginHistoryClear'])->name('system.login-history.clear');
});
