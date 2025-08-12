<?php 
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ../login.php');
    exit;
}

// Include database connection with error handling
$db_files = [
    '../database/db.php',
    './db.php',
    './database/db.php',
    '../db.php'
];

$db_connected = false;
foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        include $db_file;
        $db_connected = true;
        break;
    }
}

if (!$db_connected) {
    die('Error: Database connection file not found.');
}

include '../navbar.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แดชบอร์ดกราฟและแผนภูมิ - ระบบจัดการโครงการ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Noto Sans Thai Looped -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-select, .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .chart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .btn-chart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-chart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-add-chart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .btn-add-chart:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
            color: white;
        }
        
        .chart-canvas {
            max-height: 400px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .stats-card:hover::before {
            left: 100%;
        }
        
        /* Clickable Cards Styles */
        .clickable-card {
            position: relative;
        }
        
        .clickable-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        
        /* SweetAlert2 Custom Styles */
        .stats-modal {
            font-family: 'Noto Sans Thai Looped', sans-serif !important;
        }
        
        .stats-modal-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
        }
        
        .stats-modal-content .detail-info {
            text-align: left;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 15px 0;
        }
        
        .stats-modal-content .detail-info h5 {
            color: #495057;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .stats-modal-content .detail-info ul li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .stats-modal-content .detail-info ul li:last-child {
            border-bottom: none;
        }
        
        /* Fullscreen Modal Styles */
        .stats-modal-fullscreen {
            padding: 20px !important;
        }
        
        .stats-modal-content-fullscreen {
            max-height: 85vh !important;
            overflow-y: auto !important;
            padding: 10px !important;
        }
        
        .no-charts {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-charts i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .chart-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-add-chart {
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-2">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-chart-bar me-3"></i>แดชบอร์ดกราฟและแผนภูมิ</h1>
            <p class="mb-0">สร้างและจัดการกราฟแผนภูมิเพื่อวิเคราะห์ข้อมูลโครงการ</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-filter me-2"></i>เครื่องมือกรองข้อมูล</h5>
                <a href="chart_builder.php" class="btn btn-chart">
                    <i class="fas fa-magic me-1"></i>ตัวสร้างกราฟขั้นสูง
                </a>
            </div>
            
            <form id="filterForm">
                <!-- Row 1: Primary Filters -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #007bff;"><i class="fas fa-calendar-alt me-2" style="color: #007bff;"></i>ปีโครงการ (เริ่มต้น)</label>
                        <select class="form-select" id="projectYearStartFilter" name="project_year_start">
                            <option value="">ไม่กำหนด</option>
                            <?php
                            $years = $conn->query("SELECT DISTINCT ProjectYear FROM projects WHERE ProjectYear IS NOT NULL ORDER BY ProjectYear ASC");
                            while ($year = $years->fetch_assoc()) {
                                $selected = (isset($_GET['project_year_start']) && $_GET['project_year_start'] == $year['ProjectYear']) ? 'selected' : '';
                                echo "<option value='{$year['ProjectYear']}' $selected>พ.ศ. {$year['ProjectYear']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #007bff;"><i class="fas fa-calendar-check me-2" style="color: #007bff;"></i>ปีโครงการ (สิ้นสุด)</label>
                        <select class="form-select" id="projectYearEndFilter" name="project_year_end">
                            <option value="">ไม่กำหนด</option>
                            <?php
                            $years = $conn->query("SELECT DISTINCT ProjectYear FROM projects WHERE ProjectYear IS NOT NULL ORDER BY ProjectYear DESC");
                            while ($year = $years->fetch_assoc()) {
                                $selected = (isset($_GET['project_year_end']) && $_GET['project_year_end'] == $year['ProjectYear']) ? 'selected' : '';
                                echo "<option value='{$year['ProjectYear']}' $selected>พ.ศ. {$year['ProjectYear']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #dc3545;"><i class="fas fa-map-marked-alt me-2" style="color: #dc3545;"></i>จังหวัด</label>
                        <select class="form-select" id="provinceFilter" name="province">
                            <option value="">ทุกจังหวัด</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #dc3545;"><i class="fas fa-city me-2" style="color: #dc3545;"></i>อำเภอ</label>
                        <select class="form-select" id="districtFilter" name="district">
                            <option value="">ทุกอำเภอ</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                </div>

                <!-- Row 1.5: Location Filters (Second Row for Location) -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #dc3545;"><i class="fas fa-map-pin me-2" style="color: #dc3545;"></i>ตำบล</label>
                        <select class="form-select" id="subdistrictFilter" name="subdistrict">
                            <option value="">ทุกตำบล</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #dc3545;"><i class="fas fa-home me-2" style="color: #dc3545;"></i>หมู่บ้าน/ชุมชน</label>
                        <select class="form-select" id="villageFilter" name="village">
                            <option value="">ทุกหมู่บ้าน/ชุมชน</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <!-- Empty column for spacing -->
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <!-- Empty column for spacing -->
                    </div>
                </div>

                <!-- Row 2: Secondary Filters -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #007bff;"><i class="fas fa-sitemap me-2" style="color: #007bff;"></i>โครงการหลัก</label>
                        <select class="form-select" id="mainProjectFilter" name="main_project">
                            <option value="">ทุกโครงการหลัก</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #28a745;"><i class="fas fa-chess me-2" style="color: #28a745;"></i>ยุทธศาสตร์</label>
                        <select class="form-select" id="strategyFilter" name="strategy">
                            <option value="">ทุกยุทธศาสตร์</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #17a2b8;"><i class="fas fa-building me-2" style="color: #17a2b8;"></i>หน่วยงาน</label>
                        <select class="form-select" id="agencyFilter" name="agency">
                            <option value="">ทุกหน่วยงาน</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #6f42c1;"><i class="fas fa-users me-2" style="color: #6f42c1;"></i>กลุ่มเป้าหมาย</label>
                        <select class="form-select" id="targetGroupFilter" name="target_group">
                            <option value="">ทุกกลุ่มเป้าหมาย</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                </div>

                <!-- Row 3: Additional Filters -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label" style="color: #ffc107;"><i class="fas fa-user-tie me-2" style="color: #ffc107;"></i>อาจารย์/ผู้รับผิดชอบ</label>
                        <select class="form-select" id="teacherFilter" name="teacher">
                            <option value="">ทุกอาจารย์/ผู้รับผิดชอบ</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <!-- Empty column for future expansion -->
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <!-- Empty column for future expansion -->
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <!-- Empty column for future expansion -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-chart me-2" onclick="applyFilters()">
                                <i class="fas fa-search me-1"></i>ค้นหา
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="fas fa-times me-2"></i>ล้างตัวกรอง
                            </button>
                            <div class="ms-auto">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>กราฟจะอัพเดทอัตโนมัติเมื่อเปลี่ยนตัวกรอง
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 mx-1 mt-3" id="statsCards">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="2" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
                    <i class="fas fa-project-diagram fa-2x mb-2"></i>
                    <h3 id="totalProjects">-</h3>
                    <p class="mb-0">โครงการทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="5" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-coins fa-2x mb-2"></i>
                    <h3 id="totalBudget">-</h3>
                    <p class="mb-0">งบประมาณรวม (ล้านบาท)</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="9" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h3 id="totalIndicators">-</h3>
                    <p class="mb-0">ตัวชี้วัดทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="13" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                    <h3 id="totalLocations">-</h3>
                    <p class="mb-0">พื้นที่ดำเนินการ</p>
                </div>
            </div>
        </div>

        <!-- Row 2: Additional Stats -->
        <div class="row mb-4 mx-1 mt-2" id="additionalStats">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="10" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <h3 id="totalProducts">-</h3>
                    <p class="mb-0">ผลิตภัณฑ์ทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="11" style="background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%);">
                    <i class="fas fa-school fa-2x mb-2"></i>
                    <h3 id="totalSchools">-</h3>
                    <p class="mb-0">โรงเรียนที่เข้าร่วม</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="12" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 id="totalTargetGroups">-</h3>
                    <p class="mb-0">กลุ่มเป้าหมายทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card clickable-card" data-column="8" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <i class="fas fa-building fa-2x mb-2"></i>
                    <h3 id="totalAgencies">-</h3>
                    <p class="mb-0">หน่วยงานที่เข้าร่วม</p>
                </div>
            </div>
        </div>

        <!-- Charts Container -->
        <div id="chartsContainer">
            <!-- Default Charts will be loaded here -->
        </div>

        <!-- Saved Charts Section -->
        <div class="mt-5" id="savedChartsSection">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h4><i class="fas fa-bookmark me-2 text-success"></i>กราฟที่บันทึกไว้</h4>
                    <p class="text-muted mb-0">กราฟที่สร้างและบันทึกไว้จากตัวสร้างกราฟขั้นสูง</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-success" onclick="loadSavedCharts()">
                        <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                    </button>
                </div>
            </div>
            
            <!-- Saved Charts Container -->
            <div id="savedChartsContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">กำลังโหลดกราฟที่บันทึกไว้...</p>
                </div>
            </div>
            
            <!-- Pagination for Saved Charts -->
            <div id="savedChartsPagination" class="d-flex justify-content-center mt-4" style="display: none !important;">
                <!-- Pagination will be loaded here -->
            </div>
        </div>

        <!-- No Charts Message -->
        <div class="no-charts" id="noChartsMessage" style="display: none;">
            <i class="fas fa-chart-pie"></i>
            <h4>ยังไม่มีกราฟแสดงผล</h4>
            <p>คลิกปุ่ม + เพื่อเพิ่มกราฟใหม่</p>
        </div>
    </div>

    <!-- Add Chart Button -->
    <button class="btn-add-chart" onclick="showAddChartModal()">
        <i class="fas fa-plus"></i>
    </button>

    <!-- Add Chart Modal -->
    <div class="modal fade" id="addChartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มกราฟใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addChartForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ชื่อกราฟ</label>
                                <input type="text" class="form-control" id="chartTitle" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ประเภทกราฟ</label>
                                <select class="form-select" id="chartType" required>
                                    <option value="">เลือกประเภทกราฟ</option>
                                    <option value="bar">แผนภูมิแท่ง (Bar Chart)</option>
                                    <option value="line">แผนภูมิเส้น (Line Chart)</option>
                                    <option value="pie">แผนภูมิวงกลม (Pie Chart)</option>
                                    <option value="doughnut">แผนภูมิโดนัท (Doughnut Chart)</option>
                                    <option value="radar">แผนภูมิเรดาร์ (Radar Chart)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ข้อมูลแกน X</label>
                                <select class="form-select" id="xAxisData" required>
                                    <option value="">เลือกข้อมูลแกน X</option>
                                    <option value="project_year">ปีโครงการ</option>
                                    <option value="strategy">ยุทธศาสตร์</option>
                                    <option value="main_project">โครงการหลัก</option>
                                    <option value="agency">หน่วยงาน</option>
                                    <option value="province">จังหวัด</option>
                                    <option value="target_group">กลุ่มเป้าหมาย</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ข้อมูลแกน Y</label>
                                <select class="form-select" id="yAxisData" required>
                                    <option value="">เลือกข้อมูลแกน Y</option>
                                    <option value="project_count">จำนวนโครงการ</option>
                                    <option value="budget_sum">งบประมาณรวม</option>
                                    <option value="target_count">จำนวนกลุ่มเป้าหมาย</option>
                                    <option value="sroi_avg">SROI เฉลี่ย</option>
                                    <option value="indicator_count">จำนวนตัวชี้วัด</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-chart" onclick="addChart()">สร้างกราฟ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let chartInstances = {};
        let chartCounter = 0;
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initializeDynamicFilters();
            loadDefaultCharts();
            updateStats();
            loadSavedCharts();
            initializeStatsCardHandlers();
        });
        
        // Initialize stats card click handlers
        function initializeStatsCardHandlers() {
            $('.clickable-card').on('click', function() {
                const cardType = $(this).data('column');
                const cardText = $(this).find('p').text();
                const cardValue = $(this).find('h3').text();
                const cardIcon = $(this).find('i').attr('class');
                
                // Define detail queries based on card type
                const detailQueries = {
                    '2': 'projects_detail',
                    '5': 'budget_detail', 
                    '9': 'indicators_detail',
                    '13': 'locations_detail',
                    '10': 'products_detail',
                    '11': 'schools_detail',
                    '12': 'target_groups_detail',
                    '8': 'agencies_detail'
                };
                
                const detailQuery = detailQueries[cardType];
                if (detailQuery) {
                    showStatsCardModal(cardType, cardText, cardValue, cardIcon, detailQuery);
                }
            });
        }
        
        function showStatsCardModal(cardType, cardText, cardValue, cardIcon, detailQuery) {
            // Get current filter values for the modal
            const filters = getFilterValues();
            const params = {
                type: detailQuery,
                project_year_start: $('#projectYearStartFilter').val() || '',
                project_year_end: $('#projectYearEndFilter').val() || '',
                province: $('#provinceFilter').val() || '',
                district: $('#districtFilter').val() || '',
                subdistrict: $('#subdistrictFilter').val() || '',
                village: $('#villageFilter').val() || '',
                main_project: $('#mainProjectFilter').val() || '',
                strategy: $('#strategyFilter').val() || '',
                agency: $('#agencyFilter').val() || '',
                target_group: $('#targetGroupFilter').val() || '',
                teacher: $('#teacherFilter').val() || ''
            };

            // Show loading modal first
            Swal.fire({
                title: 'กำลังโหลดข้อมูล...',
                html: '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Fetch detailed data
            $.ajax({
                url: '../get_stats_detail.php',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(data) {
                    if (data && data.success) {
                        displayDetailedStatsModal(cardType, cardText, cardValue, cardIcon, getCardBgColor(cardType), data.data);
                    } else {
                        displayBasicStatsModal(cardType, cardText, cardValue, cardIcon, getCardBgColor(cardType));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading stats detail:', error);
                    displayBasicStatsModal(cardType, cardText, cardValue, cardIcon, getCardBgColor(cardType));
                }
            });
        }

        function getCardBgColor(cardType) {
            const colors = {
                '2': '#007bff',   // โครงการ
                '5': '#28a745',   // งบประมาณ
                '9': '#ffc107',   // ตัวชี้วัด
                '13': '#dc3545',  // พื้นที่
                '10': '#28a745',  // ผลิตภัณฑ์
                '11': '#fd7e14',  // โรงเรียน
                '12': '#6f42c1',  // กลุ่มเป้าหมาย
                '8': '#17a2b8'    // หน่วยงาน
            };
            return colors[cardType] || '#6c757d';
        }

        function displayDetailedStatsModal(cardType, cardText, cardValue, cardIcon, bgColor, data) {
            let htmlContent = `
                <div class="text-center mb-4">
                    <div style="background: ${bgColor}; width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <i class="${cardIcon}" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h3 style="color: ${bgColor}; font-weight: 600;">${cardValue}</h3>
                    <p class="text-muted mb-0">${cardText}</p>
                </div>
            `;

            if (data && data.length > 0) {
                htmlContent += '<div class="detail-info"><h5>รายละเอียด</h5><ul class="list-unstyled">';
                data.slice(0, 10).forEach(function(item) {
                    htmlContent += `<li>${item.name || item.title || item.description || JSON.stringify(item)}</li>`;
                });
                if (data.length > 10) {
                    htmlContent += `<li class="text-muted">... และอีก ${data.length - 10} รายการ</li>`;
                }
                htmlContent += '</ul></div>';
            }

            Swal.fire({
                html: htmlContent,
                showConfirmButton: true,
                confirmButtonText: 'ปิด',
                customClass: {
                    popup: 'stats-modal stats-modal-fullscreen',
                    content: 'stats-modal-content-fullscreen'
                },
                width: '90%',
                heightAuto: false
            });
        }

        function displayBasicStatsModal(cardType, cardText, cardValue, cardIcon, bgColor) {
            Swal.fire({
                html: `
                    <div class="text-center">
                        <div style="background: ${bgColor}; width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                            <i class="${cardIcon}" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h3 style="color: ${bgColor}; font-weight: 600;">${cardValue}</h3>
                        <p class="text-muted mb-0">${cardText}</p>
                        <p class="mt-3 text-sm text-muted">ตามเงื่อนไขการกรองปัจจุบัน</p>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'ปิด',
                customClass: {
                    popup: 'stats-modal'
                }
            });
        }
        
        // ฟังก์ชันสำหรับโหลดข้อมูลแบบ Dynamic
        function initializeDynamicFilters() {
            // Load initial data - โหลดข้อมูลทั้งหมดเริ่มต้น (ไม่รวม project_years เพราะใช้ PHP แล้ว)
            loadFilterData('provinces');
            loadFilterData('districts');
            loadFilterData('subdistricts');
            loadFilterData('villages');
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');
            loadFilterData('teachers');

            // Event handlers สำหรับ Flexible Cascading Dropdowns
            $('#projectYearStartFilter, #projectYearEndFilter').on('change', function() {
                loadAllFilterData();
                // Auto reload charts when year filter changes
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 800);
            });

            // จังหวัด -> อำเภอ, ตำบล, หมู่บ้าน
            $('#provinceFilter').on('change', function() {
                const province = $(this).val();
                
                if (province) {
                    // โหลดอำเภอที่เกี่ยวข้องกับจังหวัดที่เลือก
                    loadFilterData('districts');
                    loadFilterData('subdistricts');
                    loadFilterData('villages');
                } else {
                    // เมื่อเลือก "ทุกจังหวัด" ให้โหลดข้อมูลทั้งหมดใหม่
                    loadAllFilterData();
                }
                
                loadOtherFilterData();
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            // อำเภอ -> ตำบล, หมู่บ้าน และอัปเดตจังหวัด
            $('#districtFilter').on('change', function() {
                const district = $(this).val();
                
                if (district) {
                    // โหลดตำบลที่เกี่ยวข้องกับอำเภอที่เลือก
                    loadFilterData('subdistricts');
                    loadFilterData('villages');
                    // โหลดจังหวัดที่เกี่ยวข้องกับอำเภอที่เลือก
                    loadFilterData('provinces');
                } else {
                    // เมื่อเลือก "ทุกอำเภอ" ให้โหลดข้อมูลที่เกี่ยวข้องใหม่
                    loadFilterData('subdistricts');
                    loadFilterData('villages');
                    loadFilterData('provinces');
                }
                
                loadOtherFilterData();
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            // ตำบล -> หมู่บ้าน และอัปเดตอำเภอ
            $('#subdistrictFilter').on('change', function() {
                const subdistrict = $(this).val();
                
                if (subdistrict) {
                    // โหลดหมู่บ้านที่เกี่ยวข้องกับตำบลที่เลือก
                    loadFilterData('villages');
                    // โหลดอำเภอที่เกี่ยวข้องกับตำบลที่เลือก
                    loadFilterData('districts');
                } else {
                    // เมื่อเลือก "ทุกตำบล" ให้โหลดข้อมูลหมู่บ้านและอำเภอใหม่
                    loadFilterData('villages');
                    loadFilterData('districts');
                }
                
                loadOtherFilterData();
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            // หมู่บ้าน -> อัปเดตอำเภอ, ตำบล
            $('#villageFilter').on('change', function() {
                const village = $(this).val();
                
                if (village) {
                    // โหลดตัวกรองที่เกี่ยวข้องทั้งหมด
                    loadFilterData('provinces');
                    loadFilterData('districts');
                    loadFilterData('subdistricts');
                } else {
                    // เมื่อเลือก "ทุกหมู่บ้าน" ให้โหลดข้อมูลทั้งหมดใหม่
                    loadAllFilterData();
                }
                
                loadOtherFilterData();
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            $('#mainProjectFilter').on('change', function() {
                loadAllFilterData();
                
                // Auto reload charts when filter changes
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            $('#strategyFilter').on('change', function() {
                loadAllFilterData();
                
                // Auto reload charts when filter changes
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            $('#agencyFilter').on('change', function() {
                loadAllFilterData();
                
                // Auto reload charts when filter changes
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            $('#targetGroupFilter').on('change', function() {
                loadAllFilterData();
                
                // Auto reload charts when filter changes
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });

            $('#teacherFilter').on('change', function() {
                loadAllFilterData();
                
                // Auto reload charts when filter changes
                setTimeout(function() {
                    refreshAllCharts();
                    updateStats();
                }, 500);
            });
        }

        function loadAllFilterData() {
            loadFilterData('provinces');
            loadFilterData('districts');
            loadFilterData('subdistricts');
            loadFilterData('villages');
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');
            loadFilterData('teachers');
        }

        function loadOtherFilterData() {
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');
            loadFilterData('teachers');
        }

        function loadFilterData(type) {
            console.log(`%c🔄 Loading Filter Data: %c${type}`, 'color: #17a2b8; font-weight: bold;', 'color: #333; background: #e1f5fe; padding: 2px 6px; border-radius: 3px;');
            
            const params = {
                type: type,
                project_year_start: $('#projectYearStartFilter').val(),
                project_year_end: $('#projectYearEndFilter').val(),
                province: $('#provinceFilter').val(),
                district: $('#districtFilter').val(),
                subdistrict: $('#subdistrictFilter').val(),
                village: $('#villageFilter').val(),
                main_project: $('#mainProjectFilter').val(),
                strategy: $('#strategyFilter').val(),
                agency: $('#agencyFilter').val(),
                teacher: $('#teacherFilter').val()
            };

            $.ajax({
                // Load filtered data path ../../api/get_filtered_data.php DON'T CHANGE!
                url: (function() {
                    // ตรวจสอบว่าไฟล์อยู่ที่ไหน โดยดูจาก path ปัจจุบัน
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('/admin/charts/')) {
                        // สำหรับ local environment
                        return '../../api/get_filtered_data.php';
                    } else {
                        // สำหรับ server environment
                        return '../api/get_filtered_data.php';
                    }
                })(),
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(data) {
                    console.log(`%c✅ Filter Data Loaded: %c${type} %c(${data ? data.length : 0} items)`, 
                        'color: #28a745; font-weight: bold;', 
                        'color: #333; background: #d4edda; padding: 2px 6px; border-radius: 3px;',
                        'color: #6c757d;'
                    );
                    updateSelectOptions(type, data);
                },
                error: function(xhr, status, error) {
                    console.log(`%c❌ Filter Data Load Failed: %c${type}`, 
                        'color: #dc3545; font-weight: bold;', 
                        'color: #333; background: #f8d7da; padding: 2px 6px; border-radius: 3px;'
                    );
                    console.error('Error details:', error);
                }
            });
        }

        function updateSelectOptions(type, data) {
            let selector, valueField, textField, defaultText;

            switch (type) {
                case 'provinces':
                    selector = '#provinceFilter';
                    valueField = 'Province';
                    textField = 'Province';
                    defaultText = 'ทุกจังหวัด';
                    break;
                case 'districts':
                    selector = '#districtFilter';
                    valueField = 'District';
                    textField = 'District';
                    defaultText = 'ทุกอำเภอ';
                    break;
                case 'subdistricts':
                    selector = '#subdistrictFilter';
                    valueField = 'Subdistrict';
                    textField = 'Subdistrict';
                    defaultText = 'ทุกตำบล';
                    break;
                case 'villages':
                    selector = '#villageFilter';
                    valueField = 'VillageName';
                    textField = 'DisplayName';
                    defaultText = 'ทุกหมู่บ้าน/ชุมชน';
                    break;
                case 'main_projects':
                    selector = '#mainProjectFilter';
                    valueField = 'MainProjectID';
                    textField = 'MainProjectName';
                    defaultText = 'ทุกโครงการหลัก';
                    break;
                case 'strategies':
                    selector = '#strategyFilter';
                    valueField = 'StrategyID';
                    textField = 'StrategyName';
                    defaultText = 'ทุกยุทธศาสตร์';
                    break;
                case 'agencies':
                    selector = '#agencyFilter';
                    valueField = 'AgencyName';
                    textField = 'AgencyName';
                    defaultText = 'ทุกหน่วยงาน';
                    break;
                case 'target_groups':
                    selector = '#targetGroupFilter';
                    valueField = 'GroupID';
                    textField = 'GroupName';
                    defaultText = 'ทุกกลุ่มเป้าหมาย';
                    break;
                case 'teachers':
                    selector = '#teacherFilter';
                    valueField = 'ResponsiblePerson';
                    textField = 'ResponsiblePerson';
                    defaultText = 'ทุกอาจารย์/ผู้รับผิดชอบ';
                    break;
            }

            const currentValue = $(selector).val();
            $(selector).html(`<option value="">${defaultText}</option>`);
            
            if (data && data.length > 0) {
                data.forEach(function(item) {
                    const value = item[valueField];
                    const text = item[textField];
                    const selected = currentValue === value ? 'selected' : '';
                    $(selector).append(`<option value="${value}" ${selected}>${text}</option>`);
                });
            }
            
            // ถ้าค่าที่เลือกไว้ก่อนหน้าไม่อยู่ในรายการใหม่ ให้รีเซ็ตเป็นค่าว่าง
            if (currentValue && !data.some(item => item[valueField] === currentValue)) {
                $(selector).val('');
            }
        }
        
        // Function to refresh all charts
        function refreshAllCharts() {
            Object.keys(chartInstances).forEach(chartId => {
                refreshChart(chartId);
            });
        }
        
        // Load default charts
        function loadDefaultCharts() {
            // Project count by year
            addDefaultChart('โครงการจำแนกตามปี', 'bar', 'project_year', 'project_count');
            // Budget by strategy
            addDefaultChart('งบประมาณจำแนกตามยุทธศาสตร์', 'pie', 'strategy', 'budget_sum');
            // Target groups by main project
            addDefaultChart('กลุ่มเป้าหมายจำแนกตามโครงการหลัก', 'doughnut', 'main_project', 'target_count');
        }
        
        // Add default chart
        function addDefaultChart(title, type, xAxis, yAxis) {
            chartCounter++;
            const chartId = 'chart_' + chartCounter;
            
            const chartHtml = `
                <div class="chart-container" id="container_${chartId}">
                    <div class="chart-actions">
                        <h5 class="chart-title">${title}</h5>
                        <div>
                            <button class="btn btn-sm btn-outline-info me-2" onclick="viewChartDetail('${chartId}', '${title}', '${type}', '${xAxis}', '${yAxis}')" title="ดูรายละเอียด">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="refreshChart('${chartId}')" title="รีเฟรช">
                                <i class="fas fa-refresh"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeChart('${chartId}')" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <canvas id="${chartId}" class="chart-canvas"></canvas>
                </div>
            `;
            
            document.getElementById('chartsContainer').insertAdjacentHTML('beforeend', chartHtml);
            createChart(chartId, type, title, xAxis, yAxis);
        }
        
        // Show add chart modal
        function showAddChartModal() {
            new bootstrap.Modal(document.getElementById('addChartModal')).show();
        }
        
        // Add new chart
        function addChart() {
            const title = document.getElementById('chartTitle').value;
            const type = document.getElementById('chartType').value;
            const xAxis = document.getElementById('xAxisData').value;
            const yAxis = document.getElementById('yAxisData').value;
            
            if (!title || !type || !xAxis || !yAxis) {
                Swal.fire('ข้อผิดพลาด', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'error');
                return;
            }
            
            chartCounter++;
            const chartId = 'chart_' + chartCounter;
            
            const chartHtml = `
                <div class="chart-container" id="container_${chartId}">
                    <div class="chart-actions">
                        <h5 class="chart-title">${title}</h5>
                        <div>
                            <button class="btn btn-sm btn-outline-info me-2" onclick="viewChartDetail('${chartId}', '${title}', '${type}', '${xAxis}', '${yAxis}')" title="ดูรายละเอียด">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="refreshChart('${chartId}')" title="รีเฟรช">
                                <i class="fas fa-refresh"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeChart('${chartId}')" title="ลบ">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <canvas id="${chartId}" class="chart-canvas"></canvas>
                </div>
            `;
            
            document.getElementById('chartsContainer').insertAdjacentHTML('beforeend', chartHtml);
            createChart(chartId, type, title, xAxis, yAxis);
            
            // Close modal and reset form
            bootstrap.Modal.getInstance(document.getElementById('addChartModal')).hide();
            document.getElementById('addChartForm').reset();
            
            Swal.fire('สำเร็จ', 'เพิ่มกราฟใหม่เรียบร้อย', 'success');
        }
        
        // Create chart
        function createChart(chartId, type, title, xAxis, yAxis) {
            const ctx = document.getElementById(chartId).getContext('2d');
            
            // Get current filter values
            const filters = getFilterValues();
            
            // Prepare FormData for POST request
            const formData = new FormData();
            formData.append('x_axis', xAxis);
            formData.append('y_axis', yAxis);
            formData.append('data_source', 'builder');
            
            // Add all filter values for comprehensive filtering (both legacy and new format)
            formData.append('project_year_start', $('#projectYearStartFilter').val() || '');
            formData.append('project_year_end', $('#projectYearEndFilter').val() || '');
            formData.append('province', $('#provinceFilter').val() || '');
            formData.append('district', $('#districtFilter').val() || '');
            formData.append('subdistrict', $('#subdistrictFilter').val() || '');
            formData.append('village', $('#villageFilter').val() || '');
            formData.append('main_project', $('#mainProjectFilter').val() || '');
            formData.append('strategy', $('#strategyFilter').val() || '');
            formData.append('agency', $('#agencyFilter').val() || '');
            formData.append('target_group', $('#targetGroupFilter').val() || '');
            formData.append('teacher', $('#teacherFilter').val() || '');
            
            // Also add legacy filter format for backward compatibility
            Object.keys(filters).forEach(key => {
                formData.append(key, filters[key]);
            });
            
            // 📊 Debug: Chart Creation Info
            console.group(`%c🚀 Creating Chart: ${title}`, 'color: #667eea; font-weight: bold; font-size: 14px;');
            console.log(`%c📊 Chart ID: %c${chartId}`, 'color: #28a745; font-weight: bold;', 'color: #333;');
            console.log(`%c📈 Chart Type: %c${type}`, 'color: #28a745; font-weight: bold;', 'color: #333;');
            console.log(`%c↔️ X-Axis: %c${xAxis}`, 'color: #28a745; font-weight: bold;', 'color: #333;');
            console.log(`%c↕️ Y-Axis: %c${yAxis}`, 'color: #28a745; font-weight: bold;', 'color: #333;');
            console.log('%c📋 Filter Parameters:', 'color: #ffc107; font-weight: bold;');
            for (let [key, value] of formData.entries()) {
                if (value) {
                    console.log(`  %c${key}: %c${value}`, 'color: #6c757d;', 'color: #333; font-weight: 500;');
                }
            }
            console.groupEnd();
            
            fetch('../api/chart_data_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.group(`%c📥 API Response for Chart: ${title}`, 'color: #17a2b8; font-weight: bold; font-size: 14px;');
                
                try {
                    // Try to parse and display formatted JSON
                    const testParse = JSON.parse(text.trim().substring(text.indexOf('{')));
                    console.log('%c📊 Parsed Data:', 'color: #28a745; font-weight: bold;');
                    console.log(`%c  ✅ Success: %c${testParse.success}`, 'color: #6c757d;', 'color: #28a745; font-weight: bold;');
                    console.log(`%c  📑 Labels Count: %c${testParse.labels ? testParse.labels.length : 0}`, 'color: #6c757d;', 'color: #333; font-weight: bold;');
                    console.log(`%c  📈 Values Count: %c${testParse.values ? testParse.values.length : 0}`, 'color: #6c757d;', 'color: #333; font-weight: bold;');
                    
                    if (testParse.labels && testParse.labels.length > 0) {
                        console.log('%c� Chart Data Preview:', 'color: #ffc107; font-weight: bold;');
                        const maxDisplay = Math.min(5, testParse.labels.length);
                        for (let i = 0; i < maxDisplay; i++) {
                            console.log(`%c  ${i + 1}. %c${testParse.labels[i]} %c→ %c${testParse.values[i]}`, 
                                'color: #6c757d;', 
                                'color: #333; font-weight: 500;', 
                                'color: #6c757d;',
                                'color: #007bff; font-weight: bold;'
                            );
                        }
                        if (testParse.labels.length > maxDisplay) {
                            console.log(`%c  ... และอีก ${testParse.labels.length - maxDisplay} รายการ`, 'color: #6c757d; font-style: italic;');
                        }
                    }
                } catch (e) {
                    console.log('%c⚠️ Raw Response (Parse Failed):', 'color: #ffc107; font-weight: bold;');
                    console.log(text);
                }
                
                console.groupEnd();
                
                try {
                    // กรอง PHP errors ออกจาก response
                    let cleanJson = text;
                    const jsonStart = text.indexOf('{');
                    if (jsonStart > 0) {
                        cleanJson = text.substring(jsonStart);
                    }
                    
                    const data = JSON.parse(cleanJson);
                    
                    if (data.success) {
                        console.log(`%c✅ Chart Created Successfully: ${title}`, 'color: #28a745; font-weight: bold; font-size: 14px; background: #d4edda; padding: 4px 8px; border-radius: 4px;');
                        
                        // คำนวณ stepSize สำหรับแกน Y
                        const maxValue = Math.max(...data.values);
                        let stepSize = 10; // ค่าเริ่มต้น
                        
                        if (yAxis === 'budget_sum') {
                            // สำหรับงบประมาณ
                            if (maxValue <= 10000000) stepSize = 1000000; // 1M
                            else if (maxValue <= 50000000) stepSize = 5000000; // 5M
                            else stepSize = 10000000; // 10M
                        } else {
                            // สำหรับข้อมูลอื่นๆ
                            if (maxValue <= 100) stepSize = 10;
                            else if (maxValue <= 500) stepSize = 50;
                            else if (maxValue <= 1000) stepSize = 100;
                            else if (maxValue <= 5000) stepSize = 500;
                            else stepSize = Math.ceil(maxValue / 10);
                        }
                        
                        const chartConfig = {
                            type: type,
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: getAxisLabel(yAxis),
                                    data: data.values,
                                    backgroundColor: generateColors(data.labels.length),
                                    borderColor: generateColors(data.labels.length, 0.8),
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: title
                                    },
                                    legend: {
                                        display: ['pie', 'doughnut', 'radar'].includes(type)
                                    }
                                },
                                scales: ['bar', 'line'].includes(type) ? {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: stepSize
                                        }
                                    }
                                } : {}
                            }
                        };
                        
                        // Destroy existing chart if exists
                        if (chartInstances[chartId]) {
                            chartInstances[chartId].destroy();
                        }
                        
                        chartInstances[chartId] = new Chart(ctx, chartConfig);
                        chartInstances[chartId].xAxis = xAxis;
                        chartInstances[chartId].yAxis = yAxis;
                        chartInstances[chartId].title = title;
                        chartInstances[chartId].type = type;
                    } else {
                        console.log(`%c❌ Chart Creation Failed: ${title}`, 'color: #dc3545; font-weight: bold; font-size: 14px; background: #f8d7da; padding: 4px 8px; border-radius: 4px;');
                        console.error('Error details:', data.message);
                        showChartError(ctx, data.message);
                    }
                } catch (e) {
                    console.group(`%c💥 JSON Parse Error for Chart: ${title}`, 'color: #dc3545; font-weight: bold; font-size: 14px;');
                    console.error('%c🚨 Parse Error:', 'color: #dc3545; font-weight: bold;', e);
                    console.error('%c📄 Response that failed to parse:', 'color: #6c757d; font-weight: bold;');
                    console.error(text);
                    console.groupEnd();
                    showChartError(ctx, 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล');
                }
            })
            .catch(error => {
                console.group(`%c🔥 Network Error for Chart: ${title}`, 'color: #dc3545; font-weight: bold; font-size: 14px;');
                console.error('%c🌐 Connection Error:', 'color: #dc3545; font-weight: bold;', error);
                console.groupEnd();
                showChartError(ctx, 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
            });
        }
        
        // Show error on chart canvas
        function showChartError(ctx, message) {
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            ctx.font = '16px Arial';
            ctx.fillStyle = '#dc3545';
            ctx.textAlign = 'center';
            ctx.fillText(message, ctx.canvas.width / 2, ctx.canvas.height / 2);
        }
        
        // Remove chart
        function removeChart(chartId) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: 'คุณต้องการลบกราหนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (chartInstances[chartId]) {
                        chartInstances[chartId].destroy();
                        delete chartInstances[chartId];
                    }
                    document.getElementById('container_' + chartId).remove();
                    
                    // Check if any charts remain
                    if (Object.keys(chartInstances).length === 0) {
                        document.getElementById('noChartsMessage').style.display = 'block';
                    }
                    
                    Swal.fire('ลบแล้ว!', 'กราฬถูกลบเรียบร้อย', 'success');
                }
            });
        }
        
        // Refresh chart
        function refreshChart(chartId) {
            if (chartInstances[chartId]) {
                const chart = chartInstances[chartId];
                createChart(chartId, chart.type, chart.title, chart.xAxis, chart.yAxis);
            }
        }
        
        // Apply filters
        function applyFilters() {
            console.log(`%c🎯 Applying Filters`, 'color: #667eea; font-weight: bold; background: #e8eaf6; padding: 4px 8px; border-radius: 4px;');
            
            // Refresh all charts
            Object.keys(chartInstances).forEach(chartId => {
                refreshChart(chartId);
            });
            
            // Update stats
            updateStats();
            
            Swal.fire('สำเร็จ', 'อัพเดทข้อมูลเรียบร้อย', 'success');
        }
        
        // Reset filters
        function resetFilters() {
            console.log(`%c🔄 Resetting All Filters`, 'color: #6c757d; font-weight: bold; background: #e9ecef; padding: 4px 8px; border-radius: 4px;');
            
            // Reset form
            document.getElementById('filterForm').reset();
            
            // Reset all select boxes manually to ensure they're cleared
            $('#projectYearStartFilter').val('');
            $('#projectYearEndFilter').val('');
            $('#provinceFilter').val('');
            $('#districtFilter').val('');
            $('#subdistrictFilter').val('');
            $('#villageFilter').val('');
            $('#mainProjectFilter').val('');
            $('#strategyFilter').val('');
            $('#agencyFilter').val('');
            $('#targetGroupFilter').val('');
            $('#teacherFilter').val('');
            
            // Reload all filter data
            loadAllFilterData();
            
            // Refresh all charts and stats
            setTimeout(function() {
                refreshAllCharts();
                updateStats();
            }, 500);
            
            // Show success message
            Swal.fire('สำเร็จ', 'ล้างตัวกรองเรียบร้อย', 'success');
        }
        
        // Get filter values
        function getFilterValues() {
            const filters = {};
            
            const yearStart = document.getElementById('projectYearStartFilter').value;
            const yearEnd = document.getElementById('projectYearEndFilter').value;
            const province = document.getElementById('provinceFilter').value;
            const district = document.getElementById('districtFilter').value;
            const subdistrict = document.getElementById('subdistrictFilter').value;
            const village = document.getElementById('villageFilter').value;
            const strategy = document.getElementById('strategyFilter').value;
            const mainProject = document.getElementById('mainProjectFilter').value;
            const agency = document.getElementById('agencyFilter').value;
            const targetGroup = document.getElementById('targetGroupFilter').value;
            const teacher = document.getElementById('teacherFilter').value;
            
            if (yearStart) filters.yearStart = yearStart;
            if (yearEnd) filters.yearEnd = yearEnd;
            if (province) filters.province = province;
            if (district) filters.district = district;
            if (subdistrict) filters.subdistrict = subdistrict;
            if (village) filters.village = village;
            if (strategy) filters.strategyFilter = strategy;
            if (mainProject) filters.mainProjectFilter = mainProject;
            if (agency) filters.agency = agency;
            if (targetGroup) filters.targetGroup = targetGroup;
            if (teacher) filters.teacher = teacher;
            
            return filters;
        }
        
        // Update stats
        function updateStats() {
            const filters = getFilterValues();
            
            const formData = new FormData();
            
            // Add all filter values for comprehensive filtering
            formData.append('project_year_start', $('#projectYearStartFilter').val() || '');
            formData.append('project_year_end', $('#projectYearEndFilter').val() || '');
            formData.append('province', $('#provinceFilter').val() || '');
            formData.append('district', $('#districtFilter').val() || '');
            formData.append('subdistrict', $('#subdistrictFilter').val() || '');
            formData.append('village', $('#villageFilter').val() || '');
            formData.append('main_project', $('#mainProjectFilter').val() || '');
            formData.append('strategy', $('#strategyFilter').val() || '');
            formData.append('agency', $('#agencyFilter').val() || '');
            formData.append('target_group', $('#targetGroupFilter').val() || '');
            formData.append('teacher', $('#teacherFilter').val() || '');
            
            // Also add legacy filter format for backward compatibility
            Object.keys(filters).forEach(key => {
                formData.append(key, filters[key]);
            });
            
            fetch('../api/stats_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.group(`%c📊 Stats API Response`, 'color: #ffc107; font-weight: bold; font-size: 14px;');
                
                try {
                    // Try to parse and display formatted JSON for stats
                    const cleanJson = text.trim();
                    const jsonStart = cleanJson.indexOf('{');
                    const jsonEnd = cleanJson.lastIndexOf('}') + 1;
                    
                    if (jsonStart >= 0 && jsonEnd > jsonStart) {
                        const testParse = JSON.parse(cleanJson.substring(jsonStart, jsonEnd));
                        console.log('%c📈 Stats Data:', 'color: #28a745; font-weight: bold;');
                        console.log(`%c  ✅ Success: %c${testParse.success}`, 'color: #6c757d;', 'color: #28a745; font-weight: bold;');
                        
                        if (testParse.stats) {
                            console.log('%c📊 Statistics Summary:', 'color: #17a2b8; font-weight: bold;');
                            const stats = testParse.stats;
                            Object.keys(stats).forEach(key => {
                                const value = stats[key];
                                const formattedValue = typeof value === 'number' ? value.toLocaleString() : value;
                                console.log(`%c  � ${key}: %c${formattedValue}`, 'color: #6c757d;', 'color: #333; font-weight: bold;');
                            });
                        }
                    } else {
                        console.log('%c⚠️ Raw Response (Invalid JSON):', 'color: #ffc107; font-weight: bold;');
                        console.log(text);
                    }
                } catch (e) {
                    console.log('%c⚠️ Raw Response (Parse Failed):', 'color: #ffc107; font-weight: bold;');
                    console.log(text);
                }
                
                console.groupEnd();
                
                try {
                    // กรอง PHP errors ออกจาก response
                    let cleanJson = text.trim();
                    
                    // หาตำแหน่งเริ่มต้นของ JSON
                    const jsonStart = cleanJson.indexOf('{');
                    const jsonEnd = cleanJson.lastIndexOf('}') + 1;
                    
                    if (jsonStart >= 0 && jsonEnd > jsonStart) {
                        cleanJson = cleanJson.substring(jsonStart, jsonEnd);
                    } else {
                        // ถ้าไม่พบ JSON format ให้ใช้ default values
                        console.warn('No valid JSON found in response:', text);
                        updateStatsWithDefaults();
                        return;
                    }
                    
                    const data = JSON.parse(cleanJson);
                    
                    if (data && data.success && data.stats) {
                        console.log(`%c✅ Stats Updated Successfully`, 'color: #28a745; font-weight: bold; background: #d4edda; padding: 4px 8px; border-radius: 4px;');
                        console.log('%c📊 Final Stats Display:', 'color: #17a2b8; font-weight: bold;');
                        
                        // Create a clean stats object for table display
                        const displayStats = {
                            'โครงการทั้งหมด': data.stats.total_projects || 0,
                            'งบประมาณรวม (บาท)': data.stats.total_budget || 0,
                            'ตัวชี้วัดทั้งหมด': data.stats.total_indicators || 0,
                            'พื้นที่ดำเนินการ': data.stats.total_locations || 0,
                            'ผลิตภัณฑ์ทั้งหมด': data.stats.total_products || 0,
                            'โรงเรียนที่เข้าร่วม': data.stats.total_schools || 0,
                            'กลุ่มเป้าหมายทั้งหมด': data.stats.total_target_groups || 0,
                            'หน่วยงานที่เข้าร่วม': data.stats.total_agencies || 0
                        };
                        console.table(displayStats);
                        
                        document.getElementById('totalProjects').textContent = data.stats.total_projects ? data.stats.total_projects.toLocaleString() : '0';
                        document.getElementById('totalBudget').textContent = data.stats.total_budget ? (data.stats.total_budget / 1000000).toFixed(1) : '0.0';
                        document.getElementById('totalIndicators').textContent = data.stats.total_indicators ? data.stats.total_indicators.toLocaleString() : '0';
                        document.getElementById('totalLocations').textContent = data.stats.total_locations ? data.stats.total_locations.toLocaleString() : '0';
                        document.getElementById('totalProducts').textContent = data.stats.total_products ? data.stats.total_products.toLocaleString() : '0';
                        document.getElementById('totalSchools').textContent = data.stats.total_schools ? data.stats.total_schools.toLocaleString() : '0';
                        document.getElementById('totalTargetGroups').textContent = data.stats.total_target_groups ? data.stats.total_target_groups.toLocaleString() : '0';
                        document.getElementById('totalAgencies').textContent = data.stats.total_agencies ? data.stats.total_agencies.toLocaleString() : '0';
                    } else {
                        console.error('Invalid stats data structure:', data);
                        updateStatsWithDefaults();
                    }
                } catch (e) {
                    console.group(`%c💥 Stats JSON Parse Error`, 'color: #dc3545; font-weight: bold; font-size: 14px;');
                    console.error('%c🚨 Parse Error:', 'color: #dc3545; font-weight: bold;', e);
                    console.error('%c📄 Attempted to parse:', 'color: #6c757d; font-weight: bold;');
                    console.error(text);
                    console.groupEnd();
                    updateStatsWithDefaults();
                }
            })
            .catch(error => {
                console.group(`%c🔥 Stats Network Error`, 'color: #dc3545; font-weight: bold; font-size: 14px;');
                console.error('%c🌐 Connection Error:', 'color: #dc3545; font-weight: bold;', error);
                console.groupEnd();
                updateStatsWithDefaults();
            });
        }
        
        // Update stats with default values when API fails
        function updateStatsWithDefaults() {
            console.log(`%c🔄 Using Default Stats Values`, 'color: #6c757d; font-weight: bold; background: #e9ecef; padding: 4px 8px; border-radius: 4px;');
            
            document.getElementById('totalProjects').textContent = '-';
            document.getElementById('totalBudget').textContent = '-';
            document.getElementById('totalIndicators').textContent = '-';
            document.getElementById('totalLocations').textContent = '-';
            document.getElementById('totalProducts').textContent = '-';
            document.getElementById('totalSchools').textContent = '-';
            document.getElementById('totalTargetGroups').textContent = '-';
            document.getElementById('totalAgencies').textContent = '-';
        }
        
        // Helper functions
        function getAxisLabel(axis) {
            const labels = {
                'project_count': 'จำนวนโครงการ',
                'budget_sum': 'งบประมาณ (บาท)',
                'target_count': 'จำนวนกลุ่มเป้าหมาย',
                'sroi_avg': 'SROI เฉลี่ย',
                'indicator_count': 'จำนวนตัวชี้วัด'
            };
            return labels[axis] || axis;
        }
        
        function generateColors(count, alpha = 0.6) {
            const colors = [
                `rgba(102, 126, 234, ${alpha})`,
                `rgba(118, 75, 162, ${alpha})`,
                `rgba(40, 167, 69, ${alpha})`,
                `rgba(255, 193, 7, ${alpha})`,
                `rgba(220, 53, 69, ${alpha})`,
                `rgba(23, 162, 184, ${alpha})`,
                `rgba(108, 117, 125, ${alpha})`,
                `rgba(253, 126, 20, ${alpha})`,
                `rgba(111, 66, 193, ${alpha})`,
                `rgba(214, 51, 132, ${alpha})`
            ];
            
            let result = [];
            for (let i = 0; i < count; i++) {
                result.push(colors[i % colors.length]);
            }
            return result;
        }
        
        // View chart detail
        function viewChartDetail(chartId, title, type, xAxis, yAxis) {
            // Get current filter values
            const filters = getFilterValues();
            const params = new URLSearchParams(filters);
            params.append('title', title);
            params.append('type', type);
            params.append('x_axis', xAxis);
            params.append('y_axis', yAxis);
            
            // Open in new tab
            window.open(`chart_detail.php?${params.toString()}`, '_blank');
        }
        
        // Load saved charts
        function loadSavedCharts(page = 1) {
            console.log(`%c📚 Loading Saved Charts (Page ${page})`, 'color: #28a745; font-weight: bold; background: #d4edda; padding: 4px 8px; border-radius: 4px;');
            
            fetch(`../api/load_saved_charts_api.php?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    console.group(`%c📊 Saved Charts Response`, 'color: #28a745; font-weight: bold; font-size: 14px;');
                    console.log('%c📄 Charts Data:', 'color: #6c757d; font-weight: bold;');
                    console.log(data);
                    console.groupEnd();
                    
                    if (data.success) {
                        displaySavedCharts(data.charts);
                        displaySavedChartsPagination(data.pagination);
                    } else {
                        document.getElementById('savedChartsContainer').innerHTML = `
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p class="mb-0">ไม่สามารถโหลดกราฟที่บันทึกไว้ได้</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.group(`%c🔥 Saved Charts Load Error`, 'color: #dc3545; font-weight: bold; font-size: 14px;');
                    console.error('%c🌐 Connection Error:', 'color: #dc3545; font-weight: bold;', error);
                    console.groupEnd();
                    
                    document.getElementById('savedChartsContainer').innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-times"></i>
                            <p class="mb-0">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>
                        </div>
                    `;
                });
        }
        
        // Display saved charts
        function displaySavedCharts(charts) {
            const container = document.getElementById('savedChartsContainer');
            
            if (charts.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-bookmark text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">ยังไม่มีกราฟที่บันทึกไว้</h5>
                        <p class="text-muted">สร้างกราฟด้วยตัวสร้างกราฟขั้นสูง แล้วบันทึกเพื่อดูที่นี่</p>
                        <a href="chart_builder.php" class="btn btn-success">
                            <i class="fas fa-magic me-1"></i>สร้างกราฟใหม่
                        </a>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="row">';
            
            charts.forEach(chart => {
                const createdDate = new Date(chart.CreatedAt).toLocaleDateString('th-TH');
                const chartTypeIcon = getChartTypeIcon(chart.ChartType);
                const chartTypeName = getChartTypeName(chart.ChartType);
                
                html += `
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">${chart.ChartTitle}</h6>
                                        <small class="text-muted">
                                            <i class="${chartTypeIcon} me-1"></i>${chartTypeName}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-calendar me-1"></i>${createdDate}
                                        </small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="viewSavedChart(${chart.ChartID})">
                                                <i class="fas fa-eye me-2"></i>ดูกราฟ
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="editSavedChart(${chart.ChartID})">
                                                <i class="fas fa-edit me-2"></i>แก้ไข
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteSavedChart(${chart.ChartID})">
                                                <i class="fas fa-trash me-2"></i>ลบ
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="card-text text-muted small mb-3">${chart.Description}</p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>${chart.CreatedBy}
                                    </small>
                                    <button class="btn btn-sm btn-primary" onclick="viewSavedChart(${chart.ChartID})">
                                        <i class="fas fa-chart-bar me-1"></i>ดูกราฟ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        }
        
        // Display pagination for saved charts
        function displaySavedChartsPagination(pagination) {
            const paginationContainer = document.getElementById('savedChartsPagination');
            
            if (pagination.total_pages <= 1) {
                paginationContainer.style.display = 'none';
                return;
            }
            
            let html = '<nav><ul class="pagination">';
            
            // Previous button
            if (pagination.has_prev) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSavedCharts(${pagination.current_page - 1})">ก่อนหน้า</a></li>`;
            } else {
                html += '<li class="page-item disabled"><span class="page-link">ก่อนหน้า</span></li>';
            }
            
            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === pagination.current_page) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSavedCharts(${i})">${i}</a></li>`;
                }
            }
            
            // Next button
            if (pagination.has_next) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadSavedCharts(${pagination.current_page + 1})">ถัดไป</a></li>`;
            } else {
                html += '<li class="page-item disabled"><span class="page-link">ถัดไป</span></li>';
            }
            
            html += '</ul></nav>';
            paginationContainer.innerHTML = html;
            paginationContainer.style.display = 'flex';
        }
        
        // Helper functions for chart types
        function getChartTypeIcon(type) {
            const icons = {
                'bar': 'fas fa-chart-bar',
                'line': 'fas fa-chart-line',
                'pie': 'fas fa-chart-pie',
                'doughnut': 'fas fa-circle-notch',
                'radar': 'fas fa-circle',
                'polarArea': 'fas fa-circle-dot',
                'scatter': 'fas fa-braille'
            };
            return icons[type] || 'fas fa-chart-bar';
        }
        
        function getChartTypeName(type) {
            const names = {
                'bar': 'แผนภูมิแท่ง',
                'line': 'แผนภูมิเส้น',
                'pie': 'แผนภูมิวงกลม',
                'doughnut': 'แผนภูมิโดนัท',
                'radar': 'แผนภูมิเรดาร์',
                'polarArea': 'แผนภูมิพื้นที่เชิงขั้ว',
                'scatter': 'แผนภูมิกระจาย'
            };
            return names[type] || type;
        }
        
        // View saved chart
        function viewSavedChart(chartId) {
            window.open(`chart_detail.php?saved_chart_id=${chartId}`, '_blank');
        }
        
        // Edit saved chart
        function editSavedChart(chartId) {
            window.open(`chart_builder.php?edit_chart_id=${chartId}`, '_blank');
        }
        
        // Delete saved chart
        function deleteSavedChart(chartId) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบกราฟนี้หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implement delete functionality
                    fetch('../api/delete_saved_chart_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `chart_id=${chartId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('ลบแล้ว!', 'กราฟถูกลบเรียบร้อยแล้ว', 'success');
                            loadSavedCharts(); // Reload the list
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถลบกราฟได้', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
