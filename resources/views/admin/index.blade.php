@extends('layouts.app')

@section('content')

<style>
    :root {
        --primary-color: #c0392b;
        --secondary-color: #e74c3c;
    }

    /* Tiêu đề gạch chân gradient */
    .page-title {
        color: var(--primary-color);
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    /* Card bao quanh bảng */
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        background: white;
        overflow: hidden;
    }

    /* Header của bảng */
    .custom-table thead tr {
        background-color: #fdf2f2; /* Nền đỏ rất nhạt */
        border-bottom: 2px solid #fcebeb;
    }
    
    .custom-table th {
        color: #7f8c8d;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 18px 15px;
        letter-spacing: 0.5px;
    }

    /* Body của bảng */
    .custom-table tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #f1f1f1;
    }
    
    .custom-table tbody tr:hover {
        background-color: #fff9f9; /* Hiệu ứng hover hồng nhạt */
        transform: scale(1.002);
    }

    .custom-table td {
        padding: 15px;
        vertical-align: middle;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    /* Các thành phần nhỏ */
    .id-badge {
        font-weight: 800;
        color: var(--primary-color);
        background: #fcebeb;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .info-sub {
        font-size: 0.8rem;
        color: #95a5a6;
        margin-top: 3px;
    }

    .btn-action-edit {
        background-color: white;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-action-edit:hover {
        background-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 10px rgba(192, 57, 43, 0.3);
    }
</style>

<div class="container mx-auto p-4 sm:p-6 lg:p-10">

    <div class="flex justify-between items-end mb-6">
        <h1 class="text-3xl font-extrabold page-title">
            <i class="fas fa-tasks me-2"></i>Quản Lý Đặt Bàn
        </h1>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r flex items-center">
            <i class="fas fa-check-circle me-3 text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        <div class="overflow-x-auto">
            <table class="min-w-full custom-table">
                <thead>
                    <tr>
                        <th class="text-center w-24">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th class="text-center">Số Khách</th>
                        <th>Thời Gian & Bàn</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-right">Hành Động</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @forelse ($reservations as $reservation)
                        <tr>
                            <td class="text-center">
                                <span class="id-badge">#{{ $reservation->id }}</span>
                            </td>

                            {{-- Khách hàng --}}
                            <td>
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">
                                            {{ $reservation->user->full_name ?? $reservation->customer_name ?? 'Khách lẻ' }}
                                        </div>
                                        <div class="info-sub">
                                            <i class="fas fa-phone-alt me-1 text-xs"></i> 
                                            {{ $reservation->user->phone_number ?? $reservation->customer_phone ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Số khách --}}
                            <td class="text-center font-bold text-gray-600">
                                {{ $reservation->num_guests }} người
                            </td>

                            {{-- Thời gian + bàn --}}
                            <td>
                                <div>
                                    <i class="far fa-clock text-red-500 me-1"></i>
                                    {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i - d/m/Y') }}
                                </div>

                                <div class="mt-1">
                                    @if($reservation->restaurantTable)
                                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded border border-red-100">
                                            <i class="fas fa-chair me-1"></i>
                                            Bàn {{ $reservation->restaurantTable->table_number }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">
                                            <i class="fas fa-chair me-1"></i> Chưa xếp bàn
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Trạng thái --}}
                            <td class="text-center">
                                @php
                                    $statusConfig = [
                                        'pending' => ['color'=>'bg-yellow-100 text-yellow-800', 'label'=>'Chờ duyệt', 'icon'=>'fa-clock'],
                                        'confirmed'=>['color'=>'bg-green-100 text-green-800','label'=>'Xác nhận','icon'=>'fa-check'],
                                        'cancelled'=>['color'=>'bg-red-100 text-red-800','label'=>'Đã hủy','icon'=>'fa-times'],
                                        'completed'=>['color'=>'bg-blue-100 text-blue-800','label'=>'Hoàn thành','icon'=>'fa-flag-checkered'],
                                    ];
                                    $st = $statusConfig[$reservation->status];
                                @endphp

                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $st['color'] }}">
                                    <i class="fas {{ $st['icon'] }} me-1"></i> {{ $st['label'] }}
                                </span>
                            </td>

                            {{-- Hành động --}}
                            <td class="text-right">
                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn-action-edit">
                                    <i class="fas fa-pen me-1"></i> Xử lý
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i><br>
                                Chưa có đơn đặt bàn nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reservations->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
