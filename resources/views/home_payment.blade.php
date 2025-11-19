{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Manwah Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-manwah {
            background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
        }
        .text-manwah {
            color: #d4af37;
        }
        .border-manwah {
            border-color: #d4af37;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-manwah text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-utensils text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold">Manwah Restaurant</h1>
                        <p class="text-yellow-100 text-sm">Nhà hàng Lẩu Manwah</p>
                    </div>
                </div>
                <div class="flex space-x-6">
                    <a href="{{ url('/') }}" class="hover:text-yellow-200 transition">
                        <i class="fas fa-home mr-1"></i>Trang chủ
                    </a>
                    <a href="{{ route('payment.pending-orders') }}" class="hover:text-yellow-200 transition">
                        <i class="fas fa-list mr-1"></i>Đơn hàng
                    </a>
                    <a href="{{ route('payment.history') }}" class="hover:text-yellow-200 transition">
                        <i class="fas fa-history mr-1"></i>Lịch sử TT
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4 text-manwah">Manwah Restaurant</h3>
                    <p class="text-gray-300">Nhà hàng Lẩu & Nướng cao cấp</p>
                    <p class="text-gray-300 mt-2">📍 123 Đường Ẩm Thực, Hà Nội</p>
                    <p class="text-gray-300">📞 0123 456 789</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Giờ mở cửa</h3>
                    <p class="text-gray-300">⏰ 10:00 - 22:00</p>
                    <p class="text-gray-300">Mở cửa cả tuần</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Phương thức thanh toán</h3>
                    <div class="flex space-x-2">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">MoMo</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">Thẻ</span>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Tiền mặt</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2024 Manwah Restaurant.  Hệ thống Thanh toán.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>