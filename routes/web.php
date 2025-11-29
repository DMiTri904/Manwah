<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController; // THÊM DÒNG NÀY
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// ============================
//   TRANG CHỦ → LANDING PAGE
// ============================
Route::get('/', function () {
    return view('landing_guest');
})->name('landing');

// ============================
//   ROUTES CÔNG KHAI
// ============================
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return view('welcome');
    } catch (\Exception $e) {
        return "❌ Lỗi kết nối DB: " . $e->getMessage();
    }
});

// Menu cho khách hàng
Route::get('/menu', [ProductController::class, 'menu'])->name('menu');
Route::get('/menu/category/{categoryId}', [ProductController::class, 'filterByCategory'])->name('menu.filter');

// Giỏ hàng công khai
// Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
// Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
// Route::put('/cart/{id}', [CartController::class, 'update']);
// Route::delete('/cart/{id}', [CartController::class, 'remove']);
// Route::post('/cart/migrate', [CartController::class, 'migrateCart'])->name('cart.migrate');

// Routes thanh toán - ĐẶT TRƯỚC ORDERS
// Route::prefix('payment')->group(function () {
//     Route::get('/{orderId}/form', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
//     Route::post('/{orderId}/process', [PaymentController::class, 'processPayment'])->name('payment.process');
//     Route::post('/momo/callback', [PaymentController::class, 'momoCallback'])->name('payment.momo.callback');
//     Route::get('/result', [PaymentController::class, 'paymentResult'])->name('payment.result');
//     Route::get('/history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
//     Route::get('/pending-orders', [PaymentController::class, 'pendingOrders'])->name('payment.pending-orders');
// });
// Thêm vào routes/web.php
Route::get('/payment/{orderId}/form', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/{orderId}/process', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/momo/callback', [PaymentController::class, 'momoCallback'])->name('payment.momo.callback');
Route::post('/payment/momo/ipn', [PaymentController::class, 'momoIPN'])->name('payment.momo.ipn');
Route::get('/api/payments/{orderId}/status', [PaymentController::class, 'checkPaymentStatus']);
// routes/web.php
Route::get('/test/momo/signature/{orderId}', [PaymentController::class, 'testMoMoSignature']);
// routes/web.php
Route::get('/debug/signature/{orderId}', [PaymentController::class, 'debugSignature']);
Route::get('/test/momo/fixed', [PaymentController::class, 'testWithFixedData']);

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/payments/{orderId}/status', [PaymentController::class, 'checkPaymentStatus']);
    Route::post('/momo/ipn', [PaymentController::class, 'momoIPN']);
    Route::get('/payment/success/{orderId}', [PaymentController::class, 'showSuccess'])->name('payment.success');
    Route::get('/payment/momo/form/{orderId}', [PaymentController::class, 'showMoMoForm'])->name('payment.momo.form'); // Hiển thị form nhập thẻ
    Route::post('/payment/momo/simulate/{orderId}', [PaymentController::class, 'simulateMoMoPayment'])->name('payment.momo.simulate'); // Xử lý submit form (giả lập)

});

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('/admin/tables/create', [TableController::class, 'create'])->name('tables.create');
    Route::post('/admin/tables', [TableController::class, 'store'])->name('tables.store');
    Route::get('/admin/tables/{id}/edit', [TableController::class, 'edit'])->name('tables.edit');
    Route::put('/admin/tables/{id}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('/admin/tables/{id}', [TableController::class, 'destroy'])->name('tables.destroy');

});

// ============================
//   ROUTES YÊU CẦU ĐĂNG NHẬP
// ============================
Route::middleware(['auth'])->group(function () {


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

    // Đơn hàng theo bàn
    Route::get('/table/{table}/order/create', [OrderController::class, 'create'])->name('table.order.create');
    Route::post('/table/{table}/order', [OrderController::class, 'store'])->name('table.order.store');

    // ============================
    //   ADMIN / NHÂN VIÊN
    // ============================
    // Đánh dấu hoàn thành đơn hàng (chỉ staff/admin)
    Route::post('/orders/{id}/complete', [OrderController::class, 'markAsCompleted'])
        ->name('orders.complete');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard');

    // Users
  Route::get('/users', [UsersController::class, 'index'])->name('users.index');
Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
Route::post('/users', [UsersController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');

    // Products
    Route::resource('products', ProductController::class)->except(['show']);

    // Orders admin view
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// ============================
//   AUTHENTICATION ROUTES
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
