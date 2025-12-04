<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tất cả đơn hàng
        $orders = Order::orderBy('created_at', 'desc')->get();

        // -------------------------
        // Báo cáo tổng quan
        // -------------------------
        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', 'completed')->sum('total_amount');
        $todayRevenue = $orders->filter(fn($o) => $o->status === 'completed' && $o->created_at->isToday())
                                ->sum('total_amount');

        $statusCounts = $orders->groupBy('status')->map->count();

        // Chỉ lấy các phương thức thanh toán có dữ liệu
        $paymentCounts = $orders->groupBy('payment_method')->map->count()->filter(fn($count, $method) => $method);

        $orderDetails = $orders;

        // -------------------------
        // Lọc theo request nếu có
        // -------------------------
        $filteredOrders = $orders;

        if ($request->start_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $filteredOrders = $filteredOrders->filter(fn($o) => $o->created_at >= $start);
        }

        if ($request->end_date) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $filteredOrders = $filteredOrders->filter(fn($o) => $o->created_at <= $end);
        }

        $filterType = $request->filter_type ?? 'day';

        $filteredTotalOrders = $filteredOrders->count();
        $filteredTotalRevenue = $filteredOrders->where('status', 'completed')->sum('total_amount');
        $filteredStatusCounts = $filteredOrders->groupBy('status')->map->count();
        $filteredPaymentCounts = $filteredOrders->groupBy('payment_method')->map->count()->filter(fn($count, $method) => $method);
        $filteredOrderDetails = $filteredOrders;

        return view('report.index', compact(
            'totalOrders', 'totalRevenue', 'todayRevenue',
            'statusCounts', 'paymentCounts', 'orderDetails',
            'filteredTotalOrders', 'filteredTotalRevenue',
            'filteredStatusCounts', 'filteredPaymentCounts',
            'filteredOrderDetails', 'filterType'
        ));
    }
}

