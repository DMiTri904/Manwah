@extends('layouts.app') <!-- Thay bằng layout của bạn -->

@section('content')
<div class="container">
    <h2>Thanh Toán MoMo - Nhập Thông Tin Thẻ (Test)</h2>
    <p>Đây là form giả lập cho môi trường test. Sử dụng thông tin thẻ test từ docs MoMo để xác nhận.</p>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <form action="{{ route('payment.momo.simulate', $order->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Số Thẻ:</label>
            <input type="text" name="card_number" class="form-control" value="9704000000000018" required>
        </div>
        <div class="form-group">
            <label>Tên Chủ Thẻ:</label>
            <input type="text" name="card_holder" class="form-control" value="NGUYEN VAN A" required>
        </div>
        <div class="form-group">
            <label>Ngày Hết Hạn (MM/YY):</label>
            <input type="text" name="expiry" class="form-control" value="03/07" required>
        </div>
        <div class="form-group">
            <label>CVV:</label>
            <input type="text" name="cvv" class="form-control" value="123" required>
        </div>
        <button type="submit" class="btn btn-primary">Xác Nhận Thanh Toán</button>
    </form>
    
    <p><strong>Lưu ý:</strong> Trong production, bạn sẽ redirect đến trang MoMo thật. Đây chỉ là giả lập để test.</p>
</div>
@endsection