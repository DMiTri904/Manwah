<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;

class OrderController extends Controller
{
    // HIỂN THỊ DANH SÁCH ĐƠN HÀNG
    public function index()
    {
        // Nếu là admin/staff, xem tất cả đơn hàng
        if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
            $orders = Order::with(['items.product', 'table'])
                          ->latest()
                          ->paginate(10);
        } else {
            // Nếu là khách hàng thông thường, chỉ xem đơn hàng của bàn hiện tại
            $tableId = session('table_id');
            if ($tableId) {
                $orders = Order::with(['items.product', 'table'])
                              ->where('table_id', $tableId)
                              ->latest()
                              ->paginate(10);
            } else {
                $orders = collect();
            }
        }
        
        return view('orders.index', compact('orders'));
    }

    // Hiển thị form thanh toán / hóa đơn
    public function create()
    {
        $tableId = session('table_id');
        if (!$tableId) {
            return redirect()->route('menu')->with('error', 'Chưa chọn bàn');
        }

        $cart = Cart::with('items.product')->where('table_id', $tableId)->first();
        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('menu')->with('error', 'Giỏ hàng đang trống');
        }

        return view('orders.create', compact('cart', 'tableId'));
    }

  public function store(Request $request)
{
    $tableId = session('table_id');
    $cart = Cart::with('items.product')->where('table_id', $tableId)->first();

    if (!$cart || $cart->items->count() == 0) {
        return redirect()->route('menu')->with('error', 'Giỏ hàng đang trống');
    }

    // Tính tổng thủ công - ĐẢM BẢO KHÔNG LỖI
    $totalAmount = 0;
    foreach ($cart->items as $item) {
        if ($item->product && $item->product->price) {
            $totalAmount += $item->quantity * $item->product->price;
        }
    }

    // Tạo hóa đơn - ĐẢM BẢO TOTAL ĐƯỢC LƯU
    $order = Order::create([
        'table_id' => $tableId,
        'user_id' => auth()->id(),
        'total_amount' => $totalAmount, // SỐ TIỀN ĐÃ TÍNH
        'status' => 'pending',
        'created_at' => now(),
        // 'updated_at' => now(),
        // 'payment_method' =>$request->payment_method?? 'cash',   
        
    ]);

    // Chuyển cart_items sang order_items
    foreach ($cart->items as $item) {
        if ($item->product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }
    }

    // Xóa giỏ hàng
    $cart->items()->delete();
    $cart->delete();

    // Cập nhật trạng thái bàn
    RestaurantTable::where('id', $tableId)->update(['status' => 'occupied']);

    return redirect()->route('orders.show', $order->id)
        ->with('success', 'Đã tạo hóa đơn thành công. Tổng tiền: ' . number_format($totalAmount) . 'đ');
}

    // Xem chi tiết hóa đơn
    public function show($id)
    {
        $order = Order::with(['items.product', 'table'])->findOrFail($id);
        
        // Kiểm tra quyền xem đơn hàng
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            $tableId = session('table_id');
            if ($order->table_id != $tableId) {
                return redirect()->route('orders.index')->with('error', 'Bạn không có quyền xem đơn hàng này');
            }
        }
        
        return view('orders.show', compact('order'));
    }

    // HỦY ĐƠN HÀNG
    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        
        // Kiểm tra quyền hủy đơn hàng
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            $tableId = session('table_id');
            if ($order->table_id != $tableId) {
                return redirect()->route('orders.index')->with('error', 'Bạn không có quyền hủy đơn hàng này');
            }
        }
        
        $order->update(['status' => 'cancelled']);
        
        return redirect()->route('orders.index')
            ->with('success', 'Đã hủy đơn hàng thành công');
    }
}