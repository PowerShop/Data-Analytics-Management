<?php
// session_start();
if (file_exists('./database/db.php')) {
    include './database/db.php';
} else {
    include 'db.php';
}

header('Content-Type: application/json; charset=utf-8');

try {
    $card_type = $_POST['card_type'] ?? '';
    
    // สร้าง WHERE clause สำหรับ filter
    $whereConditions = [];
    $params = [];
    
    if (!empty($_POST['project_year_start'])) {
        $whereConditions[] = "p.ProjectYear >= ?";
        $params[] = $_POST['project_year_start'];
    }
    
    if (!empty($_POST['project_year_end'])) {
        $whereConditions[] = "p.ProjectYear <= ?";
        $params[] = $_POST['project_year_end'];
    }
    
    if (!empty($_POST['province'])) {
        $whereConditions[] = "pv.Province = ?";
        $params[] = $_POST['province'];
    }
    
    if (!empty($_POST['district'])) {
        $whereConditions[] = "pv.District = ?";
        $params[] = $_POST['district'];
    }
    
    if (!empty($_POST['subdistrict'])) {
        $whereConditions[] = "pv.Subdistrict = ?";
        $params[] = $_POST['subdistrict'];
    }
    
    if (!empty($_POST['village'])) {
        $whereConditions[] = "pv.VillageName = ?";
        $params[] = $_POST['village'];
    }
    
    if (!empty($_POST['main_project'])) {
        $whereConditions[] = "p.MainProjectID = ?";
        $params[] = $_POST['main_project'];
    }
    
    if (!empty($_POST['strategy'])) {
        $whereConditions[] = "p.StrategyID = ?";
        $params[] = $_POST['strategy'];
    }
    
    if (!empty($_POST['agency'])) {
        $whereConditions[] = "p.AgencyName = ?";
        $params[] = $_POST['agency'];
    }
    
    if (!empty($_POST['teacher'])) {
        $whereConditions[] = "p.ResponsiblePerson = ?";
        $params[] = $_POST['teacher'];
    }
    
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    }
    
    $data = [];
    
    switch($card_type) {
        case 'projects':
            $sql = "SELECT DISTINCT p.ProjectID, p.ProjectName, p.ProjectYear, 
                           COALESCE(SUM(b.ApprovedAmount), 0) as TotalBudget, p.ResponsiblePerson
                    FROM projects p
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID
                    $whereClause
                    GROUP BY p.ProjectID, p.ProjectName, p.ProjectYear, p.ResponsiblePerson
                    ORDER BY p.ProjectYear DESC, p.ProjectName ASC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'budget':
            // ข้อมูลงบประมาณรวมและแบ่งตามปี
            $sql = "SELECT 
                        SUM(b.ApprovedAmount) as total_approved,
                        COUNT(DISTINCT p.ProjectID) as project_count
                    FROM projects p
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID
                    $whereClause";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $budget_summary = $result->fetch_assoc();
            
            // ข้อมูลแบ่งตามปี
            $sql_by_year = "SELECT 
                                p.ProjectYear as year,
                                SUM(b.ApprovedAmount) as total_budget,
                                COUNT(DISTINCT p.ProjectID) as project_count
                            FROM projects p
                            LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                            LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                            LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                            LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID
                            $whereClause
                            GROUP BY p.ProjectYear
                            ORDER BY p.ProjectYear DESC";
            
            $stmt_year = $conn->prepare($sql_by_year);
            if (!empty($params)) {
                $stmt_year->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt_year->execute();
            $result_year = $stmt_year->get_result();
            
            $by_year = [];
            while ($row = $result_year->fetch_assoc()) {
                $by_year[] = $row;
            }
            
            $data = [
                'budget_breakdown' => [
                    'total_approved' => $budget_summary['total_approved'],
                    'project_count' => $budget_summary['project_count'],
                    'by_year' => $by_year
                ]
            ];
            break;
            
        case 'indicators':
            $sql = "SELECT DISTINCT i.IndicatorName, pi.Value as TargetValue, i.Unit, p.ProjectName, p.ProjectYear
                    FROM project_indicators pi
                    JOIN indicators i ON pi.IndicatorID = i.IndicatorID
                    JOIN projects p ON pi.ProjectID = p.ProjectID
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    $whereClause
                    ORDER BY i.IndicatorName ASC, p.ProjectYear DESC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'locations':
            $sql = "SELECT DISTINCT pv.Province, pv.District, pv.Subdistrict, pv.VillageName,
                           COUNT(DISTINCT p.ProjectID) as project_count
                    FROM projectvillages pv
                    JOIN projects p ON pv.ProjectID = p.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    $whereClause
                    GROUP BY pv.Province, pv.District, pv.Subdistrict, pv.VillageName
                    ORDER BY pv.Province ASC, pv.District ASC, pv.Subdistrict ASC, pv.VillageName ASC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'target_people':
            // รวมกลุ่มเป้าหมายที่ซ้ำกัน (เช่น นักเรียน, นักเรียนชั้น ม.1) เป็นชื่อเดียวกัน
            
            // ขั้นตอน 1: หาโครงการที่ตรงกับ filter
            if (!empty($whereConditions)) {
                $project_filter_sql = "SELECT DISTINCT p.ProjectID 
                                      FROM projects p
                                      LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                                      LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                                      LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                                      $whereClause";
                
                $stmt_filter = $conn->prepare($project_filter_sql);
                if (!empty($params)) {
                    $stmt_filter->bind_param(str_repeat('s', count($params)), ...$params);
                }
                $stmt_filter->execute();
                $result_filter = $stmt_filter->get_result();
                
                $filtered_project_ids = [];
                while ($row = $result_filter->fetch_assoc()) {
                    $filtered_project_ids[] = $row['ProjectID'];
                }
                
                if (empty($filtered_project_ids)) {
                    // ไม่มีโครงการที่ตรงกับ filter
                    break;
                }
                
                $project_ids_placeholder = str_repeat('?,', count($filtered_project_ids) - 1) . '?';
                $target_where = "WHERE ptc.ProjectID IN ($project_ids_placeholder)";
                $target_params = $filtered_project_ids;
            } else {
                $target_where = "";
                $target_params = [];
            }
            
            // ขั้นตอน 2: query ข้อมูลกลุ่มเป้าหมาย
            $sql = "SELECT 
                           CASE 
                               WHEN tg.GroupName LIKE '%นักเรียน%' THEN 'นักเรียน'
                               WHEN tg.GroupName LIKE '%ครู%' OR tg.GroupName LIKE '%อาจารย์%' THEN 'ครูและบุคลากรทางการศึกษา'
                               WHEN tg.GroupName LIKE '%เกษตรกร%' THEN 'เกษตรกร'
                               WHEN tg.GroupName LIKE '%ผู้ปกครอง%' OR tg.GroupName LIKE '%พ่อแม่%' THEN 'ผู้ปกครอง'
                               WHEN tg.GroupName LIKE '%ชุมชน%' THEN 'คนในชุมชน'
                               WHEN tg.GroupName LIKE '%เยาวชน%' THEN 'เยาวชน'
                               WHEN tg.GroupName LIKE '%ผู้สูงอายุ%' THEN 'ผู้สูงอายุ'
                               WHEN tg.GroupName LIKE '%ผู้ประกอบการ%' OR tg.GroupName LIKE '%วิสาหกิจ%' THEN 'ผู้ประกอบการ/วิสาหกิจ'
                               WHEN tg.GroupName LIKE '%ผู้นำ%' THEN 'ผู้นำ'
                               ELSE tg.GroupName
                           END as GroupName,
                           SUM(ptc.TargetCount) as TargetCount,
                           COUNT(DISTINCT ptc.ProjectID) as project_count,
                           GROUP_CONCAT(DISTINCT p.ProjectName ORDER BY p.ProjectName SEPARATOR ', ') as project_names
                    FROM projecttargetcounts ptc
                    JOIN targetgroups tg ON ptc.GroupID = tg.GroupID
                    JOIN projects p ON ptc.ProjectID = p.ProjectID
                    $target_where
                    GROUP BY CASE 
                               WHEN tg.GroupName LIKE '%นักเรียน%' THEN 'นักเรียน'
                               WHEN tg.GroupName LIKE '%ครู%' OR tg.GroupName LIKE '%อาจารย์%' THEN 'ครูและบุคลากรทางการศึกษา'
                               WHEN tg.GroupName LIKE '%เกษตรกร%' THEN 'เกษตรกร'
                               WHEN tg.GroupName LIKE '%ผู้ปกครอง%' OR tg.GroupName LIKE '%พ่อแม่%' THEN 'ผู้ปกครอง'
                               WHEN tg.GroupName LIKE '%ชุมชน%' THEN 'คนในชุมชน'
                               WHEN tg.GroupName LIKE '%เยาวชน%' THEN 'เยาวชน'
                               WHEN tg.GroupName LIKE '%ผู้สูงอายุ%' THEN 'ผู้สูงอายุ'
                               WHEN tg.GroupName LIKE '%ผู้ประกอบการ%' OR tg.GroupName LIKE '%วิสาหกิจ%' THEN 'ผู้ประกอบการ/วิสาหกิจ'
                               WHEN tg.GroupName LIKE '%ผู้นำ%' THEN 'ผู้นำ'
                               ELSE tg.GroupName
                           END
                    ORDER BY TargetCount DESC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($target_params)) {
                $stmt->bind_param(str_repeat('i', count($target_params)), ...$target_params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'products':
            $sql = "SELECT pp.ProductName, 
                           pp.ProductType,
                           COUNT(DISTINCT pp.ProjectID) as project_count,
                           GROUP_CONCAT(DISTINCT p.ProjectName ORDER BY p.ProjectName SEPARATOR ', ') as project_names,
                           GROUP_CONCAT(DISTINCT CONCAT('พ.ศ. ', p.ProjectYear) ORDER BY p.ProjectYear SEPARATOR ', ') as project_years
                    FROM projectproducts pp
                    JOIN projects p ON pp.ProjectID = p.ProjectID
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    $whereClause
                    GROUP BY pp.ProductName, pp.ProductType
                    ORDER BY project_count DESC, pp.ProductName ASC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'schools':
            $sql = "SELECT ps.SchoolName,
                           COUNT(DISTINCT ps.ProjectID) as project_count,
                           GROUP_CONCAT(DISTINCT p.ProjectName ORDER BY p.ProjectName SEPARATOR ', ') as project_names,
                           GROUP_CONCAT(DISTINCT CONCAT('พ.ศ. ', p.ProjectYear) ORDER BY p.ProjectYear SEPARATOR ', ') as project_years
                    FROM projectschools ps
                    JOIN projects p ON ps.ProjectID = p.ProjectID
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    $whereClause
                    GROUP BY ps.SchoolName
                    ORDER BY project_count DESC, ps.SchoolName ASC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'agencies':
            $sql = "SELECT p.AgencyName,
                           COUNT(DISTINCT p.ProjectID) as project_count,
                           GROUP_CONCAT(DISTINCT p.ProjectName ORDER BY p.ProjectName SEPARATOR ', ') as project_names,
                           GROUP_CONCAT(DISTINCT CONCAT('พ.ศ. ', p.ProjectYear) ORDER BY p.ProjectYear SEPARATOR ', ') as project_years,
                           COALESCE(SUM(b.ApprovedAmount), 0) as total_budget
                    FROM projects p
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    LEFT JOIN budgetitems b ON p.ProjectID = b.ProjectID
                    $whereClause
                    AND p.AgencyName IS NOT NULL AND p.AgencyName != ''
                    GROUP BY p.AgencyName
                    ORDER BY project_count DESC, p.AgencyName ASC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        case 'target_groups':
            // รวมกลุ่มเป้าหมายที่ซ้ำกัน (เช่น นักเรียน, นักเรียนชั้น ม.1) เป็นชื่อเดียวกัน
            $sql = "SELECT 
                           CASE 
                               WHEN tg.GroupName LIKE '%นักเรียน%' THEN 'นักเรียน'
                               WHEN tg.GroupName LIKE '%ครู%' OR tg.GroupName LIKE '%อาจารย์%' THEN 'ครูและบุคลากรทางการศึกษา'
                               WHEN tg.GroupName LIKE '%เกษตรกร%' THEN 'เกษตรกร'
                               WHEN tg.GroupName LIKE '%ผู้ปกครอง%' OR tg.GroupName LIKE '%พ่อแม่%' THEN 'ผู้ปกครอง'
                               WHEN tg.GroupName LIKE '%ชุมชน%' THEN 'คนในชุมชน'
                               WHEN tg.GroupName LIKE '%เยาวชน%' THEN 'เยาวชน'
                               WHEN tg.GroupName LIKE '%ผู้สูงอายุ%' THEN 'ผู้สูงอายุ'
                               WHEN tg.GroupName LIKE '%ผู้ประกอบการ%' OR tg.GroupName LIKE '%วิสาหกิจ%' THEN 'ผู้ประกอบการ/วิสาหกิจ'
                               WHEN tg.GroupName LIKE '%ผู้นำ%' THEN 'ผู้นำ'
                               ELSE tg.GroupName
                           END as GroupName,
                           COUNT(DISTINCT ptc.ProjectID) as project_count,
                           SUM(ptc.TargetCount) as total_count,
                           GROUP_CONCAT(DISTINCT p.ProjectName ORDER BY p.ProjectName SEPARATOR ', ') as project_names
                    FROM targetgroups tg
                    JOIN projecttargetcounts ptc ON tg.GroupID = ptc.GroupID
                    JOIN projects p ON ptc.ProjectID = p.ProjectID
                    LEFT JOIN projectvillages pv ON p.ProjectID = pv.ProjectID
                    LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
                    LEFT JOIN strategies s ON p.StrategyID = s.StrategyID
                    $whereClause
                    GROUP BY CASE 
                               WHEN tg.GroupName LIKE '%นักเรียน%' THEN 'นักเรียน'
                               WHEN tg.GroupName LIKE '%ครู%' OR tg.GroupName LIKE '%อาจารย์%' THEN 'ครูและบุคลากรทางการศึกษา'
                               WHEN tg.GroupName LIKE '%เกษตรกร%' THEN 'เกษตรกร'
                               WHEN tg.GroupName LIKE '%ผู้ปกครอง%' OR tg.GroupName LIKE '%พ่อแม่%' THEN 'ผู้ปกครอง'
                               WHEN tg.GroupName LIKE '%ชุมชน%' THEN 'คนในชุมชน'
                               WHEN tg.GroupName LIKE '%เยาวชน%' THEN 'เยาวชน'
                               WHEN tg.GroupName LIKE '%ผู้สูงอายุ%' THEN 'ผู้สูงอายุ'
                               WHEN tg.GroupName LIKE '%ผู้ประกอบการ%' OR tg.GroupName LIKE '%วิสาหกิจ%' THEN 'ผู้ประกอบการ/วิสาหกิจ'
                               WHEN tg.GroupName LIKE '%ผู้นำ%' THEN 'ผู้นำ'
                               ELSE tg.GroupName
                           END
                    ORDER BY total_count DESC";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            break;
            
        default:
            throw new Exception('ประเภทข้อมูลไม่ถูกต้อง');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'message' => 'โหลดข้อมูลสำเร็จ'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
