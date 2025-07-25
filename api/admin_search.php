<?php
header('Content-Type: application/json; charset=utf-8');
include '../db.php';

// ตรวจสอบสิทธิ์ admin (สามารถปรับแต่งตามระบบของคุณ)
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
//     echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$table = $_POST['table'] ?? '';
$field = $_POST['field'] ?? '';
$search = $_POST['search'] ?? '';

// ตรวจสอบข้อมูลที่จำเป็น
if (empty($table) || empty($field) || empty($search)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// กำหนดตารางและฟิลด์ที่อนุญาต
$allowedTables = [
    'projects' => [
        'primary_key' => 'ProjectID',
        'allowed_fields' => ['ProjectCode', 'ProjectName', 'ProjectYear', 'AgencyName', 'ResponsiblePerson', 'Province', 'TargetArea']
    ],
    'mainprojects' => [
        'primary_key' => 'MainProjectID',
        'allowed_fields' => ['MainProjectName', 'MainProjectCode', 'MainProjectDescription']
    ],
    'strategies' => [
        'primary_key' => 'StrategyID',
        'allowed_fields' => ['StrategyName']
    ],
    'indicators' => [
        'primary_key' => 'IndicatorID',
        'allowed_fields' => ['IndicatorName', 'Unit', 'Description', 'Year']
    ],
    'projectproducts' => [
        'primary_key' => 'ID',
        'allowed_fields' => ['ProductName', 'ProductType', 'Description', 'StandardNumber']
    ],
    'projectschools' => [
        'primary_key' => 'ID',
        'allowed_fields' => ['SchoolName']
    ],
    'targetgroups' => [
        'primary_key' => 'GroupID',
        'allowed_fields' => ['GroupName']
    ],
    'projectvillages' => [
        'primary_key' => 'ID',
        'allowed_fields' => ['VillageName', 'Moo', 'Subdistrict', 'District', 'Province', 'Community']
    ],
    'budgetitems' => [
        'primary_key' => 'BudgetID',
        'allowed_fields' => ['BudgetType', 'Remark']
    ],
    'projectnetworks' => [
        'primary_key' => 'ID',
        'allowed_fields' => ['NetworkName']
    ],
    'projectenterprises' => [
        'primary_key' => 'ID',
        'allowed_fields' => ['EnterpriseName', 'EnterpriseType']
    ]
];

// ตรวจสอบตารางที่อนุญาต
if (!isset($allowedTables[$table])) {
    echo json_encode(['success' => false, 'message' => 'ตารางไม่ถูกต้อง']);
    exit;
}

// ตรวจสอบฟิลด์ที่อนุญาต
$tableConfig = $allowedTables[$table];
if (!in_array($field, $tableConfig['allowed_fields'])) {
    echo json_encode(['success' => false, 'message' => 'ฟิลด์ไม่ถูกต้อง']);
    exit;
}

try {
    // สร้าง query สำหรับค้นหา
    $primaryKey = $tableConfig['primary_key'];
    $selectFields = array_merge([$primaryKey], $tableConfig['allowed_fields']);
    $selectFieldsStr = implode(', ', $selectFields);
    
    $sql = "SELECT $selectFieldsStr FROM `$table` WHERE `$field` LIKE ? ORDER BY `$primaryKey`";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียม query: ' . $conn->error);
    }
    
    $searchParam = "%$search%";
    $stmt->bind_param('s', $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    // นับจำนวนทั้งหมด
    $countSql = "SELECT COUNT(*) as total FROM `$table` WHERE `$field` LIKE ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param('s', $searchParam);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = $countResult->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'columns' => $selectFields,
        'total' => $total,
        'table' => $table,
        'field' => $field,
        'search' => $search
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
