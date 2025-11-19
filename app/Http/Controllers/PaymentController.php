<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\User;

class PaymentController extends Controller
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;
    private $returnUrl;
    private $notifyUrl;

    public function __construct()
    {
        $this->partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
        $this->accessKey = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
        $this->secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $this->endpoint = env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $this->returnUrl = env('MOMO_RETURN_URL', 'http://localhost:8000/payment/momo/callback');
        $this->notifyUrl = env('MOMO_NOTIFY_URL', 'http://localhost:8000/payment/momo/ipn');
    }

    /**
     * Hiển thị form thanh toán - Lấy thông tin đơn hàng từ database
     */
    public function showPaymentForm($orderId)
    {
        try {
            // Lấy thông tin đơn hàng với các relationship
            $order = Order::with(['items', 'user', 'table'])->findOrFail($orderId);
            
            return view('payment.form', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Error loading order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }
    }

    /**
     * Xử lý lựa chọn phương thức thanh toán
     */
    public function processPayment(Request $request, $orderId)
    {
        try {
            // Lấy thông tin đơn hàng thực tế
            $order = Order::with(['user'])->findOrFail($orderId);
            
            $paymentMethod = $request->input('payment_method');
            
            switch ($paymentMethod) {
                case 'momo':
                    return $this->processMoMoPayment($order);
                case 'cash':
                    return $this->processCashPayment($order);
                case 'bank':
                    return $this->processBankTransfer($order);
                case 'credit_card':
                    return $this->processCreditCard($order);
                default:
                    return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ');
            }
            
        } catch (\Exception $e) {
            Log::error('Process payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi xử lý thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý thanh toán MoMo
     */
  
private function processMoMoPayment($order)
{
    try {
        // Ép amount thành int để tránh lỗi định dạng
        $amount = (int) $order->display_total;
        $requestId = (string) Str::uuid();
        $momoOrderId = 'ORDER_' . $order->id . '_' . time();
        
        // Lấy tên khách hàng từ user relationship
        $customerName = $order->user ? $order->user->name : 'Khách hàng';
        
        // URL encode orderInfo để tránh ký tự đặc biệt
        $orderInfo = urlencode("Payment for order " . $order->id);
        Log::info('=== INITIATING MOMO PAYMENT ===');
        Log::info('Order Details:', [
            'order_id' => $order->id,
            'customer' => $customerName,
            'amount' => $amount,
            'table_id' => $order->table_id,
            'momo_order_id' => $momoOrderId
        ]);
        // TẠO rawHash THEO CHUẨN MOMO (Thứ tự chính xác)
        $rawHash = "accessKey=" . $this->accessKey .
            "&amount=" . $amount .
            "&extraData=" .  // Chuỗi rỗng
            "&ipnUrl=" . $this->notifyUrl .
            "&orderId=" . $momoOrderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $this->partnerCode .
            "&redirectUrl=" . $this->returnUrl .
            "&requestId=" . $requestId .
            "&requestType=captureWallet";
        Log::info('Raw Hash for Signature:', ['rawHash' => $rawHash]);
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);
        Log::info('Generated Signature:', ['signature' => $signature]);
        $payload = [
            'partnerCode' => $this->partnerCode,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $momoOrderId,
            'orderInfo' => $orderInfo,  // Đã encode
            'redirectUrl' => $this->returnUrl,
            'ipnUrl' => $this->notifyUrl,
            'lang' => 'vi',
            'extraData' => '',
            'requestType' => 'captureWallet',
            'signature' => $signature
        ];
        Log::info('MoMo Request Payload:', $payload);
        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($this->endpoint, $payload);
        if (!$response->successful()) {
            $errorBody = $response->body();
            Log::error('MoMo API Error:', [
                'status' => $response->status(),
                'response' => $errorBody
            ]);
            throw new \Exception("MoMo API lỗi: " . $errorBody);
        }
        $result = $response->json();
        Log::info('MoMo API Response:', $result);
        if (isset($result['resultCode']) && $result['resultCode'] == 0) {
                     $order->update([
                'status' => 'pending',
                'momo_request_id' => $requestId
            ]); 
            Log::info('Redirecting to MoMo:', ['payUrl' => $result['payUrl']]);
            // return redirect()->away($result['payUrl']); chuyển hướng đến trang tạo mã QR của momo
            return redirect()->route('payment.momo.form', $order->id);

        } else {
            $errorMsg = $result['message'] ?? 'Unknown error';
            $errorCode = $result['resultCode'] ?? 'N/A';
            throw new \Exception("{$errorMsg} (Mã: {$errorCode})");
        }
    } catch (\Exception $e) {
        Log::error('MoMo Payment Exception: ' . $e->getMessage());
        return redirect()->route('payment.form', $order->id)
            ->with('error', 'Lỗi thanh toán MoMo: ' . $e->getMessage());
    }
}


    /**
     * Xử lý thanh toán tiền mặt
     */
    private function processCashPayment($order)
    {
        $order->update([
            'status' => 'completed' // Hoặc trạng thái phù hợp với hệ thống của bạn
        ]);
        
        return redirect()->route('payment.form', $order->id)
            ->with('success', 'Đã xác nhận thanh toán tiền mặt. Vui lòng thanh toán khi nhận hàng.');
    }

    /**
     * Xử lý chuyển khoản ngân hàng
     */
    private function processBankTransfer($order)
    {
        $order->update([
            'status' => 'pending_bank_transfer'
        ]);
        
        return redirect()->route('payment.form', $order->id)
            ->with('info', 'Vui lòng chuyển khoản theo thông tin ngân hàng được cung cấp.');
    }

    /**
     * Xử lý thẻ tín dụng
     */
    private function processCreditCard($order)
    {
        return redirect()->route('payment.form', $order->id)
            ->with('info', 'Tính năng thẻ tín dụng đang được phát triển.');
    }

    /**
     * Callback từ MoMo
     */
    public function momoCallback(Request $request)
    {
        $params = $request->all();
        Log::info('=== MOMO CALLBACK RECEIVED ===', $params);

                try {
            // Kiểm tra signature callback
            $isValid = $this->verifyCallbackSignature($params);
            
            if (!$isValid) {
                Log::warning('Invalid callback signature');
                return view('payment.error', ['message' => 'Invalid signature']);
            }

            // Extract order ID
            $momoOrderId = $params['orderId'];
            $orderId = explode('_', $momoOrderId)[1];
            
            $order = Order::with(['user'])->findOrFail($orderId);
            $customerName = $order->user ? $order->user->name : 'Khách hàng';

            if ($params['resultCode'] == 0) {
                // Payment success
                $order->update([
                    'status' => 'completed',
                    'transaction_id' => $params['transId'],
                    'payment_method' => 'momo'
                ]);

                return view('payment.success', [
                    'orderId' => $orderId,
                    'transactionId' => $params['transId'],
                    'amount' => $params['amount'],
                    'customerName' => $customerName,
                    'message' => 'Thanh toán thành công'
                ]);
            } else {
                // Payment failed
                $order->update([
                    'status' => 'pending'
                ]);

                return view('payment.error', [
                    'message' => $params['message'] ?? 'Thanh toán thất bại',
                    'orderId' => $orderId,
                    'customerName' => $customerName
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Callback processing error: ' . $e->getMessage());
            return view('payment.error', ['message' => 'Lỗi xử lý: ' . $e->getMessage()]);
        }
    }
    /**
     * Verify callback signature - THEO CHUẨN MOMO
     */
  private function verifyCallbackSignature($params)
{
    if (!isset($params['signature'])) {
        return false;
    }
    $responseSignature = $params['signature'];
    
    // Thứ tự CHÍNH XÁC cho callback/IPN (bao gồm orderType và payType)
    $rawHash = "accessKey=" . $this->accessKey .
        "&amount=" . $params['amount'] .
        "&extraData=" . $params['extraData'] .
        "&message=" . $params['message'] .
        "&orderId=" . $params['orderId'] .
        "&orderInfo=" . $params['orderInfo'] .
        "&orderType=" . ($params['orderType'] ?? '') .  // Thêm nếu thiếu
        "&partnerCode=" . $params['partnerCode'] .
        "&payType=" . ($params['payType'] ?? '') .      // Thêm nếu thiếu
        "&requestId=" . $params['requestId'] .
        "&responseTime=" . $params['responseTime'] .
        "&resultCode=" . $params['resultCode'] .
        "&transId=" . $params['transId'];
    $calculatedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);
    return hash_equals($calculatedSignature, $responseSignature);
}
      /**
     * IPN Handler - THEO CHUẨN MOMO
     */
    public function momoIPN(Request $request)
    {
        $params = $request->all();
        Log::info('=== MOMO IPN RECEIVED ===', $params);

        try {
            // Verify IPN signature
            if (!$this->verifyCallbackSignature($params)) {
                Log::warning('Invalid IPN signature');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $momoOrderId = $params['orderId'];
            $orderId = explode('_', $momoOrderId)[1];
            
            $order = Order::findOrFail($orderId);

            if ($params['resultCode'] == 0) {
                $order->update([
                    'status' => 'served',
                    'transaction_id' => $params['transId'],
                    'payment_method' => 'momo'
                ]);
                Log::info("IPN: Order {$orderId} paid successfully");
            } else {
                $order->update([
                    'status' => 'payment_failed'
                ]);
                Log::warning("IPN: Order {$orderId} payment failed");
            }

            return response()->json(['status' => 'success'], 200);
            
        } catch (\Exception $e) {
            Log::error('IPN processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }
    public function testMoMoSignature($orderId)
    {
        try {
            $order = Order::with(['user'])->findOrFail($orderId);
            $amount = $order->display_total;
            $requestId = (string) Str::uuid();
            $momoOrderId = 'ORDER_' . $order->id . '_' . time();
            $orderInfo = "Payment for order " . $order->id;

            // Tạo rawHash CHUẨN MOMO
            $rawHash = "accessKey=" . $this->accessKey .
                "&amount=" . $amount .
                "&extraData=" .
                "&ipnUrl=" . $this->notifyUrl .
                "&orderId=" . $momoOrderId .
                "&orderInfo=" . $orderInfo .
                "&partnerCode=" . $this->partnerCode .
                "&redirectUrl=" . $this->returnUrl .
                "&requestId=" . $requestId .
                "&requestType=captureWallet";

            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

            return response()->json([
                'success' => true,
                'rawHash' => $rawHash,
                'signature' => $signature,
                'payload' => [
                    'partnerCode' => $this->partnerCode,
                    'requestId' => $requestId,
                    'amount' => $amount,
                    'orderId' => $momoOrderId,
                    'orderInfo' => $orderInfo,
                    'redirectUrl' => $this->returnUrl,
                    'ipnUrl' => $this->notifyUrl,
                    'lang' => 'vi',
                    'extraData' => '',
                    'requestType' => 'captureWallet',
                    'signature' => $signature
                ],
                'config' => [
                    'accessKey_length' => strlen($this->accessKey),
                    'secretKey_length' => strlen($this->secretKey),
                    'endpoint' => $this->endpoint
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus($orderId)
    {
        try {
            $order = Order::with(['user', 'table'])->findOrFail($orderId);
            $customerName = $order->user ? $order->user->name : 'Khách hàng';
            $tableName = $order->table ? $order->table->name : 'N/A';
            
            return response()->json([
                'order_id' => $orderId,
                'status' => $order->status,
                'payment_method' => $order->payment_method ?? 'N/A',
                'amount' => $order->display_total,
                'customer_name' => $customerName,
                'table_name' => $tableName,
                'transaction_id' => $order->transaction_id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Order not found',
                'order_id' => $orderId
            ], 404);
        }
    }

    /**
     * Verify MoMo signature
     */
    private function verifySignature($params)
    {
        if (!isset($params['signature'])) {
            return false;
        }

        $responseSignature = $params['signature'];
        
        $rawHash = "accessKey={$this->accessKey}" .
            "&amount={$params['amount']}" .
            "&extraData={$params['extraData']}" .
            "&message={$params['message']}" .
            "&orderId={$params['orderId']}" .
            "&orderInfo={$params['orderInfo']}" .
            "&orderType={$params['orderType']}" .
            "&partnerCode={$params['partnerCode']}" .
            "&payType={$params['payType']}" .
            "&requestId={$params['requestId']}" .
            "&responseTime={$params['responseTime']}" .
            "&resultCode={$params['resultCode']}" .
            "&transId={$params['transId']}";

        $calculatedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);

        return hash_equals($calculatedSignature, $responseSignature);
    }
    /**
 * Hiển thị form nhập thông tin thẻ MoMo (giả lập cho test)
 */
public function showMoMoForm($orderId)
{
    try {
        $order = Order::with(['items', 'user', 'table'])->findOrFail($orderId);
        return view('payment.momo_form', compact('order')); 
    } catch (\Exception $e) {
        Log::error('Error loading MoMo form: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
    }
}

/**
 * Giả lập thanh toán MoMo thành công (chỉ cho test)
 */
public function simulateMoMoPayment(Request $request, $orderId)
{
    try {
        $order = Order::findOrFail($orderId);
        
        // Validate thông tin thẻ (tùy chọn, để giả lập)
        $request->validate([
            'card_number' => 'required|string',
            'card_holder' => 'required|string',
            'expiry' => 'required|string',
            'cvv' => 'required|string',
        ]);
        
        // Giả lập: Nếu thông tin khớp với test data, thành công
        $testCard = '9704000000000018';
        $testHolder = 'NGUYEN VAN A';
        if ($request->card_number === $testCard && $request->card_holder === $testHolder) {
            // Cập nhật order như thanh toán thành công
            $order->update([
                'status' => 'completed',
                'payment_method' => 'momo',
                'transaction_id' => 'SIMULATED_' . time(), // ID giả
            ]);
            
            Log::info("Simulated MoMo payment success for order {$orderId}");
            return redirect()->route('payment.success', $orderId)->with('success', 'Thanh toán MoMo thành công (giả lập)!');
        } else {
            return back()->with('error', 'Thông tin thẻ không hợp lệ. Sử dụng thông tin test từ docs MoMo.');
        }
        
    } catch (\Exception $e) {
        Log::error('Simulate MoMo payment error: ' . $e->getMessage());
        return back()->with('error', 'Lỗi giả lập thanh toán: ' . $e->getMessage());
    }
}
public function showSuccess($orderId)
{
    $order = Order::findOrFail($orderId);
    return view('payment.success', compact('order'));
}

}