<?php
// เปิด error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

try {
    include '../database/db.php';
    
    // Debug: แสดงข้อมูลที่ได้รับ
    error_log("POST data: " . print_r($_POST, true));
    
    // ตรวจสอบการ login (ปิดไว้ก่อนเพื่อทดสอบ)
    /*
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        throw new Exception('Unauthorized');
    }
    */
    
    // ตรวจสอบ method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // รับข้อมูลจาก POST
    $title = trim($_POST['title'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $dataSource = trim($_POST['data_source'] ?? 'builder');
    $xAxis = trim($_POST['x_axis'] ?? '');
    $yAxis = trim($_POST['y_axis'] ?? '');
    $customSQL = trim($_POST['custom_sql'] ?? '');
    
    // ตรวจสอบข้อมูลจำเป็น
    if (empty($title) || empty($type)) {
        throw new Exception('กรุณากรอกชื่อกราฟและเลือกประเภทกราฟ');
    }
    
    if ($dataSource === 'builder' && (empty($xAxis) || empty($yAxis))) {
        throw new Exception('กรุณาเลือกข้อมูลแกน X และ Y');
    }
    
    if ($dataSource === 'sql' && empty($customSQL)) {
        throw new Exception('กรุณาใส่ SQL Query');
    }
    
    // จัดเก็บตัวกรอง
    $filters = [];
    if (!empty($_POST['yearStart'])) $filters['yearStart'] = $_POST['yearStart'];
    if (!empty($_POST['yearEnd'])) $filters['yearEnd'] = $_POST['yearEnd'];
    if (!empty($_POST['strategyFilter'])) $filters['strategyFilter'] = $_POST['strategyFilter'];
    if (!empty($_POST['mainProjectFilter'])) $filters['mainProjectFilter'] = $_POST['mainProjectFilter'];
    
    // จัดเก็บตั้งค่ากราฟ
    $options = [
        'primaryColor' => $_POST['primaryColor'] ?? '#667eea',
        'secondaryColor' => $_POST['secondaryColor'] ?? '#764ba2',
        'opacity' => floatval($_POST['opacity'] ?? 0.6),
        'showLegend' => isset($_POST['showLegend']),
        'showGrid' => isset($_POST['showGrid'])
    ];
    
    // แปลงเป็น JSON
    $filtersJson = json_encode($filters, JSON_UNESCAPED_UNICODE);
    $optionsJson = json_encode($options, JSON_UNESCAPED_UNICODE);
    $createdBy = $_SESSION['admin_username'] ?? 'admin';
    
    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if (!$conn) {
        throw new Exception('ไม่สามารถเชื่อมต่อฐานข้อมูลได้');
    }
    
    // เตรียม SQL statement
    $sql = "INSERT INTO saved_charts 
            (ChartTitle, ChartType, DataSource, XAxisData, YAxisData, CustomSQL, ChartFilters, ChartOptions, CreatedBy) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error);
    }
    
    $stmt->bind_param(
        "sssssssss",
        $title,
        $type,
        $dataSource,
        $xAxis,
        $yAxis,
        $customSQL,
        $filtersJson,
        $optionsJson,
        $createdBy
    );
    
    if ($stmt->execute()) {
        $chartId = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'บันทึกกราฟเรียบร้อยแล้ว',
            'chart_id' => $chartId
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
