@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container">
    <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin món ăn</h5>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                    <div class="row mb-3 border-bottom pb-3">
                        <div class="col-2">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="img-fluid" style="height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 60px; width: 60px;">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-6">
                            <h6>{{ $item->product->name }}</h6>
                            <small>Số lượng: {{ $item->quantity }}</small>
                            <br>
                            <small>Đơn giá: {{ number_format($item->price) }} đ</small>
                        </div>
                        <div class="col-4 text-end">
                            <strong>{{ number_format($item->price * $item->quantity) }} đ</strong>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <h5>Tổng tiền: <span class="text-danger">{{ number_format($order->total_amount) }} đ</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge bg-{{ $order->status_color }}">
                            {{ $order->status_text }}
                        </span>
                    </p>
                    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Bàn:</strong> 
                        @if($order->table)
                            Bàn {{ $order->table->table_number }} ({{ $order->table->capacity }} người)
                        @else
                            <em>Chưa chọn bàn</em>
                        @endif
                    </p>
                    
                    @if($order->status == 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" 
                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                            <i class="fas fa-times"></i> Hủy đơn hàng
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-list"></i> Danh sách đơn hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection