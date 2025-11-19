<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán đơn hàng #{{ $order->id }}</title>
    <style>
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .order-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .payment-methods { margin: 20px 0; }
        .payment-method { margin: 15px 0; padding: 15px; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; }
        .payment-method:hover { border-color: #007bff; }
        .payment-method.selected { border-color: #007bff; background: #f0f8ff; }
        .payment-method input { margin-right: 10px; }
        .btn-payment { background: #007bff; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        .btn-payment:hover { background: #0056b3; }
        .alert { padding: 12px; border-radius: 6px; margin: 10px 0; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .test-info { background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .order-items { margin: 15px 0; }
        .order-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💳 Thanh toán đơn hàng</h1>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="order-info">
            <h3>📦 Thông tin đơn hàng #{{ $order->id }}</h3>
            <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'N/A' }}</p>
            <p><strong>Bàn:</strong> {{ $order->table->name ?? 'N/A' }}</p>
            <p><strong>Tổng tiền:</strong> <span style="color: #e74c3c; font-size: 1.2em;">{{ number_format($order->display_total) }}đ</span></p>
            
            <div class="order-items">
                <h4>Chi tiết món ăn:</h4>
                @foreach($order->items as $item)
                    <div class="order-item">
                        <span>{{ $item->name }} x {{ $item->quantity }}</span>
                        <span>{{ number_format($item->price * $item->quantity) }}đ</span>
                    </div>
                @endforeach
            </div>
            
            <p><strong>Trạng thái:</strong> 
                <span style="color: 
                    @if($order->status == 'pending') #f39c12
                    @elseif($order->status == 'paid') #27ae60
                    @elseif($order->status == 'completed') #27ae60
                    @elseif($order->status == 'payment_failed') #e74c3c
                    @else #95a5a6 @endif">
                    {{ $order->status }}
                </span>
            </p>
        </div>

        @if(in_array($order->status, ['pending', 'pending_payment']))
        <div class="test-info">
            <h4>🧪 Thông tin test MoMo Sandbox:</h4>
            <p><strong>Số điện thoại test:</strong> 0334456789 hoặc 0987654321</p>
            <p><strong>Mật khẩu/OTP:</strong> 123456</p>
            <p><strong>Thẻ test:</strong> 9704198526191432198 (NGUYEN VAN A)</p>
        </div>

        <form action="{{ route('payment.process', $order->id) }}" method="POST" id="paymentForm">
            @csrf
            <h3>🔗 Chọn phương thức thanh toán</h3>
            
            <div class="payment-methods">
                <div class="payment-method" onclick="selectPayment('momo')">
                    <label>
                        <input type="radio" name="payment_method" value="momo" required>
                        <strong>📱 Ví MoMo</strong> - Thanh toán qua ứng dụng MoMo
                    </label>
                </div>

                <div class="payment-method" onclick="selectPayment('cash')">
                    <label>
                        <input type="radio" name="payment_method" value="cash">
                        <strong>💵 Tiền mặt</strong> - Thanh toán khi nhận hàng
                    </label>
                </div>

                <div class="payment-method" onclick="selectPayment('bank')">
                    <label>
                        <input type="radio" name="payment_method" value="bank">
                        <strong>🏦 Chuyển khoản ngân hàng</strong>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-payment" id="submitBtn">
                🚀 Xác nhận thanh toán
            </button>
        </form>
        @else
            <div class="alert alert-info">
                <h4>Đơn hàng đã được xử lý</h4>
                <p>Trạng thái: <strong>{{ $order->status }}</strong></p>
                @if($order->transaction_id)
                    <p>Mã giao dịch: <strong>{{ $order->transaction_id }}</strong></p>
                @endif
                @if($order->payment_method)
                    <p>Phương thức: <strong>{{ $order->payment_method }}</strong></p>
                @endif
                <a href="{{route('Dashboard')}}" class="btn-payment">← Quay về trang chủ</a>
            </div>
        @endif
    </div>

    <script>
        function selectPayment(method) {
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            document.querySelector(`input[value="${method}"]`).checked = true;
            
            // Update button text
            const btn = document.getElementById('submitBtn');
            if (method === 'momo') {
                btn.innerHTML = '🚀 Chuyển đến MoMo để thanh toán';
            } else {
                btn.innerHTML = '🚀 Xác nhận thanh toán';
            }
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!selectedMethod) {
                e.preventDefault();
                alert('Vui lòng chọn phương thức thanh toán');
                return;
            }
            
            if (selectedMethod.value === 'momo') {
                document.getElementById('submitBtn').innerHTML = '⏳ Đang chuyển hướng...';
                document.getElementById('submitBtn').disabled = true;
            }
        });
    </script>
</body>
</html>