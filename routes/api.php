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

// ── Public Auth ────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
});

// ── Protected Routes ───────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::get('user',             [AuthController::class, 'user']);
        Route::put('profile',          [AuthController::class, 'updateProfile']);
    });

    // Dashboard
    Route::get('dashboard/stats',      [DashboardApiController::class, 'stats']);

    // Resources
    Route::apiResource('products',     ProductApiController::class);
    Route::get('products-form-data',   [ProductApiController::class, 'formData']);
    Route::apiResource('customers',    CustomerApiController::class);
    Route::apiResource('sales',        SaleApiController::class);
    Route::apiResource('expenses',     ExpenseApiController::class);

    // Reports
    Route::get('reports/sales',        [ReportApiController::class, 'sales']);
    Route::get('reports/profit-loss',  [ReportApiController::class, 'profitLoss']);
    Route::get('reports/inventory',    [ReportApiController::class, 'inventory']);

    // Lookup lists (read-only)
    Route::get('categories',           fn() => response()->json(ProductCategory::select('id','name')->orderBy('name')->get()));
    Route::get('brands',               fn() => response()->json(Brand::select('id','name')->orderBy('name')->get()));
    Route::get('units',                fn() => response()->json(Unit::select('id','name','short_name')->orderBy('name')->get()));
    Route::get('suppliers',            fn() => response()->json(Supplier::select('id','name','company')->orderBy('name')->get()));
    Route::get('expense-categories',   fn() => response()->json(ExpenseCategory::select('id','name')->orderBy('name')->get()));
});
