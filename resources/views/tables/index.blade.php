@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h2 class="mb-4">Danh sách bàn</h2>

        <a href="{{ route('tables.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Thêm bàn
        </a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Số bàn</th>
                    <th>Sức chứa</th>
                    <th>Trạng thái</th>
                    <th>Nhân viên phụ trách</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $t)
                    <tr class="text-center">
                        <td>{{ $t->id }}</td>
                        <td>{{ $t->table_number }}</td>
                        <td>{{ $t->capacity }}</td>
                        <td>
                            @if($t->status === 'empty')
                                <span class="badge bg-success">Trống</span>
                            @elseif($t->status === 'occupied')
                                <span class="badge bg-danger">Có khách</span>
                            @else
                                <span class="badge bg-secondary">{{ $t->status }}</span>
                            @endif
                        </td>
                        <td>{{ $t->employee ? $t->employee->full_name : 'Chưa có' }}</td>
                        <td>
                            <a href="{{ route('tables.edit', $t->id) }}" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('tables.destroy', $t->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Xóa bàn này?')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection