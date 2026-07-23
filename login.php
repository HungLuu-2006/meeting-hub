<?php
session_start(); // Khởi tạo session để lưu trạng thái đăng nhập

// Nếu người dùng đã đăng nhập thì tự động chuyển sang trang danh sách phòng
if (isset($_SESSION['user_id'])) {
    header("Location: rooms.php");
    exit();
}

// Nhúng kết nối cơ sở dữ liệu
require_once 'config/database.php';

$error = '';

// Xử lý khi người dùng bấm nút "Đăng nhập"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password']; 

    try {
        // Truy vấn kiểm tra tài khoản
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username AND password = :password");
        $stmt->execute([
            'username' => $username,
            'password' => $password
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Lưu thông tin vào Session khi đăng nhập thành công
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            // Chuyển hướng đến trang danh sách phòng
            header("Location: rooms.php");
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
        }
    } catch (PDOException $e) {
        $error = "Lỗi hệ thống: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - XYZ Meetings</title>
    <!-- Nhúng Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #131314;
            color: #e3e3e3;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .login-card {
            background-color: #1e1f20;
            border: 1px solid #3c4043;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        /* Ô nhập dữ liệu */
        .form-control {
            background-color: #131314 !important;
            border: 1px solid #3c4043 !important;
            color: #ffffff !important; /* Màu chữ khi nhập */
        }
        /* Màu chữ gợi ý (Placeholder) */
        .form-control::placeholder {
            color: #80868b !important;
            opacity: 1;
        }
        .form-control:focus {
            background-color: #131314 !important;
            color: #ffffff !important;
            border-color: #8ab4f8 !important;
            box-shadow: none;
        }
        .form-label {
            color: #bdc1c6;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }
        .text-sub {
            color: #9aa0a6;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h2 style="color: #8ab4f8; font-weight: 700; font-size: 1.8rem;">ĐĂNG NHẬP</h2>
        <p class="text-sub mb-0">Hệ thống quản lý phòng họp</p>
    </div>

    <!-- Hiển thị thông báo lỗi nếu đăng nhập sai -->
    <?php if ($error): ?>
        <div class="alert alert-danger p-2 text-center mb-3" style="background-color: #3c1e1e; border: 1px solid #f28b82; color: #f28b82; font-size: 0.88rem;" role="alert">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Form Đăng nhập -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control py-2" id="username" name="username" required placeholder="Nhập tên đăng nhập...">
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control py-2" id="password" name="password" required placeholder="Nhập mật khẩu...">
        </div>
        
        <button type="submit" class="btn w-100 py-2 mb-3 fw-bold" style="background-color: #8ab4f8; color: #131314; border: none; border-radius: 8px;">
            Đăng nhập
        </button>
    </form>

    <!-- Nút Đăng ký -->
    <div class="text-center mt-3 pt-3" style="border-top: 1px solid #3c4043;">
        <span class="text-sub d-block mb-2">Chưa có tài khoản?</span> 
        <a href="register.php" class="btn btn-outline-light w-100 py-2" style="border-color: #5f6368; color: #e3e3e3; border-radius: 8px; font-weight: 500;">
            Đăng ký ngay
        </a>
    </div>
</div>

</body>
</html>
