@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
    <div class="container my-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold">🛒 Giỏ hàng bàn {{ $tableId ?? 'chưa chọn' }}</h2>
            <p class="text-muted">Vui lòng kiểm tra kỹ món ăn trước khi gửi vào bếp</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger text-center alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($items->count() == 0)
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Giỏ hàng đang trống</h4>
                <p class="text-muted mb-4">Hãy chọn món từ thực đơn để bắt đầu!</p>
                <a href="{{ route('table.menu', ['table' => $tableId]) }}" class="btn btn-primary btn-lg rounded-pill">
                    <i class="fas fa-utensils me-2"></i>Xem thực đơn
                </a>
            </div>
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
                                            <h5 class="card-title fw-bold">{{ $item->product->name }}</h5>
                                            <p class="card-text text-danger fw-bold mb-1">
                                                {{ number_format($item->product->price) }}đ
                                            </p>
                                            <p class="card-text mb-1">
                                                <small class="text-muted">Số lượng:</small>
                                                <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                            </p>
                                            <p class="card-text fw-bold text-primary">
                                                Tổng: {{ number_format($item->product->price * $item->quantity) }}đ
                                            </p>
                                        </div>
                                        <div class="d-flex gap-2 mt-2">
                                            {{-- Form cập nhật số lượng --}}
                                            <form
                                                action="{{ route('table.cart.update', ['table' => $tableId, 'id' => $item->id]) }}"
                                                method="POST" class="d-flex align-items-center flex-grow-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99"
                                                    class="form-control form-control-sm me-2" style="width:70px;">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-sync-alt me-1"></i>Cập nhật
                                                </button>
                                            </form>

                                            {{-- Nút xóa --}}
                                            <form
                                                action="{{ route('table.cart.remove', ['table' => $tableId, 'id' => $item->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Xác nhận xóa món {{ $item->product->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tổng tiền và các nút action --}}
            <div class="card mt-4 shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Tổng cộng:
                        </h4>
                        <h3 class="mb-0 fw-bold">{{ number_format($total) }}đ</h3>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('table.order.create', ['table' => $tableId]) }}" class="btn btn-light btn-lg fw-bold"
                            <i class="fas fa-paper-plane me-2"></i>GỬI VÀO BẾP
                        </a>

                        <a href="{{ route('table.menu', ['table' => $tableId]) }}" class="btn btn-outline-light">
                            <i class="fas fa-plus-circle me-2"></i>Tiếp tục chọn món
                        </a>
                    </div>
                </div>
            </div>

            {{-- Nút xóa toàn bộ (ẩn ở cuối) --}}
            <div class="text-center mt-3">
                <button type="button" class="btn btn-link text-danger text-decoration-none" data-bs-toggle="collapse"
                    data-bs-target="#clearCartCollapse">
                    <small><i class="fas fa-exclamation-triangle me-1"></i>Xóa toàn bộ giỏ hàng</small>
                </button>

                <div class="collapse" id="clearCartCollapse">
                    <div class="card card-body bg-light mt-2">
                        <p class="mb-2"><strong>⚠️ Cảnh báo:</strong> Bạn chắc chắn muốn xóa tất cả món trong giỏ?</p>
                        <form action="{{ route('table.cart.clear', ['table' => $tableId]) }}" method="POST"
                            onsubmit="return confirm('XÁC NHẬN XÓA TOÀN BỘ GIỎ HÀNG?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Xác nhận xóa toàn bộ
                            </button>
                        </form>
                    </div>
                </div>
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

        .btn-outline-danger:hover i {
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Gradient button hover effect */
        .btn-light:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
    </style>
@endsection