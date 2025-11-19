@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Chi tiết đơn hàng #{{ $order->id }}</h6>
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Bàn:</strong> 
                                @if($order->table)
                                    Bàn {{ $order->table->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                            <p><strong>Trạng thái:</strong> 
                                @if($order->status == 'pending')
                                    <span class="badge bg-gradient-warning">Chờ xử lý</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-gradient-success">Hoàn thành</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-gradient-danger">Đã hủy</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                           <td>
    <p class="text-xs font-weight-bold mb-0">
        {{ number_format($order->total_amount) }}đ
    </p>
</td>

                            <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <h6 class="mb-3">Chi tiết món ăn:</h6>
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tên món</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Đơn giá</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Số lượng</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    @if($item->product)
                                                        {{ $item->product->name }}
                                                    @else
                                                        <span class="text-muted">Món đã bị xóa</span>
                                                    @endif
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($item->price) }}đ</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $item->quantity }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($item->price * $item->quantity) }}đ</p>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-success">
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td>
                                        <strong class="text-success fs-6">
                                            {{ number_format($order->display_total) }}đ
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Nút hủy đơn hàng -->
                    @if($order->status == 'pending')
                    <div class="mt-4 text-end">
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                <i class="fas fa-times me-1"></i>Hủy đơn hàng
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection