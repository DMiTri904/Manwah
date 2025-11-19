{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Danh sách đơn hàng')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Danh sách đơn hàng</h6>
                        <div>
                            <a href="{{ route('menu') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-utensils me-1"></i>Quay lại menu
                            </a>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <a href="{{ route('Dashboard') }}" class="btn btn-info btn-sm ms-2">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($orders->count() > 0)
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã đơn</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Bàn</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tổng tiền</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Trạng thái</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thanh toán</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ngày tạo</th>
                                    <th class="text-secondary opacity-7">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">#{{ $order->id }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @if($order->table)
                                                Bàn {{ $order->table->name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($order->total) }}đ</p>
                                    </td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge badge-sm bg-gradient-warning">Chờ xử lý</span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge badge-sm bg-gradient-success">Hoàn thành</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge badge-sm bg-gradient-danger">Đã hủy</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_status == 'paid')
                                            <span class="badge badge-sm bg-gradient-success">
                                                <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                            </span>
                                        @elseif($order->payment_status == 'pending')
                                            <span class="badge badge-sm bg-gradient-warning">
                                                <i class="fas fa-clock me-1"></i>Chờ thanh toán
                                            </span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">
                                                {{ $order->payment_status ?? 'Chưa xác định' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @if($order->created_at instanceof \Carbon\Carbon)
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <!-- Nút Xem chi tiết -->
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info mb-1">
                                                <i class="fas fa-eye me-1"></i>Chi tiết
                                            </a>
                                            
                                            <!-- Nút Thanh toán - chỉ hiện khi chưa thanh toán -->
                                            @if(($order->payment_status == 'pending' || !$order->payment_status) && $order->status != 'cancelled')
                                            <a href="{{ route('payment.form', ['orderId' => $order->id]) }}" class="btn btn-success mb-1">
                                                <i class="fas fa-credit-card me-1"></i>Thanh toán
                                            </a>
                                            @endif

                                            <!-- Nút Hủy đơn - chỉ hiện khi đang chờ xử lý -->
                                            @if($order->status == 'pending')
                                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline w-100">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                                    <i class="fas fa-times me-1"></i>Hủy
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Phân trang -->
                    <div class="mt-4 px-4">
                        {{ $orders->links() }}
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có đơn hàng nào</h5>
                        <p class="text-muted">Hãy đặt món và thanh toán để xem đơn hàng tại đây</p>
                        <a href="{{ route('menu') }}" class="btn btn-primary">
                            <i class="fas fa-utensils me-1"></i>Đặt món ngay
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .badge-sm {
        font-size: 0.65rem;
        padding: 0.3em 0.6em;
    }
    .btn-group-vertical .btn {
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .table td {
        vertical-align: middle;
    }
    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffa726, #fb8c00);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #66bb6a, #43a047);
    }
    .bg-gradient-danger {
        background: linear-gradient(45deg, #ef5350, #e53935);
    }
    .bg-gradient-secondary {
        background: linear-gradient(45deg, #78909c, #546e7a);
    }
</style>
@endsection

@section('scripts')
<script>
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection