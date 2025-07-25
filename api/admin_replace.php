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
$findText = $_POST['findText'] ?? '';
$replaceText = $_POST['replaceText'] ?? '';
$ids = $_POST['ids'] ?? [];

// ตรวจสอบข้อมูลที่จำเป็น
if (empty($table) || empty($field) || empty($findText) || !is_array($ids) || empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// กำหนดตารางและฟิลด์ที่อนุญาต (เหมือนกับ admin_search.php)
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
    $conn->begin_transaction();
    
    $primaryKey = $tableConfig['primary_key'];
    $affectedRows = 0;
    
    // สร้าง placeholders สำหรับ IN clause
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // อัพเดทข้อมูล - ใช้ REPLACE function เพื่อแทนที่เฉพาะส่วนที่ต้องการ
    $sql = "UPDATE `$table` SET `$field` = REPLACE(`$field`, ?, ?) WHERE `$primaryKey` IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียม query: ' . $conn->error);
    }
    
    // เตรียม parameters
    $params = [$findText, $replaceText];
    $params = array_merge($params, $ids);
    
    // สร้าง types string
    $types = 'ss' . str_repeat('i', count($ids)); // 2 strings + integers for IDs
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    if ($stmt->error) {
        throw new Exception('เกิดข้อผิดพลาดในการอัพเดท: ' . $stmt->error);
    }
    
    $affectedRows = $stmt->affected_rows;
    
    // Log การเปลี่ยนแปลง (สามารถเพิ่มระบบ log ได้)
    $logData = [
        'table' => $table,
        'field' => $field,
        'find_text' => $findText,
        'replace_text' => $replaceText,
        'affected_ids' => $ids,
        'affected_rows' => $affectedRows,
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['username'] ?? 'admin' // ปรับตามระบบของคุณ
    ];
    
    // สามารถบันทึก log ลงฐานข้อมูลหรือไฟล์ได้ที่นี่
    // insertChangeLog($conn, $logData);
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
        'affected_rows' => $affectedRows,
        'details' => [
            'table' => $table,
            'field' => $field,
            'find_text' => $findText,
            'replace_text' => $replaceText,
            'ids_count' => count($ids)
        ]
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();

// ฟังก์ชันสำหรับบันทึก log (ตัวอย่าง)
function insertChangeLog($conn, $logData) {
    try {
        $sql = "INSERT INTO admin_change_log (table_name, field_name, find_text, replace_text, affected_ids, affected_rows, created_at, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $affectedIdsJson = json_encode($logData['affected_ids']);
            $stmt->bind_param('sssssiis', 
                $logData['table'],
                $logData['field'],
                $logData['find_text'],
                $logData['replace_text'],
                $affectedIdsJson,
                $logData['affected_rows'],
                $logData['timestamp'],
                $logData['user']
            );
            $stmt->execute();
        }
    } catch (Exception $e) {
        // Log error but don't fail the main operation
        error_log('Failed to insert change log: ' . $e->getMessage());
    }
}
?>
