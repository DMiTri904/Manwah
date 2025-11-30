@extends('layouts.app')

@section('content')

{{-- 1. CSS Tùy chỉnh cho trang Admin (Manwah Style) --}}
<style>
    :root {
        --primary-color: #c0392b;
        --secondary-color: #e74c3c;
        --accent-color: #f39c12;
    }

    /* Gradient Header */
    .admin-header-title {
        color: var(--primary-color);
        border-bottom: 2px solid #eee;
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
    }
    
    .admin-header-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 60px;
        height: 2px;
        background-color: var(--primary-color);
    }

    /* Cards */
    .admin-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        background: white;
        overflow: hidden;
    }

    .card-header-custom {
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }

    /* Form Elements */
    .form-select-custom {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px;
        transition: all 0.3s;
    }
    
    .form-select-custom:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.2);
        outline: none;
    }

    /* Buttons */
    .btn-update {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        font-weight: 600;
        border-radius: 50px;
        padding: 12px 30px;
        box-shadow: 0 4px 15px rgba(192, 57, 43, 0.3);
        transition: all 0.3s;
    }
    
    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(192, 57, 43, 0.4);
    }

    .btn-back {
        color: #7f8c8d;
        transition: 0.3s;
        text-decoration: none;
    }
    .btn-back:hover {
        color: var(--primary-color);
        transform: translateX(-5px);
    }

    /* Info Row */
    .info-row {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #eee;
    }
    .info-row:last-child { border-bottom: none; }
    
    .info-icon {
        width: 35px;
        height: 35px;
        background-color: #fff5f5;
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
</style>


<div class="container mx-auto p-4 sm:p-6 lg:p-10">

    {{-- Nút quay lại --}}
    <div class="mb-6">
        <a href="{{ route('admin.reservations.index') }}" class="btn-back flex items-center font-medium">
            <i class="fas fa-arrow-left me-2"></i> Quay lại Danh sách
        </a>
    </div>

    <div class="flex justify-between items-start mb-8">
        <h1 class="text-3xl font-extrabold admin-header-title">
            Quản Lý Đặt Bàn <span class="text-gray-400">#{{ $reservation->id }}</span>
        </h1>

        {{-- Badge trạng thái --}}
        @php
            $colors = [
                'pending'=>'bg-yellow-100 text-yellow-800',
                'confirmed'=>'bg-green-100 text-green-800',
                'cancelled'=>'bg-red-100 text-red-800',
                'completed'=>'bg-blue-100 text-blue-800',
            ];
            $labels = [
                'pending'=>'Chờ duyệt',
                'confirmed'=>'Đã xác nhận',
                'cancelled'=>'Đã hủy',
                'completed'=>'Hoàn thành',
            ];
        @endphp

        <span class="px-4 py-2 rounded-full border shadow-sm {{ $colors[$reservation->status] }}">
            {{ $labels[$reservation->status] }}
        </span>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-600 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- THÔNG TIN ĐƠN --}}
        <div class="lg:col-span-2">
            <div class="admin-card p-6">

                {{-- Tên --}}
                <div class="info-row">
                    <div class="info-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase">Khách hàng</p>
                        <p class="text-lg font-bold">{{ $reservation->customer_name }}</p>
                    </div>
                </div>

                {{-- SDT --}}
                <div class="info-row">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase">Số điện thoại</p>
                        <p class="text-lg font-mono">{{ $reservation->customer_phone }}</p>
                    </div>
                </div>

                {{-- Số khách + thời gian --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="info-row">
                        <div class="info-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase">Số lượng</p>
                            <p class="text-lg font-bold">{{ $reservation->num_guests }} khách</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon"><i class="far fa-clock"></i></div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase">Thời gian</p>
                            <p class="text-lg font-bold text-red-600">
                                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i - d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Gán bàn --}}
                @if($reservation->table_id)
                <div class="mt-6 p-4 bg-red-50 rounded-xl border">
                    <p class="text-xs text-red-400 uppercase">Bàn đã gán</p>
                    <p class="text-xl font-bold text-red-700">
                        Bàn {{ $reservation->restaurantTable->table_number }}
                    </p>
                </div>
                @endif

                {{-- yêu cầu --}}
                @if($reservation->special_requests)
                    <div class="mt-6">
                        <p class="text-gray-500 text-xs uppercase">Yêu cầu đặc biệt</p>
                        <p class="p-4 bg-yellow-50 rounded border italic">
                            "{{ $reservation->special_requests }}"
                        </p>
                    </div>
                @endif

            </div>
        </div>

        {{-- FORM XỬ LÝ --}}
        <div>
            <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="admin-card p-6">

                    {{-- Trạng thái --}}
                    <label class="text-sm font-bold">Trạng thái</label>
                    <select name="status" class="form-select-custom w-full mt-2">
                        <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>🕒 Chờ duyệt</option>
                        <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>✅ Xác nhận</option>
                        <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>❌ Hủy</option>
                        <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>🏁 Hoàn thành</option>
                    </select>

                    {{-- BÀN --}}
                    <label class="text-sm font-bold mt-6 block">Xếp bàn</label>
                    <select name="table_id" class="form-select-custom w-full mt-2">
                        <option value="">-- Chưa gán bàn --</option>

                        @foreach($tables as $table)
                            <option 
                                value="{{ $table->id }}"
                                {{ $reservation->table_id == $table->id ? 'selected' : '' }}
                            >
                                Bàn {{ $table->table_number }} ({{ $table->capacity }} chỗ) - {{ ucfirst($table->status) }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full btn-update mt-6">
                        <i class="fas fa-save me-2"></i> Cập nhật
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>

@endsection
