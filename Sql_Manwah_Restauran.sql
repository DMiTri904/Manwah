-- ===============================
-- Database: manwah_sales_system
-- ===============================
CREATE DATABASE IF NOT EXISTS Manwah_Restaurant
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE Manwah_Restaurant;

-- ===============================
-- USERS
-- ===============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'admin', 'staff') DEFAULT 'customer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

INSERT INTO users (full_name, email, password, phone, address, role) VALUES
('Admin Manwah', 'admin@manwah.com', 'Admin123@', '0123456789', 'Hà Nội', 'admin'),
('Nguyễn Văn An', 'an@manwah.com', 'Customer123@', '0981112233', 'TP.HCM', 'customer'),
('Trần Thị Bình', 'binh@manwah.com', 'Customer123@', '0977334455', 'Đà Nẵng', 'customer'),
('Nhân viên Phục vụ', 'staff@manwah.com', 'Staff123@', '0911223344', 'Hà Nội', 'staff');

-- ===============================
-- USER SESSIONS
-- ===============================
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(50),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===============================
-- CATEGORIES (Loại món)
-- ===============================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, description) VALUES
('Lẩu', 'Các loại lẩu và nước dùng'),
('Thịt', 'Các loại thịt bò, gà, heo, hải sản'),
('Rau', 'Rau củ ăn kèm'),
('Đồ uống', 'Nước ngọt, bia, rượu'),
('Tráng miệng', 'Kem, chè, hoa quả');

-- ===============================
-- PRODUCTS (Món ăn / Đồ uống)
-- ===============================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO products (category_id, name, description, price, stock) VALUES
(1, 'Lẩu Thái', 'Nước lẩu chua cay kiểu Thái', 150000, 50),
(1, 'Lẩu Nấm', 'Nước lẩu nấm thanh ngọt', 140000, 40),
(2, 'Bò Mỹ', 'Thịt bò Mỹ cắt lát', 250000, 100),
(2, 'Ba chỉ heo', 'Thịt ba chỉ heo tươi', 180000, 80),
(2, 'Tôm sú', 'Tôm sú tươi', 220000, 60),
(3, 'Rau tổng hợp', 'Rau cải, nấm, ngô...', 70000, 100),
(4, 'Coca Cola', 'Lon 330ml', 25000, 200),
(4, 'Bia Heineken', 'Chai 330ml', 35000, 150),
(5, 'Kem Trà xanh', 'Kem viên hương trà xanh', 40000, 80),
(5, 'Hoa quả dĩa', 'Dưa hấu, nho, dứa...', 50000, 70);

-- ===============================
-- CARTS
-- ===============================
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO carts (user_id) VALUES
(2),
(3);

-- ===============================
-- CART ITEMS
-- ===============================
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO cart_items (cart_id, product_id, quantity) VALUES
(1, 1, 1), -- Nguyễn Văn An chọn Lẩu Thái
(1, 3, 2), -- Nguyễn Văn An chọn 2 phần Bò Mỹ
(1, 7, 3), -- Nguyễn Văn An gọi 3 Coca
(2, 2, 1), -- Trần Thị Bình chọn Lẩu Nấm
(2, 5, 2); -- Trần Thị Bình chọn 2 phần Tôm sú

-- ===============================
-- RESTAURANT TABLES (Bàn ăn)
-- ===============================
CREATE TABLE restaurant_tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) UNIQUE NOT NULL,
    capacity INT NOT NULL,
    status ENUM('available','occupied','reserved') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO restaurant_tables (table_number, capacity, status) VALUES
('T01', 4, 'occupied'),
('T02', 6, 'available'),
('T03', 2, 'reserved'),
('T04', 8, 'available');

-- ===============================
-- ORDERS
-- ===============================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    table_id INT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','confirmed','served','completed','cancelled') DEFAULT 'pending',
    total_amount DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (table_id) REFERENCES restaurant_tables(id)
);

INSERT INTO orders (user_id, table_id, status, total_amount) VALUES
(2, 1, 'confirmed', 675000), -- Nguyễn Văn An: Lẩu Thái + Bò Mỹ + Coca
(3, 3, 'pending', 580000);   -- Trần Thị Bình: Lẩu Nấm + Tôm sú

-- ===============================
-- ORDER ITEMS
-- ===============================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 150000),  -- Lẩu Thái
(1, 3, 2, 250000),  -- Bò Mỹ
(1, 7, 3, 25000),   -- Coca
(2, 2, 1, 140000),  -- Lẩu Nấm
(2, 5, 2, 220000);  -- Tôm sú

-- ===============================
-- PAYMENTS
-- ===============================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('cash','credit_card','momo','bank_transfer') NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending','paid','failed') DEFAULT 'pending',
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

INSERT INTO payments (order_id, payment_method, amount, status) VALUES
(1, 'cash', 675000, 'paid'),
(2, 'momo', 580000, 'pending');
