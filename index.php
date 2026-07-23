<?php
// Nhúng kết nối cơ sở dữ liệu
require_once 'config/database.php';

// Nhúng Header và Sidebar
include 'includes/header.php';

// ==========================================
// XỬ LÝ LOGIC TRUY VẤN DỮ LIỆU TỪ POSTGRESQL
// ==========================================

try {
    // 1. Lấy tổng số cuộc họp (không tính các cuộc họp đã bị hủy)
    $stmtTotal = $pdo->query("SELECT COUNT(*) as total FROM Meetings WHERE status != 'Cancelled'");
    $totalMeetings = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 2. Lấy thông tin cuộc họp sắp diễn ra
    // Dựa trên cột start_time (TIMESTAMP), lấy cuộc họp gần nhất ở tương lai
    $stmtNext = $pdo->query("
        SELECT title, start_time, end_time 
        FROM Meetings 
        WHERE start_time >= CURRENT_TIMESTAMP AND status != 'Cancelled'
        ORDER BY start_time ASC 
        LIMIT 1
    ");
    $nextMeeting = $stmtNext->fetch(PDO::FETCH_ASSOC);

    if ($nextMeeting) {
        $nextTitle = htmlspecialchars($nextMeeting['title']);
        
        // Tách ngày và giờ từ chuỗi TIMESTAMP của PostgreSQL
        $dateObj = new DateTime($nextMeeting['start_time']);
        $dayOfWeek = $dateObj->format('N'); // Trả về từ 1 (Thứ 2) đến 7 (Chủ nhật)
        $dayName = ($dayOfWeek == 7) ? 'Chủ Nhật' : 'Thứ ' . ($dayOfWeek + 1);
        
        $startTime = $dateObj->format('H:i');
        $endTime = (new DateTime($nextMeeting['end_time']))->format('H:i');
        
        $nextTimeStr = "$dayName : $startTime - $endTime";
    } else {
        $nextTitle = "Không có cuộc họp nào";
        $nextTimeStr = "Trống";
    }

    // 3. Tính toán số phòng ĐANG BẬN / TRỐNG ngay lúc này
    // Lấy tổng số phòng
    $stmtTotalRooms = $pdo->query("SELECT COUNT(*) as total_rooms FROM Rooms");
    $totalRooms = $stmtTotalRooms->fetch(PDO::FETCH_ASSOC)['total_rooms'] ?? 0;

    // Số phòng bận: Cuộc họp đang diễn ra (thời gian hiện tại nằm giữa start_time và end_time)
    $stmtBusy = $pdo->query("
        SELECT COUNT(DISTINCT room_id) as busy_count 
        FROM Meetings 
        WHERE start_time <= CURRENT_TIMESTAMP 
          AND end_time >= CURRENT_TIMESTAMP 
          AND status != 'Cancelled'
    ");
    $busyRooms = $stmtBusy->fetch(PDO::FETCH_ASSOC)['busy_count'] ?? 0;

    // Số phòng trống
    $availableRooms = max(0, $totalRooms - $busyRooms);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger m-3'>Lỗi SQL: " . htmlspecialchars($e->getMessage()) . "</div>";
    $totalMeetings = 0; $nextTitle = "Lỗi kết nối"; $nextTimeStr = "-"; $availableRooms = 0; $busyRooms = 0;
}
?>

<!-- ==========================================
// GIAO DIỆN HIỂN THỊ (HTML)
// ========================================== -->
<div class="main-content">
    <h4 class="mb-4 text-uppercase" style="color: #a0a5b1;">TỔNG QUAN CUỘC HỌP</h4>
    
    <div class="row g-4">
        <!-- Thẻ 1: Cuộc họp sắp diễn ra -->
        <div class="col-md-6">
            <div class="p-3 rounded" style="background-color: #3c4043; color: white; height: 100%;">
                <p class="mb-3" style="font-size: 1.1rem;">cuộc họp sắp diễn ra</p>
                <div class="text-center p-4 rounded d-flex flex-column justify-content-center" style="background-color: #1e1f20; min-height: 120px;">
                    <h3 class="fw-bold mb-2"><?= $nextTitle ?></h3>
                    <p class="mb-0 fw-bold" style="font-size: 0.9rem;"><?= $nextTimeStr ?></p>
                </div>
            </div>
        </div>

        <!-- Thẻ 2: Tổng số cuộc họp -->
        <div class="col-md-6">
            <div class="p-3 rounded" style="background-color: #3c4043; color: white; height: 100%;">
                <p class="mb-3" style="font-size: 1.1rem;">tổng số cuộc họp</p>
                <div class="text-center p-4 rounded d-flex align-items-center justify-content-center" style="background-color: #1e1f20; min-height: 120px;">
                    <h2 class="fw-bold mb-0"><?= $totalMeetings ?></h2>
                </div>
            </div>
        </div>

        <!-- Thẻ 3: Số phòng họp trống -->
        <div class="col-md-6">
            <div class="p-3 rounded" style="background-color: #3c4043; color: white; height: 100%;">
                <p class="mb-3" style="font-size: 1.1rem;">số phòng họp trống</p>
                <div class="text-center p-4 rounded d-flex align-items-center justify-content-center" style="background-color: #1e1f20; min-height: 120px;">
                    <h2 class="fw-bold mb-0"><?= $availableRooms ?></h2>
                </div>
            </div>
        </div>

        <!-- Thẻ 4: Số phòng họp bận -->
        <div class="col-md-6">
            <div class="p-3 rounded" style="background-color: #3c4043; color: white; height: 100%;">
                <p class="mb-3" style="font-size: 1.1rem;">số phòng họp bận</p>
                <div class="text-center p-4 rounded d-flex align-items-center justify-content-center" style="background-color: #1e1f20; min-height: 120px;">
                    <h2 class="fw-bold mb-0"><?= $busyRooms ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
