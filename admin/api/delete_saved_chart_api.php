<?php
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
include '../database/db.php';

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ตรวจสอบ method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $chartId = intval($_POST['chart_id'] ?? 0);
    
    if ($chartId <= 0) {
        throw new Exception('ไม่พบข้อมูลกราฟที่ต้องการลบ');
    }
    
    // ตรวจสอบว่ากราฟมีอยู่จริง
    $checkStmt = $conn->prepare("SELECT ChartID FROM saved_charts WHERE ChartID = ? AND IsActive = 1");
    $checkStmt->bind_param("i", $chartId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('ไม่พบกราฟที่ต้องการลบ');
    }
    
    // อัพเดทสถานะเป็น inactive แทนการลบจริง (soft delete)
    $stmt = $conn->prepare("UPDATE saved_charts SET IsActive = 0, UpdatedAt = CURRENT_TIMESTAMP WHERE ChartID = ?");
    $stmt->bind_param("i", $chartId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'ลบกราฟเรียบร้อยแล้ว'
        ]);
    } else {
        throw new Exception('เกิดข้อผิดพลาดในการลบกราฟ');
    }
    
    $stmt->close();
    $checkStmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
