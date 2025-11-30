@extends('layouts.app')

@section('content')
<style>
    /* --- CSS Tùy chỉnh cho Lịch sử đặt bàn (Manwah Style) --- */
    
    /* 1. Card Container */
    .history-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* 2. Header Gradient */
    .history-header {
        background: linear-gradient(135deg, #c0392b, #e74c3c); /* Màu đỏ cam chủ đạo */
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-title {
        color: white;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-back {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 5px 15px;
        border-radius: 20px;
    }

    .btn-back:hover {
        background-color: white;
        color: #c0392b;
    }

    /* 3. Bảng (Table) */
    .custom-table {
        margin-bottom: 0;
        width: 100%;
    }

    .custom-table thead th {
        background-color: #f8f9fa;
        color: #7f8c8d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #eee;
        padding: 15px;
    }

    .custom-table tbody tr {
        transition: all 0.2s ease-in-out;
    }

    .custom-table tbody tr:hover {
        background-color: #fff5f5; /* Màu nền đỏ rất nhạt khi di chuột */
        transform: scale(1.005); /* Hiệu ứng nổi nhẹ */
    }

    .custom-table td {
        padding: 15px;
        vertical-align: middle;
        color: #2c3e50;
        border-bottom: 1px solid #f1f1f1;
    }

    /* 4. Các cột đặc biệt */
    .col-id {
        color: #c0392b;
        font-weight: 800;
    }

    .col-time {
        font-weight: 600;
    }

    /* 5. Trạng thái (Badge) */
    .status-badge {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-confirmed { background-color: #d4edda; color: #155724; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }

    /* 6. Trạng thái trống */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }
    
    .btn-start-now {
        background-color: #c0392b;
        color: white;
        border-radius: 30px;
        padding: 10px 25px;
        font-weight: 600;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(192, 57, 43, 0.3);
    }
    
    .btn-start-now:hover {
        background-color: #a93226;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card history-card">
                
                <div class="history-header">
                    <h1 class="history-title">
                        <i class="fas fa-history me-2"></i> Lịch Sử Đặt Bàn
                    </h1>
                    <a href="{{ route('landing') }}" class="btn-back">
                        <i class="fas fa-arrow-left me-1"></i> Trang Chủ
                    </a>
                </div>

                <div class="card-body p-0">
                    @if($reservations->isEmpty())
                        <div class="empty-state">
                            <div class="mb-4">
                                <i class="fas fa-calendar-times fa-4x text-muted opacity-50"></i>
                            </div>
                            <h5 class="mb-3">Bạn chưa có lịch sử đặt bàn nào</h5>
                            <p class="mb-4 text-muted">Hãy đặt bàn ngay để thưởng thức những món ngon tuyệt vời.</p>
                            <a href="{{ route('reservations.create') }}" class="btn btn-start-now">
                                Đặt bàn ngay
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Thời Gian</th>
                                        <th>Số Khách</th>
                                        <th>Trạng Thái</th>
                                        <th>Ngày Tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservations as $reservation)
                                        <tr>
                                            <td class="col-id">#{{ $reservation->id }}</td>
                                            <td class="col-time">
                                                <i class="far fa-clock text-danger me-1"></i>
                                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i - d/m/Y') }}
                                            </td>
                                            <td>
                                                <i class="fas fa-users text-secondary me-1"></i> 
                                                {{ $reservation->num_guests }} người
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($reservation->status) {
                                                        'pending' => 'status-pending',
                                                        'confirmed' => 'status-confirmed',
                                                        'cancelled' => 'status-cancelled',
                                                        default => 'bg-light text-dark'
                                                    };
                                                    
                                                    $statusText = match($reservation->status) {
                                                        'pending' => 'Chờ xác nhận',
                                                        'confirmed' => 'Đã xác nhận',
                                                        'cancelled' => 'Đã hủy',
                                                        default => ucfirst($reservation->status)
                                                    };
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="text-muted small">
                                                {{ $reservation->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection