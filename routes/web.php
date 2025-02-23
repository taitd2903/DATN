<?php

use App\Http\Controllers\Admin\Coupons\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Categories\CategoryController;
use App\Http\Controllers\Admin\Products\ProductController;
use App\Http\Controllers\Users\ProductController as UserProductController;
use App\Http\Controllers\Admin\Products\ProductVariantController;
use App\Http\Controllers\Admin\Statistics\StatisticsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VnPayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/



// ========================= XÁC THỰC (AUTH) =========================
Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Chuyển đổi sang user
Route::middleware(['auth', 'role:admin'])->get('/switch-to-user', [AuthController::class, 'switchToUser'])->name('switch.to.user');

// ========================= QUẢN TRỊ VIÊN (ADMIN) =========================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/admin', fn () => view('admin.dashboard'))->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class);
    Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::resource('users', UserController::class);
    Route::resource('coupons', CouponController::class);
    Route::resource('statistics', StatisticsController::class);
    Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('variants.create');
    Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
});


// ========================= NGƯỜI DÙNG (USER) =========================
Route::middleware(['auth'])->group(function () {
    Route::get('/user', fn () => view('users.dashboard'))->name('users.dashboard');
});

// ========================= GIỎ HÀNG (CART) =========================
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
});

// ========================= SẢN PHẨM (PRODUCTS) =========================
Route::get('/categories', [CategoryController::class, 'showCategories'])->name('categories.show');
Route::prefix('')->name('products.')->group(function () {
    Route::get('/', [UserProductController::class, 'index'])->name('index');
    Route::get('/{product}', [UserProductController::class, 'show'])->name('show');
});

// ========================= THANH TOÁN (VNPAY) =========================
Route::get('/vnpay', function () {
    return view('vnpay.index');
})->name('vnpay.index');
Route::get('/vnpay/pay', function () {
    return view('vnpay.pay');
})->name('vnpay.payForm');
Route::post('/vnpay/pay', [VnPayController::class, 'createPayment'])->name('vnpay.pay');
Route::get('/vnpay/query', function () {
    return view('vnpay.query_transaction');
})->name('vnpay.queryForm');
Route::post('/vnpay/query', [VnPayController::class, 'queryTransaction'])->name('vnpay.query');
Route::get('/vnpay/refund', function () {
    return view('vnpay.refund');
})->name('vnpay.refundForm');
Route::post('/vnpay/refund', [VnPayController::class, 'processRefund'])->name('vnpay.refund');
Route::get('/vnpay/response', function () {
    return view('vnpay.response');
})->name('vnpay.response');
Route::post('/vnpay/payment', [VnPayController::class, 'createPayment'])->name('vnpay.payment');
Route::get('/vnpay/payment_return', [VnPayController::class, 'paymentReturn'])->name('vnpay.payment_return');
