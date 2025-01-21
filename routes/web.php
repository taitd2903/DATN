<?php

use App\Http\Controllers\Admin\Coupons\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
    return view('index');
});

Route::resource('users', UserController::class);
Route::get('users/create', [UserController::class, 'create'])->name('users.create');
Route::post('users', [UserController::class, 'store'])->name('users.store');

Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');


//quang dat
Route::get('/', function () {
    return view('index');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('coupons', CouponController::class);
});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('coupons', CouponController::class);
});