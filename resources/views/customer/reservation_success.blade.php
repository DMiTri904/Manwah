@extends('layouts.app')

@section('content')

<style>
    /* --- CSS Style cho trang Success (Ticket Style) --- */
    
    .success-container {
        padding: 50px 15px;
    }

    .ticket-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        background: white;
        position: relative;
    }

    /* Phần đầu: Màu nền đỏ cam */
    .ticket-header {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        padding: 40px 20px 30px;
        text-align: center;
        color: white;
        position: relative;
    }

    /* Vòng tròn icon dấu tích */
    .icon-circle {
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .success-icon {
        color: #27ae60;
        font-size: 40px;
    }

    .main-title {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    /* Phần thân: Chi tiết vé */
    .ticket-body {
        padding: 30px;
    }

    /* Đường kẻ đứt nét giống hóa đơn */
    .dashed-line {
        border-top: 2px dashed #e0e0e0;
        margin: 20px 0;
        position: relative;
    }

    /* Tạo 2 hình bán nguyệt lõm vào ở đường kẻ (hiệu ứng vé) */
    .dashed-line::before, .dashed-line::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: #f8f9fa; /* Trùng màu nền trang web */
        border-radius: 50%;
        top: -11px;
    }
    .dashed-line::before { left: -40px; }
    .dashed-line::after { right: -40px; }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .detail-label {
        color: #7f8c8d;
        font-weight: 500;
    }

    .detail-value {
        color: #2c3e50;
        font-weight: 700;
        text-align: right;
    }

    .time-highlight {
        color: #c0392b; /* Màu đỏ thương hiệu */
        font-size: 1.1rem;
    }

    /* Nút bấm */
    .btn-home {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        color: white;
        border-radius: 30px;
        padding: 12px 35px;
        font-weight: 600;
        border: none;
        box-shadow: 0 5px 15px rgba(192, 57, 43, 0.3);
        transition: all 0.3s;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    .btn-home:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(192, 57, 43, 0.4);
        color: white;
    }

    /* Animation đơn giản */
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="container success-container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="ticket-card">
                <div class="ticket-header">
                    <div class="icon-circle">
                        <i class="fas fa-check success-icon"></i>
                    </div>
                    <h2 class="main-title">Đặt Bàn Thành Công!</h2>
                    <p class="mb-0 opacity-75">Cảm ơn quý khách đã lựa chọn chúng tôi</p>
                </div>

                <div class="ticket-body">
                    
                    <div class="text-center mb-4">
                        <p class="text-muted">Mã đặt bàn của bạn là:</p>
                        <h3 class="text-dark fw-bold" style="letter-spacing: 2px;">#{{ $reservation->id }}</h3>
                    </div>

                    <div class="dashed-line"></div>

                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-label"><i class="far fa-clock me-2"></i>Thời gian:</span>
                            <span class="detail-value time-highlight">
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i - d/m/Y') }}
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-user-friends me-2"></i>Số lượng:</span>
                            <span class="detail-value">{{ $reservation->num_guests }} Khách</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-info-circle me-2"></i>Trạng thái:</span>
                            <span class="badge bg-warning text-dark">Đang chờ xác nhận</span>
                        </div>
                    </div>

                    <div class="alert alert-light border mt-4 text-center small text-muted">
                        <i class="fas fa-envelope-open-text me-1"></i> 
                        Chúng tôi sẽ gửi email xác nhận trong vòng 15 phút.
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('landing') }}" class="btn btn-home">
                            <i class="fas fa-home me-2"></i> Quay Về Trang Chủ
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection