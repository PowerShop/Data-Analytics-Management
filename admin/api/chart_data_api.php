<?php
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include database connection with error handling
$db_files = [
    '../database/db.php',
    './db.php',
    './database/db.php',
    '../db.php'
];

$db_connected = false;
foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        include $db_file;
        $db_connected = true;
        break;
    }
}

if (!$db_connected) {
    die('Error: Database connection file not found.');
}

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    // รับข้อมูลจาก POST แทน GET
    $x_axis = $_POST['x_axis'] ?? '';
    $y_axis = $_POST['y_axis'] ?? '';
    
    if (empty($x_axis) || empty($y_axis)) {
        throw new Exception('Missing required parameters: x_axis=' . $x_axis . ', y_axis=' . $y_axis);
    }
    
    // Build WHERE clause based on filters
    $where_conditions = [];
    $params = [];
    $types = '';
    
    // Project year filters
    if (!empty($_POST['yearStart']) || !empty($_POST['project_year_start'])) {
        $year_start = !empty($_POST['yearStart']) ? $_POST['yearStart'] : $_POST['project_year_start'];
        $where_conditions[] = "p.ProjectYear >= ?";
        $params[] = $year_start;
        $types .= 'i';
    }
    
    if (!empty($_POST['yearEnd']) || !empty($_POST['project_year_end'])) {
        $year_end = !empty($_POST['yearEnd']) ? $_POST['yearEnd'] : $_POST['project_year_end'];
        $where_conditions[] = "p.ProjectYear <= ?";
        $params[] = $year_end;
        $types .= 'i';
    }
    
    // Geographic filters
    if (!empty($_POST['province'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Province = ?)";
        $params[] = $_POST['province'];
        $types .= 's';
    }
    
    if (!empty($_POST['district'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.District = ?)";
        $params[] = $_POST['district'];
        $types .= 's';
    }
    
    if (!empty($_POST['subdistrict'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Subdistrict = ?)";
        $params[] = $_POST['subdistrict'];
        $types .= 's';
    }
    
    if (!empty($_POST['village'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND (pv.VillageName = ? OR pv.Community = ?))";
        $params[] = $_POST['village'];
        $params[] = $_POST['village'];
        $types .= 'ss';
    }
    
    // Project type filters
    if (!empty($_POST['strategyFilter']) || !empty($_POST['strategy'])) {
        $strategy = !empty($_POST['strategyFilter']) ? $_POST['strategyFilter'] : $_POST['strategy'];
        $where_conditions[] = "p.StrategyID = ?";
        $params[] = $strategy;
        $types .= 'i';
    }
    
    if (!empty($_POST['mainProjectFilter']) || !empty($_POST['main_project'])) {
        $main_project = !empty($_POST['mainProjectFilter']) ? $_POST['mainProjectFilter'] : $_POST['main_project'];
        $where_conditions[] = "p.MainProjectID = ?";
        $params[] = $main_project;
        $types .= 'i';
    }
    
    // Agency filter
    if (!empty($_POST['agency'])) {
        $where_conditions[] = "p.AgencyName LIKE ?";
        $params[] = '%' . $_POST['agency'] . '%';
        $types .= 's';
    }
    
    // Target group filter
    if (!empty($_POST['targetGroup']) || !empty($_POST['target_group'])) {
        $target_group = !empty($_POST['targetGroup']) ? $_POST['targetGroup'] : $_POST['target_group'];
        $where_conditions[] = "EXISTS (SELECT 1 FROM projecttargetcounts ptc JOIN targetgroups tg ON ptc.GroupID = tg.GroupID WHERE ptc.ProjectID = p.ProjectID AND tg.GroupID = ?)";
        $params[] = $target_group;
        $types .= 'i';
    }
    
    // Teacher/Responsible person filter
    if (!empty($_POST['teacher'])) {
        $where_conditions[] = "p.ResponsiblePerson LIKE ?";
        $params[] = '%' . $_POST['teacher'] . '%';
        $types .= 's';
    }
    
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Build query based on x_axis and y_axis
    $query = buildChartQuery($x_axis, $y_axis, $where_clause);
    
    if ($query === false) {
        throw new Exception('Invalid axis parameters');
    }
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $labels = [];
    $values = [];
    
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['label'];
        $values[] = floatval($row['value']);
    }
    
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'values' => $values
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function buildChartQuery($x_axis, $y_axis, $where_clause) {
    $base_from = "FROM projects p
                  LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                  LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                  LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID
                  LEFT JOIN projecttargetcounts ptc ON p.ProjectID = ptc.ProjectID
                  LEFT JOIN projectsroi ps ON p.ProjectID = ps.ProjectID
                  LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID";
    
    switch ($x_axis) {
        case 'project_year':
            $x_select = "p.ProjectYear as label";
            $group_by = "GROUP BY p.ProjectYear";
            $order_by = "ORDER BY p.ProjectYear";
            break;
            
        case 'strategy':
            $x_select = "COALESCE(s.StrategyName, 'ไม่ระบุ') as label";
            $group_by = "GROUP BY s.StrategyID, s.StrategyName";
            $order_by = "ORDER BY s.StrategyName";
            break;
            
        case 'main_project':
            $x_select = "COALESCE(mp.MainProjectName, 'ไม่ระบุ') as label";
            $group_by = "GROUP BY mp.MainProjectID, mp.MainProjectName";
            $order_by = "ORDER BY mp.MainProjectName";
            break;
            
        case 'agency':
            $x_select = "COALESCE(p.AgencyName, 'ไม่ระบุ') as label";
            $group_by = "GROUP BY p.AgencyName";
            $order_by = "ORDER BY p.AgencyName";
            break;
            
        case 'province':
            $x_select = "COALESCE(p.Province, 'ไม่ระบุ') as label";
            $group_by = "GROUP BY p.Province";
            $order_by = "ORDER BY p.Province";
            break;
            
        case 'target_group':
            // This is more complex, need to join with target groups
            $base_from = "FROM projects p
                          LEFT JOIN projecttargetcounts ptc ON p.ProjectID = ptc.ProjectID
                          LEFT JOIN targetgroups tg ON ptc.GroupID = tg.GroupID
                          LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                          LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                          LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID
                          LEFT JOIN projectsroi ps ON p.ProjectID = ps.ProjectID
                          LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID";
            $x_select = "COALESCE(tg.GroupName, 'ไม่ระบุ') as label";
            $group_by = "GROUP BY tg.GroupID, tg.GroupName";
            $order_by = "ORDER BY tg.GroupName";
            break;
            
        default:
            return false;
    }
    
    switch ($y_axis) {
        case 'project_count':
            $y_select = "COUNT(DISTINCT p.ProjectID) as value";
            break;
            
        case 'budget_sum':
            $y_select = "COALESCE(SUM(DISTINCT b.ApprovedAmount), 0) as value";
            break;
            
        case 'target_count':
            if ($x_axis === 'target_group') {
                $y_select = "COALESCE(SUM(ptc.TargetCount), 0) as value";
            } else {
                $y_select = "COALESCE(SUM(DISTINCT ptc.TargetCount), 0) as value";
            }
            break;
            
        case 'sroi_avg':
            $y_select = "COALESCE(AVG(ps.SROIResult), 0) as value";
            break;
            
        case 'indicator_count':
            $y_select = "COUNT(DISTINCT pi.ID) as value";
            break;
            
        default:
            return false;
    }
    
    return "SELECT $x_select, $y_select $base_from $where_clause $group_by $order_by LIMIT 50";
}
?>
