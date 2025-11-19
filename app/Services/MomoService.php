<?php
// app/Services/MomoService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $endpoint;
    protected $returnUrl;
    protected $notifyUrl;

    public function __construct()
    {
        $this->partnerCode = config('momo.partner_code');
        $this->accessKey = config('momo.access_key');
        $this->secretKey = config('momo.secret_key');
        $this->endpoint = config('momo.endpoint');
        $this->returnUrl = config('momo.return_url');
        $this->notifyUrl = config('momo.notify_url');
    }

    /**
     * Tạo yêu cầu thanh toán MoMo
     */
    public function createPayment($orderId, $amount, $orderInfo = 'Thanh toán đơn hàng')
    {
        try {
            $requestId = time() . '';
            $extraData = base64_encode(json_encode([
                'order_id' => $orderId,
                'merchant' => env('APP_NAME', 'Laravel App')
            ]));

            // Tạo raw hash để ký
            $rawHash = "accessKey=" . $this->accessKey .
                "&amount=" . $amount .
                "&extraData=" . $extraData .
                "&ipnUrl=" . $this->notifyUrl .
                "&orderId=" . $orderId .
                "&orderInfo=" . $orderInfo .
                "&partnerCode=" . $this->partnerCode .
                "&redirectUrl=" . $this->returnUrl .
                "&requestId=" . $requestId .
                "&requestType=captureWallet";

            // Tạo signature
            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

            $data = [
                'partnerCode' => $this->partnerCode,
                'partnerName' => "Test Merchant",
                'storeId' => $this->partnerCode,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $this->returnUrl,
                'ipnUrl' => $this->notifyUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => 'captureWallet',
                'signature' => $signature
            ];

            Log::info('MoMo Request Data:', $data);

            // Gọi API MoMo
            $response = Http::timeout(30)->post($this->endpoint, $data);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info('MoMo API Response:', $result);

                if ($result['resultCode'] == 0) {
                    return [
                        'success' => true,
                        'payUrl' => $result['payUrl'],
                        'requestId' => $requestId,
                        'message' => 'Thành công'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $this->getErrorMessage($result['resultCode']),
                        'error_code' => $result['resultCode']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không thể kết nối đến MoMo'
            ];

        } catch (\Exception $e) {
            Log::error('MoMo Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xác thực chữ ký từ MoMo
     */
    public function verifySignature($data)
    {
        try {
            $rawHash = "accessKey=" . $data['accessKey'] .
                "&amount=" . $data['amount'] .
                "&extraData=" . $data['extraData'] .
                "&message=" . $data['message'] .
                "&orderId=" . $data['orderId'] .
                "&orderInfo=" . $data['orderInfo'] .
                "&orderType=" . $data['orderType'] .
                "&partnerCode=" . $data['partnerCode'] .
                "&payType=" . $data['payType'] .
                "&requestId=" . $data['requestId'] .
                "&responseTime=" . $data['responseTime'] .
                "&resultCode=" . $data['resultCode'] .
                "&transId=" . $data['transId'];

            $signature = hash_hmac('sha256', $rawHash, $this->secretKey);
            
            return $signature === $data['signature'];
        } catch (\Exception $e) {
            Log::error('Signature Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy thông báo lỗi
     */
    private function getErrorMessage($errorCode)
    {
        $errors = [
            '0' => 'Thành công',
            '9000' => 'Giao dịch được xác nhận thành công',
            '8000' => 'Giao dịch đang được xử lý',
            '7000' => 'Giao dịch bị nghi ngờ gian lận',
            '1000' => 'Giao dịch đã được khởi tạo',
            '11' => 'Giao dịch đang được xử lý',
            '12' => 'Giao dịch bị từ chối',
            '13' => 'Giao dịch bị hủy',
            '20' => 'Giao dịch thất bại',
            '40' => 'Giao dịch đã bị hoàn trả',
            '41' => 'Tài khoản không đủ số dư',
            '42' => 'Số tiền thanh toán vượt quá hạn mức',
            '43' => 'Số tiền thanh toán nhỏ hơn mức tối thiểu',
            '45' => 'Số tiền thanh toán không hợp lệ',
            '51' => 'Tài khoản không tồn tại',
            '65' => 'Tài khoản đã bị khóa',
            '75' => 'Mã thanh toán không hợp lệ',
            '79' => 'Mật khẩu thanh toán không đúng',
            '99' => 'Lỗi không xác định',
            '1001' => 'Hệ thống đang bảo trì',
            '1003' => 'Đơn hàng đã được thanh toán',
            '1004' => 'Đơn hàng không tồn tại',
            '1005' => 'Đơn hàng đã bị hủy',
            '1006' => 'Số tiền không hợp lệ'
        ];

        return $errors[$errorCode] ?? "Lỗi không xác định (Mã: $errorCode)";
    }
}