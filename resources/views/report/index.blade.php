@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📊 Báo cáo chi tiết</h2>

    {{-- ------------------- Form lọc ------------------- --}}
    <form method="GET" action="{{ route('report.index') }}" class="row mb-4 g-2 align-items-end">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Ngày bắt đầu</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Ngày kết thúc</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-3">
            <label for="filter_type" class="form-label">Lọc theo</label>
            <select name="filter_type" id="filter_type" class="form-select">
                <option value="day" {{ request('filter_type')=='day'?'selected':'' }}>Ngày</option>
                <option value="month" {{ request('filter_type')=='month'?'selected':'' }}>Tháng</option>
                <option value="year" {{ request('filter_type')=='year'?'selected':'' }}>Năm</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    {{-- ------------------- Báo cáo tổng hợp ------------------- --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 text-center shadow-sm border-0">
                <h5>Tổng đơn hàng</h5>
                <h2>{{ $totalOrders }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center shadow-sm border-0">
                <h5>Tổng doanh thu</h5>
                <h2>{{ number_format($totalRevenue) }}₫</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center shadow-sm border-0">
                <h5>Doanh thu hôm nay</h5>
                <h2>{{ number_format($todayRevenue) }}₫</h2>
            </div>
        </div>
    </div>

    {{-- Thống kê trạng thái và thanh toán --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <h5>Thống kê theo trạng thái</h5>
            <ul class="list-group list-group-flush">
                @foreach($statusCounts as $status => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ ucfirst($status) }}
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-6">
            <h5>Thống kê theo phương thức thanh toán</h5>
            <ul class="list-group list-group-flush">
                @foreach($paymentCounts as $method => $count)
                    @if($method) {{-- loại bỏ key rỗng --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $method }}
                            <span class="badge bg-success rounded-pill">{{ $count }} đơn</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Bảng chi tiết đơn hàng --}}
    <h4 class="mt-4 mb-3">Danh sách đơn hàng chi tiết</h4>
    <div class="table-responsive" style="max-height: 450px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
        <table class="table table-bordered table-striped table-hover mb-0">
            <thead class="thead-light" style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">
                <tr class="text-center align-middle">
                    <th>ID</th>
                    <th>User</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Total Amount</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderDetails as $order)
                <tr class="text-center align-middle">
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user_id }}</td>
                    <td>{{ $order->table_id }}</td>
                    <td>
                        @if($order->status === 'completed')
                            <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                        @elseif($order->status === 'pending')
                            <span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span>
                        @elseif($order->status === 'cancelled')
                            <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $order->payment_method ?? 'Tiền mặt' }}</span>
                    </td>
                    <td>{{ number_format($order->total_amount) }}₫</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Báo cáo theo filter (nếu lọc) --}}
    @if(request('start_date') || request('end_date'))
        <div class="mt-5">
            <h3>📊 Báo cáo theo bộ lọc ({{ ucfirst($filterType) }})</h3>
            <p>Khoảng: {{ request('start_date') ?? '...' }} → {{ request('end_date') ?? '...' }}</p>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card p-3 text-center shadow-sm border-0">
                        <h5>Tổng đơn hàng</h5>
                        <h2>{{ $filteredTotalOrders }}</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 text-center shadow-sm border-0">
                        <h5>Tổng doanh thu</h5>
                        <h2>{{ number_format($filteredTotalRevenue) }}₫</h2>
                    </div>
                </div>
            </div>

            {{-- Thống kê trạng thái --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5>Trạng thái</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($filteredStatusCounts as $status => $count)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ ucfirst($status) }}
                                <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Phương thức thanh toán</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($filteredPaymentCounts as $method => $count)
                            @if($method)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $method }}
                                    <span class="badge bg-success rounded-pill">{{ $count }} đơn</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Bảng chi tiết đơn hàng theo filter --}}
            <h4 class="mt-4 mb-3">Danh sách đơn hàng theo bộ lọc</h4>
            <div class="table-responsive" style="max-height: 450px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="thead-light" style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">
                        <tr class="text-center align-middle">
                            <th>ID</th>
                            <th>User</th>
                            <th>Table</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total Amount</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredOrderDetails as $order)
                        <tr class="text-center align-middle">
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user_id }}</td>
                            <td>{{ $order->table_id }}</td>
                            <td>
                                @if($order->status === 'completed')
                                    <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $order->payment_method ?? 'Tiền mặt' }}</span>
                            </td>
                            <td>{{ number_format($order->total_amount) }}₫</td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
</style>
@endsection
