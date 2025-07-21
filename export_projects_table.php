<?php
include 'db.php';

// รับค่าจาก URL parameters
$project_year = $_GET['project_year'] ?? '';
$subdistrict = $_GET['subdistrict'] ?? '';
$district = $_GET['district'] ?? '';
$province = $_GET['province'] ?? '';
$main_project = $_GET['main_project'] ?? '';
$strategy = $_GET['strategy'] ?? '';
$agency = $_GET['agency'] ?? '';
$target_group = $_GET['target_group'] ?? '';

// สร้าง WHERE clause สำหรับ filter
$where_conditions = [];

if (!empty($project_year)) {
    $where_conditions[] = "p.ProjectYear = '" . $conn->real_escape_string($project_year) . "'";
}

if (!empty($subdistrict)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Subdistrict = '" . $conn->real_escape_string($subdistrict) . "')";
}

if (!empty($district)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.District = '" . $conn->real_escape_string($district) . "')";
}

if (!empty($province)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Province = '" . $conn->real_escape_string($province) . "')";
}

if (!empty($main_project)) {
    $where_conditions[] = "p.MainProjectID = '" . $conn->real_escape_string($main_project) . "'";
}

if (!empty($strategy)) {
    $where_conditions[] = "p.StrategyID = '" . $conn->real_escape_string($strategy) . "'";
}

if (!empty($agency)) {
    $where_conditions[] = "p.AgencyName = '" . $conn->real_escape_string($agency) . "'";
}

if (!empty($target_group)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projecttargetcounts ptc WHERE ptc.ProjectID = p.ProjectID AND ptc.GroupID = '" . $conn->real_escape_string($target_group) . "')";
}

// สร้าง WHERE clause รวม
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'AND ' . implode(' AND ', $where_conditions);
}

