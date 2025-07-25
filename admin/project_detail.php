<?php include 'db.php'; ?>
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
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดโครงการ - <?= htmlspecialchars($project['ProjectName']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            padding: 2rem;
            max-width: 1200px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .page-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.2rem;
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
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .section-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .section-card .card-header {
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        
        .section-card .card-header h5,
        .section-card .card-header h6 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-card .card-body {
            padding: 1.5rem;
        }
        
        .info-item {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        
        .info-item strong {
            color: #667eea;
            font-weight: 600;
        }
        
        .badge-modern {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .table-modern {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .table-modern thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table-modern tbody tr {
            transition: background-color 0.3s ease;
        }
        
        .table-modern tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
        
        .table-modern td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .btn-action {
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .list-item {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .list-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(5px);
        }
        
        .alert-modern {
            border-radius: 15px;
            border: none;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .section-divider {
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1px;
            margin: 2rem 0;
        }
        
        /* Styles for Indicators */
        .indicator-group {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .indicator-group:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .indicator-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
            border-bottom: 2px solid #667eea !important;
        }
        
        .value-item {
            transition: background-color 0.3s ease;
        }
        
        .value-item:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
        }
        
        .note-box {
            border-left: 3px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .note-box:hover {
            background-color: rgba(102, 126, 234, 0.1) !important;
            transform: translateX(5px);
        }
        
        .stats-summary {
            border-top: 2px solid #667eea;
        }
        
        .stat-item {
            padding: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .stat-item:hover {
            transform: scale(1.05);
        }
        
        .text-purple {
            color: #667eea !important;
        }
        
        .indicator-values {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .indicator-values::-webkit-scrollbar {
            width: 6px;
        }
        
        .indicator-values::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .indicator-values::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 3px;
        }
        
        .indicator-values::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="main-container animate__animated animate__fadeIn">
            
            <!-- Page Header -->
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="projects_list.php"><i class="fas fa-list"></i> รายการโครงการ</a></li>
                        <li class="breadcrumb-item active">รายละเอียด</li>
                    </ol>
                </nav>
                <h1><i class="fas fa-file-alt"></i> รายละเอียดโครงการ</h1>
                <p class="mb-0"><?= htmlspecialchars($project['ProjectName']) ?></p>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <!-- <a href="edit_project.php?id=<?= $id ?>" class="btn btn-warning btn-action" disabled>
                    <i class="fas fa-edit"></i> แก้ไขโครงการ
                </a> -->
                <a href="projects_table_view.php" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-left"></i> กลับสู่รายการ
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
            </div>

        <!-- ข้อมูลโครงการหลัก -->
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-folder-open"></i> ข้อมูลโครงการหลัก</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong><i class="fas fa-tag"></i> ชื่อโครงการ:</strong><br>
                            <?= htmlspecialchars($project['ProjectName']) ?>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-barcode"></i> รหัสโครงการ:</strong><br>
                            <span class="badge badge-modern bg-secondary"><?= htmlspecialchars($project['ProjectCode']) ?></span>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-user-tie"></i> ผู้รับผิดชอบ:</strong><br>
                            <?= htmlspecialchars($project['ResponsiblePerson'] ?? 'ไม่ระบุ') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong><i class="fas fa-building"></i> หน่วยงาน:</strong><br>
                            <?= htmlspecialchars($project['AgencyName']) ?>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-map-marker-alt"></i> จังหวัด:</strong><br>
                            <?= htmlspecialchars($project['Province']) ?>
                        </div>
                        <div class="info-item">
                            <strong><i class="fas fa-calendar"></i> ปีดำเนินการ:</strong><br>
                            <span class="badge badge-modern bg-info"><?= htmlspecialchars($project['ProjectYear']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="info-item">
                            <strong><i class="fas fa-chess"></i> ยุทธศาสตร์:</strong><br>
                            <?= htmlspecialchars($project['StrategyName'] ?? 'ไม่ระบุ') ?>
                        </div>
                        <?php if (!empty($project['MainProjectName'])): ?>
                        <div class="info-item">
                            <strong><i class="fas fa-folder"></i> โครงการหลัก:</strong><br>
                            <span class="badge badge-modern bg-primary fs-6">
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
        <div class="section-card animate__animated animate__fadeInUp">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> กลุ่มเป้าหมาย</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($targets as $target): ?>
                    <div class="col-md-4 mb-3">
                        <div class="list-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-modern bg-success"><?= htmlspecialchars($target['GroupName']) ?></span>
                                <small class="text-muted"><i class="fas fa-user"></i> <?= number_format($target['TargetCount']) ?> คน</small>
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
                <h5><i class="fas fa-users"></i> กลุ่มเป้าหมาย</h5>
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
                            <?= htmlspecialchars($school['SchoolName']) ?>
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
                            <?= htmlspecialchars($network['NetworkName']) ?>
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
                                        <i class="fas fa-<?= $enterprise['EnterpriseType'] == 'วิสาหกิจ' ? 'building' : 'user-tie' ?>"></i>
                                        <?= htmlspecialchars($enterprise['EnterpriseType']) ?>
                                    </span>
                                    <br>
                                    <small class="text-muted mt-1"><?= htmlspecialchars($enterprise['EnterpriseName']) ?></small>
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
                <h5><i class="fas fa-store"></i> วิสาหกิจ/ผู้ประกอบการ</h5>
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
                <h5><i class="fas fa-box"></i> ผลิตภัณฑ์</h5>
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
                                <td><strong><?= htmlspecialchars($product['ProductName']) ?></strong></td>
                                <td><span class="badge badge-modern bg-info"><?= htmlspecialchars($product['ProductType']) ?></span></td>
                                <td><small class="text-muted"><?= htmlspecialchars($product['Description']) ?></small></td>
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
                <h5><i class="fas fa-box"></i> ผลิตภัณฑ์</h5>
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

    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add smooth animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Animate cards on scroll
        const cards = document.querySelectorAll('.section-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        });
        
        cards.forEach(card => {
            observer.observe(card);
        });
        
        // Add hover effects to action buttons
        document.querySelectorAll('.btn-action').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

</body>
</html>
