<?php
// Nhúng kết nối cơ sở dữ liệu
require_once 'config/database.php';

// Nhúng Header và Sidebar
include 'includes/header.php';

// ==========================================
// XỬ LÝ LOGIC TRUY VẤN DỮ LIỆU TỪ POSTGRESQL
// ==========================================
try {
    // Truy vấn lấy danh sách phòng và tự động kiểm tra trạng thái (Bận/Trống)
    $stmt = $pdo->query("
        SELECT 
            r.room_id, 
            r.room_name, 
            r.capacity, 
            r.equipment,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM Meetings m 
                    WHERE m.room_id = r.room_id 
                      AND m.start_time <= CURRENT_TIMESTAMP 
                      AND m.end_time >= CURRENT_TIMESTAMP 
                      AND m.status != 'Cancelled'
                ) THEN 'Bận'
                ELSE 'Trống'
            END as current_status
        FROM Rooms r
        ORDER BY r.room_id ASC
    ");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div class='alert alert-danger m-3'>Lỗi truy vấn: " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<!-- ==========================================
// GIAO DIỆN HIỂN THỊ (HTML)
// ========================================== -->
<div class="main-content">
    <h4 class="mb-4 text-uppercase" style="color: #a0a5b1;">DANH SÁCH PHÒNG HỌP</h4>
    
    <div class="row g-4">
        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card text-center p-4 h-100" style="background-color: #22252e; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <!-- Biểu tượng phòng (Dùng Emoji cho nhẹ và giống thiết kế) -->
                        <div class="mb-3" style="font-size: 3.5rem;">👥</div>
                        
                        <!-- Tên phòng -->
                        <h4 class="fw-bold mb-2" style="color: #3498db;"><?= htmlspecialchars($room['room_name']) ?></h4>
                        
                        <!-- Sức chứa -->
                        <p class="mb-4" style="color: #a0a5b1; font-size: 0.95rem;">
                            Sức chứa: <?= htmlspecialchars($room['capacity']) ?> người
                        </p>
                        
                        <!-- Trạng thái phòng -->
                        <div>
                            <?php if ($room['current_status'] === 'Trống'): ?>
                                <span style="display: inline-block; border: 1px solid #2ecc71; color: #2ecc71; padding: 6px 20px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                    ĐANG TRỐNG
                                </span>
                            <?php else: ?>
                                <span style="display: inline-block; border: 1px solid #e74c3c; color: #e74c3c; padding: 6px 20px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                    ĐANG BẬN
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p style="color: #a0a5b1;">Chưa có phòng họp nào trên hệ thống.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
