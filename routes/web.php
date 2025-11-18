<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;


// ============================
//   TRANG CHỦ → ĐĂNG NHẬP
// ============================
Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    // Chọn bàn
    Route::get('/choose-table', [TableController::class, 'choose'])->name('choose.table');
    Route::post('/choose-table', [TableController::class, 'setTable'])->name('set.table');
    Route::post('/table/{table}/release', [TableController::class, 'releaseTable'])->name('table.release');


    // Menu theo bàn
    Route::get('/table/{table}/menu', [ProductController::class, 'menu'])->name('table.menu');

    // Giỏ hàng theo bàn
    Route::get('/table/{table}/cart', [CartController::class, 'index'])->name('table.cart');
    Route::post('/table/{table}/cart/add', [CartController::class, 'add'])->name('table.cart.add');
    Route::put('/table/{table}/cart/item/{id}', [CartController::class, 'update'])->name('table.cart.update');
    Route::delete('/table/{table}/cart/item/{id}', [CartController::class, 'remove'])->name('table.cart.remove');
    Route::post('/table/{table}/cart/clear', [CartController::class, 'clear'])->name('table.cart.clear');


    // Cập nhật món
    Route::put('/table/{table}/cart/item/{id}', [CartController::class, 'update'])->name('table.cart.update');

    // Xóa món
    Route::delete('/table/{table}/cart/item/{id}', [CartController::class, 'remove'])->name('table.cart.remove');


    // Hóa đơn theo bàn
    Route::get('/table/{table}/order/create', [OrderController::class, 'create'])->name('table.order.create');
    Route::post('/table/{table}/order', [OrderController::class, 'store'])->name('table.order.store');


    // ============================
//     ADMIN / NHÂN VIÊN
// ============================

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard');

    // Users
    Route::resource('users', UsersController::class)->except(['show']);

    // Products
    Route::resource('products', ProductController::class)->except(['show']);

    // Orders admin view
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});


// ============================
//     PASSWORD RESET
// ============================
Route::get('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->name('password.update');

Auth::routes();


Route::get('/', function () {
    return view('landing_guest');   
})->name('landing');

