<?php
// ปิด error reporting เพื่อป้องกัน error ที่อาจรบกวน JSON response
error_reporting(0);
ini_set('display_errors', 0);

// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json; charset=utf-8');

if (file_exists('./database/db.php')) {
    include './database/db.php';
} else {
    include 'db.php';
}

// รับค่าจาก DataTables
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$search_value = $_POST['search']['value'];

// รับค่าจาก filters
$project_year_start = $_POST['project_year_start'] ?? '';
$project_year_end = $_POST['project_year_end'] ?? '';
$subdistrict = $_POST['subdistrict'] ?? '';
$district = $_POST['district'] ?? '';
$province = $_POST['province'] ?? '';
$village = $_POST['village'] ?? '';
$main_project = $_POST['main_project'] ?? '';
$strategy = $_POST['strategy'] ?? '';
$agency = $_POST['agency'] ?? '';
$target_group = $_POST['target_group'] ?? '';
$teacher = $_POST['teacher'] ?? '';

// สร้าง WHERE clause สำหรับ filter
$where_conditions = [];

// ตรวจสอบช่วงปีโครงการ
if (!empty($project_year_start) && !empty($project_year_end)) {
    $where_conditions[] = "p.ProjectYear BETWEEN '" . $conn->real_escape_string($project_year_start) . "' AND '" . $conn->real_escape_string($project_year_end) . "'";
} elseif (!empty($project_year_start)) {
    $where_conditions[] = "p.ProjectYear >= '" . $conn->real_escape_string($project_year_start) . "'";
} elseif (!empty($project_year_end)) {
    $where_conditions[] = "p.ProjectYear <= '" . $conn->real_escape_string($project_year_end) . "'";
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

if (!empty($village)) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND (pv.VillageName = '" . $conn->real_escape_string($village) . "' OR pv.Community = '" . $conn->real_escape_string($village) . "'))";
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

if (!empty($teacher)) {
    $where_conditions[] = "p.ResponsiblePerson = '" . $conn->real_escape_string($teacher) . "'";
}

// สร้าง WHERE clause รวม
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'AND ' . implode(' AND ', $where_conditions);
}

// Search clause
$search_clause = '';
if (!empty($search_value)) {
    $search_value = $conn->real_escape_string($search_value);
    $search_clause = "AND (
        p.ProjectName LIKE '%$search_value%' OR 
        p.ProjectCode LIKE '%$search_value%' OR 
        p.ResponsiblePerson LIKE '%$search_value%' OR 
        p.ProjectYear LIKE '%$search_value%' OR 
        p.AgencyName LIKE '%$search_value%' OR 
        mp.MainProjectName LIKE '%$search_value%' OR 
        s.StrategyName LIKE '%$search_value%' OR
        EXISTS (SELECT 1 FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID AND (
            pv.Province LIKE '%$search_value%' OR 
            pv.District LIKE '%$search_value%' OR 
            pv.Subdistrict LIKE '%$search_value%' OR 
            pv.VillageName LIKE '%$search_value%' OR 
            pv.Community LIKE '%$search_value%'
        )) OR
        EXISTS (SELECT 1 FROM projectproducts pp WHERE pp.ProjectID = p.ProjectID AND (
            pp.ProductName LIKE '%$search_value%' OR 
            pp.ProductType LIKE '%$search_value%'
        )) OR
        EXISTS (SELECT 1 FROM projectschools ps WHERE ps.ProjectID = p.ProjectID AND 
            ps.SchoolName LIKE '%$search_value%'
        ) OR
        EXISTS (SELECT 1 FROM projecttargetcounts ptc 
            JOIN targetgroups tg ON ptc.GroupID = tg.GroupID 
            WHERE ptc.ProjectID = p.ProjectID AND tg.GroupName LIKE '%$search_value%'
        ) OR
        EXISTS (SELECT 1 FROM project_indicators pi 
            JOIN indicators i ON pi.IndicatorID = i.IndicatorID 
            WHERE pi.ProjectID = p.ProjectID AND (
                i.IndicatorName LIKE '%$search_value%' OR 
                pi.Value LIKE '%$search_value%'
            )
        )
    )";
}

