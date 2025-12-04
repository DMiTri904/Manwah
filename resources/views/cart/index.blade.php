@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
    <div class="container my-4">
        <h2 class="mb-4 text-center">Giỏ hàng bàn {{ $tableId ?? 'chưa chọn' }}</h2>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        @if($items->count() == 0)
            <p class="text-center">Giỏ hàng đang trống.</p>
        @else
            <div class="row g-3">
                @php $total = 0; @endphp
                @foreach($items as $item)
                    @php $total += $item->product->price * $item->quantity; @endphp
                    <div class="col-md-6">
                        <div class="card shadow-sm rounded-3 h-100">
                            <div class="row g-0">
                                <div class="col-4">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/ProductsImage/' . $item->product->image) }}"
                                            class="img-fluid rounded-start" alt="{{ $item->product->name }}"
                                            style="height:100%; object-fit:cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:100%;">
                                            <i class="fas fa-utensils fa-2x text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-8">
                                    <div class="card-body d-flex flex-column justify-content-between h-100">
                                        <div>
                                            <h5 class="card-title">{{ $item->product->name }}</h5>
                                            <p class="card-text text-danger fw-bold mb-1">
                                                {{ number_format($item->product->price, 0, '.', '.') }}đ
                                            </p>
                                            <p class="card-text">Số lượng: {{ $item->quantity }}</p>
                                            <p class="card-text fw-bold">Tổng:
                                                {{ number_format($item->product->price * $item->quantity, 0, '.', '.') }}đ
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            {{-- Xóa món --}}
                                            <form
                                                action="{{ route('table.cart.remove', ['table' => $tableId, 'id' => $item->id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Xóa</button>
                                            </form>

                                            {{-- Cập nhật số lượng --}}
                                            <form
                                                action="{{ route('table.cart.update', ['table' => $tableId, 'id' => $item->id]) }}"
                                                method="POST" class="ms-2 d-flex align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                    class="form-control form-control-sm" style="width:60px;">
                                                <button type="submit" class="btn btn-primary btn-sm ms-2">Cập nhật</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tổng tiền và thanh toán --}}
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <h4>Tổng cộng: <span class="text-danger">{{ number_format($total, 0, '.', '.') }}đ</span></h4>
                <a href="{{ route('table.order.create', ['table' => $tableId]) }}"
                    class="btn btn-success btn-lg rounded-pill">Thanh toán</a>
            </div>

            {{-- Xóa toàn bộ giỏ hàng --}}
            <div class="mt-3 text-end">
                <form action="{{ route('table.cart.clear', ['table' => $tableId]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Xóa toàn bộ giỏ hàng</button>
                </form>
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection