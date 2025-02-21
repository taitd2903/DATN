<?php

use App\Http\Controllers\Admin\Coupons\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Categories\CategoryController as CategoryController;
use App\Http\Controllers\Admin\Products\ProductController;
use App\Http\Controllers\Users\ProductController as UserProductController;
use App\Http\Controllers\Admin\Products\ProductVariantController as ProductVariantController;
use App\Http\Controllers\CartController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//Sáng

//đăng nhập, đăng ký


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
   
    Route::resource('users', UserController::class);
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    

});

Route::middleware(['auth'])->group(function () {
        Route::get('/user', function () {
            return view('users.dashboard');
        })->name('users.dashboard');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//chuyển sang user
Route::middleware(['auth', 'role:admin'])->get('/switch-to-user', [AuthController::class, 'switchToUser'])->name('switch.to.user');

//

// Tài
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class);
    Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])
        ->name('products.variants.destroy');
});






//---------------------------------Đạt------------------------------------
//Giỏ Hàng
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update'); 
});
//Mã giảm giá
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('coupons', CouponController::class);
});
//-------------------------------End Đạt----------------------------------





Route::get('/categories', [CategoryController::class, 'showCategories'])->name('categories.show');

Route::get('products', [UserProductController::class, 'index'])->name('products.index');
Route::get('products/{product}', [UserProductController::class, 'show'])->name('products.show');
