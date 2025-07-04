<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y') + 543;
    $strategyId = isset($_GET['strategyId']) ? $_GET['strategyId'] : '';
    $mainProjectId = isset($_GET['mainProjectId']) ? $_GET['mainProjectId'] : '';
    
    $sql = "SELECT i.*, s.StrategyName, m.MainProjectName 
            FROM indicators i 
            LEFT JOIN strategies s ON i.StrategyID = s.StrategyID 
            LEFT JOIN mainprojects m ON i.MainProjectID = m.MainProjectID 
            WHERE i.Year = ?";
    
    $params = [$year];
    $types = "i";
    
    if (!empty($strategyId)) {
        $sql .= " AND i.StrategyID = ?";
        $params[] = $strategyId;
        $types .= "i";
    }
    
    if (!empty($mainProjectId)) {
        $sql .= " AND i.MainProjectID = ?";
        $params[] = $mainProjectId;
        $types .= "i";
    }
    
    $sql .= " ORDER BY i.IndicatorID DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
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
