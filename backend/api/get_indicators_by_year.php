<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $year = isset($_GET['year']) ? intval($_GET['year']) : 0;
    
    if ($year <= 0) {
        throw new Exception('Invalid year');
    }
    
    $sql = "SELECT IndicatorID, IndicatorName, Unit, Description FROM indicators 
            WHERE Year = ? AND IsActive = 1 
            ORDER BY IndicatorName";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $indicators = [];
    while ($row = $result->fetch_assoc()) {
        $indicators[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $indicators
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