// Query หลักสำหรับดึงข้อมูล
$main_query = "
    SELECT 
        p.ProjectID,
        p.ProjectCode,
        p.ProjectName,
        p.ProjectYear,
        p.ResponsiblePerson,
        p.AgencyName,
        mp.MainProjectName,
        s.StrategyName,
        
        -- ข้อมูลพื้นที่ (เอาข้อมูลแรกที่พบ)
        (SELECT DISTINCT pv.Province FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID LIMIT 1) as Province,
        (SELECT DISTINCT pv.District FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID LIMIT 1) as District,
        (SELECT DISTINCT pv.Subdistrict FROM projectvillages pv WHERE pv.ProjectID = p.ProjectID LIMIT 1) as Subdistrict,
        
        -- งบประมาณรวม
        (SELECT COALESCE(SUM(bi.ApprovedAmount), 0) FROM budgetitems bi WHERE bi.ProjectID = p.ProjectID) as TotalBudget,
        
        -- จำนวนตัวชี้วัด
        (SELECT COUNT(DISTINCT pi.IndicatorID) FROM project_indicators pi WHERE pi.ProjectID = p.ProjectID) as IndicatorCount,
        
        -- จำนวนผลิตภัณฑ์
        (SELECT COUNT(*) FROM projectproducts pp WHERE pp.ProjectID = p.ProjectID) as ProductCount,
        
        -- จำนวนกลุ่มเป้าหมาย
        (SELECT COUNT(DISTINCT ptc.GroupID) FROM projecttargetcounts ptc WHERE ptc.ProjectID = p.ProjectID) as TargetGroupCount
        
    FROM projects p
    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
    WHERE 1=1 $where_clause $search_clause
";

// นับจำนวนรวมสำหรับ pagination
$count_query = "
    SELECT COUNT(DISTINCT p.ProjectID) as total
    FROM projects p
    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
    WHERE 1=1 $where_clause $search_clause
";

// ดึงจำนวนรวม
$total_result = $conn->query($count_query);
if (!$total_result) {
    // ส่ง JSON error response
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Database error: ' . $conn->error
    ]);
    exit;
}
$total_records = $total_result->fetch_assoc()['total'];

// เรียงลำดับ
$order_column = '';
$order_dir = 'DESC';

if (isset($_POST['order'][0]['column'])) {
    $columns = [
        0 => 'p.ProjectCode',
        1 => 'p.ProjectName', 
        2 => 'p.ProjectYear',
        3 => 'mp.MainProjectName',
        4 => 's.StrategyName',
        5 => 'p.AgencyName',
        6 => 'Province',
        7 => 'District',
        8 => 'Subdistrict',
        9 => 'TotalBudget',
        10 => 'IndicatorCount',
        11 => 'ProductCount',
        12 => 'TargetGroupCount'
    ];
    
    $order_column_index = intval($_POST['order'][0]['column']);
    if (isset($columns[$order_column_index])) {
        $order_column = $columns[$order_column_index];
        $order_dir = strtoupper($_POST['order'][0]['dir']) == 'ASC' ? 'ASC' : 'DESC';
    }
}

// ถ้าไม่มีการเรียงลำดับ ให้เรียงตาม ProjectYear DESC
if (empty($order_column)) {
    $order_column = 'p.ProjectYear';
    $order_dir = 'DESC';
}

// เพิ่ม ORDER BY และ LIMIT
$final_query = $main_query . " ORDER BY $order_column $order_dir";

if ($length != -1) {
    $final_query .= " LIMIT $start, $length";
}

// ดึงข้อมูล
$result = $conn->query($final_query);

