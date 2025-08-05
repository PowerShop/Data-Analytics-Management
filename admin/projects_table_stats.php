<?php
if (file_exists('./database/db.php')) {
    include './database/db.php';
} else {
    include 'db.php';
}

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

// คำนวณสถิติต่างๆ
$stats = [];

// 1. จำนวนโครงการทั้งหมด
$total_projects_query = "
    SELECT COUNT(DISTINCT p.ProjectID) as total
    FROM projects p
    WHERE 1=1 $where_clause
";
$result = $conn->query($total_projects_query);
$stats['total_projects'] = $result ? $result->fetch_assoc()['total'] : 0;

// 2. งบประมาณรวม
$total_budget_query = "
    SELECT COALESCE(SUM(bi.ApprovedAmount), 0) as total
    FROM budgetitems bi
    JOIN projects p ON bi.ProjectID = p.ProjectID
    WHERE 1=1 $where_clause
";
$result = $conn->query($total_budget_query);
$stats['total_budget'] = $result ? floatval($result->fetch_assoc()['total']) : 0;

// 3. จำนวนตัวชี้วัดทั้งหมด
$total_indicators_query = "
    SELECT COUNT(*) as total
    FROM project_indicators pi
    JOIN projects p ON pi.ProjectID = p.ProjectID
    WHERE 1=1 $where_clause
";
$result = $conn->query($total_indicators_query);
$stats['total_indicators'] = $result ? $result->fetch_assoc()['total'] : 0;

// 4. จำนวนพื้นที่ดำเนินการ (ตำบล)
$total_locations_query = "
    SELECT COUNT(CONCAT(COALESCE(pv.Province, ''), '-', COALESCE(pv.District, ''), '-', COALESCE(pv.Subdistrict, ''))) as total
    FROM projectvillages pv
    JOIN projects p ON pv.ProjectID = p.ProjectID
    WHERE pv.Subdistrict IS NOT NULL AND pv.Subdistrict != '' $where_clause
";
$result = $conn->query($total_locations_query);
$stats['total_locations'] = $result ? $result->fetch_assoc()['total'] : 0;

// 4.1 จำนวนคนในกลุ่มเป้าหมายทั้งหมด  
$total_target_people_query = "
    SELECT COALESCE(SUM(ptc.TargetCount), 0) as total
    FROM projecttargetcounts ptc
    JOIN projects p ON ptc.ProjectID = p.ProjectID
    WHERE 1=1 $where_clause
";
$result = $conn->query($total_target_people_query);
$stats['total_target_people'] = $result ? $result->fetch_assoc()['total'] : 0;

// 5. ข้อมูลเพิ่มเติม
$additional_stats_query = "
    SELECT 
        COUNT(DISTINCT mp.MainProjectID) as total_main_projects,
        COUNT(DISTINCT s.StrategyID) as total_strategies,
        COUNT(DISTINCT p.AgencyName) as total_agencies,
        COUNT(DISTINCT pp.ID) as total_products,
        COUNT(DISTINCT pe.ID) as total_enterprises,
        COUNT(DISTINCT ps.ID) as total_schools,
        COUNT(DISTINCT pn.ID) as total_networks
    FROM projects p
    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
    LEFT JOIN projectproducts pp ON p.ProjectID = pp.ProjectID
    LEFT JOIN projectenterprises pe ON p.ProjectID = pe.ProjectID
    LEFT JOIN projectschools ps ON p.ProjectID = ps.ProjectID
    LEFT JOIN projectnetworks pn ON p.ProjectID = pn.ProjectID
    WHERE 1=1 $where_clause
";
$result = $conn->query($additional_stats_query);
if ($result) {
    $additional = $result->fetch_assoc();
    $stats = array_merge($stats, [
        'total_main_projects' => $additional['total_main_projects'],
        'total_strategies' => $additional['total_strategies'],
        'total_agencies' => $additional['total_agencies'],
        'total_products' => $additional['total_products'],
        'total_enterprises' => $additional['total_enterprises'],
        'total_schools' => $additional['total_schools'],
        'total_networks' => $additional['total_networks']
    ]);
}

// 6. การกระจายตามปี
$yearly_distribution_query = "
    SELECT 
        p.ProjectYear,
        COUNT(DISTINCT p.ProjectID) as project_count,
        COALESCE(SUM(bi.ApprovedAmount), 0) as total_budget
    FROM projects p
    LEFT JOIN budgetitems bi ON p.ProjectID = bi.ProjectID
    WHERE p.ProjectYear IS NOT NULL $where_clause
    GROUP BY p.ProjectYear
    ORDER BY p.ProjectYear DESC
";
$result = $conn->query($yearly_distribution_query);
$yearly_data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $yearly_data[] = [
            'year' => $row['ProjectYear'],
            'projects' => intval($row['project_count']),
            'budget' => floatval($row['total_budget'])
        ];
    }
}
$stats['yearly_distribution'] = $yearly_data;

// 7. การกระจายตามพื้นที่
$location_distribution_query = "
    SELECT 
        pv.Province,
        pv.District,
        COUNT(DISTINCT p.ProjectID) as project_count
    FROM projectvillages pv
    JOIN projects p ON pv.ProjectID = p.ProjectID
    WHERE pv.Province IS NOT NULL AND pv.District IS NOT NULL $where_clause
    GROUP BY pv.Province, pv.District
    ORDER BY project_count DESC
    LIMIT 10
";
$result = $conn->query($location_distribution_query);
$location_data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $location_data[] = [
            'location' => $row['Province'] . ' - ' . $row['District'],
            'projects' => intval($row['project_count'])
        ];
    }
}
$stats['location_distribution'] = $location_data;

// 8. Top ตัวชี้วัด
$top_indicators_query = "
    SELECT 
        i.IndicatorName,
        i.Unit,
        COUNT(DISTINCT pi.ProjectID) as project_count,
        AVG(pi.Value) as avg_value
    FROM indicators i
    JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID
    JOIN projects p ON pi.ProjectID = p.ProjectID
    WHERE i.IsActive = 1 $where_clause
    GROUP BY i.IndicatorID, i.IndicatorName, i.Unit
    ORDER BY project_count DESC
    LIMIT 5
";
$result = $conn->query($top_indicators_query);
$indicator_data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $indicator_data[] = [
            'name' => $row['IndicatorName'],
            'unit' => $row['Unit'],
            'projects' => intval($row['project_count']),
            'avg_value' => floatval($row['avg_value'])
        ];
    }
}
$stats['top_indicators'] = $indicator_data;

// ส่งผลลัพธ์กลับในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode($stats, JSON_UNESCAPED_UNICODE);
?>
