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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Dashboard Sales Routes
Route::get('/dashboard/sales/pos', function () {
    return view('dashboard.sales.pos');
})->middleware('auth')->name('dashboard.sales.pos');
Route::get('/dashboard/sales/transactions', function () {
    return view('dashboard.sales.transactions');
})->middleware('auth')->name('dashboard.sales.transactions');
Route::get('/dashboard/sales/receipts', function () {
    return view('dashboard.sales.receipts');
})->middleware('auth')->name('dashboard.sales.receipts');

// Dashboard Inventory Routes
Route::get('/dashboard/inventory/products', function () {
    return view('dashboard.inventory.products');
})->middleware('auth')->name('dashboard.inventory.products');
Route::get('/dashboard/inventory/categories', function () {
    return view('dashboard.inventory.categories');
})->middleware('auth')->name('dashboard.inventory.categories');
Route::get('/dashboard/inventory/stock', function () {
    return view('dashboard.inventory.stock');
})->middleware('auth')->name('dashboard.inventory.stock');
Route::get('/dashboard/inventory/suppliers', function () {
    return view('dashboard.inventory.suppliers');
})->middleware('auth')->name('dashboard.inventory.suppliers');

// Dashboard Customers Routes
Route::get('/dashboard/customers/all', function () {
    return view('dashboard.customers.all');
})->middleware('auth')->name('dashboard.customers.all');
Route::get('/dashboard/customers/loyalty', function () {
    return view('dashboard.customers.loyalty');
})->middleware('auth')->name('dashboard.customers.loyalty');

// Dashboard Reports Routes
Route::get('/dashboard/reports/sales', function () {
    return view('dashboard.reports.sales');
})->middleware('auth')->name('dashboard.reports.sales');
Route::get('/dashboard/reports/inventory', function () {
    return view('dashboard.reports.inventory');
})->middleware('auth')->name('dashboard.reports.inventory');

// Dashboard Settings Route
Route::get('/dashboard/settings', function () {
    return view('dashboard.settings');
})->middleware('auth')->name('dashboard.settings');

// User Management Routes
Route::get('/dashboard/user-management/users', function () {
    return view('dashboard.user-management.users');
})->middleware('auth')->name('dashboard.user-management.users');
Route::get('/dashboard/user-management/roles', function () {
    return view('dashboard.user-management.roles');
})->middleware('auth')->name('dashboard.user-management.roles');
Route::get('/dashboard/user-management/locations', function () {
    return view('dashboard.user-management.locations');
})->middleware('auth')->name('dashboard.user-management.locations');
Route::get('/dashboard/user-management/sales-commission-agents', function () {
    return view('dashboard.user-management.sales-commission-agents');
})->middleware('auth')->name('dashboard.user-management.sales-commission-agents');
Route::get('/dashboard/profile', [App\Http\Controllers\Dashboard\UserManagementController::class, 'profile'])->middleware('auth')->name('dashboard.profile');
Route::put('/dashboard/profile', [App\Http\Controllers\Dashboard\UserManagementController::class, 'updateProfile'])->middleware('auth')->name('dashboard.profile.update');

// Contacts Routes
Route::get('/dashboard/contacts/suppliers', function () {
    return view('dashboard.contacts.suppliers');
})->middleware('auth')->name('dashboard.contacts.suppliers');
Route::get('/dashboard/contacts/customers', [App\Http\Controllers\Dashboard\CustomerController::class, 'index'])->middleware('auth')->name('dashboard.contacts.customers');
Route::get('/dashboard/contacts/customer-groups', function () {
    return view('dashboard.contacts.customer-groups');
})->middleware('auth')->name('dashboard.contacts.customer-groups');
Route::get('/dashboard/contacts/import-contacts', function () {
    return view('dashboard.contacts.import-contacts');
})->middleware('auth')->name('dashboard.contacts.import-contacts');

