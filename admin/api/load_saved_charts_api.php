<?php
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
include '../database/db.php';

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // ถ้ามี chart_id ให้โหลดกราฟเฉพาะ
    if (isset($_GET['chart_id'])) {
        $chartId = intval($_GET['chart_id']);
        
        $stmt = $conn->prepare("
            SELECT ChartID, ChartTitle, ChartType, DataSource, XAxisData, YAxisData, 
                   CustomSQL, ChartFilters, ChartOptions, CreatedBy, CreatedAt
            FROM saved_charts 
            WHERE ChartID = ? AND IsActive = 1
        ");
        $stmt->bind_param("i", $chartId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($chart = $result->fetch_assoc()) {
            // แปลง JSON กลับเป็น array
            $chart['ChartFilters'] = json_decode($chart['ChartFilters'], true) ?: [];
            $chart['ChartOptions'] = json_decode($chart['ChartOptions'], true) ?: [];
            
            echo json_encode([
                'success' => true,
                'chart' => $chart
            ]);
        } else {
            throw new Exception('ไม่พบกราฟที่ระบุ');
        }
        
        $stmt->close();
        
    } else {
        // โหลดรายการกราฟทั้งหมด
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // นับจำนวนกราฟทั้งหมด
        $countResult = $conn->query("SELECT COUNT(*) as total FROM saved_charts WHERE IsActive = 1");
        $totalCharts = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCharts / $limit);
        
        // ดึงข้อมูลกราฟ
        $stmt = $conn->prepare("
            SELECT ChartID, ChartTitle, ChartType, DataSource, CreatedBy, CreatedAt,
                   CASE 
                       WHEN DataSource = 'builder' THEN CONCAT(XAxisData, ' vs ', YAxisData)
                       ELSE 'Custom SQL Query'
                   END as Description
            FROM saved_charts 
            WHERE IsActive = 1 
            ORDER BY CreatedAt DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $charts = [];
        while ($row = $result->fetch_assoc()) {
            $charts[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'charts' => $charts,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_charts' => $totalCharts,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ]);
        
        $stmt->close();
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
