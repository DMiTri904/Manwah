@extends('layouts.app')

@section('title', 'Thực đơn')

@section('content')
    @php
        $tableId = $tableId ?? session('table_id');
    @endphp

    <div class="container my-4">
        <h1 class="mb-4 text-center">Thực đơn - Bàn {{ $tableId ?? 'chưa chọn' }}</h1>

        <!-- Thông báo session -->
        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <!-- Form tổng gửi nhiều món -->
        <form action="{{ route('table.cart.add', ['table' => $tableId]) }}" method="POST">
            @csrf

            <!-- Bộ lọc danh mục -->
            <div class="mb-4 text-center">
                <a href="{{ route('table.menu', ['table' => $tableId]) }}"
                    class="btn btn-outline-danger mx-1 mb-1 {{ request('category_id') ? '' : 'active' }}">
                    Tất cả
                </a>

                @foreach($categories as $category)
                    <a href="{{ route('table.menu', ['table' => $tableId, 'category_id' => $category->id]) }}"
                        class="btn btn-outline-danger mx-1 mb-1 {{ request('category_id') == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100 rounded-3">

                            @if($product->image)
                                <img src="{{ asset('storage/ProductsImage/' . $product->image) }}" class="card-img-top"
                                    alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-utensils fa-2x text-secondary"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-danger fw-bold">{{ number_format($product->price) }} VND</p>
                                </div>

                                <!-- Input ẩn product_id -->
                                <input type="hidden" name="product_id[]" value="{{ $product->id }}">

                                <!-- Quantity -->
                                <div class="quantity-control d-flex align-items-center justify-content-center mb-2">
                                    <button type="button"
                                        class="btn btn-outline-secondary btn-sm decrement rounded-circle">-</button>
                                    <input type="text" name="quantity[]" value="0" readonly
                                        class="form-control quantity-input mx-2 text-center"
                                        style="width: 50px; font-weight: bold;">
                                    <button type="button"
                                        class="btn btn-outline-secondary btn-sm increment rounded-circle">+</button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Nút thêm nhiều món -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill">
                    Thêm vào giỏ hàng
                </button>
            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.quantity-control').forEach(function (control) {
                const incrementBtn = control.querySelector('.increment');
                const decrementBtn = control.querySelector('.decrement');
                const quantityInput = control.querySelector('.quantity-input');

                incrementBtn.addEventListener('click', function () {
                    let qty = parseInt(quantityInput.value);
                    quantityInput.value = qty + 1;
                });

                decrementBtn.addEventListener('click', function () {
                    let qty = parseInt(quantityInput.value);
                    if (qty > 0) quantityInput.value = qty - 1;
                });
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transition: 0.2s;
        }
    </style>
@endsection