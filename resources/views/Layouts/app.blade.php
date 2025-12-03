<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manwah Restaurant</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            color: #d32f2f !important;
        }

        .card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }

        .btn-primary:hover {
            background-color: #b71c1c;
            border-color: #b71c1c;
        }
    </style>

    @yield('styles')
</head>

<body>
    @php
        $tableId = session('table_id') ?? null;
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/dashboard') }}">
                <i class="fas fa-utensils me-2"></i>Manwah Restaurant
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/dashboard') }}">
                            <i class="fas fa-home me-1"></i>Trang Chủ
                        </a>
                    </li>

                    <li class="nav-item">
                        @if($tableId)
                            <a class="nav-link" href="{{ route('table.menu', ['table' => $tableId]) }}">
                                <i class="fas fa-utensils me-1"></i>Thực Đơn
                            </a>
                        @else
                            <a class="nav-link" href="{{ route('choose.table') }}">
                                <i class="fas fa-utensils me-1"></i>Thực Đơn
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if($tableId)
                            <a class="nav-link" href="{{ route('table.cart', ['table' => $tableId]) }}">
                                <i class="fas fa-shopping-cart me-1"></i>Giỏ Hàng
                            </a>
                        @else
                            <a class="nav-link" href="{{ route('choose.table') }}">
                                <i class="fas fa-shopping-cart me-1"></i>Chọn bàn / Giỏ Hàng
                            </a>
                        @endif
                    </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reservations.create') }}">
                               <i class="fa-solid fa-utensils"></i> Đặt bàn ngay
                            </a>
                        </li>
                               <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.reservations.index') }}">
                               <i class="fa-solid fa-utensils"></i> quản lý đặt bàn
                            </a>
                        </li>
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Đăng ký
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->full_name }}
                                @if(Auth::user()->isAdmin())
                                    <span class="badge bg-danger ms-1">Admin</span>
                                @elseif(Auth::user()->isStaff())
                                    <span class="badge bg-warning ms-1">Staff</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="fas fa-list me-2"></i>Đơn hàng của tôi
                                    </a>
                                </li>

                                 <li>
                                    <a class="dropdown-item" href="{{ route('reservations.history') }}">
                                        <i class="fas fa-list me-2"></i>Lịch sử đặt bàn
                                    </a>
                                </li>

                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="fas fa-cog me-2"></i>Quản lý đơn hàng
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function () {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @yield('scripts')
</body>

</html>