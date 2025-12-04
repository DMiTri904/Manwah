<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manwah — Taiwanese Hotpot</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap for quick layout (optional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;}
    .hero{
      min-height:64vh;
      background-image: url(../storage/app/public/images/landing_hero.jpg);
      background-size:cover;
      background-position:center;
      color:#fff;
      display:flex;
      align-items:center;
      position:relative;
    }
    .overlay{position:absolute;inset:0;background:rgba(0,0,0,0.45)}
    .hero .container{position:relative;z-index:2}
    .card-menu img{height:120px;object-fit:cover;width:100%;border-radius:6px}
    .gallery img{width:100%;height:160px;object-fit:cover;border-radius:6px}
    footer{background:#0b0b0b;color:#ddd;padding:30px 0}
    .btn-primary{background:#d43c3c;border:0}
  </style>
</head>
<body>
  <!-- Header -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">Manwah</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navNav" aria-controls="navNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="#about">Giới thiệu</a></li>
          <li class="nav-item"><a class="nav-link" href="#menu">Thực đơn</a></li>
          <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Liên hệ</a></li>
          <li class="nav-item ms-3"><a class="btn btn-outline-primary" href="{{ route('login') }}">Đăng nhập</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <header class="hero">
    <div class="overlay"></div>
    <div class="container text-center text-white">
      <h1 class="display-5 fw-bold">Manwah — Lẩu Đài Loan chính hiệu</h1>
      <p class="lead">Hương vị đậm đà, nguyên liệu tươi ngon — phù hợp cho gia đình & bạn bè</p>
      <div class="d-flex justify-content-center gap-2">
        <a href="#menu" class="btn btn-primary btn-lg">Xem thực đơn</a>
        <a href="#contact" class="btn btn-outline-light btn-lg">Đặt bàn</a>
      </div>
      <p class="mt-3 small">Trang này có thể xem mà không cần đăng nhập — khách hàng dễ tiếp cận thông tin.</p>
    </div>
  </header>

  <!-- About -->
  <section id="about" class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h3>Về Manwah</h3>
          <p>Manwah là chuỗi lẩu tiêu biểu mang hương vị Đài Loan, với nước lẩu đặc trưng, gia vị tươi và nhiều loại topping. Không gian ấm cúng, phù hợp các buổi tụ họp.</p>
          <ul>
            <li>Đa dạng nước lẩu: Mala, Cà chua, Nấm, Thanh đạm</li>
            <li>Buffet & gọi món — phù hợp theo nhóm</li>
            <li>Ưu đãi đặt bàn trực tuyến</li>
          </ul>
        </div>
        <div class="col-md-6">
          <img src="https://images.unsplash.com/photo-1541963463532-d68292c34b19?auto=format&fit=crop&w=800&q=80" alt="Lẩu" class="img-fluid rounded shadow">
        </div>
      </div>
    </div>
  </section>

  <!-- Menu highlights -->
  <section id="menu" class="py-5 bg-light">
    <div class="container">
      <h3 class="mb-4">Món nổi bật</h3>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card p-3 card-menu h-100">
            <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80" alt="Set lẩu">
            <div class="card-body p-0 mt-2">
              <h5 class="mb-1">Set Lẩu Phong Vân</h5>
              <p class="small mb-0">Set cho 2-3 người — nhiều topping, nước lẩu đặc trưng.</p>
              <p class="mt-2 fw-bold">Từ 259.000 đ</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 card-menu h-100">
            <img src="https://images.unsplash.com/photo-1604908176940-20f49a4f3b4b?auto=format&fit=crop&w=800&q=80" alt="Hải sản">
            <div class="card-body p-0 mt-2">
              <h5 class="mb-1">Combo Hải Sản Tươi</h5>
              <p class="small mb-0">Tôm, mực, sò — phù hợp ăn cùng lẩu nấm.</p>
              <p class="mt-2 fw-bold">Từ 199.000 đ</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 card-menu h-100">
            <img src="https://images.unsplash.com/photo-1604908177199-9f2b3a1a6f2e?auto=format&fit=crop&w=800&q=80" alt="Rau nấm">
            <div class="card-body p-0 mt-2">
              <h5 class="mb-1">Đĩa Rau & Nấm</h5>
              <p class="small mb-0">Rau xanh tươi và nấm đa dạng — bù vị cho nước lẩu.</p>
              <p class="mt-2 fw-bold">Từ 79.000 đ</p>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="#contact" class="btn btn-outline-primary">Đặt bàn / Liên hệ</a>
      </div>
    </div>
  </section>

  <!-- Gallery -->
  <section id="gallery" class="py-5">
    <div class="container">
      <h3 class="mb-4">Gallery</h3>
      <div class="row g-3 gallery">
        <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1550317138-10000687a72b?auto=format&fit=crop&w=600&q=80" alt="photo"></div>
        <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80" alt="photo"></div>
        <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80" alt="photo"></div>
        <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80" alt="photo"></div>
      </div>
    </div>
  </section>

  <!-- Contact / Location -->
  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <h3>Liên hệ & Địa chỉ</h3>
      <div class="row mt-3">
        <div class="col-md-6">
          <p class="mb-1"><strong>Manwah - Chi nhánh Trung tâm</strong></p>
          <p class="small mb-1">Số 1 Example Street, Quận X, Thành phố Y</p>
          <p class="small mb-1">Điện thoại: <a href="tel:+84-912-345-678">(+84) 912 345 678</a></p>
          <p class="small mb-1">Giờ mở cửa: 10:00 — 22:30 (hàng ngày)</p>
          <div class="mt-3">
            <a href="https://maps.google.com" target="_blank" class="btn btn-primary me-2">Chỉ đường</a>
            <a href="mailto:hello@manwah.vn" class="btn btn-outline-secondary">Gửi email</a>
          </div>
        </div>
        <div class="col-md-6">
          <!-- simple booking form (public) -->
          <div class="card p-3">
            <h5>Đặt bàn nhanh</h5>
           <form action="{{ route('reservations.store') }}" method="POST">
    @csrf

    <div class="mb-2">
        <input class="form-control" name="customer_name" placeholder="Họ tên" required>
    </div>

    <div class="mb-2">
        <input class="form-control" name="customer_phone" placeholder="Số điện thoại" required>
    </div>

    <div class="mb-2">
        <input class="form-control" name="reservation_time" type="datetime-local" required>
    </div>

    <div class="mb-2">
        <input class="form-control" name="num_guests" type="number" min="1" required>
    </div>

    <button class="btn btn-primary w-100">Gửi yêu cầu</button>
</form>

            <small class="text-muted d-block mt-2">Lưu ý: khách không cần đăng nhập để gửi yêu cầu đặt bàn.</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container text-center">
      <p class="mb-1">© {{ date('Y') }} Manwah — All rights reserved.</p>
      <p class="small mb-0">Xem thêm trên <a href="{{ url('/') }}" class="text-decoration-underline text-white">Trang chính</a></p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
