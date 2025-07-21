<?php
include 'db.php';

// รับค่าจาก DataTables
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$search_value = $_POST['search']['value'];

// รับค่าจาก filters
$project_year = $_POST['project_year'] ?? '';
$subdistrict = $_POST['subdistrict'] ?? '';
$district = $_POST['district'] ?? '';
$province = $_POST['province'] ?? '';
$main_project = $_POST['main_project'] ?? '';
$strategy = $_POST['strategy'] ?? '';
$agency = $_POST['agency'] ?? '';
$target_group = $_POST['target_group'] ?? '';

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

// Search clause
$search_clause = '';
if (!empty($search_value)) {
    $search_value = $conn->real_escape_string($search_value);
    $search_clause = "AND (
        p.ProjectName LIKE '%$search_value%' OR 
        p.ProjectCode LIKE '%$search_value%' OR 
        p.AgencyName LIKE '%$search_value%' OR 
        mp.MainProjectName LIKE '%$search_value%' OR 
        s.StrategyName LIKE '%$search_value%'
    )";
}

// Query หลักสำหรับดึงข้อมูล
$main_query = "
    SELECT 
        p.ProjectID,
        p.ProjectCode,
        p.ProjectName,
        p.ProjectYear,
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
$total_records = $total_result ? $total_result->fetch_assoc()['total'] : 0;

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
        
        // ตรวจสอบและจัดรูปแบบข้อมูล
        $data[] = [
            'ProjectID' => $row['ProjectID'],
            'ProjectCode' => $row['ProjectCode'] ?: '-',
            'ProjectName' => $row['ProjectName'] ?: '-',
            'ProjectYear' => $row['ProjectYear'] ?: '-',
            'MainProjectName' => $row['MainProjectName'] ?: '-',
            'StrategyName' => $row['StrategyName'] ?: '-',
            'AgencyName' => $row['AgencyName'] ?: '-',
            'Province' => $row['Province'] ?: '-',
            'District' => $row['District'] ?: '-',
            'Subdistrict' => $row['Subdistrict'] ?: '-',
            'TotalBudget' => floatval($row['TotalBudget']),
            'IndicatorDetails' => $indicators_data,
            'ProductDetails' => $products_data,
            'TargetGroupCount' => intval($row['TargetGroupCount'])
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
