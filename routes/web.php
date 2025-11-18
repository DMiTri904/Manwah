<?php

use Illuminate\Support\Facades\Route;

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




use App\Http\Controllers\UsersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

// routes/web.php
Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/migrate', [CartController::class, 'migrateCart'])->name('cart.migrate');

// Order routes
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// Route::resource('orders', OrderController::class);


Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
return view('welcome');
    } catch (\Exception $e) {
        return "❌ Lỗi kết nối DB: " . $e->getMessage();
    }
});
Route::get('/', function () {
    return view('Dashboard');
})->name('Dashboard');

// Route::get('/users', function () {
//     try{
//     $users = DB::table('users')->get();
//     return view('Users', ['users' => $users]); 
//     }catch (\Exception $e) {
//         return "❌ Lỗi kết nối DB: " . $e->getMessage();
//     }
// });
Route::get('/users', [UsersController::class, 'index'])->name('users.index');
Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
Route::post('/users', [UsersController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
Route::get('/users/{user}', [UsersController::class, 'show'])->name('users.show');//thêm 



Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');  




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
