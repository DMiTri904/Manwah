@extends('layouts.app')
@section('content')
<title>Dashboard</title>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">
            <h3 class="text-center mb-4">Admin Panel</h3>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="{{ route('Dashboard') }}" class="nav-link text-white {{ request()->is('dashboard') ? 'active fw-bold' : '' }}">
                        🏠 Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('users.index') }}" class="nav-link text-white {{ request()->is('users*') ? 'active fw-bold' : '' }}">
                        👤 Quản lý Users
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('products.index') }}" class="nav-link text-white {{ request()->is('products*') ? 'active fw-bold' : '' }}">
                        📦 Quản lý Products
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2 class="mb-4">📊 Dashboard</h2>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">👤 Người dùng</h5>
                            <p class="card-text">Quản lý danh sách người dùng hệ thống.</p>
                            <a href="{{ route('users.index') }}" class="btn btn-primary">Đi tới Users</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">📦 Sản phẩm</h5>
                            <p class="card-text">Quản lý danh sách sản phẩm.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-success">Đi tới Products</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bạn có thể thêm các thống kê nhỏ ở đây --}}
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h3>120</h3>
                            <p>Người dùng</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h3>35</h3>
                            <p>Sản phẩm</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h3>15</h3>
                            <p>Đơn hàng</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
