@extends('layouts.app')
@section('content')
<div class="container mt-4">

    <h2 class="mb-4">➕ Thêm sản phẩm mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Lỗi!</strong> Vui lòng kiểm tra lại dữ liệu.<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giá</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tồn kho</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected':'' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
