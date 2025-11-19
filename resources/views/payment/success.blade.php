@extends('layouts.app') <!-- Thay bằng layout của bạn nếu khác -->

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg" style="max-width: 500px; width: 100%; border: none; border-radius: 15px;">
        <!-- Header với logo MoMo giả lập -->
        <div class="card-header text-center bg-primary text-white" style="background-color: #00AEEF !important; border-radius: 15px 15px 0 0;">
            <img src="https://developers.momo.vn/v2/img/logo-momo.png" alt="MoMo Logo" style="height: 40px; margin-bottom: 10px;"> <!-- Logo MoMo từ URL công khai -->
            <h4 class="mb-0">Thanh Toán Thành Công</h4>
        </div>
        
        <!-- Body với thông tin giao dịch -->
        <div class="card-body text-center p-4">
            <!-- Icon thành công -->
            <div class="mb-3">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #28a745;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M8 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            
            <!-- Thông tin đơn hàng -->
            <p class="text-muted mb-3">Cảm ơn bạn đã thanh toán qua MoMo!</p>
            <div class="row text-start">
                <div class="col-6"><strong>Mã Đơn Hàng:</strong></div>
                <div class="col-6">{{ $order->id }}</div>
                
                <div class="col-6"><strong>Số Tiền:</strong></div>
                <div class="col-6">{{ number_format($order->display_total, 0, ',', '.') }} VND</div>
                
                <div class="col-6"><strong>Phương Thức:</strong></div>
                <div class="col-6">MoMo</div>
                
                <div class="col-6"><strong>Trạng Thái:</strong></div>
                <div class="col-6"><span class="badge bg-success">Thành Công</span></div>
                
                <div class="col-6"><strong>Mã Giao Dịch:</strong></div>
                <div class="col-6">{{ $order->transaction_id ?? 'N/A' }}</div>
                
                <div class="col-6"><strong>Thời Gian:</strong></div>
                <div class="col-6">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
            </div>
            
            <!-- Thông báo thành công -->
            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
        </div>
        
        <!-- Footer với nút hành động -->
        <div class="card-footer text-center bg-light" style="border-radius: 0 0 15px 15px;">
            <a href="{{ route('Dashboard') }}" class="btn btn-primary me-2" style="background-color: #00AEEF; border-color: #00AEEF;">Quay Lại Trang Chủ</a>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary">Xem Chi Tiết Đơn Hàng</a>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh để giống MoMo hơn -->
<style>
    .card {
        box-shadow: 0 4px 20px rgba(0, 174, 239, 0.2) !important;
    }
    .card-header {
        font-family: 'Arial', sans-serif;
    }
    .badge {
        font-size: 0.9em;
    }
    .btn-primary:hover {
        background-color: #008CBA !important;
        border-color: #008CBA !important;
    }
</style>
@endsection