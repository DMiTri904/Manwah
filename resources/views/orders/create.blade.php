@extends('layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
<div class="container">
    <h2>Xác nhận đơn hàng</h2>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin món ăn</h5>
                </div>
                <div class="card-body">
                    @foreach($cart->items as $item)
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
                            <small>Đơn giá: {{ number_format($item->product->price) }} đ</small>
                        </div>
                        <div class="col-4 text-end">
                            <strong>{{ number_format($item->product->price * $item->quantity) }} đ</strong>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <h5>Tổng tiền: <span class="text-danger">{{ number_format($totalAmount) }} đ</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Thông tin đặt bàn</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="table_id" class="form-label">Chọn bàn *</label>
                            <select class="form-select @error('table_id') is-invalid @enderror" 
                                    id="table_id" name="table_id" required>
                                <option value="">-- Chọn bàn --</option>
                                @foreach($availableTables as $table)
                                <option value="{{ $table->id }}" 
                                    {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                    Bàn {{ $table->table_number }} ({{ $table->capacity }} người)
                                </option>
                                @endforeach
                            </select>
                            @error('table_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Vui lòng chọn bàn phù hợp với số lượng người trong nhóm của bạn.
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-check"></i> Xác nhận đặt món
                        </button>
                        
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection