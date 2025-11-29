@extends('layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
    <div class="container my-4">
        <h3 class="text-center mb-4">
            <i class="fas fa-clipboard-check text-danger me-2"></i>
            Xác nhận đơn hàng - Bàn {{ $tableId }}
        </h3>

        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Lưu ý:</strong> Sau khi xác nhận, đơn hàng sẽ được gửi vào bếp và không thể tự ý hủy!
        </div>

        {{-- Danh sách món --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Danh sách món
                </h5>
            </div>
            <div class="card-body">
                @php $total = 0; @endphp
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Món ăn</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                            @php $total += $item->product->price * $item->quantity; @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-end">{{ number_format($item->product->price) }}đ</td>
                                <td class="text-end fw-bold">{{ number_format($item->product->price * $item->quantity) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                            <td class="text-end fw-bold text-danger fs-5">{{ number_format($total) }}đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Form xác nhận --}}
        <form action="{{ route('table.order.store', ['table' => $tableId]) }}" method="POST" id="confirmOrderForm">
            @csrf

            <div class="d-flex gap-2">
                <a href="{{ route('table.cart', ['table' => $tableId]) }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng
                </a>

                <button type="submit" class="btn btn-success btn-lg flex-grow-1">
                    <i class="fas fa-paper-plane me-2"></i>XÁC NHẬN GỬI VÀO BẾP
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('confirmOrderForm').addEventListener('submit', function () {
            // Xóa localStorage khi submit
            const tableId = '{{ $tableId }}';
            const storageKey = 'temp_qty_table_' + tableId;
            localStorage.removeItem(storageKey);
        });
    </script>
@endsection