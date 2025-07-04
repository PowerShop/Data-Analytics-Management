<?php
include 'db.php';

// ตรวจสอบประเภทรายงาน
$report_type = $_GET['type'] ?? 'project_summary';

// Build WHERE clause จาก filters
$where_conditions = [];
$params = [];

if (!empty($_GET['province'])) {
    $where_conditions[] = "pv.Province = ?";
    $params[] = $_GET['province'];
}

if (!empty($_GET['budget_min'])) {
    $where_conditions[] = "b.ApprovedAmount >= ?";
    $params[] = $_GET['budget_min'];
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// ตั้งค่า Headers สำหรับ CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $report_type . '_' . date('Y-m-d') . '.csv"');

// เริ่มต้น output
$output = fopen('php://output', 'w');

// เพิ่ม BOM สำหรับ UTF-8
fwrite($output, "\xEF\xBB\xBF");

switch ($report_type) {
    case 'project_summary':
        generateProjectSummaryReport($conn, $output, $where_clause, $params);
        break;
    case 'budget':
        generateBudgetReport($conn, $output, $where_clause, $params);
        break;
    case 'performance':
        generatePerformanceReport($conn, $output, $where_clause, $params);
        break;
    case 'area':
        generateAreaReport($conn, $output, $where_clause, $params);
        break;
    case 'enterprise':
        generateEnterpriseReport($conn, $output, $where_clause, $params);
        break;
    default:
        generateProjectSummaryReport($conn, $output, $where_clause, $params);
}

fclose($output);

// ฟังก์ชันสำหรับรายงานสรุปโครงการ
function generateProjectSummaryReport($conn, $output, $where_clause, $params) {
    // Headers
    fputcsv($output, [
        'รหัสโครงการ',
        'ชื่อโครงการ',
        'หน่วยงาน',
        'จังหวัด',
        'จำนวนกลุ่มเป้าหมาย',
        'จำนวนหมู่บ้าน',
        'จำนวนวิสาหกิจ',
        'จำนวนผลิตภัณฑ์',
        'จำนวนโรงเรียน',
        'จำนวนเครือข่าย',
        'งบประมาณอนุมัติ',
        'ค่า SROI เฉลี่ย',
        'ค่า GVH เฉลี่ย'
    ]);

    $query = "
        SELECT p.ProjectID, p.ProjectName, p.AgencyName, p.Province,
               COALESCE(SUM(ptc.TargetCount), 0) as target_count,
               COUNT(DISTINCT pv.ID) as village_count,
               COUNT(DISTINCT pe.ID) as enterprise_count,
               COUNT(DISTINCT pp.ID) as product_count,
               COUNT(DISTINCT ps.ID) as school_count,
               COUNT(DISTINCT pn.ID) as network_count,
               COALESCE(SUM(b.ApprovedAmount), 0) as total_budget,
               COALESCE(AVG(psr.SROIResult), 0) as avg_sroi,
               COALESCE(AVG(pg.PerformanceResult), 0) as avg_gvh
        FROM Projects p
        LEFT JOIN ProjectTargetCounts ptc ON p.ProjectID = ptc.ProjectID
        LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
        LEFT JOIN ProjectEnterprises pe ON p.ProjectID = pe.ProjectID
        LEFT JOIN ProjectProducts pp ON p.ProjectID = pp.ProjectID
        LEFT JOIN ProjectSchools ps ON p.ProjectID = ps.ProjectID
        LEFT JOIN ProjectNetworks pn ON p.ProjectID = pn.ProjectID
        LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
        LEFT JOIN ProjectSROI psr ON p.ProjectID = psr.ProjectID
        LEFT JOIN ProjectGVH pg ON p.ProjectID = pg.ProjectID
        $where_clause
        GROUP BY p.ProjectID, p.ProjectName, p.AgencyName, p.Province
        ORDER BY p.ProjectID
    ";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ProjectID'],
            $row['ProjectName'],
            $row['AgencyName'] ?: 'ไม่ระบุ',
            $row['Province'] ?: 'ไม่ระบุ',
            $row['target_count'],
            $row['village_count'],
            $row['enterprise_count'],
            $row['product_count'],
            $row['school_count'],
            $row['network_count'],
            number_format($row['total_budget'], 2),
            number_format($row['avg_sroi'], 2),
            number_format($row['avg_gvh'], 2)
        ]);
    }
}

// ฟังก์ชันสำหรับรายงานงบประมาณ
function generateBudgetReport($conn, $output, $where_clause, $params) {
    fputcsv($output, [
        'รหัสโครงการ',
        'ชื่อโครงการ',
        'หน่วยงาน',
        'หมวดงบประมาณ',
        'งบประมาณที่ขอ',
        'งบประมาณที่อนุมัติ',
        'อัตราการอนุมัติ (%)'
    ]);

    $query = "
        SELECT p.ProjectID, p.ProjectName, p.AgencyName, b.Category, 
               b.RequestedAmount, b.ApprovedAmount,
               (b.ApprovedAmount / b.RequestedAmount * 100) as approval_rate
        FROM Projects p
        JOIN BudgetItems b ON p.ProjectID = b.ProjectID
        LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
        $where_clause
        ORDER BY p.ProjectID, b.Category
    ";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ProjectID'],
            $row['ProjectName'],
            $row['AgencyName'] ?: 'ไม่ระบุ',
            $row['Category'],
            number_format($row['RequestedAmount'], 2),
            number_format($row['ApprovedAmount'], 2),
            number_format($row['approval_rate'], 2)
        ]);
    }
}

