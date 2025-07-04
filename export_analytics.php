<?php
require_once 'db.php';

// ฟังก์ชันสำหรับสร้าง WHERE clause จาก filters (เหมือนกับใน analytics.php)
function buildFilterWhereClause() {
    global $conn;
    $where_conditions = [];
    
    // Filter โครงการหลัก
    if (!empty($_GET['main_project'])) {
        $main_project = $conn->real_escape_string($_GET['main_project']);
        $where_conditions[] = "p.MainProjectID = '$main_project'";
    }
    
    // Filter ยุทธศาสตร์
    if (!empty($_GET['strategy'])) {
        $strategy = $conn->real_escape_string($_GET['strategy']);
        $where_conditions[] = "p.StrategyID = '$strategy'";
    }
    
    // Filter ปีโครงการ
    if (!empty($_GET['year'])) {
        $year = $conn->real_escape_string($_GET['year']);
        $where_conditions[] = "p.ProjectYear = '$year'";
    }
    
    // Filter หน่วยงาน
    if (!empty($_GET['agency'])) {
        $agency = $conn->real_escape_string($_GET['agency']);
        $where_conditions[] = "p.AgencyName = '$agency'";
    }
    
    // Filter จังหวัด
    if (!empty($_GET['province'])) {
        $province = $conn->real_escape_string($_GET['province']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM ProjectVillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Province = '$province')";
    }
    
    // Filter อำเภอ
    if (!empty($_GET['district'])) {
        $district = $conn->real_escape_string($_GET['district']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM ProjectVillages pv WHERE pv.ProjectID = p.ProjectID AND pv.District = '$district')";
    }
    
    // Filter ตำบล
    if (!empty($_GET['subdistrict'])) {
        $subdistrict = $conn->real_escape_string($_GET['subdistrict']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM ProjectVillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Subdistrict = '$subdistrict')";
    }
    
    // Filter กลุ่มเป้าหมาย
    if (!empty($_GET['target_group'])) {
        $target_group = $conn->real_escape_string($_GET['target_group']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM ProjectTargetCounts ptc WHERE ptc.ProjectID = p.ProjectID AND ptc.GroupID = '$target_group')";
    }
    
    // Filter ประเภทวิสาหกิจ
    if (!empty($_GET['enterprise_type'])) {
        $enterprise_type = $conn->real_escape_string($_GET['enterprise_type']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM ProjectEnterprises pe WHERE pe.ProjectID = p.ProjectID AND pe.EnterpriseType = '$enterprise_type')";
    }
    
    // Filter งบประมาณ
    if (!empty($_GET['budget_min'])) {
        $budget_min = $conn->real_escape_string($_GET['budget_min']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM BudgetItems bi WHERE bi.ProjectID = p.ProjectID AND bi.ApprovedAmount >= $budget_min)";
    }
    
    if (!empty($_GET['budget_max'])) {
        $budget_max = $conn->real_escape_string($_GET['budget_max']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM BudgetItems bi WHERE bi.ProjectID = p.ProjectID AND bi.ApprovedAmount <= $budget_max)";
    }
    
    // Filter SROI
    if (!empty($_GET['sroi_min'])) {
        $sroi_min = $conn->real_escape_string($_GET['sroi_min']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM ProjectSROI ps WHERE ps.ProjectID = p.ProjectID AND ps.SROIResult >= $sroi_min)";
    }
    
    // Filter ตัวชี้วัด
    if (!empty($_GET['indicator'])) {
        $indicator = $conn->real_escape_string($_GET['indicator']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM project_indicators pi WHERE pi.ProjectID = p.ProjectID AND pi.IndicatorID = '$indicator')";
    }
    
    // Filter ค่าตัวชี้วัด
    if (!empty($_GET['indicator_value_min'])) {
        $indicator_value_min = $conn->real_escape_string($_GET['indicator_value_min']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM project_indicators pi WHERE pi.ProjectID = p.ProjectID AND pi.Value >= $indicator_value_min)";
    }
    
    if (!empty($_GET['indicator_value_max'])) {
        $indicator_value_max = $conn->real_escape_string($_GET['indicator_value_max']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM project_indicators pi WHERE pi.ProjectID = p.ProjectID AND pi.Value <= $indicator_value_max)";
    }
    
    // Filter ปีตัวชี้วัด
    if (!empty($_GET['indicator_year'])) {
        $indicator_year = $conn->real_escape_string($_GET['indicator_year']);
        $where_conditions[] = "EXISTS (SELECT 1 FROM project_indicators pi JOIN indicators i ON pi.IndicatorID = i.IndicatorID WHERE pi.ProjectID = p.ProjectID AND i.Year = '$indicator_year')";
    }
    
    return empty($where_conditions) ? "" : " AND " . implode(" AND ", $where_conditions);
}

