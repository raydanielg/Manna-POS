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
    return view('landing');
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
