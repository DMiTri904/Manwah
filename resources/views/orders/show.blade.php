@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container my-4">
    {{-- Header --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">
                        <i class="fas fa-receipt text-danger me-2"></i>
                        Đơn hàng #{{ $order->id }}
                    </h3>
                    <div class="d-flex gap-3 flex-wrap">
                        <span class="badge bg-primary">
                            <i class="fas fa-chair me-1"></i>
                            Bàn {{ $order->table->table_number }}
                        </span>

                        @if($order->status == 'confirmed')
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Đang chuẩn bị
                            </span>
                        @elseif($order->status == 'completed')
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Đã hoàn thành
                            </span>
                        @elseif($order->status == 'paid')
                            <span class="badge bg-info">
                                <i class="fas fa-money-bill me-1"></i>Đã thanh toán
                            </span>
                        @elseif($order->status == 'cancelled')
                            <span class="badge bg-danger">
                                <i class="fas fa-times me-1"></i>Đã hủy
                            </span>
                        @endif

                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <h4 class="text-danger mb-0">
                        {{ number_format($order->total_amount) }}đ
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách món theo Batch --}}
    @php
        $groupedItems = $order->items->groupBy('batch_number')->sortKeys();
    @endphp

    @foreach($groupedItems as $batchNumber => $items)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-utensils text-danger me-2"></i>
                        Đợt {{ $batchNumber }}
                        @if($batchNumber == 1)
                            <span class="badge bg-secondary ms-2">Gọi đầu tiên</span>
                        @endif
                    </h5>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $items->first()->batch_created_at->format('H:i') }}
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50%">Món ăn</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $batchTotal = 0; @endphp
                            @foreach($items as $item)
                                @php $batchTotal += $item->price * $item->quantity; @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/ProductsImage/' . $item->product->image) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="rounded me-2"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <strong>{{ $item->product->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($item->price) }}đ</td>
                                    <td class="text-end fw-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Tổng đợt {{ $batchNumber }}:</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($batchTotal) }}đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Tổng cộng --}}
    <div class="card shadow-sm border-danger">
        <div class="card-body bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-calculator me-2"></i>TỔNG CỘNG:</h4>
                <h3 class="mb-0 text-danger fw-bold">{{ number_format($order->total_amount) }}đ</h3>
            </div>
        </div>
    </div>

    {{-- Các nút action --}}
    <div class="mt-4">
        @if($order->status == 'confirmed')
            {{-- Đang chuẩn bị - Có thể gọi thêm hoặc đánh dấu hoàn thành --}}
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('table.menu', ['table' => $order->table_id]) }}"
                   class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>Gọi thêm món
                </a>

                @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                    <form action="{{ route('orders.complete', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg"
                                onclick="return confirm('Xác nhận các món đã phục vụ xong?')">
                            <i class="fas fa-check-circle me-2"></i>Đánh dấu hoàn thành
                        </button>
                    </form>
                @endif
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Lưu ý:</strong> Bạn có thể tiếp tục gọi thêm món. Các món mới sẽ được thêm vào đơn hàng này.
            </div>

        @elseif($order->status == 'completed')
            {{-- Đã hoàn thành - Chờ thanh toán --}}
            <a href="{{ route('payment.form', $order->id) }}"
               class="btn btn-success btn-lg w-100">
                <i class="fas fa-credit-card me-2"></i>Thanh toán
            </a>

            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Các món đã được phục vụ xong. Vui lòng thanh toán để hoàn tất.
            </div>

        @elseif($order->status == 'paid')
            {{-- Đã thanh toán --}}
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                Đơn hàng đã được thanh toán. Cảm ơn quý khách!
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-list me-2"></i>Xem danh sách đơn hàng
            </a>

        @elseif($order->status == 'cancelled')
            {{-- Đã hủy --}}
            <div class="alert alert-danger">
                <i class="fas fa-times-circle me-2"></i>
                Đơn hàng đã bị hủy.
            </div>
        @endif

        {{-- Nút quay lại --}}
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mt-2">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>
</div>
@endsection
