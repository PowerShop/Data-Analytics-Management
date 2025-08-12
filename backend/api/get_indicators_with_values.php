<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $year = isset($_GET['year']) ? intval($_GET['year']) : 0;
    $project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
    
    if ($year <= 0) {
        throw new Exception('Invalid year');
    }
    
    if ($project_id <= 0) {
        throw new Exception('Invalid project ID');
    }
    
    // ดึงตัวชี้วัดทั้งหมดของปีนั้น พร้อมกับค่าที่มีอยู่ (ถ้ามี)
    $sql = "
        SELECT 
            i.IndicatorID, 
            i.IndicatorName, 
            i.Unit, 
            i.Description,
            pi.Value,
            pi.Note
        FROM indicators i 
        LEFT JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID AND pi.ProjectID = ?
        WHERE i.Year = ? AND i.IsActive = 1 
        ORDER BY i.IndicatorName, pi.Value
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $project_id, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $indicators = [];
    while ($row = $result->fetch_assoc()) {
        $indicator_id = $row['IndicatorID'];
        
        // ถ้ายังไม่มีตัวชี้วัดนี้ในผลลัพธ์ ให้สร้างใหม่
        if (!isset($indicators[$indicator_id])) {
            $indicators[$indicator_id] = [
                'IndicatorID' => $row['IndicatorID'],
                'IndicatorName' => $row['IndicatorName'],
                'Unit' => $row['Unit'],
                'Description' => $row['Description'],
                'values' => []
            ];
        }
        
        // ถ้ามีค่าตัวชี้วัด ให้เพิ่มเข้าไป
        if ($row['Value'] !== null) {
            $indicators[$indicator_id]['values'][] = [
                'Value' => $row['Value'],
                'Note' => $row['Note']
            ];
        }
    }
    
    // แปลงจาก associative array เป็น indexed array
    $result_indicators = array_values($indicators);
    
    echo json_encode([
        'success' => true,
        'data' => $result_indicators
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
