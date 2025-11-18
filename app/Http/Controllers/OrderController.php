<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Hiển thị form xác nhận đơn hàng
    public function create()
    {
        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Lấy danh sách bàn có sẵn
        $availableTables = RestaurantTable::where('status', 'available')->get();

        $totalAmount = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('orders.create', compact('cart', 'totalAmount', 'availableTables'));
    }

    // Xử lý tạo đơn hàng
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
        ]);

        try {
            DB::beginTransaction();

            // Lấy giỏ hàng
            $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống');
            }

            // Kiểm tra bàn có sẵn không
            $table = RestaurantTable::findOrFail($request->table_id);
            if ($table->status !== 'available') {
                return redirect()->back()->with('error', 'Bàn đã được chọn hoặc không có sẵn');
            }

            // Tính tổng tiền
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => Auth::id(),
                'table_id' => $request->table_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Tạo order items từ cart items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
            }

            // Cập nhật trạng thái bàn
            $table->update(['status' => 'occupied']);

            // Xóa giỏ hàng
            CartItem::where('cart_id', $cart->id)->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                            ->with('success', 'Đơn hàng đã được tạo thành công! Mã đơn hàng: ' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    // Hiển thị chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['items.product', 'table'])
                      ->where('user_id', Auth::id())
                      ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    // Danh sách đơn hàng của user
    public function index()
    {
        $orders = Order::with(['items.product', 'table'])
                      ->where('user_id', Auth::id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // Hủy đơn hàng
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('table')
                          ->where('user_id', Auth::id())
                          ->where('status', 'pending')
                          ->findOrFail($id);

            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => 'cancelled']);

            // Cập nhật trạng thái bàn về available
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
        }
    }
}
 function(Request $request){
    $order = Order::with(['',''])
    ->where('user_id', Auth::id())
->findOrFail($request->id);
return view('
', compact('order'));   
<nav
    class="navbar navbar-dark bg-primary"
>
    <a class="navbar-brand" href="#">Navbar</a>
    <button
        class="navbar-toggler d-lg-none"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#collapsibleNavId"
        aria-controls="collapsibleNavId"
        aria-expanded="false"
        aria-label="Toggle navigation"
    ></button>
    <div class="collapse navbar-collapse" id="collapsibleNavId">
        <ul class="navbar-nav me-auto mt-2 mt-lg-0">
            <li class="nav-item">
                <a class="nav-link active" href="#" aria-current="page"
                    >Home <span class="visually-hidden">(current)</span></a
                >
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="dropdownId"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    >Dropdown</a
                >
                <div class="dropdown-menu" aria-labelledby="dropdownId">
                    <a class="dropdown-item" href="#">Action 1</a>
                    <a class="dropdown-item" href="#">Action 2</a>
                </div>
            </li>
        </ul>
        <form class="d-flex my-2 my-lg-0">
            <input
                class="form-control me-sm-2"
                type="text"
                placeholder="Search"
            />
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
                Search
            </button>
        </form>
    </div>
</nav> 
 keu
 <class= ""
e;ngbfvuo3jduejfuebjbjbjefbelbebfebfBKBjdbvjbd kgnmlgmeml4ig dvnkeknd  mlvmlmm
 }