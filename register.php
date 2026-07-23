<?php
session_start();

// Nếu người dùng đã đăng nhập thì tự động chuyển sang trang danh sách phòng
if (isset($_SESSION['user_id'])) {
    header("Location: rooms.php");
    exit();
}

// Nhúng kết nối cơ sở dữ liệu
require_once 'config/database.php';

$error = '';
$success = '';

// Xử lý khi bấm nút "Đăng ký ngay"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
        $error = "Vui lòng điền đầy đủ các thông tin bắt buộc (*)!";
    } else {
        try {
            // Kiểm tra tên đăng nhập hoặc email đã tồn tại chưa
            $stmtCheck = $pdo->prepare("SELECT user_id FROM Users WHERE username = :username OR email = :email");
            $stmtCheck->execute(['username' => $username, 'email' => $email]);
            
            if ($stmtCheck->fetch()) {
                $error = "Tên đăng nhập hoặc Email đã tồn tại trong hệ thống!";
            } else {
                // Thêm tài khoản mới vào CSDL (Mặc định vai trò là 'Employee' hoặc 'User')
                $stmtInsert = $pdo->prepare("
                    INSERT INTO Users (username, password, full_name, email, role) 
                    VALUES (:username, :password, :full_name, :email, 'Employee')
                ");
                $stmtInsert->execute([
                    'username'  => $username,
                    'password'  => $password, // Lưu ý: Nên dùng password_hash() nếu dự án yêu cầu mã hóa
                    'full_name' => $full_name,
                    'email'     => $email
                ]);

                $success = "Đăng ký thành công! Đang chuyển hướng sang trang đăng nhập...";
                header("refresh:2;url=login.php");
            }
        } catch (PDOException $e) {
            $error = "Lỗi hệ thống: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - XYZ Meetings</title>
    <!-- Nhúng Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #131314;
            color: #e3e3e3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .register-card {
            background-color: #1e1f20;
            border: 1px solid #3c4043;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            padding: 36px 40px;
            width: 100%;
            max-width: 440px;
        }
        /* Ô nhập dữ liệu */
        .form-control {
            background-color: #131314 !important;
            border: 1px solid #3c4043 !important;
            color: #ffffff !important; /* Màu chữ khi người dùng gõ */
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

<div class="register-card">
    <div class="text-center mb-4">
        <h2 style="color: #8ab4f8; font-weight: 700; font-size: 1.75rem;">ĐĂNG KÝ TÀI KHOẢN</h2>
        <p class="text-sub mb-0">Tạo tài khoản mới để đặt phòng họp</p>
    </div>

    <!-- Hiển thị thông báo Lỗi hoặc Thành công -->
    <?php if ($error): ?>
        <div class="alert p-2 text-center mb-3" style="background-color: #3c1e1e; border: 1px solid #f28b82; color: #f28b82; font-size: 0.88rem;" role="alert">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert p-2 text-center mb-3" style="background-color: #1e3a29; border: 1px solid #81c995; color: #81c995; font-size: 0.88rem;" role="alert">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- Form Đăng ký -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập (*)</label>
            <input type="text" class="form-control py-2" id="username" name="username" required placeholder="Nhập tên đăng nhập...">
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu (*)</label>
            <input type="password" class="form-control py-2" id="password" name="password" required placeholder="Nhập mật khẩu...">
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Họ và tên (*)</label>
            <input type="text" class="form-control py-2" id="full_name" name="full_name" required placeholder="Nhập họ và tên đầy đủ...">
        </div>

        <div class="mb-4">
            <label for="email" class="form-label">Địa chỉ Email (*)</label>
            <input type="email" class="form-control py-2" id="email" name="email" required placeholder="Nhập email...">
        </div>
        
        <button type="submit" class="btn w-100 py-2 mb-3 fw-bold" style="background-color: #8ab4f8; color: #131314; border: none; border-radius: 8px;">
            Đăng ký ngay
        </button>
    </form>

    <!-- Nút Đăng nhập -->
    <div class="text-center mt-3 pt-3" style="border-top: 1px solid #3c4043;">
        <span class="text-sub d-block mb-2">Đã có tài khoản?</span> 
        <a href="login.php" class="btn btn-outline-light w-100 py-2" style="border-color: #5f6368; color: #e3e3e3; border-radius: 8px; font-weight: 500;">
            Đăng nhập ngay
        </a>
    </div>
</div>

</body>
</html>
