<?php 
include '../db.php'; 
include 'navbar.php'; 

// ตรวจสอบสิทธิ์ admin (สามารถปรับแต่งตามระบบของคุณ)
// if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
//     header('Location: login.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Admin Find & Replace - ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .admin-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .search-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #dc3545;
        }
        
        .replace-section {
            background: #fff3cd;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #ffc107;
        }
        
        .results-container {
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
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            color: #000;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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
        
        .highlight-search {
            background-color: #ffeb3b;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .match-count {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .selected-row {
            background-color: #d1ecf1 !important;
            border-left: 4px solid #17a2b8;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .spinner-border-lg {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center text-white">
            <div class="spinner-border spinner-border-lg text-light" role="status">
                <span class="visually-hidden">กำลังประมวลผล...</span>
            </div>
            <p class="mt-3 mb-0">กำลังดำเนินการ...</p>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Header -->
        <div class="admin-header">
            <h2 class="fw-bold mb-3">
                <i class="fas fa-search-plus me-3"></i>Admin Find & Replace
            </h2>
            <p class="mb-0">เครื่องมือค้นหาและแทนที่ข้อมูลแบบ Real-time สำหรับผู้ดูแลระบบ</p>
            <div class="mt-3">
                <span class="badge bg-warning text-dark fs-6">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    โปรดระวัง: การแก้ไขข้อมูลจะมีผลกับฐานข้อมูลจริง
                </span>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h5 class="mb-4">
                <i class="fas fa-search me-2"></i>ค้นหาข้อมูล
            </h5>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">เลือกตาราง</label>
                    <select class="form-select" id="tableSelect">
                        <option value="projects">โครงการ (Projects)</option>
                        <option value="mainprojects">โครงการหลัก (Main Projects)</option>
                        <option value="strategies">ยุทธศาสตร์ (Strategies)</option>
                        <option value="indicators">ตัวชี้วัด (Indicators)</option>
                        <option value="projectproducts">ผลิตภัณฑ์โครงการ (Project Products)</option>
                        <option value="projectschools">โรงเรียนโครงการ (Project Schools)</option>
                        <option value="targetgroups">กลุ่มเป้าหมาย (Target Groups)</option>
                        <option value="projectvillages">หมู่บ้านโครงการ (Project Villages)</option>
                        <option value="budgetitems">งบประมาณ (Budget Items)</option>
                        <option value="projectnetworks">เครือข่ายโครงการ (Project Networks)</option>
                        <option value="projectenterprises">วิสาหกิจโครงการ (Project Enterprises)</option>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">เลือกฟิลด์</label>
                    <select class="form-select" id="fieldSelect">
                        <option value="">เลือกฟิลด์ที่ต้องการค้นหา</option>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-12 mb-3">
                    <label class="form-label">ค้นหาข้อความ</label>
                    <input type="text" class="form-control" id="searchText" placeholder="พิมพ์ข้อความที่ต้องการค้นหา...">
                </div>
            </div>
        </div>

        <!-- Replace Section -->
        <div class="replace-section" id="replaceSection" style="display: none;">
            <h5 class="mb-4">
                <i class="fas fa-edit me-2"></i>แทนที่ข้อมูล
            </h5>
            
            <div class="warning-box">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <strong>คำเตือน:</strong> การแทนที่ข้อมูลจะเปลี่ยนแปลงข้อมูลในฐานข้อมูลถาวร กรุณาตรวจสอบให้แน่ใจก่อนดำเนินการ
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">ข้อความเดิม (Find)</label>
                    <input type="text" class="form-control" id="findText" readonly>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">ข้อความใหม่ (Replace)</label>
                    <input type="text" class="form-control" id="replaceText" placeholder="พิมพ์ข้อความใหม่...">
                </div>
                
                <div class="col-lg-4 col-md-12 mb-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="button" class="btn btn-warning flex-fill" onclick="previewReplace()">
                            <i class="fas fa-eye me-2"></i>ดูตัวอย่าง
                        </button>
                        <button type="button" class="btn btn-danger flex-fill" onclick="executeReplace()">
                            <i class="fas fa-save me-2"></i>ดำเนินการ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Container -->
        <div class="results-container">
            <div id="matchCount" class="match-count" style="display: none;">
                <h4 class="mb-0">พบข้อมูล <span id="countNumber">0</span> รายการ</h4>
            </div>
            
            <div class="table-responsive">
                <table id="resultsTable" class="table table-striped table-hover" style="width:100%; display: none;">
                    <thead>
                        <!-- Headers จะถูกสร้างแบบ dynamic -->
                    </thead>
                    <tbody>
                        <!-- ข้อมูลจะถูกโหลดแบบ real-time -->
                    </tbody>
                </table>
            </div>
            
            <div id="noResults" class="text-center py-5" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">ไม่พบข้อมูลที่ค้นหา</h4>
                <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือเลือกฟิลด์อื่น</p>
            </div>
            
            <div id="initialMessage" class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                <h4 class="text-info">เริ่มต้นใช้งาน</h4>
                <p class="text-muted">เลือกตารางและฟิลด์ จากนั้นพิมพ์ข้อความที่ต้องการค้นหา</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let searchTimeout;
        let currentTable = 'projects';
        let resultsDataTable;

        // Table field mappings
        const tableFields = {
            'projects': {
                'ProjectID': 'รหัสโครงการ (ID)',
                'ProjectCode': 'รหัสโครงการ (Code)',
                'ProjectName': 'ชื่อโครงการ',
                'ProjectYear': 'ปีโครงการ',
                'AgencyName': 'หน่วยงาน',
                'ResponsiblePerson': 'ผู้รับผิดชอบโครงการ',
                'Province': 'จังหวัด',
                'TargetArea': 'พื้นที่ดำเนินงาน'
            },
            'mainprojects': {
                'MainProjectID': 'รหัสโครงการหลัก (ID)',
                'MainProjectName': 'ชื่อโครงการหลัก',
                'MainProjectCode': 'รหัสโครงการหลัก (Code)',
                'MainProjectDescription': 'รายละเอียด'
            },
            'strategies': {
                'StrategyID': 'รหัสยุทธศาสตร์ (ID)',
                'StrategyName': 'ชื่อยุทธศาสตร์'
            },
            'indicators': {
                'IndicatorID': 'รหัสตัวชี้วัด (ID)',
                'IndicatorName': 'ชื่อตัวชี้วัด',
                'Unit': 'หน่วย',
                'Description': 'คำอธิบาย',
                'Year': 'ปีของตัวชี้วัด'
            },
            'projectproducts': {
                'ID': 'รหัส (ID)',
                'ProductName': 'ชื่อผลิตภัณฑ์',
                'ProductType': 'ประเภทผลิตภัณฑ์',
                'Description': 'รายละเอียด',
                'StandardNumber': 'เลขมาตรฐานผลิตภัณฑ์'
            },
            'projectschools': {
                'ID': 'รหัส (ID)',
                'SchoolName': 'ชื่อโรงเรียน'
            },
            'targetgroups': {
                'GroupID': 'รหัสกลุ่มเป้าหมาย (ID)',
                'GroupName': 'ชื่อกลุ่มเป้าหมาย'
            },
            'projectvillages': {
                'ID': 'รหัส (ID)',
                'VillageName': 'ชื่อหมู่บ้าน',
                'Moo': 'หมู่',
                'Subdistrict': 'ตำบล',
                'District': 'อำเภอ',
                'Province': 'จังหวัด',
                'Community': 'ชุมชน'
            },
            'budgetitems': {
                'BudgetID': 'รหัสงบประมาณ (ID)',
                'BudgetType': 'ประเภทงบประมาณ',
                'Remark': 'หมายเหตุ'
            },
            'projectnetworks': {
                'ID': 'รหัส (ID)',
                'NetworkName': 'ชื่อเครือข่าย'
            },
            'projectenterprises': {
                'ID': 'รหัส (ID)',
                'EnterpriseName': 'ชื่อวิสาหกิจ/สถานประกอบการ',
                'EnterpriseType': 'ประเภท'
            }
        };

        $(document).ready(function() {
            loadTableFields();
            
            // Event listeners
            $('#tableSelect').on('change', function() {
                currentTable = $(this).val();
                loadTableFields();
                clearResults();
            });
            
            $('#fieldSelect').on('change', function() {
                clearResults();
                performSearch();
            });
            
            $('#searchText').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    performSearch();
                }, 500); // ค้นหาหลังจากหยุดพิมพ์ 500ms
            });
        });

        function loadTableFields() {
            const table = $('#tableSelect').val();
            const fieldSelect = $('#fieldSelect');
            
            fieldSelect.empty().append('<option value="">เลือกฟิลด์ที่ต้องการค้นหา</option>');
            
            if (tableFields[table]) {
                Object.keys(tableFields[table]).forEach(function(field) {
                    fieldSelect.append(`<option value="${field}">${tableFields[table][field]}</option>`);
                });
            }
        }

        function performSearch() {
            const searchText = $('#searchText').val().trim();
            const field = $('#fieldSelect').val();
            const table = $('#tableSelect').val();
            
            if (!searchText || !field) {
                clearResults();
                return;
            }
            
            showLoading();
            
            $.ajax({
                url: 'api/admin_search.php',
                type: 'POST',
                data: {
                    table: table,
                    field: field,
                    search: searchText
                },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        displayResults(response.data, response.columns, searchText);
                        updateMatchCount(response.total);
                        
                        // แสดงส่วน Replace
                        $('#replaceSection').show();
                        $('#findText').val(searchText);
                    } else {
                        showError('เกิดข้อผิดพลาด: ' + response.message);
                        clearResults();
                    }
                },
                error: function() {
                    hideLoading();
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
                    clearResults();
                }
            });
        }

        function displayResults(data, columns, searchText) {
            // ลบ DataTable เก่าถ้ามีและ clear ข้อมูลให้สมบูรณ์
            if (resultsDataTable) {
                resultsDataTable.clear();
                resultsDataTable.destroy();
                resultsDataTable = null;
            }
            
            // ซ่อนตารางก่อนปรับโครงสร้าง
            $('#resultsTable').hide();
            
            // Clear ทั้งตารางให้สมบูรณ์
            $('#resultsTable thead').empty();
            $('#resultsTable tbody').empty();
            
            // สร้าง headers ใหม่ทั้งหมด
            let headers = '<tr>';
            headers += '<th><input type="checkbox" id="selectAll"></th>';
            columns.forEach(function(column) {
                const displayName = tableFields[currentTable][column] || column;
                headers += `<th>${displayName}</th>`;
            });
            headers += '</tr>';
            
            $('#resultsTable thead').html(headers);
            
            // Clear tbody และสร้างข้อมูลแถวใหม่
            const tbody = $('#resultsTable tbody');
            tbody.empty();
            
            data.forEach(function(row, index) {
                let rowHtml = '<tr>';
                rowHtml += `<td><input type="checkbox" class="row-select" data-index="${index}" data-row='${JSON.stringify(row)}'></td>`;
                
                columns.forEach(function(column) {
                    let cellData = row[column] || '';
                    // Highlight search text
                    if (cellData && typeof cellData === 'string') {
                        const regex = new RegExp(`(${escapeRegExp(searchText)})`, 'gi');
                        cellData = cellData.replace(regex, '<span class="highlight-search">$1</span>');
                    }
                    rowHtml += `<td>${cellData}</td>`;
                });
                
                rowHtml += '</tr>';
                tbody.append(rowHtml);
            });
            
            // สร้าง DataTable ใหม่
            resultsDataTable = $('#resultsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                },
                pageLength: 25,
                responsive: true,
                destroy: true // เพิ่มตัวเลือกนี้เพื่อป้องกันข้อผิดพลาด
            });
            
            // แสดงตาราง
            $('#resultsTable').show();
            $('#initialMessage').hide();
            $('#noResults').hide();
            
            // Event listener สำหรับ select all
            $('#selectAll').off('change').on('change', function() {
                $('.row-select').prop('checked', $(this).prop('checked'));
            });
        }

        function updateMatchCount(count) {
            $('#countNumber').text(count);
            $('#matchCount').show();
        }

        function clearResults() {
            // ลบ DataTable อย่างสมบูรณ์
            if (resultsDataTable) {
                resultsDataTable.clear();
                resultsDataTable.destroy();
                resultsDataTable = null;
            }
            
            // ซ่อนตารางก่อน
            $('#resultsTable').hide();
            
            // Clear ข้อมูลในตารางให้สมบูรณ์
            $('#resultsTable thead').empty();
            $('#resultsTable tbody').empty();
            
            $('#matchCount').hide();
            $('#noResults').hide();
            $('#initialMessage').show();
            $('#replaceSection').hide();
        }

        function previewReplace() {
            const findText = $('#findText').val();
            const replaceText = $('#replaceText').val();
            
            if (!replaceText.trim()) {
                showError('กรุณากรอกข้อความใหม่');
                return;
            }
            
            const selectedRows = $('.row-select:checked').length;
            if (selectedRows === 0) {
                showError('กรุณาเลือกรายการที่ต้องการแก้ไข');
                return;
            }
            
            Swal.fire({
                title: 'ตัวอย่างการแทนที่',
                html: `
                    <div class="text-start">
                        <p><strong>ข้อความเดิม:</strong> <span class="text-danger">"${findText}"</span></p>
                        <p><strong>ข้อความใหม่:</strong> <span class="text-success">"${replaceText}"</span></p>
                        <p><strong>จำนวนรายการที่เลือก:</strong> ${selectedRows} รายการ</p>
                        <hr>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>ข้อมูลจะถูกเปลี่ยนแปลงในฐานข้อมูลจริง</p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'ดำเนินการแทนที่',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeReplace();
                }
            });
        }

        function executeReplace() {
            const table = $('#tableSelect').val();
            const field = $('#fieldSelect').val();
            const findText = $('#findText').val();
            const replaceText = $('#replaceText').val();
            
            if (!replaceText.trim()) {
                showError('กรุณากรอกข้อความใหม่');
                return;
            }
            
            const selectedRows = $('.row-select:checked');
            if (selectedRows.length === 0) {
                showError('กรุณาเลือกรายการที่ต้องการแก้ไข');
                return;
            }
            
            // เก็บ ID ของรายการที่เลือก
            const selectedIds = [];
            selectedRows.each(function() {
                const rowData = JSON.parse($(this).attr('data-row'));
                // ใช้ค่า ID จากข้อมูลแถวตาม primary key ของแต่ละตาราง
                const tableConfig = {
                    'projects': 'ProjectID',
                    'mainprojects': 'MainProjectID',
                    'strategies': 'StrategyID',
                    'indicators': 'IndicatorID',
                    'projectproducts': 'ID',
                    'projectschools': 'ID',
                    'targetgroups': 'GroupID',
                    'projectvillages': 'ID',
                    'budgetitems': 'BudgetID',
                    'projectnetworks': 'ID',
                    'projectenterprises': 'ID'
                };
                
                const primaryKey = tableConfig[table];
                if (primaryKey && rowData[primaryKey]) {
                    selectedIds.push(rowData[primaryKey]);
                }
            });
            
            if (selectedIds.length === 0) {
                showError('ไม่สามารถระบุ ID ของรายการที่เลือกได้');
                return;
            }
            
            showLoading();
            
            $.ajax({
                url: 'api/admin_replace.php',
                type: 'POST',
                data: {
                    table: table,
                    field: field,
                    findText: findText,
                    replaceText: replaceText,
                    ids: selectedIds
                },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        Swal.fire({
                            title: 'สำเร็จ!',
                            text: `แก้ไขข้อมูลเรียบร้อยแล้ว ${response.affected_rows} รายการ`,
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            // รีเฟรชผลการค้นหา
                            performSearch();
                        });
                    } else {
                        showError('เกิดข้อผิดพลาด: ' + response.message);
                    }
                },
                error: function() {
                    hideLoading();
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
                }
            });
        }

        function showLoading() {
            $('#loadingOverlay').css('display', 'flex');
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        function showError(message) {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: message,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    </script>
</body>
</html>
