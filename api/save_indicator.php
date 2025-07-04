<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $indicatorId = isset($_POST['indicatorId']) ? intval($_POST['indicatorId']) : 0;
    $indicatorName = isset($_POST['indicatorName']) ? trim($_POST['indicatorName']) : '';
    $unit = isset($_POST['unit']) ? trim($_POST['unit']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
    $strategyId = isset($_POST['strategyId']) ? intval($_POST['strategyId']) : null;
    $mainProjectId = isset($_POST['mainProjectId']) ? intval($_POST['mainProjectId']) : null;
    $isActive = isset($_POST['isActive']) ? intval($_POST['isActive']) : 1;
    
    // Validation
    if (empty($indicatorName)) {
        throw new Exception('ชื่อตัวชี้วัดไม่สามารถเป็นค่าว่างได้');
    }
    
    if ($year <= 0) {
        throw new Exception('ปีไม่ถูกต้อง');
    }
    
    if (empty($strategyId)) {
        throw new Exception('กรุณาเลือกยุทธศาสตร์');
    }
    
    if (empty($mainProjectId)) {
        throw new Exception('กรุณาเลือกโครงการหลัก');
    }
    
    // Convert 0 to null for foreign keys
    $strategyId = $strategyId > 0 ? $strategyId : null;
    $mainProjectId = $mainProjectId > 0 ? $mainProjectId : null;
    
    if ($indicatorId > 0) {
        // Update existing indicator
        $sql = "UPDATE indicators SET 
                IndicatorName = ?, 
                Unit = ?, 
                Description = ?, 
                Year = ?, 
                StrategyID = ?, 
                MainProjectID = ?, 
                IsActive = ? 
                WHERE IndicatorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiiii", $indicatorName, $unit, $description, $year, $strategyId, $mainProjectId, $isActive, $indicatorId);
    } else {
        // Insert new indicator
        $sql = "INSERT INTO indicators (IndicatorName, Unit, Description, Year, StrategyID, MainProjectID, IsActive) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiii", $indicatorName, $unit, $description, $year, $strategyId, $mainProjectId, $isActive);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $indicatorId > 0 ? 'อัพเดทข้อมูลสำเร็จ' : 'เพิ่มข้อมูลสำเร็จ'
        ]);
    } else {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
