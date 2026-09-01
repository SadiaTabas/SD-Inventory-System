<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CouponController;



// Home
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

// Reset Password
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Protected Routes
Route::middleware('auth')->group(function () {

    // Admin Group
    Route::middleware('role:admin')->group(function () {

        // Admin Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Products
        Route::get('/product', [ProductController::class, 'index'])->name('product.index');
        Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
        Route::post('/product', [ProductController::class, 'store'])->name('product.store');
        Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
        Route::put('/product/{product}/update', [ProductController::class, 'update'])->name('product.update');
        Route::delete('/product/{product}/destroy', [ProductController::class, 'destroy'])->name('product.destroy');

        // Categories
        Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
        Route::put('/category/{category}/update', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/category/{category}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');

        // Orders
        Route::resource('order', OrderController::class);

        // Customers
        Route::resource('customer', CustomerController::class);

        // Admins
        Route::resource('admin', AdminController::class);
        Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('order.invoice');
 

Route::resource('coupons', CouponController::class);
Route::post('/orders/{order}/payments', [OrderController::class, 'addPayment'])->name('order.payments.store');


    });

    // Customer Group
    Route::middleware('role:customer')->group(function () {
        // Customer specific frontend routes (cart, checkout, profile)
    });

});