// Query สำหรับส่งออกข้อมูลครบถ้วน
$export_query = "
    SELECT 
        p.ProjectID,
        p.ProjectCode as 'รหัสโครงการ',
        p.ProjectName as 'ชื่อโครงการ',
        p.ProjectYear as 'ปีโครงการ',
        mp.MainProjectName as 'โครงการหลัก',
        s.StrategyName as 'ยุทธศาสตร์',
        p.AgencyName as 'หน่วยงาน',
        p.ResponsiblePerson as 'ผู้รับผิดชอบ',
        p.TargetArea as 'พื้นที่เป้าหมาย',
        
        -- ข้อมูลพื้นที่ (รวมทั้งหมด)
        (SELECT GROUP_CONCAT(DISTINCT pv.Province SEPARATOR ', ') 
         FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Province IS NOT NULL) as 'จังหวัด',
        (SELECT GROUP_CONCAT(DISTINCT pv.District SEPARATOR ', ') 
         FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.District IS NOT NULL) as 'อำเภอ',
        (SELECT GROUP_CONCAT(DISTINCT pv.Subdistrict SEPARATOR ', ') 
         FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.Subdistrict IS NOT NULL) as 'ตำบล',
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(COALESCE(pv.VillageName, ''), ' หมู่ ', COALESCE(pv.Moo, '')) SEPARATOR ', ') 
         FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND pv.VillageName IS NOT NULL) as 'หมู่บ้าน',
        
        -- ข้อมูลงบประมาณ
        (SELECT COALESCE(SUM(bi.RequestedAmount), 0) FROM budgetitems bi WHERE bi.ProjectID = p.ProjectID) as 'งบประมาณที่ขอ',
        (SELECT COALESCE(SUM(bi.ApprovedAmount), 0) FROM budgetitems bi WHERE bi.ProjectID = p.ProjectID) as 'งบประมาณที่อนุมัติ',
        (SELECT GROUP_CONCAT(DISTINCT bi.BudgetType SEPARATOR ', ') 
         FROM budgetitems bi WHERE bi.ProjectID = p.ProjectID AND bi.BudgetType IS NOT NULL) as 'ประเภทงบประมาณ',
        
        -- ข้อมูลตัวชี้วัด
        (SELECT COUNT(DISTINCT pi.IndicatorID) FROM project_indicators pi WHERE pi.ProjectID = p.ProjectID) as 'จำนวนตัวชี้วัด',
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(i.IndicatorName, ': ', pi.Value, ' ', COALESCE(i.Unit, '')) SEPARATOR ' | ') 
         FROM project_indicators pi 
         JOIN indicators i ON pi.IndicatorID = i.IndicatorID 
         WHERE pi.ProjectID = p.ProjectID) as 'ตัวชี้วัดและค่า',
        
        -- ข้อมูลผลิตภัณฑ์
        (SELECT COUNT(*) FROM projectproducts pp WHERE pp.ProjectID = p.ProjectID) as 'จำนวนผลิตภัณฑ์',
        (SELECT GROUP_CONCAT(DISTINCT pp.ProductName SEPARATOR ', ') 
         FROM projectproducts pp WHERE pp.ProjectID = p.ProjectID AND pp.ProductName IS NOT NULL) as 'รายชื่อผลิตภัณฑ์',
        (SELECT GROUP_CONCAT(DISTINCT pp.ProductType SEPARATOR ', ') 
         FROM projectproducts pp WHERE pp.ProjectID = p.ProjectID AND pp.ProductType IS NOT NULL) as 'ประเภทผลิตภัณฑ์',
        
        -- ข้อมูลกลุ่มเป้าหมาย
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(tg.GroupName, ': ', ptc.TargetCount, ' คน') SEPARATOR ', ') 
         FROM projecttargetcounts ptc 
         JOIN targetgroups tg ON ptc.GroupID = tg.GroupID 
         WHERE ptc.ProjectID = p.ProjectID) as 'กลุ่มเป้าหมาย',
        
        -- ข้อมูลวิสาหกิจ
        (SELECT COUNT(*) FROM projectenterprises pe WHERE pe.ProjectID = p.ProjectID) as 'จำนวนวิสาหกิจ',
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(pe.EnterpriseName, ' (', pe.EnterpriseType, ')') SEPARATOR ', ') 
         FROM projectenterprises pe WHERE pe.ProjectID = p.ProjectID) as 'รายชื่อวิสาหกิจ',
        
        -- ข้อมูลโรงเรียน
        (SELECT COUNT(*) FROM projectschools ps WHERE ps.ProjectID = p.ProjectID) as 'จำนวนโรงเรียน',
        (SELECT GROUP_CONCAT(DISTINCT ps.SchoolName SEPARATOR ', ') 
         FROM projectschools ps WHERE ps.ProjectID = p.ProjectID) as 'รายชื่อโรงเรียน',
        
        -- ข้อมูลเครือข่าย
        (SELECT COUNT(*) FROM projectnetworks pn WHERE pn.ProjectID = p.ProjectID) as 'จำนวนเครือข่าย',
        (SELECT GROUP_CONCAT(DISTINCT pn.NetworkName SEPARATOR ', ') 
         FROM projectnetworks pn WHERE pn.ProjectID = p.ProjectID) as 'รายชื่อเครือข่าย',
        
        -- ข้อมูลมหาวิทยาลัย
        (SELECT COUNT(*) FROM projectuniversities pu WHERE pu.ProjectID = p.ProjectID) as 'จำนวนมหาวิทยาลัย',
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(pu.UniversityName, ' (', COALESCE(pu.UniversityType, ''), ')') SEPARATOR ', ') 
         FROM projectuniversities pu WHERE pu.ProjectID = p.ProjectID) as 'รายชื่อมหาวิทยาลัย',
        
        -- ข้อมูลองค์กรปกครองส่วนท้องถิ่น
        (SELECT COUNT(*) FROM projectlocaladmins pla WHERE pla.ProjectID = p.ProjectID) as 'จำนวนอปท',
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(pla.AdminName, ' (', pla.AdminType, ')') SEPARATOR ', ') 
         FROM projectlocaladmins pla WHERE pla.ProjectID = p.ProjectID) as 'รายชื่ออปท',
        
        -- ข้อมูลองค์กรอื่นๆ
        (SELECT COUNT(*) FROM projectothers po WHERE po.ProjectID = p.ProjectID) as 'จำนวนองค์กรอื่น',
        (SELECT GROUP_CONCAT(DISTINCT CONCAT(po.OrganizationName, ' (', COALESCE(po.OrganizationType, ''), ')') SEPARATOR ', ') 
         FROM projectothers po WHERE po.ProjectID = p.ProjectID) as 'รายชื่อองค์กรอื่น',
        
        p.CreateAt as 'วันที่สร้าง'
        
    FROM projects p
    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
    WHERE 1=1 $where_clause
    ORDER BY p.ProjectYear DESC, p.ProjectID ASC
";

// ดึงข้อมูล
$result = $conn->query($export_query);

if (!$result) {
    die("Error in query: " . $conn->error);
}

// สร้างชื่อไฟล์
$filename = 'projects_export_' . date('Y-m-d_H-i-s') . '.csv';

// ตั้งค่า header สำหรับดาวน์โหลด
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// เพิ่ม BOM สำหรับ UTF-8
echo "\xEF\xBB\xBF";

// สร้าง CSV
$output = fopen('php://output', 'w');

// เขียนหัวตาราง
if ($result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $headers = array_keys($first_row);
    
    // ลบ ProjectID ออกจาก header
    $filtered_headers = array_filter($headers, function($header) {
        return $header !== 'ProjectID';
    });
    
    fputcsv($output, $filtered_headers);
    
    // เขียนแถวแรก
    $filtered_first_row = array_diff_key($first_row, ['ProjectID' => '']);
    fputcsv($output, $filtered_first_row);
    
    // เขียนแถวที่เหลือ
    while ($row = $result->fetch_assoc()) {
        $filtered_row = array_diff_key($row, ['ProjectID' => '']);
        fputcsv($output, $filtered_row);
    }
} else {
    // ถ้าไม่มีข้อมูล
    fputcsv($output, ['ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา']);
}

fclose($output);
exit();
?>
