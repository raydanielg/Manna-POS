<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PublicStoreController;

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
    $plans = \App\Models\SubscriptionPlan::where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    return view('landing', compact('latestBlogs', 'plans'));
});

// Public Store Routes (no auth required)
Route::get('/store/{slug}', [PublicStoreController::class, 'show'])->name('store.public');
Route::post('/store/{slug}/order', [PublicStoreController::class, 'storeOrder'])->name('store.order');

// Product pages
Route::get('/features', function () {
    return view('product.features');
});
Route::get('/pricing', function () {
    $plans = \App\Models\SubscriptionPlan::where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    return view('product.pricing', compact('plans'));
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

// Setup wizard
Route::get('/setup', [App\Http\Controllers\SetupController::class, 'index'])->middleware('auth');
Route::post('/setup', [App\Http\Controllers\SetupController::class, 'complete'])->middleware('auth');

// Subscription plans (user-facing)
Route::get('/subscription/plans', [App\Http\Controllers\UserSubscriptionController::class, 'plans'])->middleware('auth');
Route::post('/subscription/choose', [App\Http\Controllers\UserSubscriptionController::class, 'choosePlan'])->middleware('auth');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'user.dashboard'])->name('home');

Route::prefix('dashboard')->middleware(['auth', 'user.dashboard', 'subscription'])->group(function () {

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

    // Settings Routes
    Route::get('/settings', function () { return view('dashboard.settings'); })->name('dashboard.settings');
    Route::get('/settings/general', function () { return view('dashboard.settings.general'); })->name('dashboard.settings.general');
    Route::get('/settings/business-location', function () { return view('dashboard.settings.business-location'); })->name('dashboard.settings.business-location');
    Route::get('/settings/invoice-settings', function () { return view('dashboard.settings.invoice-settings'); })->name('dashboard.settings.invoice-settings');
    Route::get('/settings/barcode-settings', function () { return view('dashboard.settings.barcode-settings'); })->name('dashboard.settings.barcode-settings');
    Route::get('/settings/tax-rates', function () { return view('dashboard.settings.tax-rates'); })->name('dashboard.settings.tax-rates');

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
    Route::get('/inventory/list-products', [App\Http\Controllers\Dashboard\ProductController::class, 'create'])->name('dashboard.inventory.list-products');
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
    Route::get('/reports/sales-report', [App\Http\Controllers\Dashboard\ReportController::class, 'salesReport'])->name('dashboard.reports.sales-report');
    Route::get('/reports/purchase-report', [App\Http\Controllers\Dashboard\ReportController::class, 'purchaseReport'])->name('dashboard.reports.purchase-report');
    Route::get('/reports/inventory-report', [App\Http\Controllers\Dashboard\ReportController::class, 'inventoryReport'])->name('dashboard.reports.inventory-report');
    Route::get('/reports/expense-report', [App\Http\Controllers\Dashboard\ReportController::class, 'expenseReport'])->name('dashboard.reports.expense-report');
    Route::get('/reports/profit-loss-report', [App\Http\Controllers\Dashboard\ReportController::class, 'profitLossReport'])->name('dashboard.reports.profit-loss-report');
    Route::get('/reports/suppliers-report', [App\Http\Controllers\Dashboard\ReportController::class, 'suppliersReport'])->name('dashboard.reports.suppliers-report');
    Route::get('/reports/supplier-price-comparison', [App\Http\Controllers\Dashboard\ReportController::class, 'supplierPriceComparison'])->name('dashboard.reports.supplier-price-comparison');
    Route::get('/reports/expiry-report', [App\Http\Controllers\Dashboard\ReportController::class, 'expiryReport'])->name('dashboard.reports.expiry-report');
    Route::get('/reports/product-trends-report', [App\Http\Controllers\Dashboard\ReportController::class, 'productTrendsReport'])->name('dashboard.reports.product-trends-report');

    // CRM Routes
    Route::get('/crm/activities', [App\Http\Controllers\Dashboard\CrmController::class, 'activities'])->name('dashboard.crm.activities');
    Route::get('/crm/dashboard', [App\Http\Controllers\Dashboard\CrmController::class, 'dashboard'])->name('dashboard.crm.dashboard');

    // Calendar & Todo Routes
    Route::get('/calendar', [App\Http\Controllers\Dashboard\TodoController::class, 'index'])->name('dashboard.calendar');

    // Notification Templates Routes
    Route::get('/notification-templates', function () { return view('dashboard.notification-templates.index'); })->name('dashboard.notification-templates');

    // Settings Routes
    Route::get('/settings/tax-rates', function () { return view('dashboard.settings.tax-rates'); })->name('dashboard.settings.tax-rates');

    // Plan Management Routes
    Route::get('/plan-management/plans', function () { return view('dashboard.plan-management.plans'); })->name('dashboard.plan-management.plans');
    Route::get('/plan-management/subscriptions', function () { return view('dashboard.plan-management.subscriptions'); })->name('dashboard.plan-management.subscriptions');

    // Banking / Cashflow Routes
    Route::get('/banking', [\App\Http\Controllers\Dashboard\BankingController::class, 'index'])->name('dashboard.banking');
    Route::get('/banking/accounts', [\App\Http\Controllers\Dashboard\BankingController::class, 'accounts'])->name('dashboard.banking.accounts');
    Route::post('/banking/accounts', [\App\Http\Controllers\Dashboard\BankingController::class, 'storeAccount'])->name('dashboard.banking.accounts.store');
    Route::put('/banking/accounts/{account}', [\App\Http\Controllers\Dashboard\BankingController::class, 'updateAccount'])->name('dashboard.banking.accounts.update');
    Route::delete('/banking/accounts/{account}', [\App\Http\Controllers\Dashboard\BankingController::class, 'destroyAccount'])->name('dashboard.banking.accounts.destroy');
    Route::get('/banking/transactions', [\App\Http\Controllers\Dashboard\BankingController::class, 'transactions'])->name('dashboard.banking.transactions');
    Route::post('/banking/transactions', [\App\Http\Controllers\Dashboard\BankingController::class, 'storeTransaction'])->name('dashboard.banking.transactions.store');

    // Feedback & Support Routes (Customer)
    Route::get('/feedback', [\App\Http\Controllers\Dashboard\FeedbackController::class, 'index'])->name('dashboard.feedback.index');
    Route::get('/feedback/create', [\App\Http\Controllers\Dashboard\FeedbackController::class, 'create'])->name('dashboard.feedback.create');
    Route::post('/feedback', [\App\Http\Controllers\Dashboard\FeedbackController::class, 'store'])->name('dashboard.feedback.store');
    Route::get('/feedback/{feedback}', [\App\Http\Controllers\Dashboard\FeedbackController::class, 'show'])->name('dashboard.feedback.show');
    Route::post('/feedback/{feedback}/reply', [\App\Http\Controllers\Dashboard\FeedbackController::class, 'reply'])->name('dashboard.feedback.reply');

    // Admin Feedback Routes
    Route::get('/feedback-admin', [\App\Http\Controllers\Dashboard\AdminFeedbackController::class, 'index'])->name('dashboard.feedback.admin.index');
    Route::get('/feedback-admin/{feedback}', [\App\Http\Controllers\Dashboard\AdminFeedbackController::class, 'show'])->name('dashboard.feedback.admin.show');
    Route::post('/feedback-admin/{feedback}/reply', [\App\Http\Controllers\Dashboard\AdminFeedbackController::class, 'reply'])->name('dashboard.feedback.admin.reply');
    Route::patch('/feedback-admin/{feedback}/status', [\App\Http\Controllers\Dashboard\AdminFeedbackController::class, 'updateStatus'])->name('dashboard.feedback.admin.status');
    Route::patch('/feedback-admin/{feedback}/priority', [\App\Http\Controllers\Dashboard\AdminFeedbackController::class, 'updatePriority'])->name('dashboard.feedback.admin.priority');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/profile', fn() => view('admin.profile'))->name('admin.profile');

    // User Management
    Route::get('/users',            [\App\Http\Controllers\Admin\AdminUsersController::class, 'index'])->name('admin.users');
    Route::get('/users/create',     [\App\Http\Controllers\Admin\AdminUsersController::class, 'create'])->name('admin.users.create');
    Route::get('/roles',            fn() => view('admin.roles.index'))->name('admin.roles');
    Route::get('/sales-commission-agents', fn() => view('admin.roles.sales-agents'))->name('admin.sales-commission-agents');
    Route::get('/user-activity',    [\App\Http\Controllers\Admin\AdminSystemController::class, 'activityLogs'])->name('admin.user-activity');
    Route::get('/blocked-users',    [\App\Http\Controllers\Admin\AdminUsersController::class, 'blocked'])->name('admin.blocked-users');

    // Plan Management
    Route::get('/plans',            fn() => view('admin.plans.index'))->name('admin.plans');
    Route::get('/plans/create',     fn() => view('admin.plans.index'))->name('admin.plans.create');
    Route::get('/subscriptions',    [\App\Http\Controllers\Admin\AdminSubscriptionController::class, 'index'])->name('admin.subscriptions');
    Route::get('/subscriptions/active',  [\App\Http\Controllers\Admin\AdminSubscriptionController::class, 'active'])->name('admin.subscriptions.active');
    Route::get('/subscriptions/expired', [\App\Http\Controllers\Admin\AdminSubscriptionController::class, 'expired'])->name('admin.subscriptions.expired');
    Route::get('/subscriptions/trial',   [\App\Http\Controllers\Admin\AdminSubscriptionController::class, 'trial'])->name('admin.subscriptions.trial');

    // Notification Templates
    Route::get('/notification-templates', fn() => view('admin.notification-templates.index'))->name('admin.notification-templates');

    // Settings
    Route::get('/settings/general',           fn() => view('admin.settings.general'))->name('admin.settings.general');
    Route::get('/settings/business-location', fn() => view('admin.settings.business-location'))->name('admin.settings.business-location');
    Route::get('/settings/invoice-settings',  fn() => view('admin.settings.invoice-settings'))->name('admin.settings.invoice-settings');
    Route::get('/settings/barcode-settings',  fn() => view('admin.settings.barcode-settings'))->name('admin.settings.barcode-settings');
    Route::get('/settings/tax-rates',         fn() => view('admin.settings.tax-rates'))->name('admin.settings.tax-rates');
    Route::get('/settings/currency',          [\App\Http\Controllers\Admin\AdminSystemController::class, 'currency'])->name('admin.settings.currency');
    Route::get('/settings/receipt-printers',  [\App\Http\Controllers\Admin\AdminSystemController::class, 'receiptPrinters'])->name('admin.settings.receipt-printers');

    // Reports Dashboard
    Route::get('/reports', fn() => view('admin.reports.sales'))->name('admin.reports');

    // ── Staff ──
    Route::get('/staff',             [\App\Http\Controllers\Admin\AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/staff/create',      [\App\Http\Controllers\Admin\AdminStaffController::class, 'index'])->name('admin.staff.create');
    Route::get('/staff/roles',       [\App\Http\Controllers\Admin\AdminStaffController::class, 'roles'])->name('admin.staff.roles');
    Route::get('/staff/attendance',  [\App\Http\Controllers\Admin\AdminStaffController::class, 'attendance'])->name('admin.staff.attendance');
    Route::get('/staff/schedules',   [\App\Http\Controllers\Admin\AdminStaffController::class, 'schedules'])->name('admin.staff.schedules');
    Route::get('/staff/performance', [\App\Http\Controllers\Admin\AdminStaffController::class, 'performance'])->name('admin.staff.performance');

    // ── Business ──
    Route::get('/business',           [\App\Http\Controllers\Admin\AdminBusinessController::class, 'index'])->name('admin.business.index');
    Route::get('/business/categories', [\App\Http\Controllers\Admin\AdminBusinessController::class, 'categories'])->name('admin.business.categories');
    Route::get('/business/verifications', [\App\Http\Controllers\Admin\AdminBusinessController::class, 'verifications'])->name('admin.business.verifications');
    Route::get('/business/locations',  [\App\Http\Controllers\Admin\AdminBusinessController::class, 'locations'])->name('admin.business.locations');
    Route::get('/business/pending',    [\App\Http\Controllers\Admin\AdminBusinessController::class, 'pending'])->name('admin.business.pending');

    // ── Billing ──
    Route::get('/billing/invoices',    [\App\Http\Controllers\Admin\AdminBillingController::class, 'invoices'])->name('admin.billing.invoices');
    Route::get('/billing/payments',    [\App\Http\Controllers\Admin\AdminBillingController::class, 'payments'])->name('admin.billing.payments');
    Route::get('/billing/payments/pending', [\App\Http\Controllers\Admin\AdminBillingController::class, 'payments'])->name('admin.billing.payments.pending');
    Route::get('/billing/gateways',    [\App\Http\Controllers\Admin\AdminBillingController::class, 'gateways'])->name('admin.billing.gateways');
    Route::get('/billing/transactions',[ \App\Http\Controllers\Admin\AdminBillingController::class, 'transactions'])->name('admin.billing.transactions');
    Route::get('/billing/refunds',     [\App\Http\Controllers\Admin\AdminBillingController::class, 'refunds'])->name('admin.billing.refunds');

    // ── Finance ──
    Route::get('/finance/revenue',     [\App\Http\Controllers\Admin\AdminFinanceController::class, 'revenue'])->name('admin.finance.revenue');
    Route::get('/finance/tax-reports', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'taxReports'])->name('admin.finance.tax-reports');
    Route::get('/finance/commissions', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'commissions'])->name('admin.finance.commissions');
    Route::get('/finance/payouts',     [\App\Http\Controllers\Admin\AdminFinanceController::class, 'payouts'])->name('admin.finance.payouts');

    // ── Communication ──
    Route::get('/communication/email-templates', [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'emailTemplates'])->name('admin.communication.email-templates');
    Route::get('/communication/sms-templates',   [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'smsTemplates'])->name('admin.communication.sms-templates');
    Route::get('/communication/announcements',   [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'announcements'])->name('admin.communication.announcements');
    Route::get('/communication/push',            [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'push'])->name('admin.communication.push');
    Route::get('/communication/broadcast',       [\App\Http\Controllers\Admin\AdminCommunicationController::class, 'broadcast'])->name('admin.communication.broadcast');

    // ── Support ──
    Route::get('/support/tickets',   [\App\Http\Controllers\Admin\AdminSupportController::class, 'tickets'])->name('admin.support.tickets');

    // ── System ──
    Route::get('/system/config',      [\App\Http\Controllers\Admin\AdminSystemController::class, 'config'])->name('admin.system.config');
    Route::get('/system/activity-logs', [\App\Http\Controllers\Admin\AdminSystemController::class, 'activityLogs'])->name('admin.system.activity-logs');
    Route::get('/system/backups',     [\App\Http\Controllers\Admin\AdminSystemController::class, 'backups'])->name('admin.system.backups');
    Route::get('/system/health',      [\App\Http\Controllers\Admin\AdminSystemController::class, 'health'])->name('admin.system.health');
    Route::get('/system/login-history', [\App\Http\Controllers\Admin\AdminSystemController::class, 'loginHistory'])->name('admin.system.login-history');
    Route::get('/system/email-config',  [\App\Http\Controllers\Admin\AdminSystemController::class, 'emailConfig'])->name('admin.system.email-config');
    Route::get('/system/sms-config',    [\App\Http\Controllers\Admin\AdminSystemController::class, 'smsConfig'])->name('admin.system.sms-config');
    Route::get('/system/api-keys',      [\App\Http\Controllers\Admin\AdminSystemController::class, 'apiKeys'])->name('admin.system.api-keys');
    Route::get('/system/security',      [\App\Http\Controllers\Admin\AdminSystemController::class, 'security'])->name('admin.system.security');
    Route::get('/system/maintenance',   [\App\Http\Controllers\Admin\AdminSystemController::class, 'maintenance'])->name('admin.system.maintenance');
    Route::get('/system/error-logs',    [\App\Http\Controllers\Admin\AdminSystemController::class, 'errorLogs'])->name('admin.system.error-logs');
    Route::get('/system/logs',          [\App\Http\Controllers\Admin\AdminSystemController::class, 'logs'])->name('admin.system.logs');
    Route::get('/system/file-backups',  [\App\Http\Controllers\Admin\AdminSystemController::class, 'fileBackups'])->name('admin.system.file-backups');
    Route::get('/system/backup-restore', [\App\Http\Controllers\Admin\AdminSystemController::class, 'backupRestore'])->name('admin.system.backup-restore');
    Route::get('/system/backup-schedule', [\App\Http\Controllers\Admin\AdminSystemController::class, 'backupSchedule'])->name('admin.system.backup-schedule');
    Route::get('/system/updates',       [\App\Http\Controllers\Admin\AdminSystemController::class, 'updates'])->name('admin.system.updates');

    // ── Cache / Database / File ──
    Route::get('/cache/manage',       [\App\Http\Controllers\Admin\AdminCacheController::class, 'manage'])->name('admin.cache.manage');
    Route::get('/database',           [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'index'])->name('admin.database.manager');
    Route::get('/file-manager',       [\App\Http\Controllers\Admin\AdminFileManagerController::class, 'index'])->name('admin.file.manager');
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
use App\Http\Controllers\Dashboard\ShipmentController;
use App\Http\Controllers\Dashboard\SellingPriceGroupController;
use App\Http\Controllers\Dashboard\ProductVariationController;
use App\Http\Controllers\Dashboard\BusinessLocationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CrmActivityController;

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
    Route::apiResource('shipments',              ShipmentController::class);
    Route::apiResource('selling-price-groups',   SellingPriceGroupController::class);
    Route::apiResource('product-variations',     ProductVariationController::class);
    Route::apiResource('business-locations',     BusinessLocationController::class);

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

    // Settings API
    Route::get('settings', function(\Illuminate\Http\Request $req) {
        $u = auth()->user();
        $pos = $u->pos_settings ? json_decode($u->pos_settings, true) : [];
        return response()->json(array_merge([
            'business_name'    => $u->business_name,
            'business_email'   => $u->email,
            'phone'            => $u->phone,
            'address'          => $u->business_address,
            'city'             => $u->business_city,
            'country'          => $u->business_country,
            'currency'         => $u->currency,
            'fy_start'         => $u->fiscal_year_start,
            'tax_number'       => $u->tax_percentage,
        ], $pos));
    });
    // Store API
    Route::post('store/generate-slug',   [PublicStoreController::class, 'generateSlug']);
    Route::put('store/settings',         [PublicStoreController::class, 'updateSettings']);

    Route::put('settings', function(\Illuminate\Http\Request $req) {
        $u = auth()->user();
        $fill = [];
        if ($req->has('business_name'))  $fill['business_name']     = $req->business_name;
        if ($req->has('phone'))          $fill['phone']              = $req->phone;
        if ($req->has('address'))        $fill['business_address']   = $req->address;
        if ($req->has('city'))           $fill['business_city']      = $req->city;
        if ($req->has('country'))        $fill['business_country']   = $req->country;
        if ($req->has('currency'))       $fill['currency']           = $req->currency;
        if ($req->has('fy_start'))       $fill['fiscal_year_start']  = $req->fy_start;
        if ($req->has('tax_number'))     $fill['tax_percentage']     = $req->tax_number;
        // POS-specific settings (invoice + barcode) stored as JSON
        $posKeys = ['invoice_title','invoice_prefix','invoice_header','invoice_footer','payment_terms',
                    'show_logo','show_tax_number','barcode_type','barcode_height','label_size',
                    'label_width','label_height','label_show_name','label_show_price','label_show_sku','barcode_copies'];
        $existing = $u->pos_settings ? json_decode($u->pos_settings, true) : [];
        foreach ($posKeys as $k) {
            if ($req->has($k)) $existing[$k] = $req->input($k);
        }
        $fill['pos_settings'] = json_encode($existing);
        if (!empty($fill)) $u->update($fill);
        if ($req->business_email && $req->business_email !== $u->email) {
            $u->email = $req->business_email;
            $u->save();
        }
        return response()->json(['success' => true, 'message' => 'Settings saved successfully']);
    });

    // Dashboard Stats
    Route::get('stats', [DashboardController::class, 'stats']);
    Route::get('crm/dashboard', [DashboardController::class, 'crmStats']);

    // CRM Activities
    Route::get('crm-activities', [CrmActivityController::class, 'index']);
    Route::post('crm-activities', [CrmActivityController::class, 'store']);

    // Todo API
    Route::get('todos', [App\Http\Controllers\Dashboard\TodoController::class, 'list']);
    Route::post('todos', [App\Http\Controllers\Dashboard\TodoController::class, 'store']);
    Route::put('todos/{todo}', [App\Http\Controllers\Dashboard\TodoController::class, 'update']);
    Route::put('todos/{todo}/toggle', [App\Http\Controllers\Dashboard\TodoController::class, 'toggleStatus']);
    Route::put('todos/sort/update', [App\Http\Controllers\Dashboard\TodoController::class, 'updateSort']);
    Route::delete('todos/{todo}', [App\Http\Controllers\Dashboard\TodoController::class, 'destroy']);

    // Calendar data
    Route::get('calendar/data', [App\Http\Controllers\Dashboard\TodoController::class, 'calendarData']);
});

