@extends('layouts.app')

@section('title', 'Hệ Thống Thanh Toán - Manwah Restaurant')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-manwah text-white">
                <h4 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Hệ Thống Thanh Toán
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Thống kê nhanh -->
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x text-primary mb-3"></i>
                <h3>{{ \App\Models\Order::count() }}</h3>
                <p class="text-muted mb-0">Tổng đơn hàng</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                <h3>{{ \App\Models\Payment::where('status', 'paid')->count() }}</h3>
                <p class="text-muted mb-0">Đã thanh toán</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                <h3>{{ \App\Models\Payment::where('status', 'pending')->count() }}</h3>
                <p class="text-muted mb-0">Chờ thanh toán</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-info mb-3"></i>
                <h3>{{ \App\Models\User::count() }}</h3>
                <p class="text-muted mb-0">Khách hàng</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Đơn hàng chờ thanh toán -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-manwah text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Đơn Hàng Chờ Thanh Toán
                </h5>
            </div>
            <div class="card-body">
                @php
                    $pendingOrders = \App\Models\Order::with(['user', 'payments'])
                        ->whereIn('status', ['pending', 'confirmed', 'served'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @forelse($pendingOrders as $order)
                <div class="border rounded p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">Đơn hàng #{{ $order->id }}</h6>
                            <p class="text-muted mb-1 small">
                                <i class="fas fa-user me-1"></i>{{ $order->user->full_name }}
                                @if($order->table_id)
                                | <i class="fas fa-table me-1"></i>Bàn {{ $order->table_id }}
                                @endif
                            </p>
                            <span class="badge bg-{{ $order->status == 'confirmed' ? 'success' : ($order->status == 'served' ? 'primary' : 'warning') }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="col-md-4 text-end">
                            <strong class="text-success">{{ number_format($order->total_amount) }} ₫</strong>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('payment.form', $order->id) }}" 
                               class="btn btn-sm btn-manwah">
                                <i class="fas fa-credit-card me-1"></i>TT
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Không có đơn hàng chờ thanh toán</p>
                </div>
                @endforelse

                @if($pendingOrders->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('payment.pending-orders') }}" class="btn btn-outline-manwah">
                        Xem tất cả đơn hàng
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <!-- Test Payment -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h6 class="mb-0">
                    <i class="fas fa-flask me-2"></i>Test Thanh Toán
                </h6>
            </div>
            <div class="card-body">
                @php
                    $testOrders = [
                        ['id' => 'TEST_' . time(), 'amount' => 150000, 'name' => 'Lẩu Thái 1 người'],
                        ['id' => 'TEST_' . (time() + 1), 'amount' => 350000, 'name' => 'Combo 2 người'],
                        ['id' => 'TEST_' . (time() + 2), 'amount' => 600000, 'name' => 'Combo 4 người']
                    ];
                @endphp
                
                @foreach($testOrders as $testOrder)
                <div class="border rounded p-3 mb-2 payment-method-card"
                     onclick="selectTestOrder('{{ $testOrder['id'] }}', {{ $testOrder['amount'] }}, '{{ $testOrder['name'] }}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $testOrder['name'] }}</h6>
                            <p class="text-success mb-0">{{ number_format($testOrder['amount']) }} ₫</p>
                        </div>
                        <i class="fas fa-arrow-right text-manwah"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card">
            <div class="card-header bg-manwah text-white">
                <h6 class="mb-0">
                    <i class="fas fa-link me-2"></i>Truy cập nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('payment.history') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Lịch sử thanh toán
                    </a>
                    <a href="{{ route('payment.pending-orders') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-list me-2"></i>Danh sách đơn hàng
                    </a>
                    <a href="{{ route('cart.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Selection Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-manwah text-white">
                <h5 class="modal-title">
                    <i class="fas fa-utensils me-2"></i>Xác nhận đơn hàng test
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="orderDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-manwah" onclick="proceedToPayment()">
                    <i class="fas fa-credit-card me-2"></i>Thanh toán ngay
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedOrder = null;

function selectTestOrder(orderId, amount, name) {
    selectedOrder = { orderId, amount, name };
    document.getElementById('orderDetails').innerHTML = `
        <div class="alert alert-info">
            <h6>Thông tin đơn hàng test</h6>
            <p class="mb-1"><strong>Đơn hàng:</strong> ${name}</p>
            <p class="mb-1"><strong>Mã đơn hàng:</strong> <code>${orderId}</code></p>
            <p class="mb-0"><strong>Tổng tiền:</strong> <span class="text-success">${amount.toLocaleString()} ₫</span></p>
        </div>
        <p class="text-muted small">Đây là đơn hàng test để kiểm tra hệ thống thanh toán</p>
    `;
    var modal = new bootstrap.Modal(document.getElementById('orderModal'));
    modal.show();
}

function proceedToPayment() {
    if (selectedOrder) {
        const url = `/payment/${selectedOrder.orderId}/form?test=true&amount=${selectedOrder.amount}&name=${encodeURIComponent(selectedOrder.name)}`;
        window.location.href = url;
    }
}
</script>
@endpush

@push('styles')
<style>
.btn-manwah {
    background-color: #d32f2f;
    border-color: #d32f2f;
    color: white;
}
.btn-manwah:hover {
    background-color: #b71c1c;
    border-color: #b71c1c;
    color: white;
}
.btn-outline-manwah {
    color: #d32f2f;
    border-color: #d32f2f;
}
.btn-outline-manwah:hover {
    background-color: #d32f2f;
    border-color: #d32f2f;
    color: white;
}
</style>
@endpush