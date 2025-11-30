@extends('layouts.app')

@section('content')

<style>
    :root {
        --primary-color: #c0392b;
        --secondary-color: #e74c3c;
    }

    /* Bọc trang chỉnh sửa, căn giữa giống container */
    .page-wrapper {
        max-width: 900px;
        margin: 30px auto 60px;   /* căn giữa & cách trên dưới */
        padding: 0 15px;
    }

    .btn-back {
        color: #555;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        margin-bottom: 15px;
        text-decoration: none;
    }
    .btn-back i { margin-right: 6px; }
    .btn-back:hover { color: var(--primary-color); }

    .card-edit {
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 6px 25px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .card-edit-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
        padding: 20px 24px;
    }

    .card-edit-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .card-edit-header span {
        font-size: 0.9rem;
        opacity: .9;
    }

    .card-edit-body {
        padding: 24px;
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #7f8c8d;
        margin-bottom: 8px;
        letter-spacing: .6px;
    }

    .info-box {
        border-radius: 12px;
        border: 1px dashed #f0b3b3;
        background: #fff7f7;
        padding: 14px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .info-avatar {
        height: 40px;
        width: 40px;
        border-radius: 999px;
        background: #ffffff;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #e74c3c;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .info-line-main {
        font-weight: 600;
    }

    .info-line-sub {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .form-label-custom {
        font-weight: 600;
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 10px 12px;
        font-size: 0.95rem;
        background-color: #fafafa;
        transition: all .2s;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #fff;
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 0.2rem rgba(231,76,60,.18);
    }

    .badge-small {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.8rem;
        background: #f4f4f4;
        color: #555;
    }

    .divider {
        border-top: 1px dashed #eee;
        margin: 18px 0;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        border-radius: 999px;
        padding: 10px 26px;
        font-weight: 700;
        letter-spacing: .4px;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(192,57,43,0.35);
        transition: 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(192,57,43,0.45);
    }

    @media (max-width: 768px) {
        .page-wrapper {
            margin-top: 15px;
            margin-bottom: 30px;
        }
    }
</style>

<div class="page-wrapper">

    <a href="{{ route('admin.reservations.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>

    {{-- Thông báo lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card --}}
    <div class="card-edit">
        <div class="card-edit-header">
            <h3>
                Quản lý đặt bàn
                <span class="ms-2">#{{ $reservation->id }}</span>
            </h3>
            <span>
                Đặt lúc: {{ \Carbon\Carbon::parse($reservation->created_at)->format('H:i d/m/Y') }}
            </span>
        </div>

        <div class="card-edit-body">

            {{-- Thông tin khách --}}
            <div class="section-title">Thông tin khách hàng</div>
            <div class="info-box">
                <div class="info-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="info-line-main">
                        {{ $reservation->user->full_name ?? 'Khách lẻ' }}
                    </div>
                    <div class="info-line-sub">
                        <i class="fas fa-phone me-1"></i>
                        {{ $reservation->user->phone ?? 'N/A' }}
                        &nbsp; • &nbsp;
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                        &nbsp; • &nbsp;
                        <i class="far fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                        &nbsp; • &nbsp;
                        <i class="fas fa-users me-1"></i>
                        {{ $reservation->guest_count }} khách
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.reservations.update', $reservation->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Chọn trạng thái --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Trạng thái đơn</label>
                        @php
                            $statusOptions = [
                                'pending'   => 'Chờ duyệt',
                                'confirmed' => 'Đã xác nhận',
                                'cancelled' => 'Đã hủy',
                                'completed' => 'Hoàn tất',
                            ];
                        @endphp
                        <select name="status" class="form-select">
                            @foreach ($statusOptions as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $reservation->status) == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Chọn bàn --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">
                            Bàn phục vụ
                            <span class="badge-small ms-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Chỉ hiện bàn trống + bàn đang gán
                            </span>
                        </label>
                        <select name="table_id" class="form-select">
                            <option value="">-- Chưa xếp bàn --</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}"
                                    @selected(old('table_id', $reservation->restaurant_table_id) == $table->id)>
                                    Bàn {{ $table->table_number }} ({{ $table->type ?? 'Thông thường' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Ngày & giờ đặt</label>
                        <div class="badge-small">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                            &nbsp; • &nbsp;
                            <i class="far fa-clock me-1"></i>
                            {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Số lượng khách</label>
                        <div class="badge-small">
                            <i class="fas fa-users me-1"></i>
                            {{ $reservation->guest_count }} khách
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
