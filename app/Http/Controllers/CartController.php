<?php
namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(Request $request) {
        $cart = $this->getOrCreateCart($request);
        $cart->load('items.product');
        return view('cart.index', compact('cart'));
    }  

    public function add(Request $request, $productId) {
        $product = Product::findOrFail($productId);
        $cart = $this->getOrCreateCart($request);
        
        $item = $cart->items()->where('product_id', $productId)->first();
        
        if($item){
            $item->increment('quantity', $request->input('quantity', 1));
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $request->input('quantity', 1)
            ]);
        }

        return redirect()->route('cart.index')->with('success','Đã thêm vào giỏ');
    }

    public function update(Request $request, $itemId) {
        $item = CartItem::findOrFail($itemId);
        $request->validate(['quantity' => 'required|integer|min:1']);
        $item->update(['quantity' => $request->quantity]);
        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công');
    }

    public function remove($itemId) {
        $item = CartItem::findOrFail($itemId);
        $item->delete();
        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm');
    }

    // Helper method để lấy hoặc tạo giỏ hàng
    private function getOrCreateCart(Request $request)
    {
        // Nếu user đã login
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        
        // Nếu chưa login, sử dụng session
        $sessionId = $request->session()->get('cart_session_id');
        if (!$sessionId) {
            $sessionId = Str::random(30);
            $request->session()->put('cart_session_id', $sessionId);
        }
        
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    // Chuyển giỏ hàng từ session sang user khi login
    public function migrateCart(Request $request)
    {
        if (Auth::check()) {
            $sessionId = $request->session()->get('cart_session_id');
            if ($sessionId) {
                $sessionCart = Cart::with('items')->where('session_id', $sessionId)->first();
                $userCart = Cart::with('items')->where('user_id', Auth::id())->first();
                
                if ($sessionCart && $sessionCart->items->isNotEmpty()) {
                    if (!$userCart) {
                        // Nếu user chưa có cart, chuyển session cart thành user cart
                        $sessionCart->update([
                            'user_id' => Auth::id(),
                            'session_id' => null
                        ]);
                    } else {
                        // Nếu user đã có cart, merge items
                        foreach ($sessionCart->items as $sessionItem) {
                            $existingItem = $userCart->items()
                                ->where('product_id', $sessionItem->product_id)
                                ->first();
                            
                            if ($existingItem) {
                                $existingItem->increment('quantity', $sessionItem->quantity);
                            } else {
                                $userCart->items()->create([
                                    'product_id' => $sessionItem->product_id,
                                    'quantity' => $sessionItem->quantity
                                ]);
                            }
                        }
                        // Xóa session cart
                        $sessionCart->items()->delete();
                        $sessionCart->delete();
                    }
                    
                    $request->session()->forget('cart_session_id');
                }
            }
        }
        
        return redirect()->route('cart.index');
    }
}