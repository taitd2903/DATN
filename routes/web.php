<?php

use App\Http\Controllers\Admin\Banners\BannerController;
use App\Http\Controllers\Admin\Coupons\CouponController;
use App\Http\Controllers\Auth\RegisterController;
// use App\Http\Controllers\ComboController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Categories\CategoryController;
use App\Http\Controllers\Admin\Products\ProductController;
use App\Http\Controllers\Users\ProductController as UserProductController;
use App\Http\Controllers\Users\ProductFilterController;
use App\Http\Controllers\Admin\Products\ProductVariantController;
use App\Http\Controllers\Admin\Statistics\StatisticsController;
use App\Http\Controllers\Admin\Trash\TrashController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\VnPayController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\Reviews\AdminReviewController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/



// ========================= XÁC THỰC (AUTH) =========================
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');



Route::controller(AuthController::class)->group(function () {
    // Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Chuyển đổi sang user
Route::middleware(['auth', 'role:admin'])->get('/switch-to-user', [AuthController::class, 'switchToUser'])->name('switch.to.user');

// ========================= QUẢN TRỊ VIÊN (ADMIN) =========================
Route::prefix('admin')->middleware(['auth', 'role:admin,staff'])->name('admin.')->group(function () {
    Route::resource('banners', BannerController::class);
    Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class);
    Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
    // HEAD

    //combo
    // Route::resource('combos', ComboController::class);
    Route::resource('trash', TrashController::class);
    Route::patch('/trash/restore/{id}', [TrashController::class, 'restore'])->name('trash.restore');

    Route::get('products/{id}', [ProductController::class, 'show'])->name('admin.products.show');

    Route::resource('users', UserController::class);
    Route::resource('coupons', CouponController::class);
    Route::resource('statistics', StatisticsController::class);
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('variants.create');
    Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
});


// ========================= NGƯỜI DÙNG (USER) =========================
Route::middleware(['auth'])->group(function () {
    Route::get('/user', fn() => view('users.dashboard'))->name('users.dashboard');
});
Route::prefix('user/profile')->name('user.profile.')->middleware('auth')->group(function () {
    Route::get('/edit', [UserController::class, 'editProfile'])->name('edit'); // Chỉnh sửa profile
    Route::put('/update', [UserController::class, 'updateProfile'])->name('update'); // Cập nhật profile

});

Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('users.profile.edit');
Route::get('/get-districts/{city}', [UserController::class, 'getDistricts']);


// ========================= GIỎ HÀNG (CART) =========================
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
    Route::get('/check-stock', [CartController::class, 'checkStock'])->name('checkStock');
    Route::post('/update-price/{variantId}', [CartController::class, 'updatePrice'])->name('update.price');
});

// ========================= SẢN PHẨM (PRODUCTS) =========================
Route::get('/categories', [ProductFilterController::class, 'index'])->name('categories.show');
Route::get('/', [UserProductController::class, 'index'])->name('index');
Route::prefix('products')->name('products.')->group(function () {
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
Route::get('/checkout/continue/{order}', [VnPayController::class, 'continuePayment'])->name('checkout.continue');



//checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon')->middleware('auth');
Route::get('/checkout/get-applied-coupons', [CheckoutController::class, 'getAppliedCoupons'])->name('checkout.getAppliedCoupons')->middleware('auth');
Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon')->middleware('auth');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
Route::get('/checkout/invoice/{id}', [CheckoutController::class, 'invoice'])->name('checkout.invoice');

//admin/oder
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [CheckoutController::class, 'orderList'])->name('orders.index');
    Route::get('/orders/{order}/edit-status', [CheckoutController::class, 'editStatus'])->name('orders.editStatus');
    Route::put('/orders/{order}/update-status', [CheckoutController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show');

    // check reviews
    Route::get('/check_reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/approved', [AdminReviewController::class, 'approved'])->name('reviews.approved'); // hien thi danh gia da duyet
    Route::post('/reviews/{id}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});
// phần user người mua show ra
Route::middleware(['auth'])->group(function () {
    Route::get('users/tracking/order_tracking', [CheckoutController::class, 'orderTracking'])->name('order.tracking');
    Route::post('users/tracking/order_tracking/cancel/{order}', [CheckoutController::class, 'cancelOrder'])->name('order.cancel');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ========================= Quên mật khẩu =========================


Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

// chuyển quyền admin
Route::post('/admin/users/transfer-admin', [UserController::class, 'transferAdmin'])
    ->name('users.transferAdmin');
//khóa tài khoản
Route::get('/admin/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');




// danh gia
Route::get('/product/{id}/review', [ReviewController::class, 'create'])->name('product.review');
Route::post('/product/{id}/review', [ReviewController::class, 'store'])->name('product.review.store');
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

//=========================Xử lý Chat=========================//
Route::middleware('auth')->group(function () {
    Route::get('/admin/chat', [ChatController::class, 'index'])->name('admin.chat');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/history', [ChatController::class, 'getHistory'])->name('chat.history');
});

// ==========================language=========================//
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'vi'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return back();
})->name('change.language');