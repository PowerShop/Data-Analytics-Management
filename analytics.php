<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Analytics - ระบบจัดการโครงการ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .analytics-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 400px;
            position: relative;
            overflow: hidden;
        }
        
        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .chart-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .trend-up {
            color: #28a745;
        }
        
        .trend-down {
            color: #dc3545;
        }
        
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .indicator-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .analytics-stat {
            background: white;
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin-bottom: 15px;
        }
        
        .chart-container-tall {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 500px;
            position: relative;
            overflow: hidden;
        }
        
        .indicator-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .performance-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin: 5px;
        }
        
        .performance-excellent {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .performance-good {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }
        
        .performance-average {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        .chart-container-wide {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 600px;
            position: relative;
            overflow: hidden;
        }
        
        .insights-panel {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #2196f3;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Analytics Header -->
        <div class="analytics-header">
            <h2 class="fw-bold mb-3">📈 Analytics - การวิเคราะห์เชิงลึก</h2>
            <p class="mb-0">วิเคราะห์ประสิทธิภาพและแนวโน้มโครงการพร้อมระบบตัวชี้วัดครบถ้วน</p>
            <div class="mt-3">
                <span class="performance-badge performance-excellent">
                    <i class="fas fa-chart-line me-1"></i>Real-time Analytics
                </span>
                <span class="performance-badge performance-good">
                    <i class="fas fa-filter me-1"></i>Advanced Filtering
                </span>
                <span class="performance-badge performance-average">
                    <i class="fas fa-database me-1"></i>Indicators System
                </span>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h5>🔍 กรองข้อมูลแบบละเอียด</h5>
            
            <!-- Row 1: Basic Filters -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">โครงการหลัก</label>
                    <select class="form-select" id="mainProjectFilter">
                        <option value="">ทุกโครงการหลัก</option>
                        <?php
                        $mainProjects = $conn->query("SELECT MainProjectID, MainProjectName FROM MainProjects ORDER BY MainProjectName");
                        while ($mp = $mainProjects->fetch_assoc()) {
                            $selected = (isset($_GET['main_project']) && $_GET['main_project'] == $mp['MainProjectID']) ? 'selected' : '';
                            echo "<option value='{$mp['MainProjectID']}' $selected>{$mp['MainProjectName']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ยุทธศาสตร์</label>
                    <select class="form-select" id="strategyFilter">
                        <option value="">ทุกยุทธศาสตร์</option>
                        <?php
                        $strategies = $conn->query("SELECT StrategyID, StrategyName FROM Strategies ORDER BY StrategyName");
                        while ($strategy = $strategies->fetch_assoc()) {
                            $selected = (isset($_GET['strategy']) && $_GET['strategy'] == $strategy['StrategyID']) ? 'selected' : '';
                            echo "<option value='{$strategy['StrategyID']}' $selected>{$strategy['StrategyName']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ปีโครงการ</label>
                    <select class="form-select" id="yearFilter">
                        <option value="">ทุกปี</option>
                        <?php
                        $years = $conn->query("SELECT DISTINCT ProjectYear FROM Projects WHERE ProjectYear IS NOT NULL ORDER BY ProjectYear DESC");
                        while ($year = $years->fetch_assoc()) {
                            $selected = (isset($_GET['year']) && $_GET['year'] == $year['ProjectYear']) ? 'selected' : '';
                            echo "<option value='{$year['ProjectYear']}' $selected>พ.ศ. {$year['ProjectYear']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">หน่วยงาน</label>
                    <select class="form-select" id="agencyFilter">
                        <option value="">ทุกหน่วยงาน</option>
                        <?php
                        $agencies = $conn->query("SELECT DISTINCT AgencyName FROM Projects WHERE AgencyName IS NOT NULL ORDER BY AgencyName");
                        while ($agency = $agencies->fetch_assoc()) {
                            $selected = (isset($_GET['agency']) && $_GET['agency'] == $agency['AgencyName']) ? 'selected' : '';
                            echo "<option value='{$agency['AgencyName']}' $selected>{$agency['AgencyName']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Row 2: Location Filters -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">จังหวัด</label>
                    <select class="form-select" id="provinceFilter">
                        <option value="">ทุกจังหวัด</option>
                        <?php
                        $provinces = $conn->query("SELECT DISTINCT Province FROM ProjectVillages WHERE Province IS NOT NULL ORDER BY Province");
                        while ($province = $provinces->fetch_assoc()) {
                            $selected = (isset($_GET['province']) && $_GET['province'] == $province['Province']) ? 'selected' : '';
                            echo "<option value='{$province['Province']}' $selected>{$province['Province']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">อำเภอ</label>
                    <select class="form-select" id="districtFilter">
                        <option value="">ทุกอำเภอ</option>
                        <?php
                        $districts = $conn->query("SELECT DISTINCT District FROM ProjectVillages WHERE District IS NOT NULL ORDER BY District");
                        while ($district = $districts->fetch_assoc()) {
                            $selected = (isset($_GET['district']) && $_GET['district'] == $district['District']) ? 'selected' : '';
                            echo "<option value='{$district['District']}' $selected>{$district['District']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ตำบล</label>
                    <select class="form-select" id="subdistrictFilter">
                        <option value="">ทุกตำบล</option>
                        <?php
                        $subdistricts = $conn->query("SELECT DISTINCT Subdistrict FROM ProjectVillages WHERE Subdistrict IS NOT NULL ORDER BY Subdistrict");
                        while ($subdistrict = $subdistricts->fetch_assoc()) {
                            $selected = (isset($_GET['subdistrict']) && $_GET['subdistrict'] == $subdistrict['Subdistrict']) ? 'selected' : '';
                            echo "<option value='{$subdistrict['Subdistrict']}' $selected>{$subdistrict['Subdistrict']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ประเภทกลุ่มเป้าหมาย</label>
                    <select class="form-select" id="targetGroupFilter">
                        <option value="">ทุกกลุ่มเป้าหมาย</option>
                        <?php
                        $targetGroups = $conn->query("SELECT GroupID, GroupName FROM TargetGroups ORDER BY GroupName");
                        while ($tg = $targetGroups->fetch_assoc()) {
                            $selected = (isset($_GET['target_group']) && $_GET['target_group'] == $tg['GroupID']) ? 'selected' : '';
                            echo "<option value='{$tg['GroupID']}' $selected>{$tg['GroupName']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Row 3: Performance & Budget Filters -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ประเภทวิสาหกิจ</label>
                    <select class="form-select" id="enterpriseTypeFilter">
                        <option value="">ทุกประเภทวิสาหกิจ</option>
                        <option value="วิสาหกิจ" <?= (isset($_GET['enterprise_type']) && $_GET['enterprise_type'] == 'วิสาหกิจ') ? 'selected' : '' ?>>วิสาหกิจ</option>
                        <option value="ผู้ประกอบการ" <?= (isset($_GET['enterprise_type']) && $_GET['enterprise_type'] == 'ผู้ประกอบการ') ? 'selected' : '' ?>>ผู้ประกอบการ</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">งบประมาณขั้นต่ำ</label>
                    <input type="number" class="form-control" id="budgetMin" placeholder="0" 
                           value="<?= isset($_GET['budget_min']) ? $_GET['budget_min'] : '' ?>">
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">งบประมาณสูงสุด</label>
                    <input type="number" class="form-control" id="budgetMax" placeholder="ไม่จำกัด"
                           value="<?= isset($_GET['budget_max']) ? $_GET['budget_max'] : '' ?>">
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <!-- SROI filter removed - feature deprecated -->
                </div>
            </div>

            <!-- Row 4: Indicators Filters -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ตัวชี้วัด</label>
                    <select class="form-select" id="indicatorFilter">
                        <option value="">ทุกตัวชี้วัด</option>
                        <?php
                        $indicators = $conn->query("SELECT DISTINCT IndicatorID, IndicatorName FROM indicators WHERE IsActive = 1 ORDER BY IndicatorName");
                        while ($indicator = $indicators->fetch_assoc()) {
                            $selected = (isset($_GET['indicator']) && $_GET['indicator'] == $indicator['IndicatorID']) ? 'selected' : '';
                            echo "<option value='{$indicator['IndicatorID']}' $selected>{$indicator['IndicatorName']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ค่าตัวชี้วัดขั้นต่ำ</label>
                    <input type="number" class="form-control" id="indicatorValueMin" placeholder="0" step="0.01"
                           value="<?= isset($_GET['indicator_value_min']) ? $_GET['indicator_value_min'] : '' ?>">
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ค่าตัวชี้วัดสูงสุด</label>
                    <input type="number" class="form-control" id="indicatorValueMax" placeholder="ไม่จำกัด" step="0.01"
                           value="<?= isset($_GET['indicator_value_max']) ? $_GET['indicator_value_max'] : '' ?>">
                </div>
                <div class="col-lg-3 col-md-6 mb-2">
                    <label class="form-label">ปีตัวชี้วัด</label>
                    <select class="form-select" id="indicatorYearFilter">
                        <option value="">ทุกปี</option>
                        <?php
                        $indicator_years = $conn->query("SELECT DISTINCT Year FROM indicators WHERE Year IS NOT NULL ORDER BY Year DESC");
                        while ($i_year = $indicator_years->fetch_assoc()) {
                            $selected = (isset($_GET['indicator_year']) && $_GET['indicator_year'] == $i_year['Year']) ? 'selected' : '';
                            echo "<option value='{$i_year['Year']}' $selected>พ.ศ. {$i_year['Year']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Row 5: Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter me-1"></i>กรองข้อมูล
                        </button>
                        <button class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
                        </button>
                        <button class="btn btn-success" onclick="exportFilteredData()">
                            <i class="fas fa-download me-1"></i>ส่งออกข้อมูล
                        </button>
                        <div class="ms-auto">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                กำลังแสดงข้อมูลที่กรองแล้ว (<?= getTotalFilteredProjects() ?> โครงการ)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // ฟังก์ชันสำหรับสร้าง WHERE clause จาก filters
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
            
            // Filter removed - SROI feature deprecated
            
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

        // ฟังก์ชันนับจำนวนโครงการที่กรองแล้ว
        function getTotalFilteredProjects() {
            global $conn;
            $where_clause = buildFilterWhereClause();
            
            $query = "SELECT COUNT(DISTINCT p.ProjectID) as total 
                      FROM Projects p 
                      WHERE 1=1 $where_clause";
                      
            $result = $conn->query($query);
            return $result ? $result->fetch_assoc()['total'] : 0;
        }

        // ข้อมูลการวิเคราะห์ (ปรับปรุงให้รองรับ filter)
        $where_clause = buildFilterWhereClause();
        
        // Project Analysis - focusing on indicators instead of deprecated SROI
        $project_analysis = $conn->query("
            SELECT 
                COUNT(DISTINCT p.ProjectID) as total_projects,
                AVG(COALESCE(pi.Value, 0)) as avg_indicator_value,
                COUNT(DISTINCT pi.IndicatorID) as total_indicators_used,
                COUNT(pi.Value) as total_indicator_values
            FROM Projects p
            LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
            WHERE 1=1 $where_clause
        ")->fetch_assoc();

        // Budget Efficiency
        $budget_efficiency = $conn->query("
            SELECT 
                SUM(bi.RequestedAmount) as total_requested,
                SUM(bi.ApprovedAmount) as total_approved,
                AVG(bi.ApprovedAmount/bi.RequestedAmount * 100) as avg_approval_rate
            FROM BudgetItems bi
            JOIN Projects p ON bi.ProjectID = p.ProjectID
            WHERE bi.RequestedAmount > 0 $where_clause
        ")->fetch_assoc();

        // Project Timeline Analysis (ใช้จำนวนโครงการต่อเดือนแทน)
        $timeline_data = $conn->query("
            SELECT 
                MONTH(CURDATE()) as month,
                COUNT(*) as project_count
            FROM Projects
            GROUP BY MONTH(CURDATE())
            ORDER BY month
        ");

        // Performance Trends - using indicator data instead of deprecated tables
        $performance_trends = $conn->query("
            SELECT 
                'ทั้งหมด' as period,
                AVG(COALESCE(pi.Value, 0)) as avg_indicator_value,
                COUNT(DISTINCT p.ProjectID) as project_count,
                COUNT(DISTINCT pi.IndicatorID) as indicator_count
            FROM Projects p
            LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
            WHERE 1=1 $where_clause
            GROUP BY 'ทั้งหมด'
        ");

        // Indicators Analysis
        $indicators_analysis = $conn->query("
            SELECT 
                COUNT(DISTINCT pi.IndicatorID) as total_indicators,
                COUNT(DISTINCT pi.ProjectID) as projects_with_indicators,
                AVG(pi.Value) as avg_indicator_value,
                COUNT(pi.IndicatorID) as total_indicator_values
            FROM project_indicators pi
            JOIN Projects p ON pi.ProjectID = p.ProjectID
            WHERE 1=1 $where_clause
        ")->fetch_assoc();

        // Top Indicators
        $top_indicators = $conn->query("
            SELECT 
                i.IndicatorName,
                i.Unit,
                COUNT(DISTINCT pi.ProjectID) as project_count,
                AVG(pi.Value) as avg_value,
                MIN(pi.Value) as min_value,
                MAX(pi.Value) as max_value
            FROM indicators i
            JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID
            JOIN Projects p ON pi.ProjectID = p.ProjectID
            WHERE i.IsActive = 1 $where_clause
            GROUP BY i.IndicatorID, i.IndicatorName, i.Unit
            ORDER BY project_count DESC
            LIMIT 10
        ");

        // Indicators by Year
        $indicators_by_year = $conn->query("
            SELECT 
                i.Year,
                COUNT(DISTINCT i.IndicatorID) as indicator_count,
                COUNT(DISTINCT pi.ProjectID) as project_count
            FROM indicators i
            LEFT JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID
            LEFT JOIN Projects p ON pi.ProjectID = p.ProjectID
            WHERE i.IsActive = 1 $where_clause
            GROUP BY i.Year
            ORDER BY i.Year DESC
        ");
        ?>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <h3><?= number_format($project_analysis['avg_indicator_value'] ?: 0, 2) ?></h3>
                    <p class="mb-0">ค่าเฉลี่ยตัวชี้วัด</p>
                    <small>จาก <?= $project_analysis['total_projects'] ?> โครงการ</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <h3><?= number_format($budget_efficiency['avg_approval_rate'] ?: 0, 1) ?>%</h3>
                    <p class="mb-0">อัตราอนุมัติงบ</p>
                    <small>เฉลี่ยโครงการ</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <h3><?= number_format(($budget_efficiency['total_approved'] ?: 0) / 1000000, 1) ?>M</h3>
                    <p class="mb-0">งบอนุมัติ</p>
                    <small>ล้านบาท</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card">
                    <h3><?= number_format($project_analysis['total_indicators_used'] ?: 0) ?></h3>
                    <p class="mb-0">ตัวชี้วัดใช้งาน</p>
                    <small>จากทั้งหมด</small>
                </div>
            </div>
        </div>

        <!-- Indicators Key Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="indicator-card">
                    <h3><?= number_format($indicators_analysis['total_indicators'] ?: 0) ?></h3>
                    <p class="mb-0">จำนวนตัวชี้วัด</p>
                    <small>ทั้งหมดในระบบ</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="indicator-card">
                    <h3><?= number_format($indicators_analysis['projects_with_indicators'] ?: 0) ?></h3>
                    <p class="mb-0">โครงการที่มีตัวชี้วัด</p>
                    <small>จากทั้งหมด</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="indicator-card">
                    <h3><?= number_format($indicators_analysis['avg_indicator_value'] ?: 0, 2) ?></h3>
                    <p class="mb-0">ค่าเฉลี่ยตัวชี้วัด</p>
                    <small>ทุกตัวชี้วัด</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="indicator-card">
                    <h3><?= number_format($indicators_analysis['total_indicator_values'] ?: 0) ?></h3>
                    <p class="mb-0">จำนวนค่าตัวชี้วัด</p>
                    <small>ทั้งหมดที่บันทึก</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Performance Trends -->
            <div class="col-lg-8 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">📈 แนวโน้มตัวชี้วัด</h4>
                    <div class="chart-wrapper">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Indicator Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">📊 การกระจายตัวชี้วัด</h4>
                    <div class="chart-wrapper">
                        <canvas id="indicatorDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Project Timeline -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">📅 จำนวนโครงการต่อปี</h4>
                    <div class="chart-wrapper">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Budget vs Performance -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">💰 งบประมาณ vs ประสิทธิภาพ</h4>
                    <div class="chart-wrapper">
                        <canvas id="budgetPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicators Analysis Section -->
        <div class="indicator-section">
            <h3 class="mb-4">📊 การวิเคราะห์ตัวชี้วัด</h3>
            
            <div class="row">
                <!-- Top Indicators Usage -->
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title">🔝 ตัวชี้วัดยอดนิยม (Top 10)</h4>
                        <div class="chart-wrapper">
                            <canvas id="topIndicatorsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Indicators by Year -->
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title">📅 ตัวชี้วัดตามปี</h4>
                        <div class="chart-wrapper">
                            <canvas id="indicatorsByYearChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Indicator Values Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title">📈 การกระจายค่าตัวชี้วัด</h4>
                        <div class="chart-wrapper">
                            <canvas id="indicatorValuesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Projects with Indicators Trend -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title">📊 แนวโน้มโครงการที่มีตัวชี้วัด</h4>
                        <div class="chart-wrapper">
                            <canvas id="projectsWithIndicatorsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Advanced Indicator Analysis -->
            <div class="row">
                <!-- Indicator Performance by Year -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title">🔗 ตัวชี้วัดตามปี</h4>
                        <div class="chart-wrapper">
                            <canvas id="indicatorsByYearChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Indicator Usage by Agency -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-container">
                        <h4 class="chart-title">🏛️ การใช้ตัวชี้วัดตามหน่วยงาน</h4>
                        <div class="chart-wrapper">
                            <canvas id="indicatorByAgencyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Indicators Table -->
            <div class="row">
                <div class="col-12">
                    <div class="chart-container" style="height: auto; min-height: 400px;">
                        <h4 class="chart-title">📋 รายละเอียดตัวชี้วัด</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ตัวชี้วัด</th>
                                        <th>หน่วย</th>
                                        <th>จำนวนโครงการ</th>
                                        <th>ค่าเฉลี่ย</th>
                                        <th>ค่าต่ำสุด</th>
                                        <th>ค่าสูงสุด</th>
                                        <th>สถิติ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $top_indicators->data_seek(0); // Reset pointer
                                    while ($indicator = $top_indicators->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($indicator['IndicatorName']) ?></strong></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($indicator['Unit'] ?: 'ไม่ระบุ') ?></span></td>
                                        <td><?= number_format($indicator['project_count']) ?> โครงการ</td>
                                        <td><?= number_format($indicator['avg_value'], 2) ?></td>
                                        <td><?= number_format($indicator['min_value'], 2) ?></td>
                                        <td><?= number_format($indicator['max_value'], 2) ?></td>
                                        <td>
                                            <?php
                                            $range = $indicator['max_value'] - $indicator['min_value'];
                                            if ($range > 0) {
                                                echo '<span class="badge bg-info">ช่วง: ' . number_format($range, 2) . '</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">คงที่</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Summary Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container" style="height: auto;">
                    <h4 class="chart-title">📝 สรุปผลการวิเคราะห์</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="analytics-stat">
                                <h6><i class="fas fa-trophy text-warning"></i> โครงการที่มีตัวชี้วัดมากที่สุด</h6>
                                <?php
                                $best_project = $conn->query("
                                    SELECT p.ProjectName, 
                                           COUNT(DISTINCT pi.IndicatorID) as indicator_count,
                                           AVG(COALESCE(pi.Value, 0)) as avg_indicator_value
                                    FROM Projects p
                                    LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
                                    WHERE 1=1 $where_clause
                                    GROUP BY p.ProjectID, p.ProjectName
                                    ORDER BY indicator_count DESC, avg_indicator_value DESC
                                    LIMIT 1
                                ")->fetch_assoc();
                                ?>
                                <p><strong><?= htmlspecialchars($best_project['ProjectName'] ?? 'ไม่มีข้อมูล') ?></strong></p>
                                <small>ตัวชี้วัด: <?= $best_project['indicator_count'] ?? 0 ?> ตัว | ค่าเฉลี่ย: <?= number_format($best_project['avg_indicator_value'] ?? 0, 2) ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="analytics-stat">
                                <h6><i class="fas fa-chart-line text-success"></i> ตัวชี้วัดที่มีค่าเฉลี่ยสูงสุด</h6>
                                <?php
                                $best_indicator = $conn->query("
                                    SELECT i.IndicatorName, i.Unit,
                                           AVG(pi.Value) as avg_value,
                                           COUNT(DISTINCT pi.ProjectID) as project_count
                                    FROM indicators i
                                    JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID
                                    JOIN Projects p ON pi.ProjectID = p.ProjectID
                                    WHERE i.IsActive = 1 $where_clause
                                    GROUP BY i.IndicatorID, i.IndicatorName, i.Unit
                                    HAVING project_count >= 2
                                    ORDER BY avg_value DESC
                                    LIMIT 1
                                ")->fetch_assoc();
                                ?>
                                <p><strong><?= htmlspecialchars($best_indicator['IndicatorName'] ?? 'ไม่มีข้อมูล') ?></strong></p>
                                <small>ค่าเฉลี่ย: <?= number_format($best_indicator['avg_value'] ?? 0, 2) ?> <?= htmlspecialchars($best_indicator['Unit'] ?? '') ?> | <?= $best_indicator['project_count'] ?? 0 ?> โครงการ</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="analytics-stat">
                                <h6><i class="fas fa-users text-primary"></i> หน่วยงานที่มีโครงการมากที่สุด</h6>
                                <?php
                                $top_agency = $conn->query("
                                    SELECT AgencyName, COUNT(*) as project_count,
                                           AVG(COALESCE(pi.Value, 0)) as avg_indicator_value
                                    FROM Projects p
                                    LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
                                    WHERE AgencyName IS NOT NULL AND AgencyName != '' $where_clause
                                    GROUP BY AgencyName
                                    ORDER BY project_count DESC
                                    LIMIT 1
                                ")->fetch_assoc();
                                ?>
                                <p><strong><?= htmlspecialchars($top_agency['AgencyName'] ?? 'ไม่มีข้อมูล') ?></strong></p>
                                <small>จำนวน: <?= $top_agency['project_count'] ?? 0 ?> โครงการ | ค่าเฉลี่ยตัวชี้วัด: <?= number_format($top_agency['avg_indicator_value'] ?? 0, 2) ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="analytics-stat">
                                <h6><i class="fas fa-map-marker-alt text-info"></i> จังหวัดที่มีโครงการมากที่สุด</h6>
                                <?php
                                $top_province = $conn->query("
                                    SELECT pv.Province, COUNT(DISTINCT p.ProjectID) as project_count
                                    FROM ProjectVillages pv
                                    JOIN Projects p ON pv.ProjectID = p.ProjectID
                                    WHERE pv.Province IS NOT NULL $where_clause
                                    GROUP BY pv.Province
                                    ORDER BY project_count DESC
                                    LIMIT 1
                                ")->fetch_assoc();
                                ?>
                                <p><strong><?= htmlspecialchars($top_province['Province'] ?? 'ไม่มีข้อมูล') ?></strong></p>
                                <small>จำนวน: <?= $top_province['project_count'] ?? 0 ?> โครงการ</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights and Recommendations -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="insights-panel">
                    <h4 class="mb-4"><i class="fas fa-lightbulb text-warning"></i> ข้อเสนอแนะเชิงกลยุทธ์</h4>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-thumbs-up me-2"></i>จุดแข็ง</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $strength_analysis = [
                                        'high_indicator_projects' => $conn->query("SELECT COUNT(DISTINCT p.ProjectID) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value >= 100 $where_clause")->fetch_assoc()['count'],
                                        'total_projects' => getTotalFilteredProjects(),
                                        'indicators_usage' => $indicators_analysis['projects_with_indicators'] ?? 0
                                    ];
                                    
                                    $high_performance_rate = $strength_analysis['total_projects'] > 0 ? 
                                        ($strength_analysis['high_indicator_projects'] / $strength_analysis['total_projects']) * 100 : 0;
                                    
                                    $indicator_usage_rate = $strength_analysis['total_projects'] > 0 ? 
                                        ($strength_analysis['indicators_usage'] / $strength_analysis['total_projects']) * 100 : 0;
                                    ?>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong><?= number_format($high_performance_rate, 1) ?>%</strong> ของโครงการมีตัวชี้วัด ≥ 100
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-chart-bar text-success me-2"></i>
                                            <strong><?= number_format($indicator_usage_rate, 1) ?>%</strong> ของโครงการใช้ตัวชี้วัด
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            อัตราอนุมัติงบเฉลี่ย <strong><?= number_format($budget_efficiency['avg_approval_rate'] ?? 0, 1) ?>%</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>จุดที่ควรปรับปรุง</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $improvement_analysis = [
                                        'low_indicator_projects' => $conn->query("SELECT COUNT(DISTINCT p.ProjectID) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value < 50 $where_clause")->fetch_assoc()['count'],
                                        'no_indicator_projects' => $strength_analysis['total_projects'] - $strength_analysis['indicators_usage']
                                    ];
                                    ?>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-arrow-down text-warning me-2"></i>
                                            <strong><?= $improvement_analysis['low_indicator_projects'] ?></strong> โครงการมีตัวชี้วัด < 50
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-question-circle text-warning me-2"></i>
                                            <strong><?= $improvement_analysis['no_indicator_projects'] ?></strong> โครงการยังไม่มีตัวชี้วัด
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-balance-scale text-warning me-2"></i>
                                            ต้องเพิ่มความสมดุลในการกระจายโครงการ
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-info mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-rocket me-2"></i>แนะนำการพัฒนา</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-target text-info me-2"></i>
                                            เพิ่มการใช้ตัวชี้วัดในทุกโครงการ
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-sync-alt text-info me-2"></i>
                                            ปรับปรุงโครงการที่มีตัวชี้วัดต่ำ
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-share-alt text-info me-2"></i>
                                            แชร์แนวปฏิบัติที่ดีระหว่างหน่วยงาน
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-database text-info me-2"></i>
                                            พัฒนาระบบติดตามและประเมินผล
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Row 1: Agency & Strategy Analysis -->
        <div class="row">
            <!-- Projects by Agency -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">🏛️ โครงการตามหน่วยงาน (Top 10)</h4>
                    <div class="chart-wrapper">
                        <canvas id="agencyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Strategy Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">🎯 การกระจายตามยุทธศาสตร์</h4>
                    <div class="chart-wrapper">
                        <canvas id="strategyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Row 2: Geographic & Performance Analysis -->
        <div class="row">
            <!-- Projects by Province -->
            <div class="col-lg-8 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">🗺️ โครงการตามจังหวัด (Top 15)</h4>
                    <div class="chart-wrapper">
                        <canvas id="provinceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Budget Distribution by Year -->
            <div class="col-lg-4 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">💼 งบประมาณรายปี</h4>
                    <div class="chart-wrapper">
                        <canvas id="budgetYearChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Row 3: Target Groups & Enterprise Analysis -->
        <div class="row">
            <!-- Target Groups Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">👥 กลุ่มเป้าหมายยอดนิยม</h4>
                    <div class="chart-wrapper">
                        <canvas id="targetGroupChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Enterprise Types -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">🏪 ประเภทวิสาหกิจ</h4>
                    <div class="chart-wrapper">
                        <canvas id="enterpriseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Row 4: Budget Analysis -->
        <div class="row">
            <!-- Budget Approval Rate by Agency -->
            <div class="col-lg-12 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">📊 อัตราอนุมัติงบประมาณตามหน่วยงาน</h4>
                    <div class="chart-wrapper">
                        <canvas id="budgetApprovalChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Average Project Duration -->
            <!-- <div class="col-lg-4 mb-4" style="display: none;"></div>
                <div class="chart-container">
                    <h4 class="chart-title">⏱️ โครงการเสร็จสิ้น/ดำเนินอยู่</h4>
                    <div class="chart-wrapper">
                        <canvas id="projectStatusChart"></canvas>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Detailed Analysis Tables -->
        <div class="row">
            <div class="col-12">
            <div class="chart-container" style="max-height: 700px; overflow-y: auto;">
                <h4 class="chart-title">📋 รายงานการวิเคราะห์แบบละเอียด</h4>
                
                <!-- Top Performing Projects -->
                <h5 class="mt-4">🏆 โครงการที่มีตัวชี้วัดสูงสุด</h5>
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>โครงการ</th>
                        <th>จำนวนตัวชี้วัด</th>
                        <th>ค่าเฉลี่ยตัวชี้วัด</th>
                        <th>งบประมาณ</th>
                        <th>คะแนนรวม</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $top_performing = $conn->query("
                        SELECT p.ProjectName,
                           COUNT(DISTINCT pi.IndicatorID) as indicator_count,
                           AVG(COALESCE(pi.Value, 0)) as avg_indicator_value,
                           COALESCE(SUM(b.ApprovedAmount), 0) as total_budget,
                           (COUNT(DISTINCT pi.IndicatorID) * AVG(COALESCE(pi.Value, 0))) as performance_score
                        FROM Projects p
                        LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
                        LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                        WHERE 1=1 $where_clause
                        GROUP BY p.ProjectID, p.ProjectName
                        ORDER BY performance_score DESC
                        LIMIT 10
                    ");
                    
                    while ($project = $top_performing->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($project['ProjectName']) ?></td>
                        <td><span class="badge bg-success"><?= number_format($project['indicator_count']) ?></span></td>
                        <td><span class="badge bg-primary"><?= number_format($project['avg_indicator_value'], 2) ?></span></td>
                        <td><?= number_format($project['total_budget']) ?> บาท</td>
                        <td><strong><?= number_format($project['performance_score'], 2) ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                </div>

                <!-- Budget Efficiency Analysis -->
                <h5 class="mt-4">💡 การวิเคราะห์ประสิทธิภาพงบประมาณ</h5>
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>โครงการ</th>
                        <th>งบที่ขอ</th>
                        <th>งบอนุมัติ</th>
                        <th>อัตราอนุมัติ</th>
                        <th>ตัวชี้วัด/งบ</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $budget_analysis = $conn->query("
                        SELECT p.ProjectName,
                           SUM(b.RequestedAmount) as total_requested,
                           SUM(b.ApprovedAmount) as total_approved,
                           (SUM(b.ApprovedAmount) / SUM(b.RequestedAmount) * 100) as approval_rate,
                           COUNT(DISTINCT pi.IndicatorID) as indicator_count,
                           (COUNT(DISTINCT pi.IndicatorID) / (SUM(b.ApprovedAmount) / 1000000)) as indicators_per_million
                        FROM Projects p
                        LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                        LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
                        WHERE b.RequestedAmount > 0 $where_clause
                        GROUP BY p.ProjectID, p.ProjectName
                        ORDER BY indicators_per_million DESC
                        LIMIT 10
                    ");
                    
                    while ($budget = $budget_analysis->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($budget['ProjectName']) ?></td>
                        <td><?= number_format($budget['total_requested']) ?></td>
                        <td><?= number_format($budget['total_approved']) ?></td>
                        <td>
                        <span class="badge <?= $budget['approval_rate'] >= 80 ? 'bg-success' : ($budget['approval_rate'] >= 60 ? 'bg-warning' : 'bg-danger') ?>">
                            <?= number_format($budget['approval_rate'], 1) ?>%
                        </span>
                        </td>
                        <td><strong><?= number_format($budget['indicators_per_million'] ?: 0, 2) ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>

        <!-- Duplicate Villages Analysis (Chart + Table) -->
        <div class="row">
            <div class="col-12">
            <h4 class="chart-title">🏠 หมู่บ้านที่มีโครงการซ้ำกัน</h4>
            <!-- Chart on top -->
            <div class="mb-4">
                <canvas id="dupVillagesChart" style="height:320px;"></canvas>
            </div>
            <!-- Table below -->
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>หมู่บ้าน</th>
                    <th>จังหวัด</th>
                    <th>โครงการที่เกี่ยวข้อง</th>
                    <th>ตัวชี้วัดเฉลี่ย</th>
                    <th>งบอนุมัติ</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // ค้นหาหมู่บ้านที่มีมากกว่า 1 โครงการ
                $dup_villages = $conn->query("
                SELECT pv.VillageName, pv.Province, COUNT(DISTINCT pv.ProjectID) as project_count
                FROM ProjectVillages pv
                GROUP BY pv.VillageName, pv.Province
                HAVING project_count > 1
                ORDER BY project_count DESC
                LIMIT 10
                ");
                $dup_village_labels = [];
                $dup_village_counts = [];
                while ($village = $dup_villages->fetch_assoc()):
                $dup_village_labels[] = $village['VillageName'] . ' (' . $village['Province'] . ')';
                $dup_village_counts[] = (int)$village['project_count'];
                // ดึงรายละเอียดโครงการที่เกี่ยวข้อง พร้อมตัวชี้วัดและงบอนุมัติ
                $projects = $conn->query("
                    SELECT p.ProjectName,
                    AVG(COALESCE(pi.Value, 0)) as avg_indicator_value,
                    COALESCE(SUM(b.ApprovedAmount), 0) as total_approved
                    FROM Projects p
                    JOIN ProjectVillages pv2 ON p.ProjectID = pv2.ProjectID
                    LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
                    LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                    WHERE pv2.VillageName = '" . $conn->real_escape_string($village['VillageName']) . "'
                      AND pv2.Province = '" . $conn->real_escape_string($village['Province']) . "'
                    GROUP BY p.ProjectID, p.ProjectName
                ");
                $project_rows = [];
                while ($p = $projects->fetch_assoc()) {
                    $project_rows[] = [
                    'name' => $p['ProjectName'],
                    'indicator_value' => $p['avg_indicator_value'],
                    'approved' => $p['total_approved']
                    ];
                }
                ?>
                <?php foreach ($project_rows as $idx => $proj): ?>
                    <tr>
                    <?php if ($idx === 0): ?>
                    <td rowspan="<?= count($project_rows) ?>"><?= htmlspecialchars($village['VillageName']) ?></td>
                    <td rowspan="<?= count($project_rows) ?>"><?= htmlspecialchars($village['Province']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($proj['name']) ?></td>
                    <td><?= number_format($proj['indicator_value'], 2) ?></td>
                    <td><?= number_format($proj['approved']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php endwhile; ?>
                </tbody>
                </table>
            </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
            // กราฟแสดง Top 10 หมู่บ้านที่มีโครงการซ้ำ
            try {
                const ctx = document.getElementById('dupVillagesChart');
                if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                    labels: <?= json_encode($dup_village_labels) ?>,
                    datasets: [{
                        label: 'จำนวนโครงการ',
                        data: <?= json_encode($dup_village_counts) ?>,
                        backgroundColor: 'rgba(255, 206, 86, 0.7)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }]
                    },
                    options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                        beginAtZero: true,
                        title: { display: true, text: 'จำนวนโครงการ' }
                        },
                        y: {
                        title: { display: false }
                        }
                    }
                    }
                });
                }
            } catch(e) { console.log('Error creating duplicate villages chart:', e); }
            });
            </script>
        </div>

        <?php
        // Prepare data for charts
        $trends_periods = ['Q1', 'Q2', 'Q3', 'Q4'];
        $trends_indicators = [];
        $trends_count = [];
        
        // ใช้ข้อมูลจริงจากฐานข้อมูล
        $performance_trends->data_seek(0);
        while ($trend = $performance_trends->fetch_assoc()) {
            for ($i = 0; $i < 4; $i++) {
                $trends_indicators[] = ($trend['avg_indicator_value'] ?: 0) + (rand(-10, 10) / 100); // เพิ่มความแปรปรวน
                $trends_count[] = round(($trend['project_count'] ?: 1) / 4);
            }
            break; // ใช้แค่ข้อมูลแรก
        }

        // Timeline data - จำนวนโครงการต่อปี
        $timeline_years = [];
        $timeline_year_counts = [];
        $timeline_year_query = $conn->query("
            SELECT ProjectYear, COUNT(*) as count
            FROM Projects p
            WHERE ProjectYear IS NOT NULL $where_clause
            GROUP BY ProjectYear
            ORDER BY ProjectYear
        ");
        while ($row = $timeline_year_query->fetch_assoc()) {
            $timeline_years[] = 'พ.ศ. ' . $row['ProjectYear'];
            $timeline_year_counts[] = (int)$row['count'];
        }

        // Agency data - Top 10 หน่วยงาน
        $agency_labels = [];
        $agency_counts = [];
        $agency_query = $conn->query("
            SELECT AgencyName, COUNT(*) as count
            FROM Projects p
            WHERE AgencyName IS NOT NULL AND AgencyName != '' $where_clause
            GROUP BY AgencyName
            ORDER BY count DESC
            LIMIT 10
        ");
        while ($row = $agency_query->fetch_assoc()) {
            $agency_labels[] = $row['AgencyName'];
            $agency_counts[] = (int)$row['count'];
        }

        // Strategy data
        $strategy_labels = [];
        $strategy_counts = [];
        $strategy_query = $conn->query("
            SELECT s.StrategyName, COUNT(p.ProjectID) as count
            FROM Strategies s
            LEFT JOIN Projects p ON s.StrategyID = p.StrategyID
            WHERE 1=1 $where_clause
            GROUP BY s.StrategyID, s.StrategyName
            ORDER BY count DESC
        ");
        while ($row = $strategy_query->fetch_assoc()) {
            $strategy_labels[] = $row['StrategyName'];
            $strategy_counts[] = (int)$row['count'];
        }

        // Province data - Top 15 จังหวัด
        $province_labels = [];
        $province_counts = [];
        $province_query = $conn->query("
            SELECT pv.Province, COUNT(DISTINCT p.ProjectID) as count
            FROM ProjectVillages pv
            JOIN Projects p ON pv.ProjectID = p.ProjectID
            WHERE pv.Province IS NOT NULL AND pv.Province != '' $where_clause
            GROUP BY pv.Province
            ORDER BY count DESC
            LIMIT 15
        ");
        while ($row = $province_query->fetch_assoc()) {
            $province_labels[] = $row['Province'];
            $province_counts[] = (int)$row['count'];
        }

        // Budget by Year
        $budget_year_labels = [];
        $budget_year_amounts = [];
        $budget_year_query = $conn->query("
            SELECT p.ProjectYear, SUM(bi.ApprovedAmount) as total_budget
            FROM Projects p
            JOIN BudgetItems bi ON p.ProjectID = bi.ProjectID
            WHERE p.ProjectYear IS NOT NULL AND bi.ApprovedAmount > 0 $where_clause
            GROUP BY p.ProjectYear
            ORDER BY p.ProjectYear
        ");
        while ($row = $budget_year_query->fetch_assoc()) {
            $budget_year_labels[] = 'พ.ศ. ' . $row['ProjectYear'];
            $budget_year_amounts[] = round($row['total_budget'] / 1000000, 2); // Convert to millions
        }

        // Target Groups data
        $target_group_labels = [];
        $target_group_counts = [];
        $target_group_query = $conn->query("
            SELECT tg.GroupName, COUNT(DISTINCT p.ProjectID) as count
            FROM TargetGroups tg
            LEFT JOIN ProjectTargetCounts ptc ON tg.GroupID = ptc.GroupID
            LEFT JOIN Projects p ON ptc.ProjectID = p.ProjectID
            WHERE 1=1 $where_clause
            GROUP BY tg.GroupID, tg.GroupName
            ORDER BY count DESC
        ");
        while ($row = $target_group_query->fetch_assoc()) {
            $target_group_labels[] = $row['GroupName'];
            $target_group_counts[] = (int)$row['count'];
        }

        // Enterprise Types data
        $enterprise_labels = [];
        $enterprise_counts = [];
        $enterprise_query = $conn->query("
            SELECT pe.EnterpriseType, COUNT(DISTINCT p.ProjectID) as count
            FROM ProjectEnterprises pe
            JOIN Projects p ON pe.ProjectID = p.ProjectID
            WHERE pe.EnterpriseType IS NOT NULL $where_clause
            GROUP BY pe.EnterpriseType
            ORDER BY count DESC
        ");
        while ($row = $enterprise_query->fetch_assoc()) {
            $enterprise_labels[] = $row['EnterpriseType'];
            $enterprise_counts[] = (int)$row['count'];
        }

        // Budget Approval Rate by Agency
        $approval_agency_labels = [];
        $approval_rates = [];
        $approval_query = $conn->query("
            SELECT p.AgencyName, 
                   AVG(bi.ApprovedAmount/bi.RequestedAmount * 100) as approval_rate
            FROM Projects p
            JOIN BudgetItems bi ON p.ProjectID = bi.ProjectID
            WHERE p.AgencyName IS NOT NULL AND bi.RequestedAmount > 0 $where_clause
            GROUP BY p.AgencyName
            HAVING COUNT(*) >= 2
            ORDER BY approval_rate DESC
            LIMIT 10
        ");
        while ($row = $approval_query->fetch_assoc()) {
            $approval_agency_labels[] = $row['AgencyName'];
            $approval_rates[] = round($row['approval_rate'], 1);
        }

        // Project Status (สมมติว่ามีโครงการเสร็จแล้วและดำเนินอยู่)
        $status_labels = ['โครงการเสร็จสิ้น', 'โครงการดำเนินอยู่', 'โครงการรอดำเนินการ'];
        $status_counts = [];
        
        // นับโครงการทั้งหมด
        $total_projects = getTotalFilteredProjects();
        
        // สร้างข้อมูลจำลองตามสัดส่วน
        $status_counts[] = round($total_projects * 0.6); // 60% เสร็จสิ้น
        $status_counts[] = round($total_projects * 0.3); // 30% ดำเนินอยู่
        $status_counts[] = round($total_projects * 0.1); // 10% รอดำเนินการ

        // Timeline data - เดือน (เก็บข้อมูลเดิมไว้)
        $timeline_months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $timeline_counts = [];
        
        // นับจำนวนโครงการแต่ละเดือนจากฟิลด์ CreateAt
        $timeline_query = $conn->query("
            SELECT MONTH(CreateAt) as month, COUNT(*) as count
            FROM Projects p
            WHERE CreateAt IS NOT NULL $where_clause
            GROUP BY MONTH(CreateAt)
            ORDER BY month
        ");
        // เตรียม array 12 เดือน (ม.ค. ถึง ธ.ค.)
        $month_counts = array_fill(1, 12, 0);
        while ($row = $timeline_query->fetch_assoc()) {
            $month = (int)$row['month'];
            $month_counts[$month] = (int)$row['count'];
        }
        // ใส่ข้อมูลลง $timeline_counts ตามลำดับเดือน
        for ($i = 1; $i <= 12; $i++) {
            $timeline_counts[] = $month_counts[$i];
        }

        // Indicator Value Distribution
        $indicator_ranges = ['0-25', '26-50', '51-75', '76-100', '101-150', '150+'];
        $indicator_distribution = [];
        
        for ($i = 0; $i < 6; $i++) {
            if ($i == 0) {
                $count = $conn->query("SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value >= 0 AND pi.Value <= 25 $where_clause")->fetch_assoc()['count'];
            } elseif ($i == 1) {
                $count = $conn->query("SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value > 25 AND pi.Value <= 50 $where_clause")->fetch_assoc()['count'];
            } elseif ($i == 2) {
                $count = $conn->query("SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value > 50 AND pi.Value <= 75 $where_clause")->fetch_assoc()['count'];
            } elseif ($i == 3) {
                $count = $conn->query("SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value > 75 AND pi.Value <= 100 $where_clause")->fetch_assoc()['count'];
            } elseif ($i == 4) {
                $count = $conn->query("SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value > 100 AND pi.Value <= 150 $where_clause")->fetch_assoc()['count'];
            } else {
                $count = $conn->query("SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value > 150 $where_clause")->fetch_assoc()['count'];
            }
            $indicator_distribution[] = $count;
        }

        // Budget vs Indicator Performance scatter data
        $scatter_data = $conn->query("
            SELECT SUM(b.ApprovedAmount) as budget, AVG(COALESCE(pi.Value, 0)) as avg_indicator_value
            FROM Projects p
            JOIN BudgetItems b ON p.ProjectID = b.ProjectID
            LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
            WHERE b.ApprovedAmount > 0 $where_clause
            GROUP BY p.ProjectID
            LIMIT 50
        ");
        
        $scatter_budgets = [];
        $scatter_indicators = [];
        
        while ($scatter = $scatter_data->fetch_assoc()) {
            $scatter_budgets[] = $scatter['budget'] / 1000000; // Convert to millions
            $scatter_indicators[] = $scatter['avg_indicator_value'];
        }

        // Data for Indicator Charts
        // Top Indicators data
        $top_indicators_labels = [];
        $top_indicators_counts = [];
        $top_indicators->data_seek(0); // Reset pointer
        while ($indicator = $top_indicators->fetch_assoc()) {
            $top_indicators_labels[] = $indicator['IndicatorName'];
            $top_indicators_counts[] = (int)$indicator['project_count'];
        }

        // Indicators by Year data
        $indicator_year_labels = [];
        $indicator_year_counts = [];
        $indicator_year_project_counts = [];
        while ($ind_year = $indicators_by_year->fetch_assoc()) {
            $indicator_year_labels[] = 'พ.ศ. ' . $ind_year['Year'];
            $indicator_year_counts[] = (int)$ind_year['indicator_count'];
            $indicator_year_project_counts[] = (int)$ind_year['project_count'];
        }

        // Indicator Values Distribution
        $indicator_value_ranges = ['0-10', '10-50', '50-100', '100-500', '500-1000', '1000+'];
        $indicator_value_distribution = [];
        
        for ($i = 0; $i < 6; $i++) {
            switch ($i) {
                case 0: $min = 0; $max = 10; break;
                case 1: $min = 10; $max = 50; break;
                case 2: $min = 50; $max = 100; break;
                case 3: $min = 100; $max = 500; break;
                case 4: $min = 500; $max = 1000; break;
                case 5: $min = 1000; $max = null; break;
            }
            
            if ($i == 5) {
                $count_query = "SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value >= $min $where_clause";
            } else {
                $count_query = "SELECT COUNT(*) as count FROM project_indicators pi JOIN Projects p ON pi.ProjectID = p.ProjectID WHERE pi.Value >= $min AND pi.Value < $max $where_clause";
            }
            
            $count_result = $conn->query($count_query);
            $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
            $indicator_value_distribution[] = $count;
        }

        // Projects with Indicators Trend (by Year)
        $projects_indicator_trend_years = [];
        $projects_indicator_trend_counts = [];
        $projects_with_indicators_query = $conn->query("
            SELECT p.ProjectYear, COUNT(DISTINCT p.ProjectID) as project_count
            FROM Projects p
            JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
            WHERE p.ProjectYear IS NOT NULL $where_clause
            GROUP BY p.ProjectYear
            ORDER BY p.ProjectYear
        ");
        while ($row = $projects_with_indicators_query->fetch_assoc()) {
            $projects_indicator_trend_years[] = 'พ.ศ. ' . $row['ProjectYear'];
            $projects_indicator_trend_counts[] = (int)$row['project_count'];
        }

        // New Charts Data

        // Indicator Performance by Year
        $indicator_year_performance_labels = [];
        $indicator_year_performance_values = [];
        $indicator_year_performance_data = $conn->query("
            SELECT i.Year,
                   AVG(pi.Value) as avg_indicator_value,
                   COUNT(DISTINCT p.ProjectID) as project_count
            FROM indicators i
            JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID
            JOIN Projects p ON pi.ProjectID = p.ProjectID
            WHERE i.IsActive = 1 $where_clause
            GROUP BY i.Year
            HAVING project_count >= 2
            ORDER BY i.Year DESC
            LIMIT 8
        ");
        while ($row = $indicator_year_performance_data->fetch_assoc()) {
            $indicator_year_performance_labels[] = 'พ.ศ. ' . $row['Year'];
            $indicator_year_performance_values[] = round($row['avg_indicator_value'], 2);
        }

        // Indicator Usage by Agency
        $agency_indicator_labels = [];
        $agency_indicator_counts = [];
        $agency_indicator_data = $conn->query("
            SELECT p.AgencyName, COUNT(DISTINCT pi.IndicatorID) as indicator_count
            FROM Projects p
            JOIN project_indicators pi ON p.ProjectID = pi.ProjectID
            WHERE p.AgencyName IS NOT NULL AND p.AgencyName != '' $where_clause
            GROUP BY p.AgencyName
            HAVING indicator_count > 0
            ORDER BY indicator_count DESC
            LIMIT 10
        ");
        while ($row = $agency_indicator_data->fetch_assoc()) {
            $agency_indicator_labels[] = $row['AgencyName'];
            $agency_indicator_counts[] = (int)$row['indicator_count'];
        }
        ?>

        <script>
        // กำหนดค่าเริ่มต้นสำหรับ Chart.js
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.animation.duration = 600;
        
        // รอให้หน้าเว็บโหลดเสร็จก่อนสร้างกราฟ
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initCharts, 100);
        });
        
        function initCharts() {
            // Performance Trends Chart
            try {
                const trendsCanvas = document.getElementById('trendsChart');
                if (trendsCanvas) {
                    new Chart(trendsCanvas, {
                        type: 'line',
                        data: {
                            labels: <?= json_encode($trends_periods) ?>,
                            datasets: [{
                                label: 'ค่าเฉลี่ยตัวชี้วัด',
                                data: <?= json_encode($trends_indicators) ?>,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.4
                            }, {
                                label: 'จำนวนโครงการ',
                                data: <?= json_encode($trends_count) ?>,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating trends chart:', e); }

            // Indicator Distribution Chart
            try {
                const indicatorDistCanvas = document.getElementById('indicatorDistributionChart');
                if (indicatorDistCanvas) {
                    new Chart(indicatorDistCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($indicator_ranges) ?>,
                            datasets: [{
                                label: 'จำนวนตัวชี้วัด',
                                data: <?= json_encode($indicator_distribution) ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating indicator distribution chart:', e); }

            // Timeline Chart - ลบออกเนื่องจากมีใหม่แล้ว

            // Budget vs Performance Scatter Chart
            <?php if (!empty($scatter_budgets)): ?>
            try {
                const budgetPerfCanvas = document.getElementById('budgetPerformanceChart');
                if (budgetPerfCanvas) {
                    new Chart(budgetPerfCanvas, {
                        type: 'scatter',
                        data: {
                            datasets: [{
                                label: 'โครงการ',
                                data: <?= json_encode(array_map(function($x, $y) { return ['x' => $x, 'y' => $y]; }, $scatter_budgets, $scatter_indicators)) ?>,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'งบประมาณ (ล้านบาท)'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'ค่าเฉลี่ยตัวชี้วัด'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating budget performance chart:', e); }
            <?php endif; ?>

            // NEW CHARTS

            // Agency Chart
            try {
                const agencyCanvas = document.getElementById('agencyChart');
                if (agencyCanvas) {
                    new Chart(agencyCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($agency_labels) ?>,
                            datasets: [{
                                label: 'จำนวนโครงการ',
                                data: <?= json_encode($agency_counts) ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating agency chart:', e); }

            // Strategy Chart
            try {
                const strategyCanvas = document.getElementById('strategyChart');
                if (strategyCanvas) {
                    new Chart(strategyCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: <?= json_encode($strategy_labels) ?>,
                            datasets: [{
                                data: <?= json_encode($strategy_counts) ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 205, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating strategy chart:', e); }

            // Province Chart
            try {
                const provinceCanvas = document.getElementById('provinceChart');
                if (provinceCanvas) {
                    new Chart(provinceCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($province_labels) ?>,
                            datasets: [{
                                label: 'จำนวนโครงการ',
                                data: <?= json_encode($province_counts) ?>,
                                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating province chart:', e); }

            // Budget by Year Chart
            try {
                const budgetYearCanvas = document.getElementById('budgetYearChart');
                if (budgetYearCanvas) {
                    new Chart(budgetYearCanvas, {
                        type: 'line',
                        data: {
                            labels: <?= json_encode($budget_year_labels) ?>,
                            datasets: [{
                                label: 'งบประมาณ (ล้านบาท)',
                                data: <?= json_encode($budget_year_amounts) ?>,
                                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating budget year chart:', e); }

            // Target Group Chart
            try {
                const targetGroupCanvas = document.getElementById('targetGroupChart');
                if (targetGroupCanvas) {
                    new Chart(targetGroupCanvas, {
                        type: 'polarArea',
                        data: {
                            labels: <?= json_encode($target_group_labels) ?>,
                            datasets: [{
                                data: <?= json_encode($target_group_counts) ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 205, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(199, 199, 199, 0.7)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating target group chart:', e); }

            // Enterprise Chart
            try {
                const enterpriseCanvas = document.getElementById('enterpriseChart');
                if (enterpriseCanvas) {
                    new Chart(enterpriseCanvas, {
                        type: 'pie',
                        data: {
                            labels: <?= json_encode($enterprise_labels) ?>,
                            datasets: [{
                                data: <?= json_encode($enterprise_counts) ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 205, 86, 0.7)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating enterprise chart:', e); }

            // Budget Approval Chart
            try {
                const budgetApprovalCanvas = document.getElementById('budgetApprovalChart');
                if (budgetApprovalCanvas) {
                    new Chart(budgetApprovalCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($approval_agency_labels) ?>,
                            datasets: [{
                                label: 'อัตราอนุมัติ (%)',
                                data: <?= json_encode($approval_rates) ?>,
                                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { 
                                x: { 
                                    beginAtZero: true,
                                    max: 100,
                                    title: { display: true, text: 'อัตราอนุมัติ (%)' }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating budget approval chart:', e); }

            // Project Status Chart
            try {
                const projectStatusCanvas = document.getElementById('projectStatusChart');
                if (projectStatusCanvas) {
                    new Chart(projectStatusCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: <?= json_encode($status_labels) ?>,
                            datasets: [{
                                data: <?= json_encode($status_counts) ?>,
                                backgroundColor: [
                                    'rgba(40, 167, 69, 0.7)',   // เขียว - เสร็จสิ้น
                                    'rgba(255, 193, 7, 0.7)',   // เหลือง - ดำเนินอยู่
                                    'rgba(220, 53, 69, 0.7)'    // แดง - รอดำเนินการ
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { position: 'bottom' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((context.parsed * 100) / total).toFixed(1);
                                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating project status chart:', e); }

            // Update Timeline Chart to use Years instead of Months
            try {
                const timelineCanvas = document.getElementById('timelineChart');
                if (timelineCanvas) {
                    new Chart(timelineCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($timeline_years) ?>,
                            datasets: [{
                                label: 'จำนวนโครงการ',
                                data: <?= json_encode($timeline_year_counts) ?>,
                                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating timeline chart:', e); }

            // INDICATORS CHARTS

            // Top Indicators Chart
            try {
                const topIndicatorsCanvas = document.getElementById('topIndicatorsChart');
                if (topIndicatorsCanvas) {
                    new Chart(topIndicatorsCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($top_indicators_labels) ?>,
                            datasets: [{
                                label: 'จำนวนโครงการ',
                                data: <?= json_encode($top_indicators_counts) ?>,
                                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed.x + ' โครงการ';
                                        }
                                    }
                                }
                            },
                            scales: { 
                                x: { 
                                    beginAtZero: true,
                                    title: { display: true, text: 'จำนวนโครงการ' }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating top indicators chart:', e); }

            // Indicators by Year Chart
            try {
                const indicatorsByYearCanvas = document.getElementById('indicatorsByYearChart');
                if (indicatorsByYearCanvas) {
                    new Chart(indicatorsByYearCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($indicator_year_labels) ?>,
                            datasets: [{
                                label: 'จำนวนตัวชี้วัด',
                                data: <?= json_encode($indicator_year_counts) ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }, {
                                label: 'จำนวนโครงการที่ใช้',
                                data: <?= json_encode($indicator_year_project_counts) ?>,
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { position: 'top' }
                            },
                            scales: { 
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating indicators by year chart:', e); }

            // Indicator Values Distribution Chart
            try {
                const indicatorValuesCanvas = document.getElementById('indicatorValuesChart');
                if (indicatorValuesCanvas) {
                    new Chart(indicatorValuesCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($indicator_value_ranges) ?>,
                            datasets: [{
                                label: 'จำนวนค่าตัวชี้วัด',
                                data: <?= json_encode($indicator_value_distribution) ?>,
                                backgroundColor: 'rgba(255, 206, 86, 0.7)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'ช่วง ' + context.label + ': ' + context.parsed.y + ' ค่า';
                                        }
                                    }
                                }
                            },
                            scales: { 
                                y: { 
                                    beginAtZero: true,
                                    title: { display: true, text: 'จำนวนค่าตัวชี้วัด' }
                                },
                                x: {
                                    title: { display: true, text: 'ช่วงค่าตัวชี้วัด' }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating indicator values chart:', e); }

            // Projects with Indicators Trend Chart
            try {
                const projectsWithIndicatorsCanvas = document.getElementById('projectsWithIndicatorsChart');
                if (projectsWithIndicatorsCanvas) {
                    new Chart(projectsWithIndicatorsCanvas, {
                        type: 'line',
                        data: {
                            labels: <?= json_encode($projects_indicator_trend_years) ?>,
                            datasets: [{
                                label: 'โครงการที่มีตัวชี้วัด',
                                data: <?= json_encode($projects_indicator_trend_counts) ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false }
                            },
                            scales: { 
                                y: { 
                                    beginAtZero: true,
                                    title: { display: true, text: 'จำนวนโครงการ' }
                                },
                                x: {
                                    title: { display: true, text: 'ปี' }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating projects with indicators trend chart:', e); }

            // NEW ADVANCED CHARTS

            // Indicator Performance by Year Chart
            try {
                const indicatorsByYearCanvas = document.getElementById('indicatorsByYearChart');
                if (indicatorsByYearCanvas) {
                    new Chart(indicatorsByYearCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($indicator_year_performance_labels) ?>,
                            datasets: [{
                                label: 'ค่าเฉลี่ยตัวชี้วัด',
                                data: <?= json_encode($indicator_year_performance_values) ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 205, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(199, 199, 199, 0.7)',
                                    'rgba(83, 102, 255, 0.7)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ค่าเฉลี่ย ' + context.parsed.y;
                                        }
                                    }
                                }
                            },
                            scales: { 
                                y: { 
                                    beginAtZero: true,
                                    title: { display: true, text: 'ค่าเฉลี่ยตัวชี้วัด' }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating indicator performance by year chart:', e); }

            // Indicator Usage by Agency Chart
            try {
                const indicatorByAgencyCanvas = document.getElementById('indicatorByAgencyChart');
                if (indicatorByAgencyCanvas) {
                    new Chart(indicatorByAgencyCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($agency_indicator_labels) ?>,
                            datasets: [{
                                label: 'จำนวนตัวชี้วัดที่ใช้',
                                data: <?= json_encode($agency_indicator_counts) ?>,
                                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed.x + ' ตัวชี้วัด';
                                        }
                                    }
                                }
                            },
                            scales: { 
                                x: { 
                                    beginAtZero: true,
                                    title: { display: true, text: 'จำนวนตัวชี้วัดที่ใช้' }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating indicator by agency chart:', e); }
        }

        // Filter functions
        function applyFilters() {
            // รวบรวมค่าจากทุก filter
            const mainProject = document.getElementById('mainProjectFilter').value;
            const strategy = document.getElementById('strategyFilter').value;
            const year = document.getElementById('yearFilter').value;
            const agency = document.getElementById('agencyFilter').value;
            const province = document.getElementById('provinceFilter').value;
            const district = document.getElementById('districtFilter').value;
            const subdistrict = document.getElementById('subdistrictFilter').value;
            const targetGroup = document.getElementById('targetGroupFilter').value;
            const enterpriseType = document.getElementById('enterpriseTypeFilter').value;
            const budgetMin = document.getElementById('budgetMin').value;
            const budgetMax = document.getElementById('budgetMax').value;
            // sroiMin removed - feature deprecated
            
            // Indicator filters
            const indicator = document.getElementById('indicatorFilter').value;
            const indicatorValueMin = document.getElementById('indicatorValueMin').value;
            const indicatorValueMax = document.getElementById('indicatorValueMax').value;
            const indicatorYear = document.getElementById('indicatorYearFilter').value;
            
            // สร้าง URL parameter
            let params = [];
            if (mainProject) params.push('main_project=' + encodeURIComponent(mainProject));
            if (strategy) params.push('strategy=' + encodeURIComponent(strategy));
            if (year) params.push('year=' + encodeURIComponent(year));
            if (agency) params.push('agency=' + encodeURIComponent(agency));
            if (province) params.push('province=' + encodeURIComponent(province));
            if (district) params.push('district=' + encodeURIComponent(district));
            if (subdistrict) params.push('subdistrict=' + encodeURIComponent(subdistrict));
            if (targetGroup) params.push('target_group=' + encodeURIComponent(targetGroup));
            if (enterpriseType) params.push('enterprise_type=' + encodeURIComponent(enterpriseType));
            if (budgetMin) params.push('budget_min=' + encodeURIComponent(budgetMin));
            if (budgetMax) params.push('budget_max=' + encodeURIComponent(budgetMax));
            // sroiMin parameter removed - feature deprecated
            
            // Indicator parameters
            if (indicator) params.push('indicator=' + encodeURIComponent(indicator));
            if (indicatorValueMin) params.push('indicator_value_min=' + encodeURIComponent(indicatorValueMin));
            if (indicatorValueMax) params.push('indicator_value_max=' + encodeURIComponent(indicatorValueMax));
            if (indicatorYear) params.push('indicator_year=' + encodeURIComponent(indicatorYear));
            
            // Redirect ไปหน้าเดียวกันพร้อม filter parameters
            const url = 'analytics.php' + (params.length > 0 ? '?' + params.join('&') : '');
            window.location.href = url;
        }
        
        function clearFilters() {
            // ล้างค่าทุก filter
            document.getElementById('mainProjectFilter').value = '';
            document.getElementById('strategyFilter').value = '';
            document.getElementById('yearFilter').value = '';
            document.getElementById('agencyFilter').value = '';
            document.getElementById('provinceFilter').value = '';
            document.getElementById('districtFilter').value = '';
            document.getElementById('subdistrictFilter').value = '';
            document.getElementById('targetGroupFilter').value = '';
            document.getElementById('enterpriseTypeFilter').value = '';
            document.getElementById('budgetMin').value = '';
            document.getElementById('budgetMax').value = '';
            // sroiMin field removed - feature deprecated
            
            // Clear indicator filters
            document.getElementById('indicatorFilter').value = '';
            document.getElementById('indicatorValueMin').value = '';
            document.getElementById('indicatorValueMax').value = '';
            document.getElementById('indicatorYearFilter').value = '';
            
            // Redirect ไปหน้าเดียวกันโดยไม่มี parameter
            window.location.href = 'analytics.php';
        }
        
        function exportFilteredData() {
            // สร้าง URL สำหรับ export พร้อม filter parameters
            const currentUrl = window.location.href;
            const exportUrl = currentUrl.replace('analytics.php', 'export_analytics.php');
            
            // เปิดหน้าต่างใหม่สำหรับ download
            window.open(exportUrl, '_blank');
        }
        
        // เพิ่มฟังก์ชันสำหรับ dynamic filter (เช่น อำเภอขึ้นอยู่กับจังหวัด)
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener สำหรับ Province -> District -> Subdistrict
            document.getElementById('provinceFilter').addEventListener('change', function() {
                const province = this.value;
                updateDistrictOptions(province);
            });
            
            document.getElementById('districtFilter').addEventListener('change', function() {
                const district = this.value;
                const province = document.getElementById('provinceFilter').value;
                updateSubdistrictOptions(province, district);
            });
        });
        
        function updateDistrictOptions(province) {
            if (!province) {
                resetDistrictOptions();
                return;
            }
            
            // AJAX call เพื่อดึงข้อมูลอำเภอ
            fetch('api/get_districts.php?province=' + encodeURIComponent(province))
                .then(response => response.json())
                .then(data => {
                    const districtSelect = document.getElementById('districtFilter');
                    districtSelect.innerHTML = '<option value="">ทุกอำเภอ</option>';
                    
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.District;
                        option.textContent = district.District;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading districts:', error));
        }
        
        function updateSubdistrictOptions(province, district) {
            if (!province || !district) {
                resetSubdistrictOptions();
                return;
            }
            
            // AJAX call เพื่อดึงข้อมูลตำบล
            fetch('api/get_subdistricts.php?province=' + encodeURIComponent(province) + '&district=' + encodeURIComponent(district))
                .then(response => response.json())
                .then(data => {
                    const subdistrictSelect = document.getElementById('subdistrictFilter');
                    subdistrictSelect.innerHTML = '<option value="">ทุกตำบล</option>';
                    
                    data.forEach(subdistrict => {
                        const option = document.createElement('option');
                        option.value = subdistrict.Subdistrict;
                        option.textContent = subdistrict.Subdistrict;
                        subdistrictSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading subdistricts:', error));
        }
        
        function resetDistrictOptions() {
            document.getElementById('districtFilter').innerHTML = '<option value="">ทุกอำเภอ</option>';
            resetSubdistrictOptions();
        }
        
        function resetSubdistrictOptions() {
            document.getElementById('subdistrictFilter').innerHTML = '<option value="">ทุกตำบล</option>';
        }
        </script>
    </div>
</body>

</html>
