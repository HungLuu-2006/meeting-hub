<?php
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Hủy hoàn toàn session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Chuyển hướng trực tiếp về trang web chính (Trang chủ index.php)
header("Location: index.php");
exit();
?>
