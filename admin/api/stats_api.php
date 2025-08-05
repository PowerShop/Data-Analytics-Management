<?php
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../database/db.php';

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    // Build WHERE clause based on filters - รองรับทั้ง GET และ POST
    $where_conditions = [];
    $params = [];
    $types = '';
    
    // รับข้อมูลจาก POST หรือ GET
    $input = $_POST ?: $_GET;
    
    if (!empty($input['project_year_start'])) {
        $where_conditions[] = "p.ProjectYear >= ?";
        $params[] = $input['project_year_start'];
        $types .= 'i';
    }
    
    if (!empty($input['project_year_end'])) {
        $where_conditions[] = "p.ProjectYear <= ?";
        $params[] = $input['project_year_end'];
        $types .= 'i';
    }
    
    if (!empty($input['strategy'])) {
        $where_conditions[] = "p.StrategyID = ?";
        $params[] = $input['strategy'];
        $types .= 'i';
    }
    
    if (!empty($input['main_project'])) {
        $where_conditions[] = "p.MainProjectID = ?";
        $params[] = $input['main_project'];
        $types .= 'i';
    }
    
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Get total projects
    $query = "SELECT COUNT(DISTINCT p.ProjectID) as total_projects FROM projects p $where_clause";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_projects = $result->fetch_assoc()['total_projects'];
    
    // Get total budget
    $query = "SELECT COALESCE(SUM(DISTINCT b.ApprovedAmount), 0) as total_budget 
              FROM projects p 
              LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID 
              $where_clause";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_budget = $result->fetch_assoc()['total_budget'];
    
    // Get total targets
    $query = "SELECT COALESCE(SUM(ptc.TargetCount), 0) as total_targets 
              FROM projects p 
              LEFT JOIN projecttargetcounts ptc ON p.ProjectID = ptc.ProjectID 
              $where_clause";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_targets = $result->fetch_assoc()['total_targets'];
    
    // Get average SROI
    $query = "SELECT AVG(ps.SROIResult) as avg_sroi 
              FROM projects p 
              LEFT JOIN projectsroi ps ON p.ProjectID = ps.ProjectID 
              $where_clause 
              AND ps.SROIResult IS NOT NULL AND ps.SROIResult > 0";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $avg_sroi = $result->fetch_assoc()['avg_sroi'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_projects' => intval($total_projects),
            'total_budget' => floatval($total_budget),
            'total_targets' => intval($total_targets),
            'avg_sroi' => $avg_sroi ? floatval($avg_sroi) : null
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
