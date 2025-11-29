@extends('layouts.app')

@section('title', 'Thực đơn')

@section('content')
    @php
        $tableId = $tableId ?? session('table_id');
    @endphp

    <div class="container-fluid px-0" style="padding-bottom: 100px; background: #f5f5f5;">

        <!-- Header -->
        <div class="menu-header sticky-top bg-white shadow-sm">
            <div class="container py-3">
                <h4 class="mb-0 fw-bold text-center">
                    <i class="fas fa-utensils text-danger me-2"></i>
                    Bàn {{ $tableId ?? 'chưa chọn' }}
                </h4>
            </div>

            <!-- Bộ lọc danh mục - Horizontal Scroll -->
            <div class="category-scroll-wrapper">
                <div class="category-scroll">
                    <a href="{{ route('table.menu', ['table' => $tableId]) }}"
                        class="category-chip {{ request('category_id') ? '' : 'active' }}">
                        <i class="fas fa-home me-1"></i> Tất cả
                    </a>

                    @foreach($categories as $category)
                        <a href="{{ route('table.menu', ['table' => $tableId, 'category_id' => $category->id]) }}"
                            class="category-chip {{ request('category_id') == $category->id ? 'active' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        @if(session('success'))
            <div class="alert alert-success mx-3 mt-3 text-center">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mx-3 mt-3 text-center">{{ session('error') }}</div>
        @endif

        <!-- FORM -->
        <form action="{{ route('table.cart.add', ['table' => $tableId]) }}" method="POST" id="menuForm">
            @csrf

            <!-- Danh sách món - Grid Style -->
            <div class="container mt-3">
                <div class="row g-3">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <div class="menu-card">
                                <!-- Image -->
                                <div class="menu-card-img">
                                    @if($product->image)
                                        <img src="{{ asset('storage/ProductsImage/' . $product->image) }}"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div class="no-img">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="menu-card-body">
                                    <h6 class="menu-card-name">{{ $product->name }}</h6>
                                    <p class="menu-card-price">{{ number_format($product->price) }}đ</p>

                                    <input type="hidden" name="product_id[]" value="{{ $product->id }}">

                                    <!-- Quantity Controls -->
                                    <div class="quantity-control">
                                        <button type="button" class="qty-btn minus" data-action="decrease">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" name="quantity[]"
                                            value="{{ $selectedQuantities[$product->id] ?? 0 }}" class="qty-display" readonly>
                                        <button type="button" class="qty-btn plus" data-action="increase">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </form>
    </div>

    <!-- Fixed Bottom Button -->
    <div class="fixed-bottom-bar">
        <div class="container">
            <button type="submit" form="menuForm" class="btn-order">
                <i class="fas fa-shopping-cart me-2"></i>
                <span>Thêm vào giỏ hàng</span>
                <span class="total-items" id="totalItems">0</span>
            </button>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        body {
            background: #f5f5f5;
        }

        /* Header */
        .menu-header {
            z-index: 100;
        }

        /* Category Scroll */
        .category-scroll-wrapper {
            background: white;
            border-top: 1px solid #eee;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .category-scroll-wrapper::-webkit-scrollbar {
            display: none;
        }

        .category-scroll {
            display: flex;
            padding: 12px 15px;
            gap: 10px;
            min-width: min-content;
        }

        .category-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .category-chip:hover {
            background: #fff3f3;
            color: #dc3545;
        }

        .category-chip.active {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        /* Menu Card - Grid */
        .menu-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .menu-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        /* Image */
        .menu-card-img {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .menu-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .menu-card:hover .menu-card-img img {
            transform: scale(1.05);
        }

        .no-img {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ddd;
            font-size: 48px;
        }

        /* Card Body */
        .menu-card-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .menu-card-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
            min-height: 44px;
        }

        .menu-card-price {
            font-size: 20px;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 16px;
        }

        /* Quantity Control */
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: auto;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #dc3545;
            background: white;
            color: #dc3545;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .qty-btn:active {
            transform: scale(0.9);
        }

        .qty-btn.plus {
            background: #dc3545;
            color: white;
        }

        .qty-btn.plus:hover {
            background: #c82333;
            border-color: #c82333;
        }

        .qty-btn.minus:hover {
            background: #fff5f5;
        }

        .qty-display {
            flex: 1;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #333;
            border: none;
            background: transparent;
            min-width: 0;
        }

        /* Fixed Bottom Bar */
        .fixed-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 12px 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .btn-order {
            width: 100%;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 25px;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-order:active {
            transform: scale(0.98);
        }

        .total-items {
            background: rgba(255, 255, 255, 0.3);
            padding: 2px 12px;
            border-radius: 15px;
            font-weight: 700;
            min-width: 30px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu-card-img {
                height: 180px;
            }

            .menu-card-name {
                font-size: 15px;
                min-height: 40px;
            }

            .menu-card-price {
                font-size: 18px;
            }
        }

        @media (max-width: 576px) {
            .menu-card-img {
                height: 160px;
            }

            .menu-card-name {
                font-size: 14px;
                min-height: 38px;
            }

            .qty-btn {
                width: 32px;
                height: 32px;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let totalCount = 0;
            const totalItemsDisplay = document.getElementById('totalItems');
            const tableId = '{{ $tableId ?? "default" }}';
            const storageKey = 'temp_qty_table_' + tableId;

            // ⭐ UPDATE TOTAL - TÍNH TỪ LOCALSTORAGE
            function updateTotal() {
                totalCount = 0;
                const saved = localStorage.getItem(storageKey);
                if (saved) {
                    try {
                        const allQuantities = JSON.parse(saved);
                        for (let qty of Object.values(allQuantities)) {
                            totalCount += parseInt(qty) || 0;
                        }
                    } catch (e) { }
                }

                totalItemsDisplay.textContent = totalCount;

                if (totalCount > 0) {
                    totalItemsDisplay.style.animation = 'none';
                    setTimeout(() => {
                        totalItemsDisplay.style.animation = 'pulse 0.3s ease';
                    }, 10);
                }
            }

            // ⭐ LƯU VÀO LOCALSTORAGE VÀ TRẢ VỀ DỮ LIỆU MỚI
            function saveQuantities() {
                let allQuantities = {};
                const saved = localStorage.getItem(storageKey);
                if (saved) {
                    try {
                        allQuantities = JSON.parse(saved);
                    } catch (e) { }
                }

                document.querySelectorAll('.menu-card').forEach(card => {
                    const productId = card.querySelector('input[name="product_id[]"]').value;
                    const qtyInput = card.querySelector('.qty-display');
                    const qty = parseInt(qtyInput.value) || 0;

                    if (qty > 0) {
                        allQuantities[productId] = qty;
                    } else {
                        delete allQuantities[productId];
                    }
                });

                localStorage.setItem(storageKey, JSON.stringify(allQuantities));
                return allQuantities; // ⭐ TRẢ VỀ DỮ LIỆU
            }

            // LOAD TỪ LOCALSTORAGE
            function loadQuantities() {
                const saved = localStorage.getItem(storageKey);

                if (saved) {
                    try {
                        const allQuantities = JSON.parse(saved);

                        document.querySelectorAll('.menu-card').forEach(card => {
                            const productId = card.querySelector('input[name="product_id[]"]').value;
                            const qtyInput = card.querySelector('.qty-display');

                            if (allQuantities[productId]) {
                                qtyInput.value = allQuantities[productId];
                            }
                        });
                    } catch (e) {
                        console.error('Error loading:', e);
                    }
                }
                updateTotal();
            }

            // Quantity buttons
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const control = this.closest('.quantity-control');
                    const input = control.querySelector('.qty-display');
                    let value = parseInt(input.value) || 0;

                    if (this.dataset.action === 'increase') {
                        value++;
                    } else if (this.dataset.action === 'decrease' && value > 0) {
                        value--;
                    }

                    input.value = value;

                    // ⭐ LƯU VÀ TÍNH TỔNG TRỰC TIẾP TỪ DỮ LIỆU VỪA LƯU
                    const allQuantities = saveQuantities();

                    // Tính tổng từ dữ liệu vừa lưu
                    totalCount = 0;
                    for (let qty of Object.values(allQuantities)) {
                        totalCount += parseInt(qty) || 0;
                    }
                    totalItemsDisplay.textContent = totalCount;

                    // Animation
                    if (totalCount > 0) {
                        totalItemsDisplay.style.animation = 'none';
                        setTimeout(() => {
                            totalItemsDisplay.style.animation = 'pulse 0.3s ease';
                        }, 10);
                    }
                });
            });

            // TRƯỚC KHI SUBMIT: THÊM TẤT CẢ MÓN TỪ LOCALSTORAGE VÀO FORM
            const form = document.getElementById('menuForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const saved = localStorage.getItem(storageKey);
                    if (saved) {
                        try {
                            const allQuantities = JSON.parse(saved);

                            const existingProductIds = new Set();
                            document.querySelectorAll('input[name="product_id[]"]').forEach(input => {
                                existingProductIds.add(input.value);
                            });

                            for (let [productId, qty] of Object.entries(allQuantities)) {
                                if (!existingProductIds.has(productId) && qty > 0) {
                                    const hiddenProductId = document.createElement('input');
                                    hiddenProductId.type = 'hidden';
                                    hiddenProductId.name = 'product_id[]';
                                    hiddenProductId.value = productId;
                                    form.appendChild(hiddenProductId);

                                    const hiddenQty = document.createElement('input');
                                    hiddenQty.type = 'hidden';
                                    hiddenQty.name = 'quantity[]';
                                    hiddenQty.value = qty;
                                    form.appendChild(hiddenQty);
                                }
                            }
                        } catch (e) {
                            console.error('Error:', e);
                        }
                    }

                    localStorage.removeItem(storageKey);
                });
            }

            // Add pulse animation CSS
            const style = document.createElement('style');
            style.textContent = `
                    @keyframes pulse {
                        0%, 100% { transform: scale(1); }
                        50% { transform: scale(1.2); }
                    }
                `;
            document.head.appendChild(style);

            // LOAD KHI VÀO TRANG
            loadQuantities();
        });
    </script>
@endsection