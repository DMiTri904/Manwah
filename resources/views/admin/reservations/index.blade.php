@extends('layouts.app')

@section('content')

<style>
    :root {
        --primary-color: #c0392b;
        --secondary-color: #e74c3c;
        --light-red: #fdf2f2;
        --border-light: #f5d5d5;
    }

    /* Bọc toàn trang, căn giữa giống trang edit */
    .page-wrapper {
        max-width: 1100px;
        margin: 30px auto 60px;
        padding: 0 15px;
    }

    /* Tiêu đề */
    .page-title {
        color: var(--primary-color);
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 55%;
        height: 3px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    /* Card chứa bảng */
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        background: white;
        overflow: hidden;
    }

    /* Header bảng */
    .custom-table thead tr {
        background-color: var(--light-red);
        border-bottom: 2px solid var(--border-light);
    }
    
    .custom-table th {
        color: #7f8c8d;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 18px 15px;
        text-transform: uppercase;
        letter-spacing: .6px;
        white-space: nowrap;
    }

    /* Body bảng */
    .custom-table tbody tr {
        transition: 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .custom-table tbody tr:hover {
        background-color: #fff8f8;
        transform: scale(1.002);
    }

    .custom-table td {
        padding: 15px;
        color: #2c3e50;
        font-size: 0.95rem;
        vertical-align: middle;
    }

    /* Badge Mã Đơn */
    .id-badge {
        font-weight: 700;
        color: var(--primary-color);
        background: #fde3e3;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    /* Avatar khách */
    .avatar-circle {
        height: 38px;
        width: 38px;
        border-radius: 50%;
        background: #f2f2f2;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 12px;
        color: #777;
    }

    .customer-name {
        font-weight: 600;
    }

    .customer-sub {
        font-size: 0.8rem;
        color: #95a5a6;
        margin-top: 2px;
    }

    /* Nút xử lý */
    .btn-action-edit {
        background-color: white;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: .3s;
        display: inline-flex;
        align-items: center;
    }
    .btn-action-edit:hover {
        background-color: var(--primary-color);
        color: white;
        box-shadow: 0 5px 12px rgba(192, 57, 43, 0.28);
    }
</style>

<div class="page-wrapper">

    <h1 class="text-3xl font-extrabold page-title">
        <i class="fas fa-list-check me-2"></i> Quản Lý Đặt Bàn
    </h1>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        <div class="overflow-x-auto">
            <table class="min-w-full custom-table">
                <thead>
                    <tr>
                        <th class="text-center w-20">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th class="text-center">Số Khách</th>
                        <th>Thời Gian & Bàn</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-right">Hành Động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($reservations as $reservation)
                        <tr>

                            {{-- Mã Đơn --}}
                            <td class="text-center">
                                <span class="id-badge">#{{ $reservation->id }}</span>
                            </td>

                            {{-- Khách hàng --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="customer-name">
                                            {{ $reservation->user->full_name ?? 'Khách lẻ' }}
                                        </div>
                                        <div class="customer-sub">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $reservation->user->phone ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Số khách --}}
                            <td class="text-center fw-bold">
                                {{ $reservation->guest_count }} người
                            </td>

                            {{-- Thời gian & bàn --}}
                            <td>
                                <div class="text-danger fw-semibold">
                                    <i class="far fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                                    - {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                                </div>

                                <div class="mt-1 small">
                                    @if($reservation->restaurantTable)
                                        <span class="bg-red-50 px-2 py-1 rounded border border-red-100 text-danger fw-semibold">
                                            <i class="fas fa-chair me-1"></i>
                                            Bàn {{ $reservation->restaurantTable->table_number }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">
                                            <i class="fas fa-chair me-1"></i> Chưa xếp bàn
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Trạng thái --}}
                            <td class="text-center">
                                @php
                                    $config = [
                                        'pending'   => ['bg-yellow-100 text-yellow-800', 'Chờ duyệt',  'fa-clock'],
                                        'confirmed' => ['bg-green-100 text-green-800',   'Xác nhận',   'fa-check'],
                                        'cancelled' => ['bg-red-100 text-red-800',       'Đã hủy',     'fa-times'],
                                        'completed' => ['bg-blue-100 text-blue-800',     'Hoàn thành', 'fa-flag-checkered'],
                                    ];
                                    $st = $config[$reservation->status] ?? $config['pending'];
                                @endphp

                                <span class="px-3 py-1 rounded-pill text-xs fw-bold d-inline-flex align-items-center {{ $st[0] }}">
                                    <i class="fas {{ $st[2] }} me-1"></i> {{ $st[1] }}
                                </span>
                            </td>

                            {{-- Hành động --}}
                            <td class="text-end">
                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn-action-edit">
                                    <i class="fas fa-pen me-1"></i> Xử lý
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-muted">
                                <i class="fas fa-clipboard-list text-4xl text-secondary mb-2"></i><br>
                                Chưa có đơn đặt bàn nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($reservations->hasPages())
            <div class="px-4 px-md-5 py-3 bg-light border-top">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
