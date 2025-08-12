<?php include '../db.php'; ?>
<?php include 'navbar.php'; ?>

<?php
if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>ไม่พบรหัสโครงการ</div>";
    exit;
}

$id = $_GET['id'];

// ดึงข้อมูลโครงการ
$result = $conn->query("SELECT p.*, s.StrategyName, mp.MainProjectName, mp.MainProjectCode, mp.MainProjectDescription
FROM projects p
LEFT JOIN strategies s ON p.StrategyID = s.StrategyID  
LEFT JOIN mainprojects mp ON p.MainProjectID = mp.MainProjectID
WHERE p.ProjectID = $id");
$project = $result->fetch_assoc();

if (!$project) {
    echo "<div class='alert alert-danger'>ไม่พบโครงการ</div>";
    exit;
}

// ดึงข้อมูลกลุ่มเป้าหมาย
$targets = [];
$target_result = $conn->query("
    SELECT tg.GroupName, ptc.TargetCount 
    FROM projecttargetcounts ptc 
    JOIN targetgroups tg ON ptc.GroupID = tg.GroupID
    WHERE ptc.ProjectID = $id
");
while ($target_row = $target_result->fetch_assoc()) {
    $targets[] = $target_row;
}

// ดึงข้อมูลหมู่บ้าน
$villages = [];
$village_result = $conn->query("SELECT * FROM projectvillages WHERE ProjectID = $id");
while ($village_row = $village_result->fetch_assoc()) {
    $villages[] = $village_row;
}

// ดึงข้อมูลโรงเรียน
$schools = [];
$school_result = $conn->query("SELECT * FROM projectschools WHERE ProjectID = $id");
while ($school_row = $school_result->fetch_assoc()) {
    $schools[] = $school_row;
}

// ดึงข้อมูลเครือข่าย
$networks = [];
$network_result = $conn->query("SELECT * FROM projectnetworks WHERE ProjectID = $id");
while ($network_row = $network_result->fetch_assoc()) {
    $networks[] = $network_row;
}

// ดึงข้อมูลวิสาหกิจ
$enterprises = [];
$enterprise_result = $conn->query("SELECT * FROM projectenterprises WHERE ProjectID = $id");
while ($enterprise_row = $enterprise_result->fetch_assoc()) {
    $enterprises[] = $enterprise_row;
}

// ดึงข้อมูลผลิตภัณฑ์
$products = [];
$product_result = $conn->query("SELECT * FROM projectproducts WHERE ProjectID = $id");
while ($product_row = $product_result->fetch_assoc()) {
    $products[] = $product_row;
}

// ดึงข้อมูลงบประมาณ
$budget_items = [];
$budget_total = 0;
$budget_result = $conn->query("SELECT * FROM budgetitems WHERE ProjectID = $id ORDER BY BudgetID");
while ($budget_row = $budget_result->fetch_assoc()) {
    $budget_items[] = $budget_row;
    $budget_total += $budget_row['ApprovedAmount'];
}

// ดึงข้อมูลตัวชี้วัดที่เกี่ยวข้องกับโครงการ
$indicators_data = [];
$strategy_id = !empty($project['StrategyID']) ? $project['StrategyID'] : 'NULL';
$main_project_id = !empty($project['MainProjectID']) ? $project['MainProjectID'] : 'NULL';
$project_year = $project['ProjectYear'];

$indicators_result = $conn->query("
    SELECT pi.ID as ProjectIndicatorID,
           pi.IndicatorID, 
           pi.Value,
           pi.CreatedAt,
           i.IndicatorName, 
           i.Unit, 
           i.Description,
           i.Year,
           i.StrategyID,
           i.MainProjectID,
           GROUP_CONCAT(pid.DetailText ORDER BY pid.DetailID SEPARATOR '|||') as Details
    FROM project_indicators pi 
    JOIN indicators i ON pi.IndicatorID = i.IndicatorID
    LEFT JOIN project_indicator_details pid ON pi.ID = pid.ProjectIndicatorID
    WHERE pi.ProjectID = $id
      AND pi.Value IS NOT NULL 
      AND pi.Value != '' 
      AND pi.Value != 0
      AND (
          i.Year = '$project_year' OR 
          i.StrategyID = $strategy_id OR 
          i.MainProjectID = $main_project_id OR
          (i.Year IS NULL AND i.StrategyID IS NULL AND i.MainProjectID IS NULL)
      )
    GROUP BY pi.ID
    ORDER BY i.IndicatorName, pi.Value DESC
");
while ($indicator_row = $indicators_result->fetch_assoc()) {
    $details = [];
    if (!empty($indicator_row['Details'])) {
        $details = explode('|||', $indicator_row['Details']);
    }
    $indicator_row['Details'] = $details;
    $indicators_data[] = $indicator_row;
}

// ดึงข้อมูล SROI
$sroi_data = [];
$sroi_result = $conn->query("SELECT * FROM projectsroi WHERE ProjectID = $id ORDER BY ID");
while ($sroi_row = $sroi_result->fetch_assoc()) {
    $sroi_data[] = $sroi_row;
}

// ดึงข้อมูลมหาวิทยาลัย
$universities = [];
$university_result = $conn->query("SELECT * FROM projectuniversities WHERE ProjectID = $id ORDER BY UniversityName");
while ($university_row = $university_result->fetch_assoc()) {
    $universities[] = $university_row;
}

// ดึงข้อมูลองค์กรปกครองส่วนท้องถิ่น
$local_admins = [];
$local_admin_result = $conn->query("SELECT * FROM projectlocaladmins WHERE ProjectID = $id ORDER BY AdminName");
while ($local_admin_row = $local_admin_result->fetch_assoc()) {
    $local_admins[] = $local_admin_row;
}

// ดึงข้อมูลองค์กรอื่นๆ
$others = [];
$other_result = $conn->query("SELECT * FROM projectothers WHERE ProjectID = $id ORDER BY OrganizationName");
while ($other_row = $other_result->fetch_assoc()) {
    $others[] = $other_row;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดโครงการ - <?= htmlspecialchars($project['ProjectName']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: #334155;
            line-height: 1.6;
        }
        
        .main-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin: 1rem auto;
            padding: 2rem;
            max-width: 1200px;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }
        
        .page-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .page-header h1 {
            margin: 0;
            font-weight: 600;
            font-size: 1.875rem;
            letter-spacing: -0.025em;
        }
        
        .page-header .breadcrumb {
            background: transparent;
            margin: 0;
            padding: 0;
            justify-content: center;
        }
        
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
        }
        
        .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }
        
        .section-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .section-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }
        
        .section-card .card-header {
            border: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: #1e40af;
            border-bottom: 2px solid #3b82f6;
        }
        
        .section-card .card-header h5,
        .section-card .card-header h6 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }
        
        .section-card .card-body {
            padding: 1.5rem;
        }
        
        .info-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #3b82f6;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .info-item:hover {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left-color: #1d4ed8;
            transform: translateX(3px);
        }
        
        .info-item strong {
            color: #1e40af;
            font-weight: 600;
        }
        
        .badge-modern {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 6px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .text-purple {
            color: #7c3aed !important;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #06b6d4, #10b981, #f59e0b, #ef4444);
        }
        
        .stat-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-card p {
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0 !important;
        }
        
        .table-modern {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        
        .table-modern thead th {
            background: #f8fafc;
            color: #0f172a;
            border: none;
            font-weight: 600;
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .table-modern tbody tr {
            transition: background-color 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .table-modern tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .table-modern td {
            padding: 1rem;
            border: none;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .btn-action {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 8px;
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            color: #3b82f6;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-action:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }
        
        .list-item {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .list-item:hover {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #3b82f6;
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.15);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.4;
            color: #94a3b8;
        }
        
        /* Minimal Indicators */
        .indicator-group {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.2s ease;
            margin-bottom: 1rem;
        }
        
        .indicator-group:hover {
            border-color: #cbd5e1;
        }
        
        .indicator-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .value-item {
            transition: background-color 0.2s ease;
        }
        
        .value-item:hover {
            background-color: #f8fafc;
        }
        
        .note-box {
            border-left: 3px solid #3b82f6;
            transition: all 0.2s ease;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        
        .stats-summary {
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        
        .stat-item {
            padding: 0.75rem;
        }
        
        .text-purple {
            color: #6366f1 !important;
        }
        
        .indicator-values {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .indicator-values::-webkit-scrollbar {
            width: 4px;
        }
        
        .indicator-values::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .indicator-values::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        .indicator-values::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Modern color palette */
        .bg-primary { background-color: #3b82f6 !important; }
        .bg-secondary { background-color: #6b7280 !important; }
        .bg-success { background-color: #10b981 !important; }
        .bg-danger { background-color: #ef4444 !important; }
        .bg-warning { background-color: #f59e0b !important; }
        .bg-info { background-color: #06b6d4 !important; }
        
        .text-primary { color: #3b82f6 !important; }
        .text-secondary { color: #6b7280 !important; }
        .text-success { color: #10b981 !important; }
        .text-danger { color: #ef4444 !important; }
        .text-warning { color: #f59e0b !important; }
        .text-info { color: #06b6d4 !important; }
        .text-muted { color: #64748b !important; }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .main-container {
                margin: 0.5rem;
                padding: 1rem;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-icon {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="main-container">
            
            <!-- Page Header -->
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="projects_list.php">รายการโครงการ</a></li>
                        <li class="breadcrumb-item active">รายละเอียด</li>
                    </ol>
                </nav>
                <h1>รายละเอียดโครงการ</h1>
                <p class="mb-0 opacity-75"><?= htmlspecialchars($project['ProjectName']) ?></p>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="projects_table_view.php" class="btn btn-outline-secondary btn-action">
                    <i class="fas fa-arrow-left me-2"></i>กลับสู่รายการ
                </a>
            </div>

            <!-- Project Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="text-primary"><?= htmlspecialchars($project['ProjectYear']) ?></h3>
                    <p class="text-muted mb-0">ปีดำเนินการ</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="text-success"><?= count($villages) ?></h3>
                    <p class="text-muted mb-0">หมู่บ้านที่เข้าร่วม</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-info"><?= count($targets) ?></h3>
                    <p class="text-muted mb-0">กลุ่มเป้าหมาย</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-purple">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="text-purple"><?= count($indicators_data) ?></h3>
                    <p class="text-muted mb-0">ตัวชี้วัดที่เกี่ยวข้อง</p>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 class="text-warning"><?= number_format($budget_total) ?></h3>
                    <p class="text-muted mb-0">งบประมาณ (บาท)</p>
                </div>
                <?php if (!empty($sroi_data)): ?>
                <div class="stat-card">
                    <div class="stat-icon text-danger">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-danger"><?= number_format($sroi_data[0]['SROIResult'], 2) ?></h3>
                    <p class="text-muted mb-0">SROI (คะแนน)</p>
                </div>
                <?php endif; ?>
            </div>

        <!-- ข้อมูลโครงการหลัก -->
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-home text-primary me-2"></i>ข้อมูลโครงการหลัก</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong><i class="fas fa-project-diagram text-primary me-2"></i>ชื่อโครงการ:</strong><br>
                            <?= htmlspecialchars($project['ProjectName']) ?>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-barcode text-info me-2"></i>รหัสโครงการ:</strong><br>
                            <span class="badge badge-modern bg-secondary text-white"><?= htmlspecialchars($project['ProjectCode']) ?></span>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-user-tie text-success me-2"></i>ผู้รับผิดชอบ:</strong><br>
                            <?= htmlspecialchars($project['ResponsiblePerson'] ?? 'ไม่ระบุ') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong><i class="fas fa-building text-warning me-2"></i>หน่วยงาน:</strong><br>
                            <?= htmlspecialchars($project['AgencyName']) ?>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-map-marker-alt text-danger me-2"></i>จังหวัด:</strong><br>
                            <?= htmlspecialchars($project['Province']) ?>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-calendar-alt text-info me-2"></i>ปีดำเนินการ:</strong><br>
                            <span class="badge badge-modern bg-info text-white"><?= htmlspecialchars($project['ProjectYear']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="info-item">
                            <strong><i class="fas fa-chess text-purple me-2"></i>ยุทธศาสตร์:</strong><br>
                            <?= htmlspecialchars($project['StrategyName'] ?? 'ไม่ระบุ') ?>
                        </div>
                        <?php if (!empty($project['MainProjectName'])): ?>
                        <div class="info-item">
                            <strong><i class="fas fa-folder-open text-primary me-2"></i>โครงการหลัก:</strong><br>
                            <span class="badge badge-modern bg-primary text-white fs-6">
                                <?= htmlspecialchars($project['MainProjectCode']) ?> - <?= htmlspecialchars($project['MainProjectName']) ?>
                            </span>
                            <?php if (!empty($project['MainProjectDescription'])): ?>
                            <br><small class="text-muted mt-2 d-block"><?= htmlspecialchars($project['MainProjectDescription']) ?></small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- กลุ่มเป้าหมาย -->
        <?php if (!empty($targets)): ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-users text-primary me-2"></i>กลุ่มเป้าหมาย</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($targets as $target): ?>
                    <div class="col-md-4 mb-3">
                        <div class="list-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-modern bg-success"><i class="fas fa-users me-1"></i><?= htmlspecialchars($target['GroupName']) ?></span>
                                <small class="text-muted"><i class="fas fa-user-friends me-1"></i><?= number_format($target['TargetCount']) ?> คน</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-users text-muted me-2"></i>กลุ่มเป้าหมาย</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h5>ไม่มีข้อมูลกลุ่มเป้าหมาย</h5>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- หมู่บ้านที่ดำเนินงาน -->
        <?php if (!empty($villages)): ?>
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-home"></i> หมู่บ้านที่ดำเนินงาน 
                    <span class="badge badge-modern bg-info ms-2"><?= count($villages) ?> หมู่บ้าน</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th><i class="fas fa-home"></i> ชื่อหมู่บ้าน</th>
                                <th><i class="fas fa-map-marker"></i> หมู่ที่</th>
                                <th><i class="fas fa-map"></i> ตำบล</th>
                                <th><i class="fas fa-map"></i> อำเภอ</th>
                                <th><i class="fas fa-map-marker-alt"></i> จังหวัด</th>
                                <th><i class="fas fa-users"></i> ชุมชน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($villages as $village): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($village['VillageName']) ?></strong></td>
                                <td><span class="badge badge-modern bg-secondary"><?= htmlspecialchars($village['Moo']) ?></span></td>
                                <td><?= htmlspecialchars($village['Subdistrict']) ?></td>
                                <td><?= htmlspecialchars($village['District']) ?></td>
                                <td><?= htmlspecialchars($village['Province']) ?></td>
                                <td><?= htmlspecialchars($village['Community']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-home"></i> หมู่บ้านที่ดำเนินงาน</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-home"></i>
                    <h5>ไม่มีข้อมูลหมู่บ้าน</h5>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- โรงเรียน -->
            <?php if (!empty($schools)): ?>
            <div class="col-md-6">
                <div class="section-card animate__animated animate__fadeInLeft">
                    <div class="card-header">
                        <h6><i class="fas fa-school"></i> โรงเรียนที่เข้าร่วม</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($schools as $school): ?>
                        <div class="list-item">
                            <i class="fas fa-graduation-cap text-warning me-2"></i>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($school['SchoolName']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header">
                        <h6><i class="fas fa-school"></i> โรงเรียนที่เข้าร่วม</h6>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-school"></i>
                            <p>ไม่มีข้อมูลโรงเรียน</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- เครือข่าย -->
            <?php if (!empty($networks)): ?>
            <div class="col-md-6">
                <div class="section-card animate__animated animate__fadeInRight">
                    <div class="card-header">
                        <h6><i class="fas fa-network-wired"></i> เครือข่ายที่เข้าร่วม</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($networks as $network): ?>
                        <div class="list-item">
                            <i class="fas fa-globe text-info me-2"></i>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($network['NetworkName']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header">
                        <h6><i class="fas fa-network-wired"></i> เครือข่ายที่เข้าร่วม</h6>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-network-wired"></i>
                            <p>ไม่มีข้อมูลเครือข่าย</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- วิสาหกิจ/ผู้ประกอบการ -->
        <?php if (!empty($enterprises)): ?>
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-store"></i> วิสาหกิจ/ผู้ประกอบการ</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($enterprises as $enterprise): ?>
                    <div class="col-md-6 mb-3">
                        <div class="list-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-modern bg-<?= $enterprise['EnterpriseType'] == 'วิสาหกิจ' ? 'primary' : 'success' ?> me-2">
                                        <i class="fas fa-<?= $enterprise['EnterpriseType'] == 'วิสาหกิจ' ? 'building' : 'user-tie' ?> me-1"></i>
                                        <?= htmlspecialchars($enterprise['EnterpriseType']) ?>
                                    </span>
                                    <br>
                                    <small class="text-muted mt-1"><i class="fas fa-store-alt me-1"></i><?= htmlspecialchars($enterprise['EnterpriseName']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-store text-muted me-2"></i>วิสาหกิจ/ผู้ประกอบการ</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-store"></i>
                    <h5>ไม่มีข้อมูลวิสาหกิจ/ผู้ประกอบการ</h5>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ผลิตภัณฑ์ -->
        <?php if (!empty($products)): ?>
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-box text-warning me-2"></i>ผลิตภัณฑ์</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th><i class="fas fa-tag"></i> ชื่อผลิตภัณฑ์</th>
                                <th><i class="fas fa-list"></i> ประเภท</th>
                                <th><i class="fas fa-info-circle"></i> รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><strong><i class="fas fa-tag text-primary me-2"></i><?= htmlspecialchars($product['ProductName']) ?></strong></td>
                                <td><span class="badge badge-modern bg-info"><i class="fas fa-list me-1"></i><?= htmlspecialchars($product['ProductType']) ?></span></td>
                                <td><small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= htmlspecialchars($product['Description']) ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-box text-muted me-2"></i>ผลิตภัณฑ์</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-box"></i>
                    <h5>ไม่มีข้อมูลผลิตภัณฑ์</h5>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ตัวชี้วัดโครงการ -->
        <?php if (!empty($indicators_data)): ?>
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> ตัวชี้วัดโครงการ 
                    <span class="badge badge-modern bg-primary ms-2"><?= count($indicators_data) ?> รายการ</span>
                </h5>
                <p class="mb-0 text-white-50">
                    <i class="fas fa-info-circle me-1"></i>
                    แสดงเฉพาะตัวชี้วัดที่เกี่ยวข้องกับโครงการ (ปี <?= $project['ProjectYear'] ?>, ยุทธศาสตร์, โครงการหลัก)
                </p>
            </div>
            <div class="card-body">
                <?php 
                // จัดกลุ่มตัวชี้วัดตามชื่อ
                $grouped_indicators = [];
                foreach ($indicators_data as $indicator) {
                    $grouped_indicators[$indicator['IndicatorName']][] = $indicator;
                }
                ?>
                
                <?php foreach ($grouped_indicators as $indicator_name => $indicator_values): ?>
                <div class="indicator-group mb-4">
                    <div class="indicator-header bg-light p-3 rounded-top border">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-primary">
                                    <i class="fas fa-chart-line me-2"></i><?= htmlspecialchars($indicator_name) ?>
                                </h6>
                                <?php if (!empty($indicator_values[0]['Unit'])): ?>
                                <span class="badge badge-modern bg-secondary">
                                    <i class="fas fa-ruler me-1"></i>หน่วย: <?= htmlspecialchars($indicator_values[0]['Unit']) ?>
                                </span>
                                <?php endif; ?>
                                
                                <!-- แสดงความเกี่ยวข้องของตัวชี้วัด -->
                                <?php if (!empty($indicator_values[0]['Year'])): ?>
                                <span class="badge badge-modern bg-info ms-1">
                                    <i class="fas fa-calendar me-1"></i>ปี <?= htmlspecialchars($indicator_values[0]['Year']) ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($indicator_values[0]['StrategyID'])): ?>
                                <span class="badge badge-modern bg-warning ms-1">
                                    <i class="fas fa-chess me-1"></i>ยุทธศาสตร์
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($indicator_values[0]['MainProjectID'])): ?>
                                <span class="badge badge-modern bg-primary ms-1">
                                    <i class="fas fa-folder me-1"></i>โครงการหลัก
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($indicator_values[0]['Description'])): ?>
                                <br><small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i><?= htmlspecialchars($indicator_values[0]['Description']) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-info fs-6"><?= count($indicator_values) ?> ค่า</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="indicator-values bg-white border border-top-0 rounded-bottom">
                        <?php foreach ($indicator_values as $index => $value): ?>
                        <div class="value-item p-3 <?= $index < count($indicator_values) - 1 ? 'border-bottom' : '' ?>">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-hashtag text-primary me-2"></i>
                                        <span class="badge badge-modern bg-success fs-6">
                                            <?= number_format($value['Value'], 2) ?>
                                            <?= !empty($value['Unit']) ? ' ' . htmlspecialchars($value['Unit']) : '' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php if (!empty($value['Details'])): ?>
                                    <div class="details-box bg-light p-2 rounded">
                                        <small class="text-muted">
                                            <i class="fas fa-list me-1"></i>
                                            <strong>รายละเอียดเพิ่มเติม:</strong>
                                        </small>
                                        <div class="mt-1">
                                            <?php foreach ($value['Details'] as $detail): ?>
                                            <span class="badge bg-info me-1 mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($detail) ?>
                                            </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <small class="text-muted">
                                        <i class="fas fa-minus me-1"></i>ไม่มีรายละเอียดเพิ่มเติม
                                    </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        บันทึกเมื่อ: <?= date('d/m/Y H:i', strtotime($value['CreatedAt'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- สรุปสถิติของตัวชี้วัดนี้ -->
                        <?php if (count($indicator_values) > 1): ?>
                        <div class="stats-summary bg-primary bg-opacity-10 p-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <i class="fas fa-chart-line text-success"></i>
                                        <div class="fw-bold text-success">
                                            <?= number_format(max(array_column($indicator_values, 'Value')), 2) ?>
                                        </div>
                                        <small class="text-muted">ค่าสูงสุด</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <i class="fas fa-chart-line text-warning"></i>
                                        <div class="fw-bold text-warning">
                                            <?= number_format(array_sum(array_column($indicator_values, 'Value')) / count($indicator_values), 2) ?>
                                        </div>
                                        <small class="text-muted">ค่าเฉลี่ย</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <i class="fas fa-chart-line text-danger"></i>
                                        <div class="fw-bold text-danger">
                                            <?= number_format(min(array_column($indicator_values, 'Value')), 2) ?>
                                        </div>
                                        <small class="text-muted">ค่าต่ำสุด</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> ตัวชี้วัดโครงการ</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-chart-bar"></i>
                    <h5>ไม่มีข้อมูลตัวชี้วัดที่เกี่ยวข้อง</h5>
                    <p class="text-muted">
                        โครงการนี้ยังไม่มีการกรอกข้อมูลตัวชี้วัดที่เกี่ยวข้องกับ<br>
                        - ปี <?= $project['ProjectYear'] ?><br>
                        - ยุทธศาสตร์ที่กำหนด<br>
                        - โครงการหลักที่เกี่ยวข้อง
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ข้อมูล SROI -->
        <?php if (!empty($sroi_data)): ?>
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> SROI (Social Return on Investment) 
                    <span class="badge badge-modern bg-danger ms-2"><?= count($sroi_data) ?> รายการ</span>
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($sroi_data as $index => $sroi): ?>
                <div class="info-item mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="stat-icon text-danger">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3 class="text-danger fw-bold mb-0"><?= number_format($sroi['SROIResult'], 2) ?></h3>
                                <small class="text-muted">คะแนน SROI</small>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <?php if (!empty($sroi['Description'])): ?>
                            <div class="note-box bg-light p-3 rounded">
                                <strong><i class="fas fa-info-circle text-info me-2"></i>รายละเอียด:</strong><br>
                                <div class="mt-2"><?= nl2br(htmlspecialchars($sroi['Description'])) ?></div>
                            </div>
                            <?php else: ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-info-circle"></i>
                                <small>ไม่มีรายละเอียดเพิ่มเติม</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if (count($sroi_data) > 1 && $index < count($sroi_data) - 1): ?>
                <hr class="my-3">
                <?php endif; ?>
                <?php endforeach; ?>
                
                
                
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line text-danger me-2"></i>SROI (Social Return on Investment)</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h5>ไม่มีข้อมูล SROI</h5>
                    <p class="text-muted">โครงการนี้ยังไม่มีการประเมิน SROI</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ข้อมูลเพิ่มเติม -->
        <div class="row">
            <!-- มหาวิทยาลัย -->
            <?php if (!empty($universities)): ?>
            <div class="col-md-6">
                <div class="section-card animate__animated animate__fadeInLeft">
                    <div class="card-header">
                        <h6><i class="fas fa-university"></i> มหาวิทยาลัยที่เข้าร่วม</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($universities as $university): ?>
                        <div class="list-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fas fa-university text-primary me-2"></i>
                                    <strong><?= htmlspecialchars($university['UniversityName']) ?></strong>
                                    <?php if (!empty($university['UniversityType'])): ?>
                                    <br><small class="badge badge-modern bg-info ms-4"><?= htmlspecialchars($university['UniversityType']) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($university['Collaboration'])): ?>
                                    <br><small class="text-muted ms-4"><i class="fas fa-handshake me-1"></i><?= htmlspecialchars($university['Collaboration']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header">
                        <h6><i class="fas fa-university"></i> มหาวิทยาลัยที่เข้าร่วม</h6>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-university"></i>
                            <p>ไม่มีข้อมูลมหาวิทยาลัย</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- องค์กรปกครองส่วนท้องถิ่น -->
            <?php if (!empty($local_admins)): ?>
            <div class="col-md-6">
                <div class="section-card animate__animated animate__fadeInRight">
                    <div class="card-header">
                        <h6><i class="fas fa-building"></i> องค์กรปกครองส่วนท้องถิ่น</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($local_admins as $admin): ?>
                        <div class="list-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fas fa-building text-warning me-2"></i>
                                    <strong><?= htmlspecialchars($admin['AdminName']) ?></strong>
                                    <?php if (!empty($admin['AdminType'])): ?>
                                    <br><small class="badge badge-modern bg-warning ms-4"><?= htmlspecialchars($admin['AdminType']) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($admin['SupportType'])): ?>
                                    <br><small class="text-muted ms-4"><i class="fas fa-hands-helping me-1"></i><?= htmlspecialchars($admin['SupportType']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="col-md-6">
                <div class="section-card">
                    <div class="card-header">
                        <h6><i class="fas fa-building"></i> องค์กรปกครองส่วนท้องถิ่น</h6>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <p>ไม่มีข้อมูลองค์กรปกครอง</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- องค์กรอื่นๆ -->
        <?php if (!empty($others)): ?>
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-users-cog"></i> องค์กรอื่นๆ ที่เข้าร่วมโครงการ 
                    <span class="badge badge-modern bg-secondary ms-2"><?= count($others) ?> องค์กร</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($others as $other): ?>
                    <div class="col-md-6 mb-3">
                        <div class="list-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <i class="fas fa-users-cog text-secondary me-2"></i>
                                    <strong><?= htmlspecialchars($other['OrganizationName']) ?></strong>
                                    <?php if (!empty($other['OrganizationType'])): ?>
                                    <br><small class="badge badge-modern bg-secondary ms-4"><?= htmlspecialchars($other['OrganizationType']) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($other['Role'])): ?>
                                    <br><small class="text-primary ms-4"><i class="fas fa-user-tag me-1"></i><?= htmlspecialchars($other['Role']) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($other['Description'])): ?>
                                    <br><small class="text-muted ms-4"><i class="fas fa-info-circle me-1"></i><?= htmlspecialchars($other['Description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card">
            <div class="card-header">
                <h5><i class="fas fa-users-cog"></i> องค์กรอื่นๆ ที่เข้าร่วมโครงการ</h5>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-users-cog"></i>
                    <h5>ไม่มีข้อมูลองค์กรอื่นๆ</h5>
                    <p class="text-muted">โครงการนี้ยังไม่มีองค์กรอื่นๆ ที่เข้าร่วม</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
