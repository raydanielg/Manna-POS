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