if (!$result) {
    // ส่ง JSON error response
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Query error: ' . $conn->error
    ]);
    exit;
}

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $project_id = $row['ProjectID'];
        
        // ดึงข้อมูลตัวชี้วัดรายละเอียด
        $indicators_query = "
            SELECT i.IndicatorName, pi.Value, i.Unit
            FROM project_indicators pi
            JOIN indicators i ON pi.IndicatorID = i.IndicatorID
            WHERE pi.ProjectID = $project_id
            ORDER BY i.IndicatorName
        ";
        $indicators_result = $conn->query($indicators_query);
        $indicators_data = [];
        if ($indicators_result) {
            while ($ind = $indicators_result->fetch_assoc()) {
                $indicators_data[] = [
                    'name' => $ind['IndicatorName'],
                    'value' => $ind['Value'],
                    'unit' => $ind['Unit'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลผลิตภัณฑ์รายละเอียด
        $products_query = "
            SELECT ProductName, ProductType
            FROM projectproducts
            WHERE ProjectID = $project_id
            ORDER BY ProductName
        ";
        $products_result = $conn->query($products_query);
        $products_data = [];
        if ($products_result) {
            while ($prod = $products_result->fetch_assoc()) {
                $products_data[] = [
                    'name' => $prod['ProductName'],
                    'type' => $prod['ProductType'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลโรงเรียนรายละเอียด
        $schools_query = "
            SELECT SchoolName
            FROM projectschools
            WHERE ProjectID = $project_id
            ORDER BY SchoolName
        ";
        $schools_result = $conn->query($schools_query);
        $schools_data = [];
        if ($schools_result) {
            while ($school = $schools_result->fetch_assoc()) {
                $schools_data[] = [
                    'name' => $school['SchoolName']
                ];
            }
        }
        
        // ดึงข้อมูลกลุ่มเป้าหมายรายละเอียด
        $targetgroups_query = "
            SELECT tg.GroupName, ptc.TargetCount
            FROM projecttargetcounts ptc
            JOIN targetgroups tg ON ptc.GroupID = tg.GroupID
            WHERE ptc.ProjectID = $project_id
            ORDER BY tg.GroupName
        ";
        $targetgroups_result = $conn->query($targetgroups_query);
        $targetgroups_data = [];
        if ($targetgroups_result) {
            while ($tg = $targetgroups_result->fetch_assoc()) {
                $targetgroups_data[] = [
                    'name' => $tg['GroupName'],
                    'count' => intval($tg['TargetCount'])
                ];
            }
        }
        
        // ดึงข้อมูลพื้นที่รายละเอียด
        $locations_query = "
            SELECT VillageName, Moo, Subdistrict, District, Province, Community
            FROM projectvillages
            WHERE ProjectID = $project_id
            ORDER BY Province, District, Subdistrict, VillageName
        ";
        $locations_result = $conn->query($locations_query);
        $locations_data = [];
        if ($locations_result) {
            while ($loc = $locations_result->fetch_assoc()) {
                $full_address = '';
                if ($loc['Subdistrict']) $full_address .= 'ต.' . $loc['Subdistrict'];
                if ($loc['District']) $full_address .= ' อ.' . $loc['District'];
                if ($loc['Province']) $full_address .= ' จ.' . $loc['Province'];
                
                $locations_data[] = [
                    'village_name' => $loc['VillageName'] ?: 'ไม่ระบุ',
                    'community' => $loc['Community'] ?: '',
                    'moo' => $loc['Moo'] ?: '',
                    'full_address' => trim($full_address)
                ];
            }
        }
        
        // ดึงข้อมูล SROI รายละเอียด
        $sroi_query = "
            SELECT SROIResult, Description
            FROM projectsroi
            WHERE ProjectID = $project_id
        ";
        $sroi_result = $conn->query($sroi_query);
        $sroi_data = [];
        if ($sroi_result) {
            while ($sroi = $sroi_result->fetch_assoc()) {
                $sroi_data[] = [
                    'result' => $sroi['SROIResult'] ?: '',
                    'description' => $sroi['Description'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลวิสาหกิจ/ผู้ประกอบการ รายละเอียด
        $enterprise_query = "
            SELECT EnterpriseName, EnterpriseType
            FROM projectenterprises
            WHERE ProjectID = $project_id
            ORDER BY EnterpriseName
        ";
        $enterprise_result = $conn->query($enterprise_query);
        $enterprise_data = [];
        if ($enterprise_result) {
            while ($enterprise = $enterprise_result->fetch_assoc()) {
                $enterprise_data[] = [
                    'name' => $enterprise['EnterpriseName'] ?: '',
                    'type' => $enterprise['EnterpriseType'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลองค์กรอื่น ๆ รายละเอียด
        $others_query = "
            SELECT OrganizationName, OrganizationType, Role
            FROM projectothers
            WHERE ProjectID = $project_id
            ORDER BY OrganizationName
        ";
        $others_result = $conn->query($others_query);
        $others_data = [];
        if ($others_result) {
            while ($other = $others_result->fetch_assoc()) {
                $others_data[] = [
                    'name' => $other['OrganizationName'] ?: '',
                    'type' => $other['OrganizationType'] ?: '',
                    'role' => $other['Role'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลเครือข่าย รายละเอียด
        $network_query = "
            SELECT NetworkName
            FROM projectnetworks
            WHERE ProjectID = $project_id
            ORDER BY NetworkName
        ";
        $network_result = $conn->query($network_query);
        $network_data = [];
        if ($network_result) {
            while ($network = $network_result->fetch_assoc()) {
                $network_data[] = [
                    'name' => $network['NetworkName'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลมหาวิทยาลัย รายละเอียด
        $university_query = "
            SELECT UniversityName, UniversityType, Collaboration
            FROM projectuniversities
            WHERE ProjectID = $project_id
            ORDER BY UniversityName
        ";
        $university_result = $conn->query($university_query);
        $university_data = [];
        if ($university_result) {
            while ($university = $university_result->fetch_assoc()) {
                $university_data[] = [
                    'name' => $university['UniversityName'] ?: '',
                    'type' => $university['UniversityType'] ?: '',
                    'collaboration' => $university['Collaboration'] ?: ''
                ];
            }
        }
        
        // ดึงข้อมูลองค์กรปกครองส่วนท้องถิ่น รายละเอียด
        $localadmin_query = "
            SELECT AdminName, AdminType, District, SupportType
            FROM projectlocaladmins
            WHERE ProjectID = $project_id
            ORDER BY AdminName
        ";
        $localadmin_result = $conn->query($localadmin_query);
        $localadmin_data = [];
        if ($localadmin_result) {
            while ($admin = $localadmin_result->fetch_assoc()) {
                $localadmin_data[] = [
                    'name' => $admin['AdminName'] ?: '',
                    'type' => $admin['AdminType'] ?: '',
                    'district' => $admin['District'] ?: '',
                    'support_type' => $admin['SupportType'] ?: ''
                ];
            }
        }
        
        // ตรวจสอบและจัดรูปแบบข้อมูล
        $data[] = [
            'ProjectID' => $row['ProjectID'],
            'ProjectCode' => $row['ProjectCode'] ?: '-',
            'ProjectName' => $row['ProjectName'] ?: '-',
            'ProjectYear' => $row['ProjectYear'] ?: '-',
            'ResponsiblePerson' => $row['ResponsiblePerson'] ?: '-',
            'MainProjectName' => $row['MainProjectName'] ?: '-',
            'StrategyName' => $row['StrategyName'] ?: '-',
            'AgencyName' => $row['AgencyName'] ?: '-',
            'Province' => $row['Province'] ?: '-',
            'District' => $row['District'] ?: '-',
            'Subdistrict' => $row['Subdistrict'] ?: '-',
            'LocationDetails' => $locations_data,
            'TotalBudget' => floatval($row['TotalBudget']),
            'IndicatorDetails' => $indicators_data,
            'ProductDetails' => $products_data,
            'SchoolDetails' => $schools_data,
            'TargetGroupDetails' => $targetgroups_data,
            'SROIData' => $sroi_data,
            'EnterpriseDetails' => $enterprise_data,
            'OtherOrganizations' => $others_data,
            'NetworkDetails' => $network_data,
            'UniversityDetails' => $university_data,
            'LocalAdminDetails' => $localadmin_data
        ];
    }
}

// ส่งผลลัพธ์กลับในรูปแบบ JSON
$response = [
    'draw' => $draw,
    'recordsTotal' => $total_records,
    'recordsFiltered' => $total_records,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
