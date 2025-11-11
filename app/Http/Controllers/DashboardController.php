<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Bảo vệ bằng auth
    }

    public function index()
    {
$stats = [
    // thống kê số lượng
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' =>Order::count()  
        ];
        return view('dashboard', compact('stats')); // return đến dashboard
    }
}