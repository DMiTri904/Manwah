
<!DOCTYPE html>
<html>
<head>
    <title>Đặt Lại Mật Khẩu</title>
</head>
<body>
    <h2>Đặt Lại Mật Khẩu</h2>
    
    <p>Xin chào,</p>
    
    <p>Bạn nhận được email này vì chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
    
    <p>Vui lòng click vào link bên dưới để đặt lại mật khẩu:</p>
    
    <a href="{{ $resetUrl }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        Đặt Lại Mật Khẩu
    </a>
    
    <p>Link này sẽ hết hạn trong 60 phút.</p>
    
    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
    
    <p>Trân trọng,<br>Đội ngũ {{ config('app.name') }}</p>
</body>
</html>