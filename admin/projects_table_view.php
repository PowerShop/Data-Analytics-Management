<?php 
// session_start();
if (file_exists('./database/db.php')) {
    include './database/db.php';
} else {
    include 'db.php';
}
include 'navbar.php'; 

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายงานโครงการแบบตาราง</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Noto Sans Thai Looped -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .filter-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #667eea;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            min-width: 120px;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            font-size: 0.8em;
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        .budget-amount {
            font-weight: 600;
            color: #28a745;
        }
        
        .project-code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #495057;
        }
        
        .indicator-value {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            color: #1976d2;
        }
        
        .indicator-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #007bff;
        }
        
        .product-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }
        
        .school-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #fd7e14;
        }
        
        .target-group-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #6f42c1;
        }
        
        .location-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #dc3545;
        }
        
        .sroi-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }
        
        .enterprise-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #007bff;
        }
        
        .other-org-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #6c757d;
        }
        
        .network-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #17a2b8;
        }
        
        .university-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #ffc107;
        }
        
        .local-admin-item {
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #dc3545;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 15px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .dt-buttons {
            margin-bottom: 15px;
        }
        
        .dt-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            color: white !important;
            margin-right: 5px !important;
            padding: 8px 15px !important;
            font-weight: 600 !important;
        }
        
        .dataTables_length select,
        .dataTables_filter input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 6px 12px;
        }
        
        .dataTables_length {
            margin-bottom: 15px;
        }
        
        .dataTables_length label {
            font-weight: 600;
            color: #495057;
        }
        
        .dataTables_filter {
            margin-bottom: 15px;
        }
        
        .dataTables_filter label {
            font-weight: 600;
            color: #495057;
        }
        
        .dataTables_info {
            color: #6c757d;
            font-weight: 500;
        }
        
        .page-link {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            color: #667eea;
            font-weight: 600;
            margin: 0 2px;
        }
        
        .page-link:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        
        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .bg-purple {
            background-color: #6f42c1 !important;
        }
        
        /* บังคับให้ DataTables length menu แสดง */
        .dataTables_length {
            display: block !important;
            visibility: visible !important;
        }
        
        .dataTables_wrapper .dataTables_length {
            float: left;
            text-align: left;
        }
        
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
        }
        
        /* Search Highlight Styles */
        .highlight {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%) !important;
            color: #333 !important;
            font-weight: bold !important;
            padding: 2px 4px !important;
            border-radius: 3px !important;
            box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3) !important;
        }
        
        .highlight-animation {
            animation: highlightPulse 1s ease-in-out;
        }
        
        @keyframes highlightPulse {
            0% { background-color: #ffd700; }
            50% { background-color: #ffed4e; }
            100% { background-color: #ffd700; }
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid mt-3">
        <!-- Header -->
        <div class="table-header">
            <h2 class="fw-bold mb-3">
                <i class="fas fa-table me-3"></i>รายงานโครงการแบบตาราง
            </h2>
            <p class="mb-0">ข้อมูลโครงการทั้งหมดพร้อมรายละเอียด</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h5 class="mb-4">
                <i class="fas fa-filter me-2"></i>เครื่องมือกรองข้อมูล
            </h5>
            
            <form id="filterForm">
                <!-- Row 1: Primary Filters -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">ปีโครงการ (เริ่มต้น)</label>
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
                        <label class="form-label">ปีโครงการ (สิ้นสุด)</label>
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
                        <label class="form-label">ตำบล</label>
                        <select class="form-select" id="subdistrictFilter" name="subdistrict">
                            <option value="">ทุกตำบล</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">อำเภอ</label>
                        <select class="form-select" id="districtFilter" name="district" disabled>
                            <option value="">ทุกอำเภอ</option>
                            <!-- จะถูกโหลดเมื่อเลือกตำบล -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">จังหวัด</label>
                        <select class="form-select" id="provinceFilter" name="province" disabled>
                            <option value="">ทุกจังหวัด</option>
                            <!-- จะถูกโหลดเมื่อเลือกอำเภอ -->
                        </select>
                    </div>
                            
                    
                </div>

                <!-- Row 2: Secondary Filters -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">โครงการหลัก</label>
                        <select class="form-select" id="mainProjectFilter" name="main_project">
                            <option value="">ทุกโครงการหลัก</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">ยุทธศาสตร์</label>
                        <select class="form-select" id="strategyFilter" name="strategy">
                            <option value="">ทุกยุทธศาสตร์</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">หน่วยงาน</label>
                        <select class="form-select" id="agencyFilter" name="agency">
                            <option value="">ทุกหน่วยงาน</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">กลุ่มเป้าหมาย</label>
                        <select class="form-select" id="targetGroupFilter" name="target_group">
                            <option value="">ทุกกลุ่มเป้าหมาย</option>
                            <!-- จะถูกโหลดด้วย JavaScript -->
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>ล้างตัวกรอง
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportData()">
                                <i class="fas fa-download me-2"></i>ส่งออกข้อมูล
                            </button>
                            <div class="ms-auto">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>ตารางจะอัพเดทอัตโนมัติเมื่อเปลี่ยนตัวกรอง
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
                <div class="stats-card">
                    <i class="fas fa-project-diagram fa-2x mb-2"></i>
                    <h3 id="totalProjects">-</h3>
                    <p class="mb-0">โครงการทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-coins fa-2x mb-2"></i>
                    <h3 id="totalBudget">-</h3>
                    <p class="mb-0">งบประมาณรวม (ล้านบาท)</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                    <h3 id="totalIndicators">-</h3>
                    <p class="mb-0">ตัวชี้วัดทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                    <h3 id="totalLocations">-</h3>
                    <p class="mb-0">พื้นที่ดำเนินการ</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 id="totalTargetPeople">-</h3>
                    <p class="mb-0">กลุ่มเป้าหมายทั้งหมด (คน)</p>
                </div>
            </div>
        </div>

        <!-- Row 2: Additional Stats -->
        <div class="row mb-4 mx-1 mt-2" id="additionalStats">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <h3 id="totalProducts">-</h3>
                    <p class="mb-0">ผลิตภัณฑ์ทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #fd7e14 0%, #ff6b35 100%);">
                    <i class="fas fa-school fa-2x mb-2"></i>
                    <h3 id="totalSchools">-</h3>
                    <p class="mb-0">โรงเรียนที่เข้าร่วม</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                    <i class="fas fa-user-friends fa-2x mb-2"></i>
                    <h3 id="totalTargetGroups">-</h3>
                    <p class="mb-0">กลุ่มเป้าหมายทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                    <i class="fas fa-building fa-2x mb-2"></i>
                    <h3 id="totalAgencies">-</h3>
                    <p class="mb-0">หน่วยงานที่เข้าร่วม</p>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="position-relative">
                <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังโหลด...</span>
                        </div>
                        <p class="mt-2 mb-0">กำลังโหลดข้อมูล...</p>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="projectsTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th><i class="fas fa-barcode me-2"></i>รหัสโครงการ</th>
                                <th><i class="fas fa-project-diagram me-2"></i>ชื่อโครงการ</th>
                                <th><i class="fas fa-calendar-alt me-2"></i>ปีโครงการ</th>
                                <th><i class="fas fa-user-tie me-2"></i>ผู้รับผิดชอบ</th>
                                <th><i class="fas fa-coins me-2"></i>งบประมาณอนุมัติ</th>
                                <th><i class="fas fa-sitemap me-2"></i>โครงการหลัก</th>
                                <th><i class="fas fa-chess me-2"></i>ยุทธศาสตร์</th>
                                <th><i class="fas fa-building me-2"></i>หน่วยงาน</th>
                                <th><i class="fas fa-chart-line me-2"></i>ตัวชี้วัด (รายละเอียด)</th>
                                <th><i class="fas fa-box me-2"></i>ผลิตภัณฑ์ (รายละเอียด)</th>
                                <th><i class="fas fa-school me-2"></i>โรงเรียน (รายละเอียด)</th>
                                <th><i class="fas fa-users me-2"></i>กลุ่มเป้าหมาย (รายละเอียด)</th>
                                <th><i class="fas fa-map-marker-alt me-2"></i>พื้นที่ดำเนินการ (รายละเอียด)</th>
                                <th><i class="fas fa-chart-pie me-2"></i>SROI</th>
                                <th><i class="fas fa-industry me-2"></i>วิสาหกิจ/ผู้ประกอบการ</th>
                                <th><i class="fas fa-handshake me-2"></i>องค์กรอื่น ๆ</th>
                                <th><i class="fas fa-network-wired me-2"></i>เครือข่าย</th>
                                <th><i class="fas fa-university me-2"></i>มหาวิทยาลัย</th>
                                <th><i class="fas fa-landmark me-2"></i>องค์กรปกครองส่วนท้องถิ่น</th>
                                <th><i class="fas fa-cog me-2"></i>การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ข้อมูลจะถูกโหลดผ่าน AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        let projectsTable;
        
        $(document).ready(function() {
            // กำหนดฟอนต์สำหรับ PDF ที่รองรับภาษาไทย
            if (typeof pdfMake !== 'undefined') {
                pdfMake.fonts = {
                    THSarabunNew: {
                        normal: 'THSarabunNew.ttf',
                        bold: 'THSarabunNew-Bold.ttf',
                        italics: 'THSarabunNew-Italic.ttf',
                        bolditalics: 'THSarabunNew-BoldItalic.ttf'
                    }
                };
                
                // ตั้งค่าฟอนต์เริ่มต้น
                pdfMake.fonts.Roboto = {
                    normal: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/fonts/Roboto/Roboto-Regular.ttf',
                    bold: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/fonts/Roboto/Roboto-Medium.ttf',
                    italics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/fonts/Roboto/Roboto-Italic.ttf',
                    bolditalics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/fonts/Roboto/Roboto-MediumItalic.ttf'
                };
            }
            
            // Initialize dynamic filters
            initializeDynamicFilters();
            initializeDataTable();
            loadStatistics();
        });

        // ฟังก์ชันสำหรับโหลดข้อมูลแบบ Dynamic
        function initializeDynamicFilters() {
            // Load initial data - เริ่มจากตำบลก่อน
            loadFilterData('subdistricts');
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');

            // Event handlers สำหรับ Reverse Cascading Dropdowns
            $('#projectYearStartFilter, #projectYearEndFilter').on('change', function() {
                loadAllFilterData();
                // Auto reload table when year filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 800);
            });

            $('#subdistrictFilter').on('change', function() {
                const subdistrict = $(this).val();
                $('#districtFilter').prop('disabled', !subdistrict).val('');
                $('#provinceFilter').prop('disabled', true).val('');
                
                if (subdistrict) {
                    loadFilterData('districts');
                } else {
                    $('#districtFilter').html('<option value="">ทุกอำเภอ</option>');
                }
                
                $('#provinceFilter').html('<option value="">ทุกจังหวัด</option>');
                loadOtherFilterData();
                // Auto reload table when subdistrict changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });

            $('#districtFilter').on('change', function() {
                const district = $(this).val();
                $('#provinceFilter').prop('disabled', !district).val('');
                
                if (district) {
                    loadFilterData('provinces');
                } else {
                    $('#provinceFilter').html('<option value="">ทุกจังหวัด</option>');
                }
                
                loadOtherFilterData();
                // Auto reload table when district changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });

            $('#provinceFilter').on('change', function() {
                loadOtherFilterData();
                // Auto reload table when filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });

            $('#mainProjectFilter').on('change', function() {
                loadFilterData('strategies');
                loadFilterData('agencies');
                loadFilterData('target_groups');
                // Auto reload table when filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });

            $('#strategyFilter').on('change', function() {
                loadFilterData('agencies');
                loadFilterData('target_groups');
                // Auto reload table when filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });

            $('#agencyFilter').on('change', function() {
                loadFilterData('target_groups');
                // Auto reload table when filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });

            $('#targetGroupFilter').on('change', function() {
                // Auto reload table when filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
                }, 500);
            });
        }

        function loadAllFilterData() {
            loadFilterData('subdistricts');
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');
            
            // Reset location filters - reverse order
            $('#districtFilter').prop('disabled', true).html('<option value="">ทุกอำเภอ</option>');
            $('#provinceFilter').prop('disabled', true).html('<option value="">ทุกจังหวัด</option>');
        }

        function loadOtherFilterData() {
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');
        }

        function loadFilterData(type) {
            const params = {
                type: type,
                project_year_start: $('#projectYearStartFilter').val(),
                project_year_end: $('#projectYearEndFilter').val(),
                province: $('#provinceFilter').val(),
                district: $('#districtFilter').val(),
                subdistrict: $('#subdistrictFilter').val(),
                main_project: $('#mainProjectFilter').val(),
                strategy: $('#strategyFilter').val(),
                agency: $('#agencyFilter').val()
            };

            $.ajax({
                url: './api/get_filtered_data.php',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(data) {
                    updateSelectOptions(type, data);
                },
                error: function() {
                    console.error('Error loading ' + type + ' data');
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

            // Enable the select if it has options
            if (type === 'districts') {
                $('#districtFilter').prop('disabled', data.length === 0);
            } else if (type === 'subdistricts') {
                $('#subdistrictFilter').prop('disabled', data.length === 0);
            }
        }

        function initializeDataTable() {
            projectsTable = $('#projectsTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'asc']],
                ajax: {
                    url: 'projects_table_data.php',
                    type: 'POST',
                    data: function(d) {
                        // เพิ่มค่าจากฟิลเตอร์
                        d.project_year_start = $('#projectYearStartFilter').val();
                        d.project_year_end = $('#projectYearEndFilter').val();
                        d.subdistrict = $('#subdistrictFilter').val();
                        d.district = $('#districtFilter').val();
                        d.province = $('#provinceFilter').val();
                        d.main_project = $('#mainProjectFilter').val();
                        d.strategy = $('#strategyFilter').val();
                        d.agency = $('#agencyFilter').val();
                        d.target_group = $('#targetGroupFilter').val();
                    },
                    beforeSend: function() {
                        $('#loadingOverlay').show();
                    },
                    complete: function() {
                        $('#loadingOverlay').hide();
                    },
                    error: function(xhr, error, code) {
                        console.error('AJAX Error:', error, code);
                        console.error('Response:', xhr.responseText);
                        alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error);
                    }
                },
                columns: [
                    { 
                        data: 'ProjectCode',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลดิบ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                return data || '';
                            }
                            return data ? `<span class="project-code">${data}</span>` : '-';
                        }
                    },
                    { 
                        data: 'ProjectName',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลดิบ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                return data || '';
                            }
                            return `<div style="min-width: 250px; white-space: normal; word-wrap: break-word;">${data}</div>`;
                        }
                    },
                    { 
                        data: 'ProjectYear',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลดิบ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                return data || '';
                            }
                            return data ? `พ.ศ. ${data}` : '-';
                        }
                    },
                    { 
                        data: 'ResponsiblePerson',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลดิบ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                return data || '';
                            }
                            return data ? `<div style="min-width: 150px; white-space: normal; word-wrap: break-word;">${data}</div>` : '-';
                        }
                    },
                    { 
                        data: 'TotalBudget',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลดิบ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                return data || '';
                            }
                            if (data && data > 0) {
                                return `<span class="budget-amount">${Number(data).toLocaleString('th-TH', {minimumFractionDigits: 2})} บาท</span>`;
                            }
                            return '-';
                        }
                    },
                    { 
                        data: 'MainProjectName',
                        searchable: true
                    },
                    { 
                        data: 'StrategyName',
                        searchable: true
                    },
                    { 
                        data: 'AgencyName',
                        searchable: true
                    },
                    { 
                        data: 'IndicatorDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อความจากตัวชี้วัด
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(indicator) {
                                        return `${indicator.name} ${indicator.value} ${indicator.unit || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(indicator) {
                                    html += `<div class="indicator-item mb-1">
                                        <strong>${indicator.name}</strong>: 
                                        <span class="text-primary">${indicator.value}</span> 
                                        <small class="text-muted">${indicator.unit || ''}</small>
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'ProductDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อความจากผลิตภัณฑ์
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(product) {
                                        return `${product.name} ${product.type || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(product, index) {
                                    html += `<div class="product-item mb-1">
                                        <span class="badge bg-info me-1"><i class="fas fa-hashtag me-1"></i>${index + 1}</span>
                                        <strong>${product.name}</strong>
                                        ${product.type ? `<br><small class="text-muted">ประเภท: ${product.type}</small>` : ''}
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'SchoolDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งชื่อโรงเรียน
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(school) {
                                        return school.name;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(school, index) {
                                    html += `<div class="school-item mb-1">
                                        <span class="badge bg-warning text-dark me-1"><i class="fas fa-hashtag me-1"></i>${index + 1}</span>
                                        <strong>${school.name}</strong>
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'TargetGroupDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งชื่อกลุ่มเป้าหมาย
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(targetGroup) {
                                        return `${targetGroup.name} ${targetGroup.count}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(targetGroup, index) {
                                    html += `<div class="target-group-item mb-1">
                                        <span class="badge bg-purple me-1"><i class="fas fa-hashtag me-1"></i>${index + 1}</span>
                                        <strong>${targetGroup.name}</strong>
                                        <br><small class="text-muted">จำนวน: ${targetGroup.count} คน</small>
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'LocationDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลที่อยู่
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(location) {
                                        let displayName = location.village_name;
                                        if (!displayName || displayName === '-' || displayName.trim() === '') {
                                            displayName = location.community || 'ไม่ระบุ';
                                        }
                                        return `${displayName} ${location.moo || ''} ${location.full_address || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(location, index) {
                                    // ตรวจสอบชื่อหมู่บ้าน หากเป็น "-" หรือไม่มี ให้ใช้ชื่อชุมชนแทน
                                    let displayName = location.village_name;
                                    if (!displayName || displayName === '-' || displayName.trim() === '') {
                                        displayName = location.community || 'ไม่ระบุ';
                                    }
                                    
                                    html += `<div class="location-item mb-1">
                                        <span class="badge bg-danger me-1"><i class="fas fa-hashtag me-1"></i>${index + 1}</span>
                                        <strong>${displayName}</strong>
                                        ${location.moo ? `<br><small class="text-muted">หมู่: ${location.moo}</small>` : ''}
                                        ${location.full_address ? `<br><small class="text-muted">${location.full_address}</small>` : ''}
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'SROIData',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูล SROI
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(sroi) {
                                        return `${sroi.result} ${sroi.description || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 120px;">';
                                data.forEach(function(sroi, index) {
                                    html += `<div class="sroi-item mb-1">
                                        <span class="badge bg-success me-1"><i class="fas fa-hashtag me-1"></i>${index + 1}</span>
                                        <strong>${sroi.result}</strong>
                                        ${sroi.description ? `<br><small class="text-muted">${sroi.description}</small>` : ''}
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'EnterpriseDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลวิสาหกิจ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(enterprise) {
                                        return `${enterprise.name} ${enterprise.type}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(enterprise, index) {
                                    html += `<div class="enterprise-item mb-1">
                                        <span class="badge bg-primary me-1">${index + 1}</span>
                                        <strong>${enterprise.name}</strong>
                                        <br><small class="text-muted">ประเภท: ${enterprise.type}</small>
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'OtherOrganizations',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลองค์กรอื่น ๆ
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(org) {
                                        return `${org.name} ${org.type || ''} ${org.role || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(org, index) {
                                    html += `<div class="other-org-item mb-1">
                                        <span class="badge bg-secondary me-1">${index + 1}</span>
                                        <strong>${org.name}</strong>
                                        ${org.type ? `<br><small class="text-muted">ประเภท: ${org.type}</small>` : ''}
                                        ${org.role ? `<br><small class="text-muted">บทบาท: ${org.role}</small>` : ''}
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'NetworkDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งชื่อเครือข่าย
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(network) {
                                        return network.name;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(network, index) {
                                    html += `<div class="network-item mb-1">
                                        <span class="badge bg-info me-1">${index + 1}</span>
                                        <strong>${network.name}</strong>
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'UniversityDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลมหาวิทยาลัย
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(university) {
                                        return `${university.name} ${university.type || ''} ${university.collaboration || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(university, index) {
                                    html += `<div class="university-item mb-1">
                                        <span class="badge bg-warning text-dark me-1">${index + 1}</span>
                                        <strong>${university.name}</strong>
                                        ${university.type ? `<br><small class="text-muted">ประเภท: ${university.type}</small>` : ''}
                                        ${university.collaboration ? `<br><small class="text-muted">ความร่วมมือ: ${university.collaboration}</small>` : ''}
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'LocalAdminDetails',
                        searchable: true,
                        render: function(data, type, row) {
                            // สำหรับการค้นหา ให้ส่งข้อมูลองค์กรปกครองส่วนท้องถิ่น
                            if (type === 'type' || type === 'sort' || type === 'search') {
                                if (data && data.length > 0) {
                                    return data.map(function(admin) {
                                        return `${admin.name} ${admin.type || ''} ${admin.district || ''} ${admin.support_type || ''}`;
                                    }).join(' ');
                                }
                                return '';
                            }
                            
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(admin, index) {
                                    html += `<div class="local-admin-item mb-1">
                                        <span class="badge bg-danger me-1">${index + 1}</span>
                                        <strong>${admin.name}</strong>
                                        ${admin.type ? `<br><small class="text-muted">ประเภท: ${admin.type}</small>` : ''}
                                        ${admin.district ? `<br><small class="text-muted">อำเภอ: ${admin.district}</small>` : ''}
                                        ${admin.support_type ? `<br><small class="text-muted">การสนับสนุน: ${admin.support_type}</small>` : ''}
                                    </div>`;
                                });
                                html += '</div>';
                                return html;
                            }
                            return `<span class="text-muted">-</span>`;
                        }
                    },
                    { 
                        data: 'ProjectID',
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group" role="group">
                                    <a href="project_detail.php?id=${data}" class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_project.php?id=${data}" class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[2, 'desc']],
                pageLength: 25,
                lengthMenu: [[25, 50, 75, 100, -1], ["25 รายการ", "50 รายการ", "75 รายการ", "100 รายการ", "ทั้งหมด"]],
                responsive: true,
                searching: true, // เปิดใช้งานการค้นหา
                searchCols: [
                    null, // ProjectCode - ค้นหาได้
                    null, // ProjectName - ค้นหาได้
                    null, // ProjectYear - ค้นหาได้
                    null, // ResponsiblePerson - ค้นหาได้
                    null, // TotalBudget - ค้นหาได้
                    null, // MainProjectName - ค้นหาได้
                    null, // StrategyName - ค้นหาได้
                    null, // AgencyName - ค้นหาได้
                    null, // IndicatorDetails - ค้นหาได้
                    null, // ProductDetails - ค้นหาได้
                    null, // SchoolDetails - ค้นหาได้
                    null, // TargetGroupDetails - ค้นหาได้
                    null, // LocationDetails - ค้นหาได้
                    null, // SROIData - ค้นหาได้
                    null, // EnterpriseDetails - ค้นหาได้
                    null, // OtherOrganizations - ค้นหาได้
                    null, // NetworkDetails - ค้นหาได้
                    null, // UniversityDetails - ค้นหาได้
                    null, // LocalAdminDetails - ค้นหาได้
                    { search: null, searchable: false } // ProjectID - ไม่ค้นหา
                ],
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>' +
                     '<"row"<"col-12"B>>' +
                     '<"row"<"col-12"rt>>' +
                     '<"row"<"col-md-5"i><"col-md-7"p>>',
                buttons: [
                    // {
                    //     extend: 'excelHtml5',
                    //     text: '<i class="fas fa-file-excel"></i> Excel',
                    //     className: 'btn btn-success btn-sm',
                    //     title: 'รายงานโครงการ',
                    //     charset: 'utf-8',
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18], // เลือกคอลัมน์ทั้งหมด ยกเว้นคอลัมน์สุดท้าย
                    //         format: {
                    //             body: function(data, row, column, node) {
                    //                 if (typeof data === 'string') {
                    //                     return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#39;/g, "'").trim();
                    //                 }
                    //                 return data || '';
                    //             }
                    //         }
                    //     }
                    // },
                    // {
                    //     extend: 'print',
                    //     text: '<i class="fas fa-print"></i> พิมพ์',
                    //     className: 'btn btn-secondary btn-sm'
                    // }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                },
                drawCallback: function(settings) {
                    // อัพเดทสถิติหลังจากโหลดข้อมูลเสร็จ
                    loadStatistics();
                    
                    // ตรวจสอบให้แน่ใจว่า length menu แสดง
                    $('.dataTables_length').show();
                }
            });
            
            // ตรวจสอบและแสดง length menu หลังจากสร้าง DataTable
            setTimeout(function() {
                $('.dataTables_length').show();
                $('.dataTables_filter').show();
                console.log('DataTable controls visibility check completed');
            }, 500);
            
            // Add search highlighting functionality
            addHighlightFunctionality();
        }
        
        // Function to add search term highlighting
        function addHighlightFunctionality() {
            // Function to highlight search terms
            function highlightSearchTerm(searchTerm) {
                // Remove previous highlights
                $('#projectsTable tbody td').each(function() {
                    var html = $(this).html();
                    html = html.replace(/<span class="highlight[^>]*">(.*?)<\/span>/gi, '$1');
                    $(this).html(html);
                });
                
                // Add new highlights if search term exists and is not just whitespace
                if (searchTerm && searchTerm.trim().length > 0) {
                    var trimmedTerm = searchTerm.trim();
                    var regex = new RegExp('(' + trimmedTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                    
                    $('#projectsTable tbody td').each(function() {
                        var cell = $(this);
                        // Skip action column (last column)
                        if (cell.index() === $('#projectsTable thead th').length - 1) return;
                        
                        var originalHtml = cell.html();
                        
                        // Only highlight if the cell contains text and the search term is meaningful
                        if (originalHtml && originalHtml.trim() !== '' && trimmedTerm.length > 0) {
                            // Create a temporary div to work with text content
                            var tempDiv = $('<div>').html(originalHtml);
                            
                            // Function to highlight text nodes only
                            function highlightTextNodes(node) {
                                if (node.nodeType === 3) { // Text node
                                    var text = node.nodeValue;
                                    if (regex.test(text)) {
                                        var highlightedText = text.replace(regex, '<span class="highlight highlight-animation">$1</span>');
                                        var wrapper = document.createElement('span');
                                        wrapper.innerHTML = highlightedText;
                                        
                                        // Replace the text node with highlighted content
                                        var parent = node.parentNode;
                                        while (wrapper.firstChild) {
                                            parent.insertBefore(wrapper.firstChild, node);
                                        }
                                        parent.removeChild(node);
                                    }
                                } else if (node.nodeType === 1) { // Element node
                                    // Skip script and style elements
                                    if (node.tagName && (node.tagName.toLowerCase() === 'script' || node.tagName.toLowerCase() === 'style')) {
                                        return;
                                    }
                                    
                                    // Process child nodes
                                    var children = Array.from(node.childNodes);
                                    children.forEach(function(child) {
                                        highlightTextNodes(child);
                                    });
                                }
                            }
                            
                            // Apply highlighting to text nodes only
                            tempDiv.get(0).childNodes.forEach(function(node) {
                                highlightTextNodes(node);
                            });
                            
                            cell.html(tempDiv.html());
                        }
                    });
                }
            }
            
            // Listen for search input changes
            projectsTable.on('search.dt', function() {
                var searchTerm = projectsTable.search();
                highlightSearchTerm(searchTerm);
            });
            
            // Listen for draw events (pagination, sorting, etc.)
            projectsTable.on('draw.dt', function() {
                var searchTerm = projectsTable.search();
                if (searchTerm) {
                    setTimeout(function() {
                        highlightSearchTerm(searchTerm);
                    }, 100);
                }
            });
        }

        function clearFilters() {
            $('#filterForm')[0].reset();
            // Reset all dynamic dropdowns - reverse order
            $('#districtFilter').prop('disabled', true).html('<option value="">ทุกอำเภอ</option>');
            $('#provinceFilter').prop('disabled', true).html('<option value="">ทุกจังหวัด</option>');
            
            // Reload all filter data
            loadAllFilterData();
            
            // Reload table
            setTimeout(function() {
                projectsTable.ajax.reload();
            }, 500);
        }

        function exportData() {
            // สร้าง URL สำหรับ export พร้อม filter parameters
            let params = new URLSearchParams();
            $('#filterForm').serializeArray().forEach(function(item) {
                if (item.value) {
                    params.append(item.name, item.value);
                }
            });
            
            window.open(`export_projects_table_detailed_xlsx.php?${params.toString()}`, '_blank');
        }

        function loadStatistics() {
            $.ajax({
                url: 'projects_table_stats.php',
                type: 'POST',
                data: {
                    project_year_start: $('#projectYearStartFilter').val(),
                    project_year_end: $('#projectYearEndFilter').val(),
                    subdistrict: $('#subdistrictFilter').val(),
                    district: $('#districtFilter').val(),
                    province: $('#provinceFilter').val(),
                    main_project: $('#mainProjectFilter').val(),
                    strategy: $('#strategyFilter').val(),
                    agency: $('#agencyFilter').val(),
                    target_group: $('#targetGroupFilter').val()
                },
                dataType: 'json',
                success: function(data) {
                    $('#totalProjects').text(data.total_projects.toLocaleString('th-TH'));
                    $('#totalBudget').text((data.total_budget / 1000000).toFixed(2));
                    $('#totalIndicators').text(data.total_indicators.toLocaleString('th-TH'));
                    $('#totalLocations').text(data.total_locations.toLocaleString('th-TH'));
                    $('#totalTargetPeople').text(data.total_target_people.toLocaleString('th-TH'));
                    $('#totalProducts').text(data.total_products.toLocaleString('th-TH'));
                    $('#totalSchools').text(data.total_schools.toLocaleString('th-TH'));
                    $('#totalTargetGroups').text(data.total_main_projects.toLocaleString('th-TH'));
                    $('#totalAgencies').text(data.total_agencies.toLocaleString('th-TH'));
                },
                error: function() {
                    console.error('ไม่สามารถโหลดสถิติได้');
                }
            });
        }

        // Auto-reload เมื่อเปลี่ยน filter (แต่ไม่ใช่สำหรับ dynamic loading)
        // $('.form-select, .form-control').on('change', function() {
        //     projectsTable.ajax.reload();
        // });
    </script>
</body>
</html>
