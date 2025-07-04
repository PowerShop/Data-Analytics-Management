<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
    
    if ($projectId <= 0) {
        throw new Exception('Invalid project ID');
    }
    
    $sql = "SELECT pi.IndicatorID, pi.Value, pi.Note 
            FROM project_indicators pi 
            WHERE pi.ProjectID = ? 
            ORDER BY pi.IndicatorID, pi.Value";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectId);
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