// ตั้งค่า header สำหรับการดาวน์โหลดไฟล์ CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=analytics_export_' . date('Y-m-d_H-i-s') . '.csv');

// เปิด output stream
$output = fopen('php://output', 'w');

// เขียน BOM สำหรับ UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// เขียน header ของ CSV
fputcsv($output, [
    'รหัสโครงการ',
    'ชื่อโครงการ', 
    'โครงการหลัก',
    'ยุทธศาสตร์',
    'หน่วยงาน',
    'ปีโครงการ',
    'จังหวัด',
    'งบประมาณที่ขอ',
    'งบประมาณที่อนุมัติ',
    'อัตราอนุมัติ (%)',
    'SROI',
    'GVH',
    'จำนวนตัวชี้วัด',
    'ตัวชี้วัดเฉลี่ย',
    'รายละเอียดตัวชี้วัด'
]);

// สร้าง WHERE clause จาก filters
$where_clause = buildFilterWhereClause();

// Query ข้อมูลโครงการ
$export_query = "
    SELECT 
        p.ProjectCode,
        p.ProjectName,
        mp.MainProjectName,
        s.StrategyName,
        p.AgencyName,
        p.ProjectYear,
        GROUP_CONCAT(DISTINCT pv.Province SEPARATOR ', ') as provinces,
        SUM(bi.RequestedAmount) as total_requested,
        SUM(bi.ApprovedAmount) as total_approved,
        CASE 
            WHEN SUM(bi.RequestedAmount) > 0 THEN (SUM(bi.ApprovedAmount) / SUM(bi.RequestedAmount) * 100)
            ELSE 0 
        END as approval_rate,
        AVG(ps.SROIResult) as avg_sroi,
        AVG(pg.PerformanceResult) as avg_gvh,
        COUNT(DISTINCT pi.IndicatorID) as indicator_count,
        AVG(pi.Value) as avg_indicator_value,
        GROUP_CONCAT(DISTINCT CONCAT(i.IndicatorName, ': ', pi.Value, ' ', COALESCE(i.Unit, '')) SEPARATOR '; ') as indicator_details
    FROM Projects p
    LEFT JOIN MainProjects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN Strategies s ON p.StrategyID = s.StrategyID
    LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
    LEFT JOIN BudgetItems bi ON p.ProjectID = bi.ProjectID
    LEFT JOIN ProjectSROI ps ON p.ProjectID = ps.ProjectID
    LEFT JOIN ProjectGVH pg ON p.ProjectID = pg.ProjectID
    LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
    LEFT JOIN indicators i ON pi.IndicatorID = i.IndicatorID
    WHERE 1=1 $where_clause
    GROUP BY p.ProjectID, p.ProjectCode, p.ProjectName, mp.MainProjectName, s.StrategyName, p.AgencyName, p.ProjectYear
    ORDER BY p.ProjectYear DESC, p.ProjectName
";

$result = $conn->query($export_query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['ProjectCode'] ?: 'ไม่ระบุ',
            $row['ProjectName'],
            $row['MainProjectName'] ?: 'ไม่ระบุ',
            $row['StrategyName'] ?: 'ไม่ระบุ',
            $row['AgencyName'] ?: 'ไม่ระบุ',
            $row['ProjectYear'] ?: 'ไม่ระบุ',
            $row['provinces'] ?: 'ไม่ระบุ',
            number_format($row['total_requested'] ?: 0, 2),
            number_format($row['total_approved'] ?: 0, 2),
            number_format($row['approval_rate'] ?: 0, 2),
            number_format($row['avg_sroi'] ?: 0, 2),
            number_format($row['avg_gvh'] ?: 0, 2),
            $row['indicator_count'] ?: 0,
            number_format($row['avg_indicator_value'] ?: 0, 2),
            $row['indicator_details'] ?: 'ไม่มีตัวชี้วัด'
        ]);
    }
} else {
    // ถ้าไม่มีข้อมูล ให้เขียนแถวว่าง
    fputcsv($output, ['ไม่มีข้อมูลตามเงื่อนไขที่กรอง']);
}

// ปิด output stream
fclose($output);
exit;
?>
