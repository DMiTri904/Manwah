@extends('layouts.app')
@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">📦 Danh sách sản phẩm</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            + Thêm sản phẩm mới
        </a>
    </div>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Bảng danh sách sản phẩm --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên SP</th>
                        <th>Mô tả</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Danh mục</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ Str::limit($product->description, 50) }}</td>
                            <td>{{ number_format($product->price) }} đ</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ $product->category->name ?? 'Không có' }}</td>
                            <td class="text-center">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                                    ✏️ Sửa
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Không có sản phẩm nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center mt-4">
    <a href="{{ route('Dashboard') }}" class="btn btn-outline-primary">
        ⬅ Quay lại Trang Chủ
    </a>
</div>

@endsection
