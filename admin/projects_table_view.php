<?php 
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (file_exists('./database/db.php')) {
    include './database/db.php';
} else {
    include 'db.php';
}
include 'navbar.php'; 

// ตรวจสอบการ logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_destroy();
    header('Location: portal/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายงานโครงการ</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.0/dist/sweetalert2.min.css">
    
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
        
        /* Individual header colors matching stats cards */
        .table thead th.header-projects {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }
        
        .table thead th.header-budget {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }
        
        .table thead th.header-year {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }
        
        .table thead th.header-teacher {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
            color: #333 !important;
        }
        
        .table thead th.header-indicators {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
            color: #333 !important;
        }
        
        .table thead th.header-products {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }
        
        .table thead th.header-schools {
            background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%) !important;
        }
        
        .table thead th.header-target-groups {
            background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%) !important;
        }
        
        .table thead th.header-locations {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }
        
        .table thead th.header-agencies {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        }
        
        .table thead th.header-main-project {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }
        
        .table thead th.header-strategy {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
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
        
        /* DataTables Responsive Enhancement */
        .table-responsive {
            max-width: 100%;
            overflow-x: visible;
        }
        
        table.dataTable tbody td.child {
            border-top: 1px solid #dee2e6;
            padding: 1rem;
            background-color: #f8f9fa;
        }
        
        table.dataTable tbody tr.child:hover {
            background-color: #e9ecef;
        }
        
        .dtr-details {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin: 0.5rem 0;
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
        
        .table-purple {
            background-color: #6f42c1 !important;
            color: white !important;
        }
        
        .table-purple th {
            background-color: #6f42c1 !important;
            color: white !important;
            border-color: #5a2d8d !important;
        }
        
        .border-purple {
            border-color: #6f42c1 !important;
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
        
        /* Fullscreen Modal Styles */
        .stats-modal-fullscreen {
            padding: 20px !important;
        }
        
        .stats-modal-content-fullscreen {
            max-height: 85vh !important;
            overflow-y: auto !important;
            padding: 10px !important;
        }
        
        .stats-modal-title {
            font-size: 1.5rem !important;
            margin-bottom: 15px !important;
        }
        
        /* Responsive tables in fullscreen modal */
        .stats-modal-fullscreen .table-responsive {
            max-height: 75vh !important;
            overflow-y: auto !important;
        }
        
        .stats-modal-fullscreen .table {
            font-size: 0.9rem !important;
        }
        
        .stats-modal-fullscreen .table th {
            position: sticky !important;
            top: 0 !important;
            z-index: 10 !important;
            background-color: var(--bs-warning) !important;
        }
        
        /* Export Modal Styles */
        .export-modal .export-popup {
            border-radius: 20px !important;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        
        .export-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            margin-bottom: 20px !important;
        }
        
        .export-content {
            font-size: 1rem !important;
        }
        
        .processing-modal .processing-popup {
            border-radius: 20px !important;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
            min-width: 400px !important;
        }
        
        .processing-popup .swal2-title {
            font-size: 1.4rem !important;
            margin-bottom: 20px !important;
        }
        
        .success-modal .success-popup {
            border-radius: 20px !important;
            box-shadow: 0 15px 35px rgba(40, 167, 69, 0.2) !important;
        }
        
        .success-popup .swal2-title {
            color: #28a745 !important;
            font-size: 1.5rem !important;
        }
        
        /* Custom Progress Bar */
        .progress {
            background-color: #e9ecef !important;
            border-radius: 10px !important;
            overflow: hidden !important;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            transition: width 0.3s ease !important;
        }
        
        /* Export Alert Styles */
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%) !important;
            border: 1px solid #bee5eb !important;
            border-radius: 10px !important;
            color: #0c5460 !important;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%) !important;
            border: 1px solid #c3e6cb !important;
            border-radius: 10px !important;
            color: #155724 !important;
        }
        
        /* Spinner Customization */
        .spinner-border {
            width: 3rem !important;
            height: 3rem !important;
            border-width: 0.3em !important;
        }
        
        .processing-popup .spinner-border {
            color: #007bff !important;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid mt-3">
        <!-- Header -->
        <div class="table-header">
            <h2 class="fw-bold mb-3">
                <i class="fas fa-table me-3"></i>รายงานโครงการ
            </h2>
            <p class="mb-0">ข้อมูลโครงการทั้งหมดพร้อมรายละเอียด และเครื่องมือกรองข้อมูล พร้อมรองรับการส่งออกแบบไฟล์ Excel</p>
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
            <div class="col-lg-3 col-md-6" style="display: none;">
                <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 id="totalTargetPeople">-</h3>
                    <p class="mb-0">กลุ่มเป้าหมายทั้งหมด (คน)</p>
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
            <div class="col-lg-3 col-md-6" style="display: none;">
                <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                    <i class="fas fa-user-friends fa-2x mb-2"></i>
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
                                <th class="header-projects"><i class="fas fa-hashtag me-2"></i>ลำดับ</th>
                                <th class="header-projects" style="display: none;"><i class="fas fa-barcode me-2"></i>รหัสโครงการ</th>
                                <th class="header-projects"><i class="fas fa-project-diagram me-2"></i>ชื่อโครงการ</th>
                                <th class="header-year"><i class="fas fa-calendar-alt me-2"></i>ปีโครงการ</th>
                                <th class="header-teacher"><i class="fas fa-user-tie me-2"></i>ผู้รับผิดชอบ</th>
                                <th class="header-budget"><i class="fas fa-coins me-2"></i>งบประมาณอนุมัติ</th>
                                <th class="header-main-project"><i class="fas fa-sitemap me-2"></i>โครงการหลัก</th>
                                <th class="header-strategy"><i class="fas fa-chess me-2"></i>ยุทธศาสตร์</th>
                                <th class="header-agencies"><i class="fas fa-building me-2"></i>หน่วยงาน</th>
                                <th class="header-indicators"><i class="fas fa-chart-line me-2"></i>ตัวชี้วัด (รายละเอียด)</th>
                                <th class="header-products"><i class="fas fa-box me-2"></i>ผลิตภัณฑ์ (รายละเอียด)</th>
                                <th class="header-schools"><i class="fas fa-school me-2"></i>โรงเรียน (รายละเอียด)</th>
                                <th class="header-target-groups"><i class="fas fa-users me-2"></i>กลุ่มเป้าหมาย (รายละเอียด)</th>
                                <th class="header-locations"><i class="fas fa-map-marker-alt me-2"></i>พื้นที่ดำเนินการ (รายละเอียด)</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.0/dist/sweetalert2.all.min.js"></script>
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
            
            // Initialize stats card click handlers
            initializeStatsCardHandlers();
        });

        // ฟังก์ชันสำหรับโหลดข้อมูลแบบ Dynamic
        function initializeDynamicFilters() {
            // Load initial data - โหลดข้อมูลทั้งหมดเริ่มต้น
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
                // Auto reload table when year filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
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
                    projectsTable.ajax.reload();
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
                    projectsTable.ajax.reload();
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
                    projectsTable.ajax.reload();
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

            $('#teacherFilter').on('change', function() {
                // โหลดตัวกรองที่เกี่ยวข้องทั้งหมดเพื่อให้เกิด cascading
                loadFilterData('provinces');
                loadFilterData('districts');
                loadFilterData('subdistricts');
                loadFilterData('villages');
                loadFilterData('main_projects');
                loadFilterData('strategies');
                loadFilterData('agencies');
                loadFilterData('target_groups');
                
                // Auto reload table when filter changes
                setTimeout(function() {
                    projectsTable.ajax.reload();
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

        function resetLocationFilters(filterTypes) {
            // ฟังก์ชันนี้จะโหลดข้อมูลใหม่แทนการรีเซ็ตเป็นค่าว่าง
            filterTypes.forEach(function(type) {
                switch (type) {
                    case 'provinces':
                        loadFilterData('provinces');
                        break;
                    case 'districts':
                        loadFilterData('districts');
                        break;
                    case 'subdistricts':
                        loadFilterData('subdistricts');
                        break;
                    case 'villages':
                        loadFilterData('villages');
                        break;
                }
            });
        }

        function loadOtherFilterData() {
            loadFilterData('main_projects');
            loadFilterData('strategies');
            loadFilterData('agencies');
            loadFilterData('target_groups');
            loadFilterData('teachers');
        }

        function loadFilterData(type) {
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
                url: '../api/get_filtered_data.php',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(data) {
                    updateSelectOptions(type, data);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading ' + type + ' data:', error);
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
                $(selector).val('').trigger('change');
            }
        }

        function initializeDataTable() {
            projectsTable = $('#projectsTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[3, 'desc']], // เรียงตามปีโครงการ (คอลัมน์ที่ 3) จากมากไปน้อย
                ajax: {
                    url: 'projects_table_data.php',
                    type: 'POST',
                    data: function(d) {
                        // เพิ่มค่าจากฟิลเตอร์
                        d.project_year_start = $('#projectYearStartFilter').val();
                        d.project_year_end = $('#projectYearEndFilter').val();
                        d.province = $('#provinceFilter').val();
                        d.district = $('#districtFilter').val();
                        d.subdistrict = $('#subdistrictFilter').val();
                        d.village = $('#villageFilter').val();
                        d.main_project = $('#mainProjectFilter').val();
                        d.strategy = $('#strategyFilter').val();
                        d.agency = $('#agencyFilter').val();
                        d.target_group = $('#targetGroupFilter').val();
                        d.teacher = $('#teacherFilter').val();
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
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        width: "60px"
                    },
                    { 
                        data: 'ProjectCode',
                        searchable: true,
                        visible: false, // ซ่อนคอลัมน์
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
                                <a href="project_detail.php?id=${data}" class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </a>
                            `;
                        }
                    }
                ],
                order: [[3, 'desc']], // เปลี่ยนจาก [[2, 'desc']] เป็น [[3, 'desc']] เพราะเพิ่มคอลัมน์ลำดับ
                pageLength: 25,
                lengthMenu: [[25, 50, 75, 100, -1], ["25 รายการ", "50 รายการ", "75 รายการ", "100 รายการ", "ทั้งหมด"]],
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRow,
                        type: 'inline',
                        target: 'tr'
                    }
                },
                columnDefs: [
                    // กำหนดความสำคัญในการแสดงคอลัมน์
                    { responsivePriority: 1, targets: 0 }, // ลำดับ
                    { responsivePriority: 2, targets: 2 }, // ชื่อโครงการ  
                    { responsivePriority: 3, targets: 3 }, // ปีโครงการ
                    { responsivePriority: 4, targets: 4 }, // ผู้รับผิดชอบ
                    { responsivePriority: 5, targets: 5 }, // งบประมาณ
                    { responsivePriority: 6, targets: 6 }, // โครงการหลัก
                    { responsivePriority: 7, targets: 7 }, // ยุทธศาสตร์
                    { responsivePriority: 8, targets: 8 }, // หน่วยงาน - คอลัมน์สุดท้ายที่แสดงเสมอ
                    { responsivePriority: 99, targets: -1 }, // ปุ่มดูรายละเอียด
                    // คอลัมน์อื่นๆ (หลังจากหน่วยงาน) จะถูกซ่อนไปใน responsive dropdown
                    { 
                        responsivePriority: 10000, 
                        targets: [1, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19], // รวม ProjectCode ที่ซ่อนอยู่
                        className: "none" // บังคับซ่อนใน responsive mode
                    }
                ],
                colReorder: false, // ปิดการเรียงลำดับคอลัมน์
                searching: true, // เปิดใช้งานการค้นหา
                searchCols: [
                    { search: null, searchable: false }, // ลำดับ - ไม่ค้นหา
                    null, // ProjectCode - ค้นหาได้ (ซ่อนแต่ยังค้นหาได้)
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
            // Reset all location dropdowns
            $('#provinceFilter').html('<option value="">ทุกจังหวัด</option>');
            $('#districtFilter').html('<option value="">ทุกอำเภอ</option>');
            $('#subdistrictFilter').html('<option value="">ทุกตำบล</option>');
            $('#villageFilter').html('<option value="">ทุกหมู่บ้าน/ชุมชน</option>');
            
            // Reload all filter data
            loadAllFilterData();
            
            // Reload table
            setTimeout(function() {
                projectsTable.ajax.reload();
            }, 500);
        }

        function exportData() {
            // ดึงข้อมูลสถิติปัจจุบันเพื่อแสดงในสรุป
            const totalProjects = $('#totalProjects').text() || '0';
            const totalBudget = $('#totalBudget').text() || '0';
            const totalIndicators = $('#totalIndicators').text() || '0';
            const totalLocations = $('#totalLocations').text() || '0';
            
            // ตรวจสอบตัวกรองที่เลือก
            const activeFilters = [];
            $('#filterForm').serializeArray().forEach(function(item) {
                if (item.value) {
                    switch(item.name) {
                        case 'project_year_start':
                            activeFilters.push(`ปีเริ่มต้น: พ.ศ. ${item.value}`);
                            break;
                        case 'project_year_end':
                            activeFilters.push(`ปีสิ้นสุด: พ.ศ. ${item.value}`);
                            break;
                        case 'province':
                            activeFilters.push(`จังหวัด: ${$('#provinceFilter option:selected').text()}`);
                            break;
                        case 'district':
                            activeFilters.push(`อำเภอ: ${$('#districtFilter option:selected').text()}`);
                            break;
                        case 'subdistrict':
                            activeFilters.push(`ตำบล: ${$('#subdistrictFilter option:selected').text()}`);
                            break;
                        case 'main_project':
                            activeFilters.push(`โครงการหลัก: ${$('#mainProjectFilter option:selected').text()}`);
                            break;
                        case 'strategy':
                            activeFilters.push(`ยุทธศาสตร์: ${$('#strategyFilter option:selected').text()}`);
                            break;
                        case 'agency':
                            activeFilters.push(`หน่วยงาน: ${$('#agencyFilter option:selected').text()}`);
                            break;
                        case 'target_group':
                            activeFilters.push(`กลุ่มเป้าหมาย: ${$('#targetGroupFilter option:selected').text()}`);
                            break;
                        case 'teacher':
                            activeFilters.push(`อาจารย์: ${$('#teacherFilter option:selected').text()}`);
                            break;
                    }
                }
            });
            
            const filterSummary = activeFilters.length > 0 ? 
                `<div class="alert alert-warning mt-2">
                    <strong>ตัวกรองที่เลือก:</strong><br>
                    ${activeFilters.join('<br>')}
                </div>` : 
                `<div class="alert alert-info mt-2">
                    <strong>ไม่มีตัวกรอง</strong> - ส่งออกข้อมูลทั้งหมด
                </div>`;

            // แสดง popup ยืนยันก่อนการส่งออก
            Swal.fire({
                title: '<i class="fas fa-download text-success"></i> ส่งออกข้อมูลรายงานโครงการ',
                html: `
                    <div class="text-start">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-project-diagram fa-2x mb-1"></i>
                                        <h5 class="mb-0">${totalProjects}</h5>
                                        <small>โครงการ</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-coins fa-2x mb-1"></i>
                                        <h5 class="mb-0">${totalBudget}</h5>
                                        <small>ล้านบาท</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-chart-line fa-2x mb-1"></i>
                                        <h5 class="mb-0">${totalIndicators}</h5>
                                        <small>ตัวชี้วัด</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-map-marker-alt fa-2x mb-1"></i>
                                        <h5 class="mb-0">${totalLocations}</h5>
                                        <small>พื้นที่</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ${filterSummary}
                        <div class="alert alert-info">
                            <i class="fas fa-file-excel me-2"></i>
                            <strong>รูปแบบไฟล์:</strong> Excel (.xlsx) พร้อม AutoFilter และ Grouping<br>
                            <i class="fas fa-clock me-2"></i>
                            <strong>เวลาประมาณ:</strong> 30 วินาที - 2 นาที (ขึ้นอยู่กับจำนวนข้อมูล)
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-download me-2"></i>เริ่มส่งออกข้อมูล',
                cancelButtonText: '<i class="fas fa-times me-2"></i>ยกเลิก',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false,
                allowEscapeKey: false,
                width: '600px',
                customClass: {
                    container: 'export-modal',
                    popup: 'export-popup',
                    title: 'export-title',
                    content: 'export-content'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // เริ่มการส่งออกจริง
                    startExportProcess(totalProjects);
                }
            });
        }

        function startExportProcess(totalProjects) {
            // แสดง popup กำลังประมวลผล
            Swal.fire({
                title: '<i class="fas fa-cog fa-spin text-primary"></i> กำลังประมวลผลข้อมูล',
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <p class="mb-2"><strong>กำลังสร้างไฟล์ Excel สำหรับ ${totalProjects} โครงการ...</strong></p>
                        <p class="text-muted mb-0" id="exportStatus">เริ่มต้นการประมวลผล...</p>
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" id="exportProgress">0%</div>
                        </div>
                        <small class="text-muted mt-2 d-block">กรุณาอย่าปิดหน้าต่างนี้จนกว่าการส่งออกจะเสร็จสิ้น</small>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCancelButton: false,
                customClass: {
                    container: 'processing-modal',
                    popup: 'processing-popup'
                }
            });

            // สร้าง URL สำหรับ export พร้อม filter parameters
            let params = new URLSearchParams();
            $('#filterForm').serializeArray().forEach(function(item) {
                if (item.value) {
                    params.append(item.name, item.value);
                }
            });
            
            // เริ่ม progress bar ตามจำนวนโครงการ
            let progress = 0;
            const totalProjectsNum = parseInt(totalProjects.replace(/,/g, '')) || 1;
            const estimatedTime = Math.max(5000, Math.min(30000, totalProjectsNum * 100)); // 5-30 วินาที
            const progressStep = estimatedTime / 100; // แบ่งเป็น 100 step
            
            const statusTexts = [
                'เริ่มต้นการประมวลผล...',
                'กำลังดึงข้อมูลโครงการ...',
                'กำลังดึงข้อมูลตัวชี้วัด...',
                'กำลังดึงข้อมูลผลิตภัณฑ์...',
                'กำลังดึงข้อมูลโรงเรียน...',
                'กำลังดึงข้อมูลกลุ่มเป้าหมาย...',
                'กำลังดึงข้อมูลพื้นที่...',
                'กำลังจัดรูปแบบข้อมูล...',
                'กำลังสร้างไฟล์ Excel...',
                'กำลังดำเนินการสุดท้าย...'
            ];
            
            let statusIndex = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 2 + 0.5; // ช้าลงมาก
                if (progress > 95) { // หยุดที่ 95%
                    clearInterval(progressInterval);
                    progress = 95;
                    $('#exportStatus').text('กำลังสร้างไฟล์ Excel...');
                    $('#exportProgress').css('width', progress + '%').text(Math.round(progress) + '%');
                    return;
                }
                
                // อัพเดทสถานะ
                const expectedStatusIndex = Math.floor(progress / 10);
                if (expectedStatusIndex > statusIndex && statusIndex < statusTexts.length - 1) {
                    statusIndex = expectedStatusIndex;
                    $('#exportStatus').text(statusTexts[statusIndex]);
                }
                
                $('#exportProgress').css('width', progress + '%').text(Math.round(progress) + '%');
            }, progressStep);

            // เริ่มการดาวน์โหลดจริง
            const downloadUrl = `export_projects_table_detailed_xlsx.php?${params.toString()}`;
            
            // ใช้ XHR เพื่อตรวจสอบการดาวน์โหลด
            const xhr = new XMLHttpRequest();
            xhr.open('GET', downloadUrl, true);
            xhr.responseType = 'blob';
            
            xhr.onprogress = function(event) {
                if (event.lengthComputable) {
                    // อัพเดท progress จาก server
                    const serverProgress = Math.min(95, (event.loaded / event.total) * 95);
                    if (serverProgress > progress) {
                        progress = serverProgress;
                        $('#exportProgress').css('width', progress + '%').text(Math.round(progress) + '%');
                    }
                }
            };
            
            xhr.onload = function() {
                clearInterval(progressInterval);
                
                if (xhr.status === 200) {
                    // สร้างลิงก์ดาวน์โหลด
                    const blob = xhr.response;
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `รายงานโครงการแบบรายละเอียด_${new Date().toISOString().slice(0,19).replace(/[:.]/g, '-')}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    
                    // อัพเดทเป็น 100%
                    $('#exportStatus').text('ส่งออกข้อมูลเสร็จสิ้น!');
                    $('#exportProgress').css('width', '100%').text('100%');
                    
                    setTimeout(() => {
                        // แสดง popup สำเร็จ
                        Swal.fire({
                            title: '<i class="fas fa-check-circle text-success"></i> ส่งออกสำเร็จ!',
                            html: `
                                <div class="text-center">
                                    <div class="alert alert-success">
                                        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                        <h5><strong>ไฟล์ Excel ถูกสร้างเรียบร้อยแล้ว</strong></h5>
                                        <p class="mb-2">ส่งออกข้อมูล ${totalProjects} โครงการสำเร็จ</p>
                                        <hr>
                                        <small class="text-muted">
                                            ไฟล์ได้ถูกดาวน์โหลดแล้ว<br>
                                            กรุณาตรวจสอบในโฟลเดอร์ Downloads ของคุณ
                                        </small>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: '<i class="fas fa-check me-2"></i>เรียบร้อย',
                            confirmButtonColor: '#28a745',
                            timer: 8000,
                            timerProgressBar: true,
                            customClass: {
                                container: 'success-modal',
                                popup: 'success-popup'
                            }
                        });
                    }, 1000);
                } else {
                    // กรณีเกิดข้อผิดพลาด
                    Swal.fire({
                        title: '<i class="fas fa-exclamation-triangle text-warning"></i> เกิดข้อผิดพลาด',
                        html: `
                            <div class="text-center">
                                <div class="alert alert-warning">
                                    <p class="mb-2"><strong>ไม่สามารถส่งออกไฟล์ได้</strong></p>
                                    <p class="mb-0">กรุณาลองใหม่อีกครั้ง หรือติดต่อผู้ดูแลระบบ</p>
                                </div>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: '<i class="fas fa-redo me-2"></i>ลองใหม่',
                        confirmButtonColor: '#dc3545'
                    });
                }
            };
            
            xhr.onerror = function() {
                clearInterval(progressInterval);
                Swal.fire({
                    title: '<i class="fas fa-exclamation-triangle text-warning"></i> เกิดข้อผิดพลาด',
                    html: `
                        <div class="text-center">
                            <div class="alert alert-warning">
                                <p class="mb-2"><strong>การเชื่อมต่อมีปัญหา</strong></p>
                                <p class="mb-0">กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ตและลองใหม่อีกครั้ง</p>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: '<i class="fas fa-redo me-2"></i>ลองใหม่',
                    confirmButtonColor: '#dc3545'
                });
            };
            
            // เริ่มการดาวน์โหลด
            xhr.send();
        }

        function initializeStatsCardHandlers() {
            // Event handler สำหรับ stats cards
            $('.stats-card').on('click', function() {
                const cardText = $(this).find('p').text();
                const cardValue = $(this).find('h3').text();
                const cardIcon = $(this).find('i').attr('class');
                
                // กำหนดประเภทของ card ตามข้อความ
                let cardType = '';
                let filterParam = '';
                let detailQuery = '';
                
                if (cardText.includes('โครงการทั้งหมด')) {
                    cardType = 'projects';
                    detailQuery = 'type=projects';
                } else if (cardText.includes('งบประมาณรวม')) {
                    cardType = 'budget';
                    detailQuery = 'type=budget';
                } else if (cardText.includes('ตัวชี้วัดทั้งหมด')) {
                    cardType = 'indicators';
                    detailQuery = 'type=indicators';
                } else if (cardText.includes('พื้นที่ดำเนินการ')) {
                    cardType = 'locations';
                    detailQuery = 'type=locations';
                } else if (cardText.includes('กลุ่มเป้าหมายทั้งหมด (คน)')) {
                    cardType = 'target_people';
                    detailQuery = 'type=target_people';
                } else if (cardText.includes('ผลิตภัณฑ์ทั้งหมด')) {
                    cardType = 'products';
                    detailQuery = 'type=products';
                } else if (cardText.includes('โรงเรียนที่เข้าร่วม')) {
                    cardType = 'schools';
                    detailQuery = 'type=schools';
                } else if (cardText.includes('กลุ่มเป้าหมายทั้งหมด') && !cardText.includes('(คน)')) {
                    cardType = 'target_groups';
                    detailQuery = 'type=target_groups';
                } else if (cardText.includes('หน่วยงานที่เข้าร่วม')) {
                    cardType = 'agencies';
                    detailQuery = 'type=agencies';
                }
                
                showStatsCardModal(cardType, cardText, cardValue, cardIcon, detailQuery);
            });
        }
        
        function showStatsCardModal(cardType, cardText, cardValue, cardIcon, detailQuery) {
            // กำหนดสีพื้นหลังตามประเภท
            let bgColor = '#007bff';
            switch(cardType) {
                case 'projects': bgColor = '#007bff'; break;
                case 'budget': bgColor = '#28a745'; break;
                case 'indicators': bgColor = '#ffc107'; break;
                case 'locations': bgColor = '#dc3545'; break;
                case 'target_people': bgColor = '#6f42c1'; break;
                case 'products': bgColor = '#28a745'; break;
                case 'schools': bgColor = '#fd7e14'; break;
                case 'target_groups': bgColor = '#6f42c1'; break;
                case 'agencies': bgColor = '#17a2b8'; break;
            }

            // แสดง loading modal ก่อน
            Swal.fire({
                title: 'กำลังโหลดข้อมูล...',
                html: '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // เรียก API เพื่อดึงข้อมูลรายละเอียด
            $.ajax({
                url: 'get_stats_detail.php',
                type: 'POST',
                data: {
                    card_type: cardType,
                    project_year_start: $('#projectYearStartFilter').val(),
                    project_year_end: $('#projectYearEndFilter').val(),
                    province: $('#provinceFilter').val(),
                    district: $('#districtFilter').val(),
                    subdistrict: $('#subdistrictFilter').val(),
                    village: $('#villageFilter').val(),
                    main_project: $('#mainProjectFilter').val(),
                    strategy: $('#strategyFilter').val(),
                    agency: $('#agencyFilter').val(),
                    target_group: $('#targetGroupFilter').val(),
                    teacher: $('#teacherFilter').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayDetailedStatsModal(cardType, cardText, cardValue, cardIcon, bgColor, response.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถโหลดข้อมูลได้'
                        });
                    }
                },
                error: function() {
                    // Fallback - แสดงข้อมูลพื้นฐาน
                    displayBasicStatsModal(cardType, cardText, cardValue, cardIcon, bgColor);
                }
            });
        }

        function displayDetailedStatsModal(cardType, cardText, cardValue, cardIcon, bgColor, data) {
            let detailContent = '';
            
            switch(cardType) {
                case 'projects':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>รายการโครงการทั้งหมด</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="projectRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="25">25 รายการ</option>
                                        <option value="50" selected>50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="500">500 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="projectTableContainer">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>รหัสโครงการ</th>
                                                <th>ชื่อโครงการ</th>
                                                <th>ปี</th>
                                                <th>ผู้รับผิดชอบ</th>
                                                <th>งบประมาณ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="projectTableBody">
                    `;
                    
                    // Function to render projects based on limit
                    const renderProjects = (limit = 50) => {
                        let projectsToShow = data || [];
                        if (limit > 0) {
                            projectsToShow = projectsToShow.slice(0, limit);
                        }
                        
                        let projectRows = '';
                        if (projectsToShow && projectsToShow.length > 0) {
                            projectsToShow.forEach((project, index) => {
                                projectRows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${project.ProjectID || '-'}</td>
                                        <td style="min-width: 250px;">${project.ProjectName}</td>
                                        <td>พ.ศ. ${project.ProjectYear}</td>
                                        <td style="min-width: 150px;">${project.ResponsiblePerson || '-'}</td>
                                        <td>${project.TotalBudget ? Number(project.TotalBudget).toLocaleString('th-TH') + ' บาท' : '-'}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            projectRows = '<tr><td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td></tr>';
                        }
                        return projectRows;
                    };
                    
                    detailContent += renderProjects(50); // Initial render with 50 items
                    
                    detailContent += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="showingCount">50</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} โครงการ</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('projectRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const tbody = document.getElementById('projectTableBody');
                                const showingCount = document.getElementById('showingCount');
                                
                                if (tbody && showingCount) {
                                    let projectsToShow = data || [];
                                    if (limit > 0) {
                                        projectsToShow = projectsToShow.slice(0, limit);
                                    }
                                    
                                    let newRows = '';
                                    if (projectsToShow && projectsToShow.length > 0) {
                                        projectsToShow.forEach((project, index) => {
                                            newRows += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${project.ProjectID || '-'}</td>
                                                    <td style="min-width: 250px;">${project.ProjectName}</td>
                                                    <td>พ.ศ. ${project.ProjectYear}</td>
                                                    <td style="min-width: 150px;">${project.ResponsiblePerson || '-'}</td>
                                                    <td>${project.TotalBudget ? Number(project.TotalBudget).toLocaleString('th-TH') + ' บาท' : '-'}</td>
                                                </tr>
                                            `;
                                        });
                                    } else {
                                        newRows = '<tr><td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td></tr>';
                                    }
                                    
                                    tbody.innerHTML = newRows;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'budget':
                    detailContent = `
                        <div style="text-align: left;">
                            <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>รายละเอียดงบประมาณ</h6>
                    `;
                    if (data && data.budget_breakdown) {
                        detailContent += `
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h5 class="text-success">${Number(data.budget_breakdown.total_approved || 0).toLocaleString('th-TH')} บาท</h5>
                                            <small>งบประมาณที่อนุมัติ</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <h5 class="text-info">${data.budget_breakdown.project_count || 0} โครงการ</h5>
                                            <small>จำนวนโครงการ</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        if (data.budget_breakdown.by_year && data.budget_breakdown.by_year.length > 0) {
                            detailContent += `
                                <h6 class="mt-3 mb-2">แบ่งตามปี:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-success">
                                            <tr><th>ปี</th><th>งบประมาณ</th><th>จำนวนโครงการ</th></tr>
                                        </thead>
                                        <tbody>
                            `;
                            data.budget_breakdown.by_year.forEach(year => {
                                detailContent += `
                                    <tr>
                                        <td>พ.ศ. ${year.year}</td>
                                        <td>${Number(year.total_budget).toLocaleString('th-TH')} บาท</td>
                                        <td>${year.project_count} โครงการ</td>
                                    </tr>
                                `;
                            });
                            detailContent += '</tbody></table></div>';
                        }
                    } else {
                        detailContent += '<p class="text-muted">ไม่มีข้อมูลรายละเอียด</p>';
                    }
                    detailContent += '</div>';
                    break;

                case 'indicators':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>รายการตัวชี้วัดทั้งหมด</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="indicatorRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="20">20 รายการ</option>
                                        <option value="50" selected>50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="indicatorContainer">
                    `;
                    
                    // Function to render indicators based on limit
                    const renderIndicators = (limit = 50) => {
                        let indicatorsToShow = data || [];
                        if (limit > 0) {
                            indicatorsToShow = indicatorsToShow.slice(0, limit);
                        }
                        
                        let indicatorContent = '';
                        if (indicatorsToShow && indicatorsToShow.length > 0) {
                            indicatorContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-warning"><tr><th style="width: 40px;">#</th><th>ชื่อตัวชี้วัด</th><th>เป้าหมาย</th><th>หน่วย</th><th>โครงการ</th><th>ปี</th></tr></thead><tbody>';
                            
                            indicatorsToShow.forEach((indicator, index) => {
                                indicatorContent += `
                                    <tr>
                                        <td style="width: 10px;"><span class="badge bg-warning text-dark" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td style="min-width: 200px;"><strong>${indicator.IndicatorName}</strong></td>
                                        <td class="text-center">${indicator.TargetValue || '-'}</td>
                                        <td class="text-center">${indicator.Unit || '-'}</td>
                                        <td style="min-width: 180px;"><small class="text-primary">${indicator.ProjectName}</small></td>
                                        <td class="text-center">พ.ศ. ${indicator.ProjectYear || '-'}</td>
                                    </tr>
                                `;
                            });
                            
                            indicatorContent += '</tbody></table></div>';
                        } else {
                            indicatorContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลตัวชี้วัด</p></div>';
                        }
                        return indicatorContent;
                    };
                    
                    detailContent += renderIndicators(50); // Initial render with 50 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="indicatorShowingCount">50</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} ตัวชี้วัด</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('indicatorRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('indicatorContainer');
                                const showingCount = document.getElementById('indicatorShowingCount');
                                
                                if (container && showingCount) {
                                    let indicatorsToShow = data || [];
                                    if (limit > 0) {
                                        indicatorsToShow = indicatorsToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (indicatorsToShow && indicatorsToShow.length > 0) {
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-warning"><tr><th style="width: 40px;">#</th><th>ชื่อตัวชี้วัด</th><th>เป้าหมาย</th><th>หน่วย</th><th>โครงการ</th><th>ปี</th></tr></thead><tbody>';
                                        
                                        indicatorsToShow.forEach((indicator, index) => {
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-warning text-dark" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td style="min-width: 200px;"><strong>${indicator.IndicatorName}</strong></td>
                                                    <td class="text-center">${indicator.TargetValue || '-'}</td>
                                                    <td class="text-center">${indicator.Unit || '-'}</td>
                                                    <td style="min-width: 180px;"><small class="text-primary">${indicator.ProjectName}</small></td>
                                                    <td class="text-center">พ.ศ. ${indicator.ProjectYear || '-'}</td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += '</tbody></table></div>';
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลตัวชี้วัด</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'locations':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>รายการพื้นที่ดำเนินการทั้งหมด</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="locationRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="50" selected>50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="200">200 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="locationContainer">
                    `;
                    
                    // Function to render locations based on limit
                    const renderLocations = (limit = 50) => {
                        let locationsToShow = data || [];
                        if (limit > 0) {
                            locationsToShow = locationsToShow.slice(0, limit);
                        }
                        
                        let locationContent = '';
                        if (locationsToShow && locationsToShow.length > 0) {
                            locationContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-danger"><tr><th style="width: 40px;">#</th><th>จังหวัด</th><th>อำเภอ</th><th>ตำบล</th><th>หมู่บ้าน/ชุมชน</th><th>จำนวนโครงการ</th></tr></thead><tbody>';
                            
                            locationsToShow.forEach((location, index) => {
                                locationContent += `
                                    <tr>
                                        <td style="width: 40px;"><span class="badge bg-danger" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td><strong>${location.Province || '-'}</strong></td>
                                        <td>${location.District || '-'}</td>
                                        <td>${location.Subdistrict || '-'}</td>
                                        <td style="min-width: 200px;">${location.VillageName || '-'}</td>
                                        <td class="text-center"><span class="badge bg-primary">${location.project_count || 1} โครงการ</span></td>
                                    </tr>
                                `;
                            });
                            
                            locationContent += '</tbody></table></div>';
                        } else {
                            locationContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลพื้นที่ดำเนินการ</p></div>';
                        }
                        return locationContent;
                    };
                    
                    detailContent += renderLocations(50); // Initial render with 50 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="locationShowingCount">50</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} พื้นที่</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('locationRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('locationContainer');
                                const showingCount = document.getElementById('locationShowingCount');
                                
                                if (container && showingCount) {
                                    let locationsToShow = data || [];
                                    if (limit > 0) {
                                        locationsToShow = locationsToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (locationsToShow && locationsToShow.length > 0) {
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-danger"><tr><th style="width: 40px;">#</th><th>จังหวัด</th><th>อำเภอ</th><th>ตำบล</th><th>หมู่บ้าน/ชุมชน</th><th>จำนวนโครงการ</th></tr></thead><tbody>';
                                        
                                        locationsToShow.forEach((location, index) => {
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-danger" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td><strong>${location.Province || '-'}</strong></td>
                                                    <td>${location.District || '-'}</td>
                                                    <td>${location.Subdistrict || '-'}</td>
                                                    <td style="min-width: 200px;">${location.VillageName || '-'}</td>
                                                    <td class="text-center"><span class="badge bg-primary">${location.project_count || 1} โครงการ</span></td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += '</tbody></table></div>';
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลพื้นที่ดำเนินการ</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'target_people':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>รายการกลุ่มเป้าหมายทั้งหมด (รวมข้อมูล)</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="targetPeopleRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="15" selected>15 รายการ</option>
                                        <option value="25">25 รายการ</option>
                                        <option value="50">50 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="targetPeopleContainer">
                    `;
                    
                    // Function to render target people based on limit
                    const renderTargetPeople = (limit = 15) => {
                        let peopleToShow = data || [];
                        if (limit > 0) {
                            peopleToShow = peopleToShow.slice(0, limit);
                        }
                        
                        let peopleContent = '';
                        if (peopleToShow && peopleToShow.length > 0) {
                            let totalPeople = 0;
                            peopleContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-purple"><tr><th style="width: 40px;">#</th><th>ชื่อกลุ่มเป้าหมาย</th><th>จำนวน (คน)</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการที่เกี่ยวข้อง</th></tr></thead><tbody>';
                            
                            peopleToShow.forEach((group, index) => {
                                totalPeople += parseInt(group.TargetCount) || 0;
                                const projectNames = group.project_names ? group.project_names.split(', ') : [];
                                const projectDisplay = projectNames.length > 3 
                                    ? projectNames.slice(0, 3).join(', ') + `... และอีก ${projectNames.length - 3} โครงการ`
                                    : projectNames.join(', ');
                                
                                peopleContent += `
                                    <tr>
                                        <td style="width: 40px;"><span class="badge bg-purple text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td style="min-width: 200px;"><strong>${group.GroupName}</strong></td>
                                        <td class="text-center"><span class="badge bg-primary" style="font-size: 0.9rem;">${Number(group.TargetCount || 0).toLocaleString('th-TH')} คน</span></td>
                                        <td class="text-center"><span class="badge bg-success">${group.project_count || 0} โครงการ</span></td>
                                        <td style="min-width: 300px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                    </tr>
                                `;
                            });
                            
                            peopleContent += `</tbody></table></div>
                                <div class="alert alert-info mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-users me-2"></i>รวมทั้งหมด: ${totalPeople.toLocaleString('th-TH')} คน</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-layer-group me-2"></i>จำนวนกลุ่ม: ${peopleToShow.length} กลุ่ม</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-project-diagram me-2"></i>โครงการเกี่ยวข้อง: ${peopleToShow.reduce((sum, group) => sum + (parseInt(group.project_count) || 0), 0)} โครงการ</strong>
                                        </div>
                                    </div>
                                </div>`;
                        } else {
                            peopleContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลกลุ่มเป้าหมาย</p></div>';
                        }
                        return peopleContent;
                    };
                    
                    detailContent += renderTargetPeople(15); // Initial render with 15 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="targetPeopleShowingCount">15</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} กลุ่มเป้าหมาย</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('targetPeopleRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('targetPeopleContainer');
                                const showingCount = document.getElementById('targetPeopleShowingCount');
                                
                                if (container && showingCount) {
                                    let peopleToShow = data || [];
                                    if (limit > 0) {
                                        peopleToShow = peopleToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (peopleToShow && peopleToShow.length > 0) {
                                        let totalPeople = 0;
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-purple"><tr><th style="width: 40px;">#</th><th>ชื่อกลุ่มเป้าหมาย</th><th>จำนวน (คน)</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการที่เกี่ยวข้อง</th></tr></thead><tbody>';
                                        
                                        peopleToShow.forEach((group, index) => {
                                            totalPeople += parseInt(group.TargetCount) || 0;
                                            const projectNames = group.project_names ? group.project_names.split(', ') : [];
                                            const projectDisplay = projectNames.length > 3 
                                                ? projectNames.slice(0, 3).join(', ') + `... และอีก ${projectNames.length - 3} โครงการ`
                                                : projectNames.join(', ');
                                            
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-purple text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td style="min-width: 200px;"><strong>${group.GroupName}</strong></td>
                                                    <td class="text-center"><span class="badge bg-primary" style="font-size: 0.9rem;">${Number(group.TargetCount || 0).toLocaleString('th-TH')} คน</span></td>
                                                    <td class="text-center"><span class="badge bg-success">${group.project_count || 0} โครงการ</span></td>
                                                    <td style="min-width: 300px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += `</tbody></table></div>
                                            <div class="alert alert-info mt-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-users me-2"></i>รวมทั้งหมด: ${totalPeople.toLocaleString('th-TH')} คน</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-layer-group me-2"></i>จำนวนกลุ่ม: ${peopleToShow.length} กลุ่ม</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-project-diagram me-2"></i>โครงการเกี่ยวข้อง: ${peopleToShow.reduce((sum, group) => sum + (parseInt(group.project_count) || 0), 0)} โครงการ</strong>
                                                    </div>
                                                </div>
                                            </div>`;
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลกลุ่มเป้าหมาย</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'products':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-box me-2"></i>รายการผลิตภัณฑ์ทั้งหมด</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="productRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="30" selected>30 รายการ</option>
                                        <option value="50">50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="productContainer">
                    `;
                    
                    // Function to render products based on limit
                    const renderProducts = (limit = 30) => {
                        let productsToShow = data || [];
                        if (limit > 0) {
                            productsToShow = productsToShow.slice(0, limit);
                        }
                        
                        let productContent = '';
                        if (productsToShow && productsToShow.length > 0) {
                            productContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-success"><tr><th style="width: 40px;">#</th><th>ชื่อผลิตภัณฑ์</th><th>ประเภท</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการ</th><th>ปีที่ดำเนินการ</th></tr></thead><tbody>';
                            
                            productsToShow.forEach((product, index) => {
                                const projectNames = product.project_names ? product.project_names.split(', ') : [];
                                const projectDisplay = projectNames.length > 2 
                                    ? projectNames.slice(0, 2).join(', ') + `... และอีก ${projectNames.length - 2} โครงการ`
                                    : projectNames.join(', ');
                                
                                productContent += `
                                    <tr>
                                        <td style="width: 40px;"><span class="badge bg-success text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td style="min-width: 200px;"><strong>${product.ProductName}</strong></td>
                                        <td class="text-center"><span class="badge bg-warning text-dark">${product.ProductType || 'ไม่ระบุ'}</span></td>
                                        <td class="text-center"><span class="badge bg-primary">${product.project_count || 0} โครงการ</span></td>
                                        <td style="min-width: 250px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                        <td class="text-center"><small class="text-info">${product.project_years || '-'}</small></td>
                                    </tr>
                                `;
                            });
                            
                            productContent += '</tbody></table></div>';
                            
                            // สรุปยอดรวม
                            const totalProducts = productsToShow.length;
                            const totalProjects = productsToShow.reduce((sum, product) => sum + (parseInt(product.project_count) || 0), 0);
                            const productTypes = [...new Set(productsToShow.map(p => p.ProductType).filter(type => type))];
                            
                            productContent += `
                                <div class="alert alert-success mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-box me-2"></i>จำนวนผลิตภัณฑ์: ${totalProducts} รายการ</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-layer-group me-2"></i>จำนวนประเภท: ${productTypes.length} ประเภท</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-project-diagram me-2"></i>ที่เกี่ยวข้อง: ${totalProjects} โครงการ</strong>
                                        </div>
                                    </div>
                                </div>`;
                        } else {
                            productContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลผลิตภัณฑ์</p></div>';
                        }
                        return productContent;
                    };
                    
                    detailContent += renderProducts(30); // Initial render with 30 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="productShowingCount">30</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} ผลิตภัณฑ์</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('productRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('productContainer');
                                const showingCount = document.getElementById('productShowingCount');
                                
                                if (container && showingCount) {
                                    let productsToShow = data || [];
                                    if (limit > 0) {
                                        productsToShow = productsToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (productsToShow && productsToShow.length > 0) {
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-success"><tr><th style="width: 40px;">#</th><th>ชื่อผลิตภัณฑ์</th><th>ประเภท</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการ</th><th>ปีที่ดำเนินการ</th></tr></thead><tbody>';
                                        
                                        productsToShow.forEach((product, index) => {
                                            const projectNames = product.project_names ? product.project_names.split(', ') : [];
                                            const projectDisplay = projectNames.length > 2 
                                                ? projectNames.slice(0, 2).join(', ') + `... และอีก ${projectNames.length - 2} โครงการ`
                                                : projectNames.join(', ');
                                            
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-success text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td style="min-width: 200px;"><strong>${product.ProductName}</strong></td>
                                                    <td class="text-center"><span class="badge bg-warning text-dark">${product.ProductType || 'ไม่ระบุ'}</span></td>
                                                    <td class="text-center"><span class="badge bg-primary">${product.project_count || 0} โครงการ</span></td>
                                                    <td style="min-width: 250px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                                    <td class="text-center"><small class="text-info">${product.project_years || '-'}</small></td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += '</tbody></table></div>';
                                        
                                        // สรุปยอดรวม
                                        const totalProducts = productsToShow.length;
                                        const totalProjects = productsToShow.reduce((sum, product) => sum + (parseInt(product.project_count) || 0), 0);
                                        const productTypes = [...new Set(productsToShow.map(p => p.ProductType).filter(type => type))];
                                        
                                        newContent += `
                                            <div class="alert alert-success mt-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-box me-2"></i>จำนวนผลิตภัณฑ์: ${totalProducts} รายการ</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-layer-group me-2"></i>จำนวนประเภท: ${productTypes.length} ประเภท</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-project-diagram me-2"></i>ที่เกี่ยวข้อง: ${totalProjects} โครงการ</strong>
                                                    </div>
                                                </div>
                                            </div>`;
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลผลิตภัณฑ์</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'schools':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-school me-2"></i>รายการโรงเรียนที่เข้าร่วมทั้งหมด</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="schoolRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="25" selected>25 รายการ</option>
                                        <option value="50">50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="schoolContainer">
                    `;
                    
                    // Function to render schools based on limit
                    const renderSchools = (limit = 25) => {
                        let schoolsToShow = data || [];
                        if (limit > 0) {
                            schoolsToShow = schoolsToShow.slice(0, limit);
                        }
                        
                        let schoolContent = '';
                        if (schoolsToShow && schoolsToShow.length > 0) {
                            schoolContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-warning"><tr><th style="width: 40px;">#</th><th>ชื่อโรงเรียน</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการที่เข้าร่วม</th><th>ปีที่เข้าร่วม</th></tr></thead><tbody>';
                            
                            schoolsToShow.forEach((school, index) => {
                                const projectNames = school.project_names ? school.project_names.split(', ') : [];
                                const projectDisplay = projectNames.length > 3 
                                    ? projectNames.slice(0, 3).join(', ') + `... และอีก ${projectNames.length - 3} โครงการ`
                                    : projectNames.join(', ');
                                
                                schoolContent += `
                                    <tr>
                                        <td style="width: 40px;"><span class="badge bg-warning text-dark" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td style="min-width: 200px;"><strong><i class="fas fa-school me-2"></i>${school.SchoolName}</strong></td>
                                        <td class="text-center"><span class="badge bg-primary">${school.project_count || 0} โครงการ</span></td>
                                        <td style="min-width: 300px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                        <td class="text-center"><small class="text-info">${school.project_years || '-'}</small></td>
                                    </tr>
                                `;
                            });
                            
                            schoolContent += '</tbody></table></div>';
                            
                            // สรุปยอดรวม
                            const totalSchools = schoolsToShow.length;
                            const totalProjects = schoolsToShow.reduce((sum, school) => sum + (parseInt(school.project_count) || 0), 0);
                            
                            schoolContent += `
                                <div class="alert alert-warning mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-school me-2"></i>จำนวนโรงเรียน: ${totalSchools} แห่ง</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-project-diagram me-2"></i>ที่เกี่ยวข้อง: ${totalProjects} โครงการ</strong>
                                        </div>
                                    </div>
                                </div>`;
                        } else {
                            schoolContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลโรงเรียน</p></div>';
                        }
                        return schoolContent;
                    };
                    
                    detailContent += renderSchools(25); // Initial render with 25 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="schoolShowingCount">25</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} โรงเรียน</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('schoolRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('schoolContainer');
                                const showingCount = document.getElementById('schoolShowingCount');
                                
                                if (container && showingCount) {
                                    let schoolsToShow = data || [];
                                    if (limit > 0) {
                                        schoolsToShow = schoolsToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (schoolsToShow && schoolsToShow.length > 0) {
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-warning"><tr><th style="width: 40px;">#</th><th>ชื่อโรงเรียน</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการที่เข้าร่วม</th><th>ปีที่เข้าร่วม</th></tr></thead><tbody>';
                                        
                                        schoolsToShow.forEach((school, index) => {
                                            const projectNames = school.project_names ? school.project_names.split(', ') : [];
                                            const projectDisplay = projectNames.length > 3 
                                                ? projectNames.slice(0, 3).join(', ') + `... และอีก ${projectNames.length - 3} โครงการ`
                                                : projectNames.join(', ');
                                            
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-warning text-dark" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td style="min-width: 200px;"><strong><i class="fas fa-school me-2"></i>${school.SchoolName}</strong></td>
                                                    <td class="text-center"><span class="badge bg-primary">${school.project_count || 0} โครงการ</span></td>
                                                    <td style="min-width: 300px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                                    <td class="text-center"><small class="text-info">${school.project_years || '-'}</small></td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += '</tbody></table></div>';
                                        
                                        // สรุปยอดรวม
                                        const totalSchools = schoolsToShow.length;
                                        const totalProjects = schoolsToShow.reduce((sum, school) => sum + (parseInt(school.project_count) || 0), 0);
                                        
                                        newContent += `
                                            <div class="alert alert-warning mt-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-school me-2"></i>จำนวนโรงเรียน: ${totalSchools} แห่ง</strong>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-project-diagram me-2"></i>ที่เกี่ยวข้อง: ${totalProjects} โครงการ</strong>
                                                    </div>
                                                </div>
                                            </div>`;
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลโรงเรียน</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'agencies':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-building me-2"></i>รายการหน่วยงานที่เข้าร่วมทั้งหมด</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="agencyRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="25" selected>25 รายการ</option>
                                        <option value="50">50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="agencyContainer">
                    `;
                    
                    // Function to render agencies based on limit
                    const renderAgencies = (limit = 25) => {
                        let agenciesToShow = data || [];
                        if (limit > 0) {
                            agenciesToShow = agenciesToShow.slice(0, limit);
                        }
                        
                        let agencyContent = '';
                        if (agenciesToShow && agenciesToShow.length > 0) {
                            agencyContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-info"><tr><th style="width: 40px;">#</th><th>ชื่อหน่วยงาน</th><th>จำนวนโครงการ</th><th>งบประมาณรวม</th><th>รายชื่อโครงการ</th><th>ปีที่เข้าร่วม</th></tr></thead><tbody>';
                            
                            agenciesToShow.forEach((agency, index) => {
                                const projectNames = agency.project_names ? agency.project_names.split(', ') : [];
                                const projectDisplay = projectNames.length > 2 
                                    ? projectNames.slice(0, 2).join(', ') + `... และอีก ${projectNames.length - 2} โครงการ`
                                    : projectNames.join(', ');
                                
                                agencyContent += `
                                    <tr>
                                        <td style="width: 40px;"><span class="badge bg-info text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td style="min-width: 200px;"><strong><i class="fas fa-building me-2"></i>${agency.AgencyName}</strong></td>
                                        <td class="text-center"><span class="badge bg-primary">${agency.project_count || 0} โครงการ</span></td>
                                        <td class="text-center"><span class="badge bg-success">${agency.total_budget ? Number(agency.total_budget).toLocaleString('th-TH') + ' บาท' : '-'}</span></td>
                                        <td style="min-width: 250px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                        <td class="text-center"><small class="text-info">${agency.project_years || '-'}</small></td>
                                    </tr>
                                `;
                            });
                            
                            agencyContent += '</tbody></table></div>';
                            
                            // สรุปยอดรวม
                            const totalAgencies = agenciesToShow.length;
                            const totalProjects = agenciesToShow.reduce((sum, agency) => sum + (parseInt(agency.project_count) || 0), 0);
                            const totalBudget = agenciesToShow.reduce((sum, agency) => sum + (parseFloat(agency.total_budget) || 0), 0);
                            
                            agencyContent += `
                                <div class="alert alert-info mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-building me-2"></i>จำนวนหน่วยงาน: ${totalAgencies} หน่วยงาน</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-project-diagram me-2"></i>โครงการทั้งหมด: ${totalProjects} โครงการ</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-coins me-2"></i>งบประมาณรวม: ${totalBudget.toLocaleString('th-TH')} บาท</strong>
                                        </div>
                                    </div>
                                </div>`;
                        } else {
                            agencyContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลหน่วยงาน</p></div>';
                        }
                        return agencyContent;
                    };
                    
                    detailContent += renderAgencies(25); // Initial render with 25 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="agencyShowingCount">25</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} หน่วยงาน</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('agencyRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('agencyContainer');
                                const showingCount = document.getElementById('agencyShowingCount');
                                
                                if (container && showingCount) {
                                    let agenciesToShow = data || [];
                                    if (limit > 0) {
                                        agenciesToShow = agenciesToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (agenciesToShow && agenciesToShow.length > 0) {
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-info"><tr><th style="width: 40px;">#</th><th>ชื่อหน่วยงาน</th><th>จำนวนโครงการ</th><th>งบประมาณรวม</th><th>รายชื่อโครงการ</th><th>ปีที่เข้าร่วม</th></tr></thead><tbody>';
                                        
                                        agenciesToShow.forEach((agency, index) => {
                                            const projectNames = agency.project_names ? agency.project_names.split(', ') : [];
                                            const projectDisplay = projectNames.length > 2 
                                                ? projectNames.slice(0, 2).join(', ') + `... และอีก ${projectNames.length - 2} โครงการ`
                                                : projectNames.join(', ');
                                            
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-info text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td style="min-width: 200px;"><strong><i class="fas fa-building me-2"></i>${agency.AgencyName}</strong></td>
                                                    <td class="text-center"><span class="badge bg-primary">${agency.project_count || 0} โครงการ</span></td>
                                                    <td class="text-center"><span class="badge bg-success">${agency.total_budget ? Number(agency.total_budget).toLocaleString('th-TH') + ' บาท' : '-'}</span></td>
                                                    <td style="min-width: 250px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                                    <td class="text-center"><small class="text-info">${agency.project_years || '-'}</small></td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += '</tbody></table></div>';
                                        
                                        // สรุปยอดรวม
                                        const totalAgencies = agenciesToShow.length;
                                        const totalProjects = agenciesToShow.reduce((sum, agency) => sum + (parseInt(agency.project_count) || 0), 0);
                                        const totalBudget = agenciesToShow.reduce((sum, agency) => sum + (parseFloat(agency.total_budget) || 0), 0);
                                        
                                        newContent += `
                                            <div class="alert alert-info mt-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-building me-2"></i>จำนวนหน่วยงาน: ${totalAgencies} หน่วยงาน</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-project-diagram me-2"></i>โครงการทั้งหมด: ${totalProjects} โครงการ</strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong><i class="fas fa-coins me-2"></i>งบประมาณรวม: ${totalBudget.toLocaleString('th-TH')} บาท</strong>
                                                    </div>
                                                </div>
                                            </div>`;
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลหน่วยงาน</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                case 'target_groups':
                    detailContent = `
                        <div style="text-align: left; max-height: 75vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>รายการกลุ่มเป้าหมายทั้งหมด (รวมข้อมูล)</h6>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 mb-0">แสดง:</label>
                                    <select id="targetGroupRowLimit" class="form-select form-select-sm" style="width: auto;">
                                        <option value="20" selected>20 รายการ</option>
                                        <option value="50">50 รายการ</option>
                                        <option value="100">100 รายการ</option>
                                        <option value="0">ทั้งหมด</option>
                                    </select>
                                </div>
                            </div>
                            <div id="targetGroupContainer">
                    `;
                    
                    // Function to render target groups based on limit
                    const renderTargetGroups = (limit = 20) => {
                        let groupsToShow = data || [];
                        if (limit > 0) {
                            groupsToShow = groupsToShow.slice(0, limit);
                        }
                        
                        let groupContent = '';
                        if (groupsToShow && groupsToShow.length > 0) {
                            let totalPeople = 0;
                            groupContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-purple"><tr><th style="width: 40px;">#</th><th>ชื่อกลุ่มเป้าหมาย</th><th>จำนวน (คน)</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการ</th></tr></thead><tbody>';
                            
                            groupsToShow.forEach((group, index) => {
                                totalPeople += parseInt(group.total_count) || 0;
                                const projectNames = group.project_names ? group.project_names.split(', ') : [];
                                const projectDisplay = projectNames.length > 3 
                                    ? projectNames.slice(0, 3).join(', ') + `... และอีก ${projectNames.length - 3} โครงการ`
                                    : projectNames.join(', ');
                                
                                groupContent += `
                                    <tr>
                                        <td style="width: 40px;"><span class="badge bg-purple text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                        <td style="min-width: 200px;"><strong>${group.GroupName}</strong></td>
                                        <td class="text-center"><span class="badge bg-primary">${Number(group.total_count || 0).toLocaleString('th-TH')} คน</span></td>
                                        <td class="text-center"><span class="badge bg-success">${group.project_count || 0} โครงการ</span></td>
                                        <td style="min-width: 300px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                    </tr>
                                `;
                            });
                            
                            groupContent += `</tbody></table></div>
                                <div class="alert alert-info mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-users me-2"></i>รวมทั้งหมด: ${totalPeople.toLocaleString('th-TH')} คน</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-layer-group me-2"></i>จำนวนกลุ่ม: ${groupsToShow.length} กลุ่ม</strong>
                                        </div>
                                    </div>
                                </div>`;
                        } else {
                            groupContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลกลุ่มเป้าหมาย</p></div>';
                        }
                        return groupContent;
                    };
                    
                    detailContent += renderTargetGroups(20); // Initial render with 20 items
                    
                    detailContent += `
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">แสดง <span id="targetGroupShowingCount">20</span> จาก ${data ? data.length.toLocaleString('th-TH') : 0} กลุ่มเป้าหมาย</small>
                            </div>
                        </div>
                    `;
                    
                    // Add event listener for dropdown change after modal is displayed
                    setTimeout(() => {
                        const limitSelect = document.getElementById('targetGroupRowLimit');
                        if (limitSelect) {
                            limitSelect.addEventListener('change', function() {
                                const limit = parseInt(this.value);
                                const container = document.getElementById('targetGroupContainer');
                                const showingCount = document.getElementById('targetGroupShowingCount');
                                
                                if (container && showingCount) {
                                    let groupsToShow = data || [];
                                    if (limit > 0) {
                                        groupsToShow = groupsToShow.slice(0, limit);
                                    }
                                    
                                    let newContent = '';
                                    if (groupsToShow && groupsToShow.length > 0) {
                                        let totalPeople = 0;
                                        newContent += '<div class="table-responsive"><table class="table table-sm table-striped"><thead class="table-purple"><tr><th style="width: 40px;">#</th><th>ชื่อกลุ่มเป้าหมาย</th><th>จำนวน (คน)</th><th>จำนวนโครงการ</th><th>รายชื่อโครงการ</th></tr></thead><tbody>';
                                        
                                        groupsToShow.forEach((group, index) => {
                                            totalPeople += parseInt(group.total_count) || 0;
                                            const projectNames = group.project_names ? group.project_names.split(', ') : [];
                                            const projectDisplay = projectNames.length > 3 
                                                ? projectNames.slice(0, 3).join(', ') + `... และอีก ${projectNames.length - 3} โครงการ`
                                                : projectNames.join(', ');
                                            
                                            newContent += `
                                                <tr>
                                                    <td style="width: 40px;"><span class="badge bg-purple text-white" style="font-size: 0.7rem;">${index + 1}</span></td>
                                                    <td style="min-width: 200px;"><strong>${group.GroupName}</strong></td>
                                                    <td class="text-center"><span class="badge bg-primary">${Number(group.total_count || 0).toLocaleString('th-TH')} คน</span></td>
                                                    <td class="text-center"><span class="badge bg-success">${group.project_count || 0} โครงการ</span></td>
                                                    <td style="min-width: 300px;"><small class="text-muted">${projectDisplay || '-'}</small></td>
                                                </tr>
                                            `;
                                        });
                                        
                                        newContent += `</tbody></table></div>
                                            <div class="alert alert-info mt-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-users me-2"></i>รวมทั้งหมด: ${totalPeople.toLocaleString('th-TH')} คน</strong>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-layer-group me-2"></i>จำนวนกลุ่ม: ${groupsToShow.length} กลุ่ม</strong>
                                                    </div>
                                                </div>
                                            </div>`;
                                    } else {
                                        newContent = '<div class="text-center py-4"><p class="text-muted">ไม่มีข้อมูลกลุ่มเป้าหมาย</p></div>';
                                    }
                                    
                                    container.innerHTML = newContent;
                                    showingCount.textContent = limit === 0 ? (data ? data.length.toLocaleString('th-TH') : 0) : Math.min(limit, data ? data.length : 0).toLocaleString('th-TH');
                                }
                            });
                        }
                    }, 100);
                    break;

                default:
                    detailContent = `
                        <div style="text-align: center; padding: 20px;">
                            <div style="font-size: 3rem; font-weight: bold; color: ${bgColor}; margin-bottom: 20px;">
                                ${cardValue}
                            </div>
                            <div style="color: #666; margin-bottom: 30px;">
                                ${cardText}<br>
                                <small>ข้อมูลตามเงื่อนไขการกรองที่เลือกไว้</small>
                            </div>
                        </div>
                    `;
            }

            Swal.fire({
                title: `<i class="${cardIcon}" style="color: ${bgColor}"></i> ${cardText}`,
                html: detailContent,
                showConfirmButton: true,
                confirmButtonText: '<i class="fas fa-check me-2"></i>ปิด',
                confirmButtonColor: bgColor,
                backdrop: true,
                allowOutsideClick: true,
                heightAuto: false,
                grow: 'fullscreen',
                customClass: {
                    popup: 'stats-modal-fullscreen',
                    title: 'stats-modal-title',
                    htmlContainer: 'stats-modal-content-fullscreen'
                }
            });
        }

        function displayBasicStatsModal(cardType, cardText, cardValue, cardIcon, bgColor) {
            // Fallback modal แสดงข้อมูลพื้นฐาน
            Swal.fire({
                title: `<i class="${cardIcon}" style="color: ${bgColor}"></i> ${cardText}`,
                html: `
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 3rem; font-weight: bold; color: ${bgColor}; margin-bottom: 20px;">
                            ${cardValue}
                        </div>
                        <div style="color: #666; margin-bottom: 30px;">
                            ${cardText}<br>
                            <small>ข้อมูลตามเงื่อนไขการกรองที่เลือกไว้</small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            ไม่สามารถโหลดข้อมูลรายละเอียดได้ในขณะนี้
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: '<i class="fas fa-check me-2"></i>ปิด',
                confirmButtonColor: bgColor,
                backdrop: true,
                allowOutsideClick: true,
                heightAuto: false,
                grow: 'fullscreen',
                customClass: {
                    popup: 'stats-modal-fullscreen',
                    title: 'stats-modal-title',
                    htmlContainer: 'stats-modal-content-fullscreen'
                }
            });
        }

        function loadStatistics() {
            $.ajax({
                url: 'projects_table_stats.php',
                type: 'POST',
                data: {
                    project_year_start: $('#projectYearStartFilter').val(),
                    project_year_end: $('#projectYearEndFilter').val(),
                    province: $('#provinceFilter').val(),
                    district: $('#districtFilter').val(),
                    subdistrict: $('#subdistrictFilter').val(),
                    village: $('#villageFilter').val(),
                    main_project: $('#mainProjectFilter').val(),
                    strategy: $('#strategyFilter').val(),
                    agency: $('#agencyFilter').val(),
                    target_group: $('#targetGroupFilter').val(),
                    teacher: $('#teacherFilter').val()
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