// Products Routes
Route::get('/dashboard/inventory/list-products', function () {
    return view('dashboard.inventory.list-products');
})->middleware('auth')->name('dashboard.inventory.list-products');
Route::get('/dashboard/inventory/add-product', function () {
    return view('dashboard.inventory.add-product');
})->middleware('auth')->name('dashboard.inventory.add-product');
Route::get('/dashboard/inventory/update-price', function () {
    return view('dashboard.inventory.update-price');
})->middleware('auth')->name('dashboard.inventory.update-price');
Route::get('/dashboard/inventory/print-labels', function () {
    return view('dashboard.inventory.print-labels');
})->middleware('auth')->name('dashboard.inventory.print-labels');
Route::get('/dashboard/inventory/variations', function () {
    return view('dashboard.inventory.variations');
})->middleware('auth')->name('dashboard.inventory.variations');
Route::get('/dashboard/inventory/import-products', function () {
    return view('dashboard.inventory.import-products');
})->middleware('auth')->name('dashboard.inventory.import-products');
Route::get('/dashboard/inventory/import-opening-stock', function () {
    return view('dashboard.inventory.import-opening-stock');
})->middleware('auth')->name('dashboard.inventory.import-opening-stock');
Route::get('/dashboard/inventory/selling-price-group', function () {
    return view('dashboard.inventory.selling-price-group');
})->middleware('auth')->name('dashboard.inventory.selling-price-group');
Route::get('/dashboard/inventory/units', function () {
    return view('dashboard.inventory.units');
})->middleware('auth')->name('dashboard.inventory.units');
Route::get('/dashboard/inventory/product-categories', function () {
    return view('dashboard.inventory.product-categories');
})->middleware('auth')->name('dashboard.inventory.product-categories');
Route::get('/dashboard/inventory/brands', function () {
    return view('dashboard.inventory.brands');
})->middleware('auth')->name('dashboard.inventory.brands');
Route::get('/dashboard/inventory/warranties', function () {
    return view('dashboard.inventory.warranties');
})->middleware('auth')->name('dashboard.inventory.warranties');

// Purchases Routes
Route::get('/dashboard/purchases/list-purchases', function () {
    return view('dashboard.purchases.list-purchases');
})->middleware('auth')->name('dashboard.purchases.list-purchases');
Route::get('/dashboard/purchases/add-purchase', function () {
    return view('dashboard.purchases.add-purchase');
})->middleware('auth')->name('dashboard.purchases.add-purchase');
Route::get('/dashboard/purchases/list-purchase-return', function () {
    return view('dashboard.purchases.list-purchase-return');
})->middleware('auth')->name('dashboard.purchases.list-purchase-return');

// Sell Routes
Route::get('/dashboard/sell/all-sales', function () {
    return view('dashboard.sell.all-sales');
})->middleware('auth')->name('dashboard.sell.all-sales');
Route::get('/dashboard/sell/add-sale', function () {
    return view('dashboard.sell.add-sale');
})->middleware('auth')->name('dashboard.sell.add-sale');
Route::get('/dashboard/sell/list-pos', function () {
    return view('dashboard.sell.list-pos');
})->middleware('auth')->name('dashboard.sell.list-pos');
Route::get('/dashboard/sell/pos', function () {
    return view('dashboard.sell.pos');
})->middleware('auth')->name('dashboard.sell.pos');
Route::get('/dashboard/sell/add-draft', function () {
    return view('dashboard.sell.add-draft');
})->middleware('auth')->name('dashboard.sell.add-draft');
Route::get('/dashboard/sell/list-drafts', function () {
    return view('dashboard.sell.list-drafts');
})->middleware('auth')->name('dashboard.sell.list-drafts');
Route::get('/dashboard/sell/add-quotation', function () {
    return view('dashboard.sell.add-quotation');
})->middleware('auth')->name('dashboard.sell.add-quotation');
Route::get('/dashboard/sell/list-quotations', function () {
    return view('dashboard.sell.list-quotations');
})->middleware('auth')->name('dashboard.sell.list-quotations');
Route::get('/dashboard/sell/list-sell-return', function () {
    return view('dashboard.sell.list-sell-return');
})->middleware('auth')->name('dashboard.sell.list-sell-return');
Route::get('/dashboard/sell/shipments', function () {
    return view('dashboard.sell.shipments');
})->middleware('auth')->name('dashboard.sell.shipments');
Route::get('/dashboard/sell/discounts', function () {
    return view('dashboard.sell.discounts');
})->middleware('auth')->name('dashboard.sell.discounts');
Route::get('/dashboard/sell/import-sales', function () {
    return view('dashboard.sell.import-sales');
})->middleware('auth')->name('dashboard.sell.import-sales');

