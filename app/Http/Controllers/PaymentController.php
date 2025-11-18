<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Cart;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::where('id', $orderId)->where('user_id', Auth::id())->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status !== 'Pending') {
            return response()->json(['message' => 'Order already processed'], 400);
        }

        // Simulate payment processing
        try {
            DB::beginTransaction();

            // Here you would integrate with a payment gateway
            // For simulation, we assume payment is always successful

            $order->status = 'Paid';
            $order->payment_reference = 'PAY-' . strtoupper(uniqid());
            $order->save();

            DB::commit();
            return response()->json(['message' => 'Payment processed successfully', 'payment_reference' => $order->payment_reference], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Payment processing failed', 'error' => $e->getMessage()], 500);
        }
    }
}