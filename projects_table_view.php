<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายงานโครงการแบบตาราง - ระบบจัดการโครงการ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <!-- Header -->
        <div class="table-header">
            <h2 class="fw-bold mb-3">
                <i class="fas fa-table me-3"></i>รายงานโครงการแบบตาราง
            </h2>
            <p class="mb-0">ข้อมูลโครงการทั้งหมดพร้อมรายละเอียดครบถ้วน แสดงผลในรูปแบบตารางที่ใช้งานง่าย</p>
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
                        <label class="form-label">ปีโครงการ</label>
                        <select class="form-select" id="projectYearFilter" name="project_year">
                            <option value="">ทุกปี</option>
                            <?php
                            $years = $conn->query("SELECT DISTINCT ProjectYear FROM projects WHERE ProjectYear IS NOT NULL ORDER BY ProjectYear DESC");
                            while ($year = $years->fetch_assoc()) {
                                $selected = (isset($_GET['project_year']) && $_GET['project_year'] == $year['ProjectYear']) ? 'selected' : '';
                                echo "<option value='{$year['ProjectYear']}' $selected>พ.ศ. {$year['ProjectYear']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">ตำบล</label>
                        <select class="form-select" id="subdistrictFilter" name="subdistrict">
                            <option value="">ทุกตำบล</option>
                            <?php
                            $subdistricts = $conn->query("SELECT DISTINCT Subdistrict FROM projectvillages WHERE Subdistrict IS NOT NULL ORDER BY Subdistrict");
                            while ($subdistrict = $subdistricts->fetch_assoc()) {
                                $selected = (isset($_GET['subdistrict']) && $_GET['subdistrict'] == $subdistrict['Subdistrict']) ? 'selected' : '';
                                echo "<option value='{$subdistrict['Subdistrict']}' $selected>{$subdistrict['Subdistrict']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">อำเภอ</label>
                        <select class="form-select" id="districtFilter" name="district">
                            <option value="">ทุกอำเภอ</option>
                            <?php
                            $districts = $conn->query("SELECT DISTINCT District FROM projectvillages WHERE District IS NOT NULL ORDER BY District");
                            while ($district = $districts->fetch_assoc()) {
                                $selected = (isset($_GET['district']) && $_GET['district'] == $district['District']) ? 'selected' : '';
                                echo "<option value='{$district['District']}' $selected>{$district['District']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">จังหวัด</label>
                        <select class="form-select" id="provinceFilter" name="province">
                            <option value="">ทุกจังหวัด</option>
                            <?php
                            $provinces = $conn->query("SELECT DISTINCT Province FROM projectvillages WHERE Province IS NOT NULL ORDER BY Province");
                            while ($province = $provinces->fetch_assoc()) {
                                $selected = (isset($_GET['province']) && $_GET['province'] == $province['Province']) ? 'selected' : '';
                                echo "<option value='{$province['Province']}' $selected>{$province['Province']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Secondary Filters -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">โครงการหลัก</label>
                        <select class="form-select" id="mainProjectFilter" name="main_project">
                            <option value="">ทุกโครงการหลัก</option>
                            <?php
                            $mainProjects = $conn->query("SELECT MainProjectID, MainProjectName FROM mainprojects ORDER BY MainProjectName");
                            while ($mp = $mainProjects->fetch_assoc()) {
                                $selected = (isset($_GET['main_project']) && $_GET['main_project'] == $mp['MainProjectID']) ? 'selected' : '';
                                echo "<option value='{$mp['MainProjectID']}' $selected>{$mp['MainProjectName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">ยุทธศาสตร์</label>
                        <select class="form-select" id="strategyFilter" name="strategy">
                            <option value="">ทุกยุทธศาสตร์</option>
                            <?php
                            $strategies = $conn->query("SELECT StrategyID, StrategyName FROM strategies ORDER BY StrategyName");
                            while ($strategy = $strategies->fetch_assoc()) {
                                $selected = (isset($_GET['strategy']) && $_GET['strategy'] == $strategy['StrategyID']) ? 'selected' : '';
                                echo "<option value='{$strategy['StrategyID']}' $selected>{$strategy['StrategyName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">หน่วยงาน</label>
                        <select class="form-select" id="agencyFilter" name="agency">
                            <option value="">ทุกหน่วยงาน</option>
                            <?php
                            $agencies = $conn->query("SELECT DISTINCT AgencyName FROM projects WHERE AgencyName IS NOT NULL ORDER BY AgencyName");
                            while ($agency = $agencies->fetch_assoc()) {
                                $selected = (isset($_GET['agency']) && $_GET['agency'] == $agency['AgencyName']) ? 'selected' : '';
                                echo "<option value='{$agency['AgencyName']}' $selected>{$agency['AgencyName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label">กลุ่มเป้าหมาย</label>
                        <select class="form-select" id="targetGroupFilter" name="target_group">
                            <option value="">ทุกกลุ่มเป้าหมาย</option>
                            <?php
                            $targetGroups = $conn->query("SELECT GroupID, GroupName FROM targetgroups ORDER BY GroupName");
                            while ($tg = $targetGroups->fetch_assoc()) {
                                $selected = (isset($_GET['target_group']) && $_GET['target_group'] == $tg['GroupID']) ? 'selected' : '';
                                echo "<option value='{$tg['GroupID']}' $selected>{$tg['GroupName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                <i class="fas fa-search me-2"></i>ค้นหา
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>ล้างตัวกรอง
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportData()">
                                <i class="fas fa-download me-2"></i>ส่งออกข้อมูล
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4" id="statsCards">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="totalProjects">-</h3>
                    <p class="mb-0">โครงการทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="totalBudget">-</h3>
                    <p class="mb-0">งบประมาณรวม (ล้านบาท)</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="totalIndicators">-</h3>
                    <p class="mb-0">ตัวชี้วัดทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="totalLocations">-</h3>
                    <p class="mb-0">พื้นที่ดำเนินการ</p>
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
                                <th>รหัสโครงการ</th>
                                <th>ชื่อโครงการ</th>
                                <th>ปีโครงการ</th>
                                <th>โครงการหลัก</th>
                                <th>ยุทธศาสตร์</th>
                                <th>หน่วยงาน</th>
                                <th>จังหวัด</th>
                                <th>อำเภอ</th>
                                <th>ตำบล</th>
                                <th>งบประมาณอนุมัติ</th>
                                <th>ตัวชี้วัด (รายละเอียด)</th>
                                <th>ผลิตภัณฑ์ (รายละเอียด)</th>
                                <th>กลุ่มเป้าหมาย</th>
                                <th>การดำเนินการ</th>
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
            initializeDataTable();
            loadStatistics();
        });

        function initializeDataTable() {
            projectsTable = $('#projectsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'projects_table_data.php',
                    type: 'POST',
                    data: function(d) {
                        // เพิ่มค่าจากฟิลเตอร์
                        d.project_year = $('#projectYearFilter').val();
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
                    }
                },
                columns: [
                    { 
                        data: 'ProjectCode',
                        render: function(data, type, row) {
                            return data ? `<span class="project-code">${data}</span>` : '-';
                        }
                    },
                    { 
                        data: 'ProjectName',
                        render: function(data, type, row) {
                            return `<div style="min-width: 250px; white-space: normal; word-wrap: break-word;">${data}</div>`;
                        }
                    },
                    { 
                        data: 'ProjectYear',
                        render: function(data, type, row) {
                            return data ? `พ.ศ. ${data}` : '-';
                        }
                    },
                    { data: 'MainProjectName' },
                    { data: 'StrategyName' },
                    { data: 'AgencyName' },
                    { data: 'Province' },
                    { data: 'District' },
                    { data: 'Subdistrict' },
                    { 
                        data: 'TotalBudget',
                        render: function(data, type, row) {
                            if (data && data > 0) {
                                return `<span class="budget-amount">${Number(data).toLocaleString('th-TH', {minimumFractionDigits: 2})} บาท</span>`;
                            }
                            return '-';
                        }
                    },
                    { 
                        data: 'IndicatorDetails',
                        render: function(data, type, row) {
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
                        render: function(data, type, row) {
                            if (data && data.length > 0) {
                                let html = '<div style="min-width: 200px;">';
                                data.forEach(function(product, index) {
                                    html += `<div class="product-item mb-1">
                                        <span class="badge bg-info me-1">${index + 1}</span>
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
                        data: 'TargetGroupCount',
                        render: function(data, type, row) {
                            return data > 0 ? `<span class="badge bg-warning text-dark">${data} กลุ่ม</span>` : '-';
                        }
                    },
                    { 
                        data: 'ProjectID',
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
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> พิมพ์',
                        className: 'btn btn-secondary btn-sm'
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                },
                drawCallback: function(settings) {
                    // อัพเดทสถิติหลังจากโหลดข้อมูลเสร็จ
                    loadStatistics();
                }
            });
        }

        function applyFilters() {
            projectsTable.ajax.reload();
        }

        function clearFilters() {
            $('#filterForm')[0].reset();
            projectsTable.ajax.reload();
        }

        function exportData() {
            // สร้าง URL สำหรับ export พร้อม filter parameters
            let params = new URLSearchParams();
            $('#filterForm').serializeArray().forEach(function(item) {
                if (item.value) {
                    params.append(item.name, item.value);
                }
            });
            
            window.open(`export_projects_table.php?${params.toString()}`, '_blank');
        }

        function loadStatistics() {
            $.ajax({
                url: 'projects_table_stats.php',
                type: 'POST',
                data: {
                    project_year: $('#projectYearFilter').val(),
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
                },
                error: function() {
                    console.error('ไม่สามารถโหลดสถิติได้');
                }
            });
        }

        // Auto-reload เมื่อเปลี่ยน filter
        $('.form-select, .form-control').on('change', function() {
            projectsTable.ajax.reload();
        });
    </script>
</body>
</html>
