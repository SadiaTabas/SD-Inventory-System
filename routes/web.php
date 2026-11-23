<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReviewModerationController;

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\AccountController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Public Customer Storefront Routes
|--------------------------------------------------------------------------
*/
Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'SD Inventory Laravel on Vercel is running!',
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
        'time' => now()->toDateTimeString(),
    ]);
});

Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/shop/category/{category}', [StorefrontController::class, 'category'])->name('shop.category');
Route::get('/product/{product}', [StorefrontController::class, 'productDetails'])->name('product.details');

// Shopping Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
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

// Public Admin Invitation Verification
Route::get('/admin/verify-invitation/{token}', [AdminController::class, 'verifyInvitation'])->name('admin.verify.invitation');

/*
|--------------------------------------------------------------------------
| Customer Account & Checkout Routes (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
    Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Customer Portal
    Route::get('/account', [AccountController::class, 'dashboard'])->name('customer.account');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('customer.profile');
    Route::post('/account/profile', [AccountController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('customer.password.update');

    // Addresses
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('customer.addresses');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('customer.addresses.store');
    Route::put('/account/addresses/{address}', [AccountController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('/account/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('customer.addresses.delete');
    Route::post('/account/addresses/{address}/default', [AccountController::class, 'setDefaultAddress'])->name('customer.addresses.default');

    // Customer Orders
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('customer.orders');
    Route::get('/account/orders/{order}', [AccountController::class, 'showOrder'])->name('customer.orders.show');
    Route::get('/account/orders/{order}/invoice', [AccountController::class, 'downloadInvoice'])->name('customer.orders.invoice');
    Route::post('/account/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('customer.orders.cancel');

    // Wishlist
    Route::get('/account/wishlist', [AccountController::class, 'wishlist'])->name('customer.wishlist');
    Route::post('/wishlist/toggle/{product}', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::post('/wishlist/remove/{product}', [AccountController::class, 'removeFromWishlist'])->name('wishlist.remove');
    Route::post('/wishlist/move-to-cart/{product}', [AccountController::class, 'moveWishlistToCart'])->name('wishlist.moveToCart');

    // Reviews
    Route::get('/account/reviews', [AccountController::class, 'reviews'])->name('customer.reviews');
    Route::post('/product/{product}/review', [AccountController::class, 'storeReview'])->name('customer.review.store');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Protected by role:admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Products
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{product}/update', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{product}/destroy', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/products/import', [ProductController::class, 'showImportForm'])->name('product.import.form');
    Route::post('/products/import', [ProductController::class, 'import'])->name('product.import');

    // Categories
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{category}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{category}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');

    // Orders
    Route::resource('order', OrderController::class);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('order.invoice');
    Route::post('/orders/{order}/payments', [OrderController::class, 'addPayment'])->name('order.payments.store');
    Route::post('/orders/{payment}/verify-payment', [OrderController::class, 'verifyPayment'])->name('order.payment.verify');
    Route::post('/orders/{order}/assign-rider', [OrderController::class, 'assignRider'])->name('order.assign.rider');

    // Customers
    Route::resource('customer', CustomerController::class);

    // Staff / Admins
    Route::resource('admin', AdminController::class);

    // Coupons
    Route::resource('coupons', CouponController::class);

    // Inventory & Stock Movements
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::post('/inventory/{product}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');

    // Deliveries & Riders
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/agents/create', [DeliveryController::class, 'create'])->name('deliveries.create');
    Route::post('/deliveries/agents/store', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::get('/deliveries/agents/{delivery}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
    Route::put('/deliveries/agents/{delivery}/update', [DeliveryController::class, 'update'])->name('deliveries.update');
    Route::post('/deliveries/orders/{order}/status', [DeliveryController::class, 'updateStatus'])->name('deliveries.order.status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');

    // Review Moderation
    Route::get('/reviews', [ReviewModerationController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/status', [ReviewModerationController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{review}', [ReviewModerationController::class, 'destroy'])->name('reviews.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Email verification
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('admin.dashboard')->with('success', 'Your email has been verified!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});
