<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Đảm bảo chỉ user đăng nhập mới truy cập
        $this->middleware('role:customer'); // Chỉ cho phép role customer truy cập
    }

    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10); // Phân trang 10 đơn/trang

        return view('Customer.dashboard_cus', compact('user', 'orders'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $user->update($request->only(['name', 'phone', 'address'])); // Giả định thêm fields phone, address vào User model

        return back()->with('success', 'Thông tin cá nhân đã được cập nhật.');
    }
}