// ฟังก์ชันสำหรับรายงานประสิทธิภาพ
function generatePerformanceReport($conn, $output, $where_clause, $params) {
    fputcsv($output, [
        'รหัสโครงการ',
        'ชื่อโครงการ',
        'หน่วยงาน',
        'ค่า SROI',
        'ค่า GVH',
        'หมู่บ้าน (GVH)',
        'ชุมชน (GVH)',
        'คำอธิบาย (SROI)'
    ]);

    $query = "
        SELECT p.ProjectID, p.ProjectName, p.AgencyName, ps.SROIResult, ps.Description as sroi_desc,
               pg.PerformanceResult, pg.VillageName as gvh_village, pg.CommunityName as gvh_community
        FROM Projects p
        LEFT JOIN ProjectSROI ps ON p.ProjectID = ps.ProjectID
        LEFT JOIN ProjectGVH pg ON p.ProjectID = pg.ProjectID
        LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
        $where_clause
        ORDER BY p.ProjectID
    ";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ProjectID'],
            $row['ProjectName'],
            $row['AgencyName'] ?: 'ไม่ระบุ',
            number_format($row['SROIResult'] ?: 0, 2),
            number_format($row['PerformanceResult'] ?: 0, 2),
            $row['gvh_village'] ?: '',
            $row['gvh_community'] ?: '',
            $row['sroi_desc'] ?: ''
        ]);
    }
}

// ฟังก์ชันสำหรับรายงานพื้นที่
function generateAreaReport($conn, $output, $where_clause, $params) {
    fputcsv($output, [
        'รหัสโครงการ',
        'ชื่อโครงการ',
        'ชื่อหมู่บ้าน',
        'หมู่',
        'ตำบล',
        'อำเภอ',
        'จังหวัด',
        'ชุมชน',
        'Soft Power'
    ]);

    $query = "
        SELECT p.ProjectID, p.ProjectName, 
               pv.VillageName, pv.Moo, pv.Subdistrict, pv.District, pv.Province, pv.Community,
               CASE WHEN sp.ID IS NOT NULL THEN 'มี' ELSE 'ไม่มี' END as has_soft_power
        FROM Projects p
        LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
        LEFT JOIN ProjectSoftPower sp ON p.ProjectID = sp.ProjectID AND pv.VillageName = sp.VillageName
        $where_clause
        ORDER BY p.ProjectID, pv.Province, pv.District, pv.VillageName
    ";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ProjectID'],
            $row['ProjectName'],
            $row['VillageName'] ?: '',
            $row['Moo'] ?: '',
            $row['Subdistrict'] ?: '',
            $row['District'] ?: '',
            $row['Province'] ?: '',
            $row['Community'] ?: '',
            $row['has_soft_power']
        ]);
    }
}

// ฟังก์ชันสำหรับรายงานวิสาหกิจ
function generateEnterpriseReport($conn, $output, $where_clause, $params) {
    fputcsv($output, [
        'รหัสโครงการ',
        'ชื่อโครงการ',
        'หน่วยงาน',
        'ชื่อวิสาหกิจ',
        'ประเภทวิสาหกิจ',
        'ผลิตภัณฑ์',
        'ประเภทผลิตภัณฑ์',
        'คำอธิบายผลิตภัณฑ์',
        'โรงเรียนที่เกี่ยวข้อง',
        'เครือข่าย'
    ]);

    $query = "
        SELECT p.ProjectID, p.ProjectName, p.AgencyName,
               pe.EnterpriseName, pe.EnterpriseType,
               pp.ProductName, pp.ProductType, pp.Description as product_desc,
               ps.SchoolName, pn.NetworkName
        FROM Projects p
        LEFT JOIN ProjectEnterprises pe ON p.ProjectID = pe.ProjectID
        LEFT JOIN ProjectProducts pp ON p.ProjectID = pp.ProjectID
        LEFT JOIN ProjectSchools ps ON p.ProjectID = ps.ProjectID
        LEFT JOIN ProjectNetworks pn ON p.ProjectID = pn.ProjectID
        LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
        $where_clause
        ORDER BY p.ProjectID, pe.EnterpriseName
    ";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ProjectID'],
            $row['ProjectName'],
            $row['AgencyName'] ?: 'ไม่ระบุ',
            $row['EnterpriseName'] ?: '',
            $row['EnterpriseType'] ?: '',
            $row['ProductName'] ?: '',
            $row['ProductType'] ?: '',
            $row['product_desc'] ?: '',
            $row['SchoolName'] ?: '',
            $row['NetworkName'] ?: ''
        ]);
    }
}

// ฟังก์ชันแปลงสถานะ (ไม่ใช้แล้วแต่เก็บไว้เผื่อใช้ในอนาคต)
function getStatusText($status) {
    return $status ?: 'ไม่ระบุ';
}
?>
