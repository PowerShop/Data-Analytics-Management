<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        throw new Exception('Invalid indicator ID');
    }
    
    // Check if indicator is being used in any projects
    $checkSql = "SELECT COUNT(*) as count FROM project_indicators WHERE IndicatorID = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkRow = $checkResult->fetch_assoc();
    
    if ($checkRow['count'] > 0) {
        // Don't delete, just deactivate
        $sql = "UPDATE indicators SET IsActive = 0 WHERE IndicatorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'ตัวชี้วัดนี้ถูกใช้งานในโครงการแล้ว จึงปิดการใช้งานแทนการลบ'
            ]);
        } else {
            throw new Exception('ไม่สามารถปิดการใช้งานตัวชี้วัดได้');
        }
    } else {
        // Safe to delete
        $sql = "DELETE FROM indicators WHERE IndicatorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'ลบตัวชี้วัดสำเร็จ'
            ]);
        } else {
            throw new Exception('ไม่สามารถลบตัวชี้วัดได้');
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
