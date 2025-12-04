@extends('layouts.app')

@section('content')
<style>
    /* 1. Tổng thể Card */
    .reservation-card {
        border: none;
        border-radius: 20px;
        overflow: hidden; /* Để bo góc header không bị che */
        transition: transform 0.3s ease;
    }
    
    .reservation-card:hover {
        transform: translateY(-5px); /* Hiệu ứng nhấc nhẹ khi di chuột */
    }

    /* 2. Header (Tiêu đề) */
    .reservation-header {
        /* Gradient màu đỏ/cam tạo cảm giác ấm cúng cho nhà hàng */
        background: linear-gradient(135deg, #c0392b, #e74c3c); 
        color: white;
        padding: 25px;
        text-align: center;
    }
    
    .reservation-header h3 {
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 0;
        text-transform: uppercase;
        font-size: 1.5rem;
    }

    /* 3. Input Form */
    .custom-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 12px 15px;
        background-color: #f9f9f9;
        transition: all 0.3s;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: #e74c3c; /* Màu viền khi focus trùng màu chủ đạo */
        box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.25);
    }

    /* 4. Nút Gửi */
    .btn-booking {
        background: linear-gradient(135deg, #c0392b, #e74c3c);
        border: none;
        border-radius: 50px; /* Nút tròn */
        padding: 12px 30px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(192, 57, 43, 0.4);
        transition: all 0.3s ease;
    }

    .btn-booking:hover {
        background: linear-gradient(135deg, #a93226, #c0392b);
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(192, 57, 43, 0.6);
    }

    /* 5. Icon và Trang trí */
    .input-group-text {
        background-color: white;
        border: 1px solid #e0e0e0;
        border-right: none;
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8"> <div class="card shadow-lg reservation-card">
                
                <div class="card-header reservation-header">
                    <h3><i class="fas fa-utensils me-2"></i> Đặt Bàn Ngay</h3>
                    <p class="mb-0 small opacity-75">Thưởng thức hương vị tuyệt vời cùng chúng tôi</p>
                </div>

                <div class="card-body p-4 p-md-5"> @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reservations.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="customer_name" class="custom-label">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                    placeholder="Nhập tên của bạn"
                                    value="{{ Auth::check() ? Auth::user()->full_name : old('customer_name') }}" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="customer_phone" class="custom-label">Số Điện Thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                    placeholder="Ví dụ: 0912..."
                                    value="{{ Auth::check() ? Auth::user()->phone : old('customer_phone') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="num_guests" class="custom-label">Số Lượng Khách <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="num_guests" name="num_guests" 
                                    value="{{ old('num_guests', 2) }}" min="1" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="reservation_time" class="custom-label">Thời Gian <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="reservation_time" name="reservation_time" 
                                    value="{{ old('reservation_time') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="special_requests" class="custom-label">Yêu Cầu Đặc Biệt</label>
                            <textarea class="form-control" id="special_requests" name="special_requests" rows="3" 
                                placeholder="Ví dụ: Cần ghế trẻ em, dị ứng hải sản, trang trí sinh nhật..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-booking text-white">
                            <i class="fas fa-paper-plane me-2"></i> Xác Nhận Đặt Bàn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection