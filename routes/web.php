<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// CONTROLLERS
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DashboardController;

// RESERVATION CONTROLLERS
use App\Http\Controllers\Customer\ReservationController as CustomerReservationController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;

/*
|--------------------------------------------------------------------------
| TRANG CHỦ
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('landing_guest');
})->name('landing');

/*
|--------------------------------------------------------------------------
| TEST DB
|--------------------------------------------------------------------------
*/
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return view('welcome');
    } catch (\Exception $e) {
        return "❌ Lỗi kết nối DB: " . $e->getMessage();
    }
});

/*
|--------------------------------------------------------------------------
| MENU CHO KHÁCH
|--------------------------------------------------------------------------
*/
Route::get('/menu', [ProductController::class, 'menu'])->name('menu');
Route::get('/menu/category/{categoryId}', [ProductController::class, 'filterByCategory'])->name('menu.filter');

/*
|--------------------------------------------------------------------------
| GIỎ HÀNG – CÔNG KHAI
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::delete('/cart/{id}', [CartController::class, 'remove']);
Route::post('/cart/migrate', [CartController::class, 'migrateCart'])->name('cart.migrate');

/*
|--------------------------------------------------------------------------
| THANH TOÁN – PAYMENT
|--------------------------------------------------------------------------
*/
Route::get('/payment/{orderId}/form', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/{orderId}/process', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/momo/callback', [PaymentController::class, 'momoCallback'])->name('payment.momo.callback');
Route::post('/payment/momo/ipn', [PaymentController::class, 'momoIPN'])->name('payment.momo.ipn');
Route::get('/api/payments/{orderId}/status', [PaymentController::class, 'checkPaymentStatus']);

Route::get('/test/momo/signature/{orderId}', [PaymentController::class, 'testMoMoSignature']);
Route::get('/debug/signature/{orderId}', [PaymentController::class, 'debugSignature']);
Route::get('/test/momo/fixed', [PaymentController::class, 'testWithFixedData']);

Route::prefix('api')->group(function () {
    Route::get('/payments/{orderId}/status', [PaymentController::class, 'checkPaymentStatus']);
    Route::post('/momo/ipn', [PaymentController::class, 'momoIPN']);
    Route::get('/payment/success/{orderId}', [PaymentController::class, 'showSuccess'])->name('payment.success');
    Route::get('/payment/momo/form/{orderId}', [PaymentController::class, 'showMoMoForm'])->name('payment.momo.form');
    Route::post('/payment/momo/simulate/{orderId}', [PaymentController::class, 'simulateMoMoPayment'])->name('payment.momo.simulate');
});

/*
|--------------------------------------------------------------------------
| ROUTES CẦN ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // CHỌN BÀN
    Route::get('/choose-table', [TableController::class, 'choose'])->name('choose.table');
    Route::post('/choose-table', [TableController::class, 'setTable'])->name('set.table');
    Route::post('/table/{table}/release', [TableController::class, 'releaseTable'])->name('table.release');

    // MENU THEO BÀN
    Route::get('/table/{table}/menu', [ProductController::class, 'menu'])->name('table.menu');

    // GIỎ HÀNG THEO BÀN
    Route::get('/table/{table}/cart', [CartController::class, 'index'])->name('table.cart');
    Route::post('/table/{table}/cart/add', [CartController::class, 'add'])->name('table.cart.add');
    Route::put('/table/{table}/cart/item/{id}', [CartController::class, 'update'])->name('table.cart.update');
    Route::delete('/table/{table}/cart/item/{id}', [CartController::class, 'remove'])->name('table.cart.remove');
    Route::post('/table/{table}/cart/clear', [CartController::class, 'clear'])->name('table.cart.clear');

    // ĐƠN HÀNG THEO BÀN
    Route::get('/table/{table}/order/create', [OrderController::class, 'create'])->name('table.order.create');
    Route::post('/table/{table}/order', [OrderController::class, 'store'])->name('table.order.store');

    // DASHBOARD ADMIN
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard');

    Route::resource('users', UsersController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);

    // QUẢN LÝ ĐƠN HÀNG
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| CUSTOMER RESERVATION
|--------------------------------------------------------------------------
*/
Route::get('/reserve', [CustomerReservationController::class, 'create'])->name('reservations.create');
Route::post('/reserve', [CustomerReservationController::class, 'store'])->name('reservations.store');
Route::get('/reserve/success/{reservationId}', [CustomerReservationController::class, 'showSuccess'])->name('reservations.success');

Route::get('/reservations/history', [CustomerReservationController::class, 'history'])
    ->middleware('auth')
    ->name('reservations.history');

/*
|--------------------------------------------------------------------------
| ADMIN RESERVATION
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:admin,staff'])
    ->name('admin.')
    ->group(function () {

        // Admin chỉ xem + sửa
        Route::resource('reservations', AdminReservationController::class)
            ->only(['index', 'edit', 'update']);
    });
