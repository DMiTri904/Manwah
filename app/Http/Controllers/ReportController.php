<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', 'completed')->sum('total_amount');
        $todayRevenue = $orders->filter(fn($o) => $o->status === 'completed' && $o->created_at->isToday())
                               ->sum('total_amount');

        // Thống kê theo trạng thái
        $statusCounts = $orders->groupBy('status')->map->count();

        // Thống kê theo phương thức thanh toán
        $paymentCounts = $orders->groupBy('payment_method')->map->count();

        // Lấy danh sách chi tiết các đơn hàng
        $orderDetails = $orders;

        return view('report.index', compact(
            'totalOrders', 'totalRevenue', 'todayRevenue',
            'statusCounts', 'paymentCounts', 'orderDetails'
        ));
    }
}