// ─── Admin API Routes ──────────────────────────────────────────────────────
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminBillingController;
use App\Http\Controllers\Admin\AdminCommunicationController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminSystemController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminCacheController;
use App\Http\Controllers\Admin\AdminDatabaseController;
use App\Http\Controllers\Admin\AdminFileManagerController;

Route::middleware(['auth', 'admin'])->prefix('api/admin')->name('admin.api.')->group(function () {

    // ── Dashboard ──
    Route::get('stats',                [AdminController::class, 'stats'])->name('stats');

    // ── Profile ──
    Route::put('profile',              [AdminController::class, 'updateProfile'])->name('profile.update');

    // ── Users ──
    Route::get('users',                [AdminUsersController::class, 'list'])->name('users.list');
    Route::post('users',               [AdminUsersController::class, 'store'])->name('users.store');
    Route::get('users/{user}',         [AdminUsersController::class, 'show'])->name('users.show');
    Route::put('users/{user}',         [AdminUsersController::class, 'update'])->name('users.update');
    Route::delete('users/{user}',      [AdminUsersController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/block',  [AdminUsersController::class, 'block'])->name('users.block');
    Route::post('users/{user}/unblock',[AdminUsersController::class, 'unblock'])->name('users.unblock');

    // ── Roles ──
    Route::get('roles',                [\App\Http\Controllers\Dashboard\RoleController::class, 'index'])->name('roles.list');
    Route::post('roles',               [\App\Http\Controllers\Dashboard\RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}',         [\App\Http\Controllers\Dashboard\RoleController::class, 'show'])->name('roles.show');
    Route::put('roles/{role}',         [\App\Http\Controllers\Dashboard\RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}',      [\App\Http\Controllers\Dashboard\RoleController::class, 'destroy'])->name('roles.destroy');

    // ── Sales Agents ──
    Route::get('sales-agents',         [AdminUsersController::class, 'list'])->name('sales-agents.list');

    // ── Staff ──
    Route::get('staff',                [AdminStaffController::class, 'list'])->name('staff.list');
    Route::post('staff',               [AdminStaffController::class, 'store'])->name('staff.store');
    Route::get('staff/{staff}',        [AdminStaffController::class, 'show'])->name('staff.show');
    Route::put('staff/{staff}',        [AdminStaffController::class, 'update'])->name('staff.update');
    Route::delete('staff/{staff}',     [AdminStaffController::class, 'destroy'])->name('staff.destroy');
    Route::get('staff/attendance',     [AdminStaffController::class, 'attendanceList'])->name('staff.attendance');
    Route::post('staff/attendance',    [AdminStaffController::class, 'attendanceStore'])->name('staff.attendance.store');
    Route::delete('staff/attendance/{attendance}', [AdminStaffController::class, 'attendanceDestroy'])->name('staff.attendance.destroy');
    Route::get('staff/schedules',      [AdminStaffController::class, 'schedulesList'])->name('staff.schedules');
    Route::post('staff/schedules',     [AdminStaffController::class, 'schedulesStore'])->name('staff.schedules.store');
    Route::delete('staff/schedules/{schedule}', [AdminStaffController::class, 'schedulesDestroy'])->name('staff.schedules.destroy');
    Route::get('staff/roles',          [AdminStaffController::class, 'rolesList'])->name('staff.roles.list');
    Route::get('staff/performance',    [AdminStaffController::class, 'performanceList'])->name('staff.performance.list');
    Route::get('staff/departments',    [AdminStaffController::class, 'departments'])->name('staff.departments');

    // ── Business ──
    Route::get('business',             [AdminBusinessController::class, 'list'])->name('business.list');
    Route::post('business',            [AdminBusinessController::class, 'store'])->name('business.store');
    Route::get('business/categories',  [AdminBusinessController::class, 'categoriesList'])->name('business.categories');
    Route::post('business/categories', [AdminBusinessController::class, 'categoriesStore'])->name('business.categories.store');
    Route::get('business/verifications',[AdminBusinessController::class, 'verificationsList'])->name('business.verifications');
    Route::post('business/verifications/{verification}/approve', [AdminBusinessController::class, 'verificationsApprove'])->name('business.verifications.approve');
    Route::post('business/verifications/{verification}/reject', [AdminBusinessController::class, 'verificationsReject'])->name('business.verifications.reject');
    Route::get('business/locations',   [AdminBusinessController::class, 'locationsList'])->name('business.locations.list');
    Route::get('business/{business}',  [AdminBusinessController::class, 'show'])->name('business.show');
    Route::put('business/{business}',  [AdminBusinessController::class, 'update'])->name('business.update');
    Route::delete('business/{business}',[AdminBusinessController::class, 'destroy'])->name('business.destroy');
    Route::post('business/{business}/verify', [AdminBusinessController::class, 'verify'])->name('business.verify');
    Route::post('business/{business}/reject', [AdminBusinessController::class, 'reject'])->name('business.reject');

    // ── Billing ──
    Route::get('billing/users',        [AdminBillingController::class, 'users'])->name('billing.users');
    Route::get('billing/invoices',     [AdminBillingController::class, 'invoicesList'])->name('billing.invoices');
    Route::post('billing/invoices',    [AdminBillingController::class, 'invoicesStore'])->name('billing.invoices.store');
    Route::get('billing/payments',     [AdminBillingController::class, 'paymentsList'])->name('billing.payments');
    Route::post('billing/payments',    [AdminBillingController::class, 'paymentsStore'])->name('billing.payments.store');
    Route::get('billing/gateways',     [AdminBillingController::class, 'gatewaysList'])->name('billing.gateways');
    Route::post('billing/gateways',    [AdminBillingController::class, 'gatewaysStore'])->name('billing.gateways.store');
    Route::get('billing/gateways/{gateway}', [AdminBillingController::class, 'gatewaysShow'])->name('billing.gateways.show');
    Route::put('billing/gateways/{gateway}', [AdminBillingController::class, 'gatewaysUpdate'])->name('billing.gateways.update');
    Route::post('billing/gateways/{gateway}/toggle', [AdminBillingController::class, 'gatewaysToggle'])->name('billing.gateways.toggle');
    Route::delete('billing/gateways/{gateway}', [AdminBillingController::class, 'gatewaysDestroy'])->name('billing.gateways.destroy');
    Route::get('billing/refunds',      [AdminBillingController::class, 'refundsList'])->name('billing.refunds.list');
    Route::post('billing/refunds/{payment}/process', [AdminBillingController::class, 'refundsProcess'])->name('billing.refunds.process');
    Route::get('billing/transactions', [AdminBillingController::class, 'transactionsList'])->name('billing.transactions.list');

    // ── Subscriptions ──
    Route::get('subscriptions',        [AdminSubscriptionController::class, 'list'])->name('subscriptions.list');
    Route::get('subscriptions/{subscription}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::put('subscriptions/{subscription}', [AdminSubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('subscriptions/{subscription}', [AdminSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // ── Plans (admin manages subscription plans) ──
    Route::get('plans',                [\App\Http\Controllers\Dashboard\PlanManagementController::class, 'indexPlans'])->name('plans.list');
    Route::post('plans',               [\App\Http\Controllers\Dashboard\PlanManagementController::class, 'storePlan'])->name('plans.store');
    Route::put('plans/{plan}',         [\App\Http\Controllers\Dashboard\PlanManagementController::class, 'updatePlan'])->name('plans.update');
    Route::delete('plans/{plan}',      [\App\Http\Controllers\Dashboard\PlanManagementController::class, 'destroyPlan'])->name('plans.destroy');

    // ── Reports ──
    Route::get('reports/sales',        [\App\Http\Controllers\Dashboard\SaleController::class, 'index'])->name('reports.sales');

    // ── Notification Templates ──
    Route::apiResource('notification-templates', \App\Http\Controllers\Dashboard\NotificationTemplateController::class)->names([
        'index'   => 'admin.notification-templates.index',
        'store'   => 'admin.notification-templates.store',
        'show'    => 'admin.notification-templates.show',
        'update'  => 'admin.notification-templates.update',
        'destroy' => 'admin.notification-templates.destroy',
    ]);

    // ── Finance ──
    Route::get('finance/revenue',      [AdminFinanceController::class, 'revenueData'])->name('finance.revenue');
    Route::get('finance/tax-reports',  [AdminFinanceController::class, 'taxReportsList'])->name('finance.tax-reports.list');
    Route::get('finance/commissions',  [AdminFinanceController::class, 'commissionsList'])->name('finance.commissions.list');
    Route::get('finance/payouts',      [AdminFinanceController::class, 'payoutsList'])->name('finance.payouts.list');
    Route::post('finance/payouts/{id}/process', [AdminFinanceController::class, 'payoutsProcess'])->name('finance.payouts.process');

    // ── Communication ──
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
    Route::get('communication/push',     [AdminCommunicationController::class, 'pushList'])->name('communication.push.list');
    Route::post('communication/push',    [AdminCommunicationController::class, 'pushStore'])->name('communication.push.store');
    Route::post('communication/broadcast', [AdminCommunicationController::class, 'broadcastSend'])->name('communication.broadcast.send');

    // ── Support ──
    Route::get('support/tickets',        [AdminSupportController::class, 'ticketsList'])->name('support.tickets.list');
    Route::get('support/tickets/{ticket}', [AdminSupportController::class, 'ticketsShow'])->name('support.tickets.show');
    Route::put('support/tickets/{ticket}', [AdminSupportController::class, 'ticketsUpdate'])->name('support.tickets.update');
    Route::delete('support/tickets/{ticket}', [AdminSupportController::class, 'ticketsDestroy'])->name('support.tickets.destroy');

    // ── System ──
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

    // ── System Extended (Email, SMS, API Keys, Security, Maintenance, Logs, etc.) ──
    Route::get('system/email-config',       [AdminSystemController::class, 'emailConfigData'])->name('system.email-config.data');
    Route::post('system/email-config',      [AdminSystemController::class, 'emailConfigSave'])->name('system.email-config.save');
    Route::post('system/test-email',        [AdminSystemController::class, 'testEmail'])->name('system.test-email');
    Route::get('system/sms-config',         [AdminSystemController::class, 'smsConfigData'])->name('system.sms-config.data');
    Route::post('system/sms-config',        [AdminSystemController::class, 'smsConfigSave'])->name('system.sms-config.save');
    Route::get('system/api-keys',           [AdminSystemController::class, 'apiKeysList'])->name('system.api-keys.list');
    Route::post('system/api-keys',          [AdminSystemController::class, 'apiKeysStore'])->name('system.api-keys.store');
    Route::post('system/api-keys/{config}/toggle', [AdminSystemController::class, 'apiKeysToggle'])->name('system.api-keys.toggle');
    Route::delete('system/api-keys/{config}', [AdminSystemController::class, 'apiKeysDestroy'])->name('system.api-keys.destroy');
    Route::get('system/security',           [AdminSystemController::class, 'securityData'])->name('system.security.data');
    Route::post('system/security',          [AdminSystemController::class, 'securitySave'])->name('system.security.save');
    Route::get('system/maintenance',        [AdminSystemController::class, 'maintenanceData'])->name('system.maintenance.data');
    Route::post('system/maintenance',       [AdminSystemController::class, 'maintenanceToggle'])->name('system.maintenance.toggle');
    Route::get('system/error-logs',         [AdminSystemController::class, 'errorLogsList'])->name('system.error-logs.list');
    Route::delete('system/error-logs',      [AdminSystemController::class, 'errorLogsClear'])->name('system.error-logs.clear');
    Route::get('system/logs',               [AdminSystemController::class, 'logsList'])->name('system.logs.list');
    Route::post('system/logs/view',         [AdminSystemController::class, 'logsView'])->name('system.logs.view');
    Route::get('system/logs/download',      [AdminSystemController::class, 'logsDownload'])->name('system.logs.download');
    Route::post('system/logs/clear',        [AdminSystemController::class, 'logsClear'])->name('system.logs.clear');
    Route::get('system/file-backups',       [AdminSystemController::class, 'fileBackupsList'])->name('system.file-backups.list');
    Route::post('system/file-backups',      [AdminSystemController::class, 'fileBackupsCreate'])->name('system.file-backups.create');
    Route::post('system/backup-restore/{backup}', [AdminSystemController::class, 'backupRestoreRun'])->name('system.backup-restore.run');
    Route::get('system/backup-schedule',    [AdminSystemController::class, 'backupScheduleData'])->name('system.backup-schedule.data');
    Route::post('system/backup-schedule',   [AdminSystemController::class, 'backupScheduleSave'])->name('system.backup-schedule.save');
    Route::get('system/updates',            [AdminSystemController::class, 'updatesList'])->name('system.updates.list');
    Route::post('system/updates/check',     [AdminSystemController::class, 'updatesCheck'])->name('system.updates.check');
    Route::post('system/updates/run',       [AdminSystemController::class, 'updatesRun'])->name('system.updates.run');

    // ── Settings (Currency, Receipt Printers) ──
    Route::get('settings/currency',         [AdminSystemController::class, 'currencyList'])->name('settings.currency.list');
    Route::post('settings/currency',        [AdminSystemController::class, 'currencyStore'])->name('settings.currency.store');
    Route::delete('settings/currency/{code}', [AdminSystemController::class, 'currencyDestroy'])->name('settings.currency.destroy');
    Route::get('settings/receipt-printers', [AdminSystemController::class, 'receiptPrintersList'])->name('settings.receipt-printers.list');
    Route::post('settings/receipt-printers',[AdminSystemController::class, 'receiptPrintersStore'])->name('settings.receipt-printers.store');
    Route::delete('settings/receipt-printers/{key}', [AdminSystemController::class, 'receiptPrintersDestroy'])->name('settings.receipt-printers.destroy');
    Route::post('settings/receipt-printers/test', [AdminSystemController::class, 'receiptPrintersTest'])->name('settings.receipt-printers.test');

    // ── Cache ──
    Route::get('cache',                    [AdminCacheController::class, 'list'])->name('cache.list');
    Route::post('cache/clear',             [AdminCacheController::class, 'clear'])->name('cache.clear');
    Route::post('cache/optimize',          [AdminCacheController::class, 'optimize'])->name('cache.optimize');

    // ── Database ──
    Route::get('database/tables',          [AdminDatabaseController::class, 'tables'])->name('database.tables');
    Route::get('database/structure/{table}', [AdminDatabaseController::class, 'structure'])->name('database.structure');
    Route::post('database/optimize/{table}', [AdminDatabaseController::class, 'optimize'])->name('database.optimize');
    Route::post('database/query',          [AdminDatabaseController::class, 'query'])->name('database.query');

    // ── File Manager ──
    Route::get('file-manager',             [AdminFileManagerController::class, 'list'])->name('file-manager.list');
    Route::post('file-manager/upload',     [AdminFileManagerController::class, 'upload'])->name('file-manager.upload');
    Route::post('file-manager/folder',     [AdminFileManagerController::class, 'createFolder'])->name('file-manager.folder');
    Route::post('file-manager/delete',     [AdminFileManagerController::class, 'delete'])->name('file-manager.delete');
    Route::get('file-manager/download',    [AdminFileManagerController::class, 'download'])->name('file-manager.download');
});
