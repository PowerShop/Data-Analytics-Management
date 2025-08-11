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
    
    // เพิ่ม filter อื่นๆ
    if (!empty($input['province'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Province = ?)";
        $params[] = $input['province'];
        $types .= 's';
    }
    
    if (!empty($input['agency'])) {
        $where_conditions[] = "p.AgencyName = ?";
        $params[] = $input['agency'];
        $types .= 's';
    }
    
    // สำหรับ district, subdistrict, village ใช้ JOIN กับ projectvillages
    if (!empty($input['district'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.District = ?)";
        $params[] = $input['district'];
        $types .= 's';
    }
    
    if (!empty($input['subdistrict'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Subdistrict = ?)";
        $params[] = $input['subdistrict'];
        $types .= 's';
    }
    
    if (!empty($input['village'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND (pv.VillageName = ? OR pv.Community = ?))";
        $params[] = $input['village'];
        $params[] = $input['village'];
        $types .= 'ss';
    }
    
    // สำหรับ target_group ใช้ JOIN กับ projecttargetcounts และ targetgroups
    if (!empty($input['target_group'])) {
        $where_conditions[] = "EXISTS (SELECT 1 FROM projecttargetcounts ptc JOIN targetgroups tg ON ptc.GroupID = tg.GroupID WHERE ptc.ProjectID = p.ProjectID AND tg.GroupID = ?)";
        $params[] = $input['target_group'];
        $types .= 'i';
    }
    
    // สำหรับ teacher/ResponsiblePerson
    if (!empty($input['teacher'])) {
        $where_conditions[] = "p.ResponsiblePerson LIKE ?";
        $params[] = '%' . $input['teacher'] . '%';
        $types .= 's';
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
    $sroi_where = $where_clause;
    if (!empty($sroi_where)) {
        $sroi_where .= " AND ps.SROIResult IS NOT NULL AND ps.SROIResult > 0";
    } else {
        $sroi_where = "WHERE ps.SROIResult IS NOT NULL AND ps.SROIResult > 0";
    }
    
    $query = "SELECT AVG(ps.SROIResult) as avg_sroi 
              FROM projects p 
              LEFT JOIN projectsroi ps ON p.ProjectID = ps.ProjectID 
              $sroi_where";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $avg_sroi = $result->fetch_assoc()['avg_sroi'];
    
    // Get total indicators
    $query = "SELECT COUNT(DISTINCT pi.IndicatorID) as total_indicators 
              FROM projects p 
              LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID 
              $where_clause";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_indicators = $result->fetch_assoc()['total_indicators'];
    
    // Get total locations (unique provinces)
    $location_where = $where_clause;
    if (!empty($location_where)) {
        $location_where .= " AND p.Province IS NOT NULL AND p.Province != ''";
    } else {
        $location_where = "WHERE p.Province IS NOT NULL AND p.Province != ''";
    }
    
    $query = "SELECT COUNT(DISTINCT p.Province) as total_locations 
              FROM projects p 
              $location_where";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_locations = $result->fetch_assoc()['total_locations'];
    
    // Get total products (unique products)
    $products_where = $where_clause;
    if (!empty($products_where)) {
        $products_where .= " AND pp.ProductName IS NOT NULL AND pp.ProductName != ''";
    } else {
        $products_where = "WHERE pp.ProductName IS NOT NULL AND pp.ProductName != ''";
    }
    
    $query = "SELECT COUNT(DISTINCT pp.ProductName) as total_products 
              FROM projects p 
              LEFT JOIN projectproducts pp ON p.ProjectID = pp.ProjectID 
              $products_where";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_products = $result->fetch_assoc()['total_products'];
    
    // Get total schools (unique schools)
    $schools_where = $where_clause;
    if (!empty($schools_where)) {
        $schools_where .= " AND ps.SchoolName IS NOT NULL AND ps.SchoolName != ''";
    } else {
        $schools_where = "WHERE ps.SchoolName IS NOT NULL AND ps.SchoolName != ''";
    }
    
    $query = "SELECT COUNT(DISTINCT ps.SchoolName) as total_schools 
              FROM projects p 
              LEFT JOIN projectschools ps ON p.ProjectID = ps.ProjectID 
              $schools_where";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_schools = $result->fetch_assoc()['total_schools'];
    
    // Get total target groups (unique target groups)
    $target_groups_where = $where_clause;
    if (!empty($target_groups_where)) {
        $target_groups_where .= " AND tg.GroupName IS NOT NULL AND tg.GroupName != ''";
    } else {
        $target_groups_where = "WHERE tg.GroupName IS NOT NULL AND tg.GroupName != ''";
    }
    
    $query = "SELECT COUNT(DISTINCT tg.GroupName) as total_target_groups 
              FROM projects p 
              LEFT JOIN projecttargetcounts ptc ON p.ProjectID = ptc.ProjectID 
              LEFT JOIN targetgroups tg ON ptc.GroupID = tg.GroupID 
              $target_groups_where";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_target_groups = $result->fetch_assoc()['total_target_groups'];
    
    // Get total agencies (unique agencies)
    $agencies_where = $where_clause;
    if (!empty($agencies_where)) {
        $agencies_where .= " AND p.AgencyName IS NOT NULL AND p.AgencyName != ''";
    } else {
        $agencies_where = "WHERE p.AgencyName IS NOT NULL AND p.AgencyName != ''";
    }
    
    $query = "SELECT COUNT(DISTINCT p.AgencyName) as total_agencies 
              FROM projects p 
              $agencies_where";
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_agencies = $result->fetch_assoc()['total_agencies'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_projects' => intval($total_projects),
            'total_budget' => floatval($total_budget),
            'total_targets' => intval($total_targets),
            'avg_sroi' => $avg_sroi ? floatval($avg_sroi) : null,
            'total_indicators' => intval($total_indicators),
            'total_locations' => intval($total_locations),
            'total_products' => intval($total_products),
            'total_schools' => intval($total_schools),
            'total_target_groups' => intval($total_target_groups),
            'total_agencies' => intval($total_agencies)
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
