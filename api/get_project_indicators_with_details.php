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
    $strategyId = isset($_GET['strategyId']) ? $_GET['strategyId'] : '';
    $mainProjectId = isset($_GET['mainProjectId']) ? $_GET['mainProjectId'] : '';
    
    if ($projectId <= 0) {
        throw new Exception('Invalid project ID');
    }
    
    // Build the WHERE clause for filtering indicators
    $sql = "SELECT 
                i.IndicatorID,
                i.IndicatorName,
                i.Unit,
                i.Description,
                i.StrategyID,
                i.MainProjectID,
                i.Year,
                s.StrategyName,
                mp.MainProjectName,
                MAX(pi.ID) as ProjectIndicatorID,
                MAX(pi.Value) as Value,
                GROUP_CONCAT(pid.DetailText ORDER BY pid.DetailID SEPARATOR '|||') as Details
            FROM indicators i 
            LEFT JOIN strategies s ON i.StrategyID = s.StrategyID 
            LEFT JOIN mainprojects mp ON i.MainProjectID = mp.MainProjectID
            LEFT JOIN project_indicators pi ON (i.IndicatorID = pi.IndicatorID AND pi.ProjectID = ?)
            LEFT JOIN project_indicator_details pid ON pi.ID = pid.ProjectIndicatorID
            WHERE i.Year = ? AND i.IsActive = 1";
    
    $params = [$projectId, $year];
    $types = "ii";
    
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
    
    $sql .= " GROUP BY i.IndicatorID ORDER BY i.IndicatorID DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $indicators = [];
    while ($row = $result->fetch_assoc()) {
        $details = [];
        if (!empty($row['Details'])) {
            $details = explode('|||', $row['Details']);
        }
        
        $indicators[] = [
            'ProjectIndicatorID' => $row['ProjectIndicatorID'], // Will be null if no existing data
            'IndicatorID' => $row['IndicatorID'],
            'IndicatorName' => $row['IndicatorName'],
            'Unit' => $row['Unit'],
            'Description' => $row['Description'],
            'StrategyID' => $row['StrategyID'],
            'MainProjectID' => $row['MainProjectID'],
            'Year' => $row['Year'],
            'StrategyName' => $row['StrategyName'],
            'MainProjectName' => $row['MainProjectName'],
            'Value' => $row['Value'], // Will be null if no existing data
            'Details' => $details // Will be empty array if no existing data
        ];
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
