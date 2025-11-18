<h1>Sửa đơn hàng #{{ $order->id }}</h1>
<form method="POST" action="{{ route('orders.update',$order->id) }}">
    @csrf @method('PUT')
    <label>Bàn:</label>
    <select name="table_id">
        <option value="">Không chọn</option>
        @foreach($tables as $table)
            <option value="{{ $table->id }}" @if($order->table_id==$table->id) selected @endif>{{ $table->table_number }}</option>
        @endforeach
    </select>

    <label>Trạng thái:</label>
    <select name="status">
        @foreach(['pending','confirmed','served','completed','cancelled'] as $status)
            <option value="{{ $status }}" @if($order->status==$status) selected @endif>{{ ucfirst($status) }}</option>
        @endforeach
    </select>

    <button type="submit">Cập nhật</button>
</form>
<a href="{{ route('orders.index') }}">Quay lại</a>
<a href="{{ route('orders.show',$order->id) }}">Xem chi tiết</a>
<form action="{{ route('orders.destroy',$order->id) }}" method="POST" style="display:inline">
    @csrf @method('DELETE')
    <button onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa đơn hàng</button>