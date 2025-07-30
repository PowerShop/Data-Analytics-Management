<?php
// Try different paths to find db.php
if (file_exists('../db.php')) {
    include '../db.php';
} elseif (file_exists(__DIR__ . '/../db.php')) {
    include __DIR__ . '/../db.php';
} else {
    die(json_encode(['success' => false, 'message' => 'Database connection file not found']));
}

header('Content-Type: application/json; charset=utf-8');

try {
    $projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y') + 543;
    
    // Debug: log the parameters
    error_log("API Debug - project_id: $projectId, year: $year");
    
    if ($projectId <= 0) {
        throw new Exception('Invalid project ID');
    }
    
    // แก้ไข query ให้ดึงตัวชี้วัดทั้งหมดในปีที่กำหนด และ join กับข้อมูลที่บันทึกไว้
    $sql = "SELECT 
                i.IndicatorID,
                i.IndicatorName,
                i.Unit,
                i.Description,
                i.Year,
                COALESCE(pi.ID, NULL) as ProjectIndicatorID,
                COALESCE(pi.Value, NULL) as Value,
                GROUP_CONCAT(DISTINCT pid.DetailText ORDER BY pid.DetailID SEPARATOR '|||') as Details
            FROM indicators i 
            LEFT JOIN project_indicators pi ON (i.IndicatorID = pi.IndicatorID AND pi.ProjectID = ?)
            LEFT JOIN project_indicator_details pid ON pi.ID = pid.ProjectIndicatorID
            WHERE i.Year = ? AND (i.IsActive IS NULL OR i.IsActive = 1)
            GROUP BY i.IndicatorID, i.IndicatorName, i.Unit, i.Description, i.Year, pi.ID, pi.Value
            ORDER BY 
                CASE WHEN pi.Value IS NOT NULL AND pi.Value != '' THEN 0 ELSE 1 END,
                i.IndicatorID ASC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("ii", $projectId, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $indicators = [];
    while ($row = $result->fetch_assoc()) {
        $details = [];
        if (!empty($row['Details'])) {
            $details = explode('|||', $row['Details']);
            // ลบค่าว่างออก
            $details = array_filter($details, function($detail) {
                return !empty(trim($detail));
            });
        }
        
        $indicators[] = [
            'ProjectIndicatorID' => $row['ProjectIndicatorID'], // จะเป็น null ถ้าไม่มีข้อมูลเดิม
            'IndicatorID' => $row['IndicatorID'],
            'IndicatorName' => $row['IndicatorName'],
            'Unit' => $row['Unit'],
            'Description' => $row['Description'],
            'Year' => $row['Year'],
            'Value' => $row['Value'], // จะเป็น null ถ้าไม่มีข้อมูลเดิม
            'Details' => $details // จะเป็น array ว่างถ้าไม่มีข้อมูลเดิม
        ];
    }
    
    // Debug: log the result count
    error_log("API Debug - Found " . count($indicators) . " indicators");
    
    echo json_encode([
        'success' => true,
        'data' => $indicators,
        'debug' => [
            'project_id' => $projectId,
            'year' => $year,
            'count' => count($indicators)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
