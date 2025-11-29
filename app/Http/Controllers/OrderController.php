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
        if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
            $orders = Order::with(['items.product', 'table'])
                ->latest()
                ->paginate(10);
        } else {
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

    // Hiển thị form xác nhận trước khi gửi bếp
    public function create($tableId)
    {
        if (!session('table_id') || session('table_id') != $tableId) {
            return redirect()->route('choose.table')
                ->with('error', 'Vui lòng chọn bàn trước');
        }

        $cart = Cart::with('items.product')->where('table_id', $tableId)->first();

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('table.menu', ['table' => $tableId])
                ->with('error', 'Giỏ hàng đang trống');
        }

        return view('orders.create', compact('cart', 'tableId'));
    }

    // ⭐ GỬI VÀO BẾP - LOGIC CHÍNH (Dim Sum Flow)
    public function store(Request $request, $tableId)
    {
        $cart = Cart::with('items.product')->where('table_id', $tableId)->first();

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('table.menu', ['table' => $tableId])
                ->with('error', 'Giỏ hàng đang trống');
        }

        // ⭐ KIỂM TRA BÀN NÀY ĐÃ CÓ ORDER ĐANG HOẠT ĐỘNG CHƯA?
        $existingOrder = Order::where('table_id', $tableId)
            ->whereIn('status', ['confirmed', 'active'])
            ->first();

        if ($existingOrder) {
            // ⭐ ĐÃ CÓ ORDER → THÊM VÀO ORDER CŨ (BATCH MỚI)

            // Tìm batch_number lớn nhất hiện tại
            $maxBatch = OrderItem::where('order_id', $existingOrder->id)
                ->max('batch_number');
            $newBatchNumber = ($maxBatch ?? 0) + 1;

            $batchTotal = 0;
            $itemCount = 0;

            foreach ($cart->items as $item) {
                if ($item->product) {
                    OrderItem::create([
                        'order_id' => $existingOrder->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'batch_number' => $newBatchNumber,
                        'batch_created_at' => now(),
                    ]);

                    $batchTotal += $item->product->price * $item->quantity;
                    $itemCount++;
                }
            }

            // Cập nhật tổng tiền order
            $existingOrder->total_amount += $batchTotal;
            $existingOrder->updated_at = now();
            $existingOrder->save();

            // ⭐ XÓA GIỎ HÀNG - QUAN TRỌNG
            try {
                $cart->items()->delete();
                $cart->delete();
            } catch (\Exception $e) {
                \Log::error('Lỗi xóa giỏ hàng: ' . $e->getMessage());
            }

            return redirect()->route('orders.show', $existingOrder->id)
                ->with('success', "✅ Đã gọi thêm {$itemCount} món (Đợt {$newBatchNumber})! Tổng order: " . number_format($existingOrder->total_amount) . 'đ');

        } else {
            // ⭐ CHƯA CÓ ORDER → TẠO ORDER MỚI (BATCH 1)

            $totalAmount = 0;
            foreach ($cart->items as $item) {
                if ($item->product && $item->product->price) {
                    $totalAmount += $item->quantity * $item->product->price;
                }
            }

            $order = Order::create([
                'table_id' => $tableId,
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'status' => 'confirmed', // Trạng thái: Đã xác nhận, đang chờ bếp
                'created_at' => now(),
            ]);

            $itemCount = 0;
            foreach ($cart->items as $item) {
                if ($item->product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'batch_number' => 1, // ⭐ ĐỢT ĐẦU TIÊN
                        'batch_created_at' => now(),
                    ]);
                    $itemCount++;
                }
            }

            // ⭐ XÓA GIỎ HÀNG - QUAN TRỌNG
            try {
                $cart->items()->delete();
                $cart->delete();
            } catch (\Exception $e) {
                \Log::error('Lỗi xóa giỏ hàng: ' . $e->getMessage());
            }

            // Cập nhật trạng thái bàn
            RestaurantTable::where('id', $tableId)->update(['status' => 'occupied']);

            return redirect()->route('orders.show', $order->id)
                ->with('success', "✅ Đã gửi {$itemCount} món vào bếp! Tổng: " . number_format($totalAmount) . 'đ');
        }
    }

    // Xem chi tiết hóa đơn
    public function show($id)
    {
        $order = Order::with(['items.product', 'table', 'user'])->findOrFail($id);

        // Kiểm tra quyền xem đơn hàng
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            $tableId = session('table_id');
            if ($order->table_id != $tableId) {
                return redirect()->route('orders.index')
                    ->with('error', 'Bạn không có quyền xem đơn hàng này');
            }
        }

        return view('orders.show', compact('order'));
    }

    // ⭐ ĐÁNH DẤU HOÀN THÀNH (ĂN XONG, CHUẨN BỊ THANH TOÁN)
    public function markAsCompleted($id)
    {
        $order = Order::findOrFail($id);

        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            return back()->with('error', 'Chỉ nhân viên mới có thể đánh dấu hoàn thành');
        }

        $order->update(['status' => 'completed']);

        return back()->with('success', '✅ Đã đánh dấu hoàn thành! Khách có thể thanh toán.');
    }

    // HỦY ĐƠN HÀNG (CHỈ ADMIN/STAFF)
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            return redirect()->route('orders.index')
                ->with('error', 'Chỉ nhân viên mới có thể hủy đơn hàng');
        }

        // Chỉ hủy được khi chưa thanh toán
        if ($order->status == 'paid') {
            return back()->with('error', 'Không thể hủy đơn hàng đã thanh toán');
        }

        $order->update(['status' => 'cancelled']);

        // Giải phóng bàn nếu đã hủy
        RestaurantTable::where('id', $order->table_id)
            ->update(['status' => 'available']);

        return redirect()->route('orders.index')
            ->with('success', 'Đã hủy đơn hàng thành công');
    }
}
