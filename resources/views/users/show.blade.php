<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin User - {{ $user->full_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Thông tin chi tiết User</h4>
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                                ← Quay lại
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Thông tin cơ bản -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h3 class="text-primary">{{ $user->full_name }}</h3>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                            </div>
                        </div>

                        <!-- Chi tiết thông tin -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>ID:</strong>
                                <span class="text-muted">#{{ $user->id }}</span>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong>Họ và tên:</strong>
                                <span class="text-muted">{{ $user->full_name }}</span>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong>
                                <span class="text-muted">{{ $user->email }}</span>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong>Số điện thoại:</strong>
                                <span class="text-muted">{{ $user->phone }}</span>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <strong>Địa chỉ:</strong>
                                <span class="text-muted">{{ $user->address }}</span>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong>Role:</strong>
                                <span class="text-muted">{{ $user->role }}</span>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $user->status }}
                                </span>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <strong>Ngày tạo:</strong>
                                <span class="text-muted">{{ $user->created_at }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                            Chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>