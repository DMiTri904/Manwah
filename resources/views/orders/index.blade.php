<h1>Danh sách đơn hàng</h1>
<a href="{{ route('orders.create') }}">Tạo đơn mới</a>
@if(session('success')) 
    <div alert alert-success>
        {{ session('success') }}
    </div> 
        @endif
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>#ID</th>
            <th>Bàn</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->table->table_number ?? 'Không có' }}</td>
            <td>{{ number_format($order->total_amount,0,'.','.') }} VND</td>
            <td>{{ ucfirst($order->status) }}</td>
            <td>{{ $order->order_date }}</td>
            <td>
                <a href="{{ route('orders.show',$order->id) }}">Xem</a> |
                <a href="{{ route('orders.edit',$order->id) }}">Sửa</a> |
                <form action="{{ route('orders.destroy',$order->id) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