// Stock Transfer Routes
Route::get('/dashboard/stock-transfer/list-stock-transfer', function () {
    return view('dashboard.stock-transfer.list-stock-transfer');
})->middleware('auth')->name('dashboard.stock-transfer.list-stock-transfer');
Route::get('/dashboard/stock-transfer/add-stock-transfer', function () {
    return view('dashboard.stock-transfer.add-stock-transfer');
})->middleware('auth')->name('dashboard.stock-transfer.add-stock-transfer');

// Stock Adjustment Routes
Route::get('/dashboard/stock-adjustment/list-stock-adjustment', function () {
    return view('dashboard.stock-adjustment.list-stock-adjustment');
})->middleware('auth')->name('dashboard.stock-adjustment.list-stock-adjustment');
Route::get('/dashboard/stock-adjustment/add-stock-adjustment', function () {
    return view('dashboard.stock-adjustment.add-stock-adjustment');
})->middleware('auth')->name('dashboard.stock-adjustment.add-stock-adjustment');

// Expenses Routes
Route::get('/dashboard/expenses/list-expenses', function () {
    return view('dashboard.expenses.list-expenses');
})->middleware('auth')->name('dashboard.expenses.list-expenses');
Route::get('/dashboard/expenses/add-expense', function () {
    return view('dashboard.expenses.add-expense');
})->middleware('auth')->name('dashboard.expenses.add-expense');
Route::get('/dashboard/expenses/expense-categories', function () {
    return view('dashboard.expenses.expense-categories');
})->middleware('auth')->name('dashboard.expenses.expense-categories');

// Reports Routes
Route::get('/dashboard/reports/sales-report', function () {
    return view('dashboard.reports.sales-report');
})->middleware('auth')->name('dashboard.reports.sales-report');
Route::get('/dashboard/reports/purchase-report', function () {
    return view('dashboard.reports.purchase-report');
})->middleware('auth')->name('dashboard.reports.purchase-report');
Route::get('/dashboard/reports/inventory-report', function () {
    return view('dashboard.reports.inventory-report');
})->middleware('auth')->name('dashboard.reports.inventory-report');
Route::get('/dashboard/reports/expense-report', function () {
    return view('dashboard.reports.expense-report');
})->middleware('auth')->name('dashboard.reports.expense-report');
Route::get('/dashboard/reports/profit-loss-report', function () {
    return view('dashboard.reports.profit-loss-report');
})->middleware('auth')->name('dashboard.reports.profit-loss-report');

// Notification Templates Routes
Route::get('/dashboard/notification-templates', function () {
    return view('dashboard.notification-templates.index');
})->middleware('auth')->name('dashboard.notification-templates');

// Settings Routes
Route::get('/dashboard/settings/general', function () {
    return view('dashboard.settings.general');
})->middleware('auth')->name('dashboard.settings.general');
Route::get('/dashboard/settings/business-location', function () {
    return view('dashboard.settings.business-location');
})->middleware('auth')->name('dashboard.settings.business-location');
Route::get('/dashboard/settings/invoice-settings', function () {
    return view('dashboard.settings.invoice-settings');
})->middleware('auth')->name('dashboard.settings.invoice-settings');
Route::get('/dashboard/settings/barcode-settings', function () {
    return view('dashboard.settings.barcode-settings');
})->middleware('auth')->name('dashboard.settings.barcode-settings');
Route::get('/dashboard/settings/tax-rates', function () {
    return view('dashboard.settings.tax-rates');
})->middleware('auth')->name('dashboard.settings.tax-rates');

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
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\PlanManagementController;

// ─── Plan Management View Routes ────────────────────────────────────────────
Route::get('/dashboard/plan-management/plans', function () {
    return view('dashboard.plan-management.plans');
})->middleware('auth')->name('dashboard.plan-management.plans');

Route::get('/dashboard/plan-management/subscriptions', function () {
    return view('dashboard.plan-management.subscriptions');
})->middleware('auth')->name('dashboard.plan-management.subscriptions');

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
    Route::get('users/stats',                    [UserManagementController::class, 'stats']);
    Route::apiResource('users',                  UserManagementController::class);
    Route::apiResource('locations',              LocationController::class);
    Route::post('roles/seed-defaults',           [RoleController::class, 'seedDefaults']);
    Route::get('roles/permissions',              [RoleController::class, 'permissionList']);

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
