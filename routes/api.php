<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\SaleApiController;
use App\Http\Controllers\Api\ExpenseApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\ExpenseCategory;
use App\Models\Business;

// ── Public Auth ────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

// ── Public Lookup (for form data) ────────────────────────────────────────────
Route::get('categories', fn() => response()->json(ProductCategory::select('id','name')->orderBy('name')->get()));

// ── Protected Routes ───────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->name('mapi.')->group(function () {

    // Auth
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout'])->name('logout');
        Route::get('user',             [AuthController::class, 'user'])->name('user');
        Route::put('profile',          [AuthController::class, 'updateProfile'])->name('profile');
    });

    // Dashboard
    Route::get('dashboard/stats',      [DashboardApiController::class, 'stats'])->name('dashboard.stats');

    // Resources
    Route::apiResource('products',     ProductApiController::class)->names('products');
    Route::get('products-form-data',   [ProductApiController::class, 'formData'])->name('products.form-data');
    Route::apiResource('customers',    CustomerApiController::class)->names('customers');
    Route::apiResource('sales',        SaleApiController::class)->names('sales');
    Route::apiResource('expenses',     ExpenseApiController::class)->names('expenses');

    // Reports
    Route::get('reports/sales',        [ReportApiController::class, 'sales'])->name('reports.sales');
    Route::get('reports/profit-loss',  [ReportApiController::class, 'profitLoss'])->name('reports.pl');
    Route::get('reports/inventory',    [ReportApiController::class, 'inventory'])->name('reports.inventory');

    // Lookup lists (read-only)
    Route::get('categories',           fn() => response()->json(ProductCategory::select('id','name')->orderBy('name')->get()))->name('categories');
    Route::get('brands',               fn() => response()->json(Brand::select('id','name')->orderBy('name')->get()))->name('brands');
    Route::get('units',                fn() => response()->json(Unit::select('id','name','short_name')->orderBy('name')->get()))->name('units');
    Route::get('suppliers',            fn() => response()->json(Supplier::select('id','name','company')->orderBy('name')->get()))->name('suppliers');
    Route::get('expense-categories',   fn() => response()->json(ExpenseCategory::select('id','name')->orderBy('name')->get()))->name('expense-categories');
    Route::get('users',                fn() => response()->json(\App\Models\User::select('id','name','email','role','business_name','business_city','business_country','currency','created_at')->orderBy('name')->get()))->name('users');
    Route::get('businesses',           fn() => response()->json(Business::with('category:id,name')->orderBy('business_name')->get()))->name('businesses');

    // Staff management
    Route::get('staff',           [App\Http\Controllers\Api\StaffApiController::class, 'index']);
    Route::post('staff',          [App\Http\Controllers\Api\StaffApiController::class, 'store']);
    Route::get('staff/{id}',      [App\Http\Controllers\Api\StaffApiController::class, 'show']);
    Route::put('staff/{id}',      [App\Http\Controllers\Api\StaffApiController::class, 'update']);
    Route::delete('staff/{id}',   [App\Http\Controllers\Api\StaffApiController::class, 'destroy']);
});
