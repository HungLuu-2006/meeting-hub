<?php
// Bắt đầu Session nếu chưa có để kiểm tra trạng thái đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$displayName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Người dùng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Hub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #131314;
            color: #e3e3e3;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
        }
        /* Thanh Navigation Ngang phong cách Gemini Dark Mode */
        .navbar-gemini {
            background-color: #1e1f20;
            border-bottom: 1px solid #3c4043;
            padding: 0.8rem 2rem;
        }
        .navbar-gemini .navbar-brand {
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.2rem;
        }
        .navbar-gemini .nav-link {
            color: #9aa0a6;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .navbar-gemini .nav-link:hover, 
        .navbar-gemini .nav-link.active {
            color: #ffffff;
            background-color: #2d2f31;
        }
        .navbar-gemini .logout-link {
            color: #f28b82 !important;
            cursor: pointer;
        }
        .navbar-gemini .logout-link:hover {
            background-color: #3c2f2f !important;
        }
        /* Badge hiển thị tên người dùng */
        .user-badge {
            background-color: #2d2f31;
            border: 1px solid #3c4043;
            color: #8ab4f8 !important;
            font-weight: 600;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        /* Khung nội dung chính */
        .main-content {
            padding: 2.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        /* Tùy chỉnh màu card và bảng cho hợp Dark Mode */
        .card, .modal-content, .table {
            background-color: #1e1f20 !important;
            color: #e3e3e3 !important;
            border-color: #3c4043 !important;
        }
        .table-dark {
            --bs-table-bg: #1e1f20;
            --bs-table-border-color: #3c4043;
        }
        .form-control, .form-select {
            background-color: #131314 !important;
            color: #e3e3e3 !important;
            border-color: #3c4043 !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #131314 !important;
            color: #e3e3e3 !important;
            border-color: #8ab4f8 !important;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-gemini sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">MEETING HUB</a>
            <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Lên lịch họp</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">Danh sách phòng</a>
                    </li>

                    <?php if ($isLoggedIn): ?>
                        <!-- HIỂN THỊ KHI ĐÃ ĐĂNG NHẬP -->
                        <li class="nav-item ms-lg-3">
                            <span class="user-badge d-inline-block">
                                👤 <?= htmlspecialchars($displayName) ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <!-- Nút kích hoạt Modal xác nhận Đăng xuất -->
                            <a class="nav-link logout-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Đăng xuất</a>
                        </li>
                    <?php else: ?>
                        <!-- HIỂN THỊ KHI CHƯA ĐĂNG NHẬP (KHÁCH) -->
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-light btn-sm px-3" href="login.php" style="border-color: #5f6368;">
                                Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm px-3 fw-bold" href="register.php" style="background-color: #8ab4f8; color: #131314;">
                                Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal Xác Nhận Đăng Xuất (Dark Mode) -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #1e1f20; color: #e3e3e3; border: 1px solid #3c4043; border-radius: 12px;">
          
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold" id="logoutModalLabel" style="color: #ffffff;">Xác nhận đăng xuất</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body py-3" style="color: #bdc1c6; font-size: 0.95rem;">
            Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?
          </div>
          
          <div class="modal-footer border-top-0 pt-0">
            <!-- Tùy chọn 1: Hủy -->
            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="background-color: #3c4043; border: none; color: #e3e3e3; border-radius: 6px;">
                Hủy
            </button>

            <!-- Tùy chọn 2: Đăng xuất -->
            <a href="logout.php" class="btn btn-danger px-3 fw-bold" style="background-color: #ea4335; border: none; border-radius: 6px;">
                Đăng xuất
            </a>
          </div>

        </div>
      </div>
    </div>
