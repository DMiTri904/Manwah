@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h2>Sửa bàn</h2>

        <form action="{{ route('tables.update', $table->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Số bàn</label>
                <input type="text" name="table_number" value="{{ $table->table_number }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Sức chứa</label>
                <input type="number" name="capacity" value="{{ $table->capacity }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Trống</option>
                    <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Đang dùng</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Nhân viên phụ trách</label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- Chọn nhân viên --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $table->employee_id == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-success">Cập nhật</button>
        </form>

    </div>
@endsection