@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h2>Thêm bàn mới</h2>

        <form action="{{ route('tables.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Mã bàn</label>
                <input type="text" name="table_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Sức chứa</label>
                <input type="number" name="capacity" class="form-control" required>
            </div>

            <button class="btn btn-primary">Lưu</button>
        </form>

    </div>
@endsection
