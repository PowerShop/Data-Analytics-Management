<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Custom Report Builder - ระบบจัดการโครงการ</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .custom-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .builder-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .section-title {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .table-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .table-card:hover {
            border-color: #007bff;
            background: #e3f2fd;
        }
        
        .table-card.selected {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .field-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            margin: 5px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .field-card:hover {
            border-color: #007bff;
            background: #e3f2fd;
            transform: translateY(-2px);
        }
        
        .field-card.selected {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .field-checkbox {
            margin: 5px 0;
        }
        
        .join-builder {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .condition-builder {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .query-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .btn-action {
            margin: 5px;
            min-width: 120px;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 400px;
        }
        
        .chart-controls {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .results-section {
            margin-top: 30px;
        }
        
        #dataTable_wrapper {
            margin-top: 20px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .step.active {
            background: #007bff;
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="custom-header">
            <h2 class="fw-bold mb-3">🛠️ Custom Report Builder</h2>
            <p class="mb-0">สร้างรายงานแบบกำหนดเองด้วยเครื่องมือที่ทรงพลัง</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" id="step1">
                <i class="fas fa-table"></i><br>
                <small>เลือกตาราง</small>
            </div>
            <div class="step" id="step2">
                <i class="fas fa-columns"></i><br>
                <small>เลือกฟิลด์</small>
            </div>
            <div class="step" id="step3">
                <i class="fas fa-link"></i><br>
                <small>กำหนด JOIN</small>
            </div>
            <div class="step" id="step4">
                <i class="fas fa-filter"></i><br>
                <small>เงื่อนไข WHERE</small>
            </div>
            <div class="step" id="step5">
                <i class="fas fa-chart-bar"></i><br>
                <small>สร้างรายงาน</small>
            </div>
        </div>

        <div class="row">
            <!-- Left Panel: Query Builder -->
            <div class="col-lg-4">
                <!-- Step 1: Select Tables -->
                <div class="builder-section" id="tables-section">
                    <h5 class="section-title">📋 เลือกตารางข้อมูล</h5>
                    <div id="table-list">
                        <!-- ตารางจะถูกโหลดด้วย AJAX -->
                    </div>
                    <button class="btn btn-primary btn-sm mt-3" onclick="loadTables()">
                        <i class="fas fa-refresh"></i> โหลดตาราง
                    </button>
                </div>

                <!-- Saved Queries Section -->
                <div class="builder-section" id="saved-queries-section">
                    <h5 class="section-title">💾 Query ที่บันทึกไว้</h5>
                    <div id="saved-queries-list">
                        <p class="text-muted">กำลังโหลด...</p>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-success btn-sm" onclick="loadSavedQueries()">
                            <i class="fas fa-refresh"></i> โหลด Query ที่บันทึกไว้
                        </button>
                        <button class="btn btn-info btn-sm" onclick="showSavedQueriesModal()">
                            <i class="fas fa-list"></i> ดู Query ทั้งหมด
                        </button>
                    </div>
                </div>

                <!-- Step 2: Select Fields -->
                <div class="builder-section" id="fields-section" style="display:none;">
                    <h5 class="section-title">🎯 เลือกฟิลด์ข้อมูล</h5>
                    <div id="field-list">
                        <!-- ฟิลด์จะถูกโหลดเมื่อเลือกตาราง -->
                    </div>
                    <div class="mt-3" id="field-actions" style="display:none;">
                        <button class="btn btn-success btn-sm" onclick="proceedToNextStep()">
                            <i class="fas fa-arrow-right"></i> ดำเนินการต่อ
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="goToStep(3)" id="btn-configure-joins" style="display:none;">
                            <i class="fas fa-link"></i> กำหนด JOIN
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="goToStep(4)">
                            <i class="fas fa-filter"></i> เพิ่มเงื่อนไข WHERE
                        </button>
                    </div>
                </div>

                <!-- Step 3: JOIN Configuration -->
                <div class="builder-section" id="joins-section" style="display:none;">
                    <h5 class="section-title">🔗 กำหนด JOIN</h5>
                    <div id="join-list">
                        <!-- JOIN conditions จะถูกสร้างที่นี่ -->
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-success btn-sm" onclick="addJoin()">
                            <i class="fas fa-plus"></i> เพิ่ม JOIN
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="goToStep(4)">
                            <i class="fas fa-arrow-right"></i> ไปเงื่อนไข WHERE
                        </button>
                        <button class="btn btn-info btn-sm" onclick="executeQuery()">
                            <i class="fas fa-play"></i> รันคำสั่งทันที
                        </button>
                    </div>
                </div>

                <!-- Step 4: WHERE Conditions -->
                <div class="builder-section" id="conditions-section" style="display:none;">
                    <h5 class="section-title">⚙️ เงื่อนไข WHERE</h5>
                    <div id="condition-list">
                        <!-- WHERE conditions จะถูกสร้างที่นี่ -->
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-warning btn-sm" onclick="addCondition()">
                            <i class="fas fa-plus"></i> เพิ่มเงื่อนไข
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="executeQuery()">
                            <i class="fas fa-chart-bar"></i> สร้างรายงาน
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Preview & Results -->
            <div class="col-lg-8">
                <!-- Query Preview -->
                <div class="builder-section">
                    <h5 class="section-title">👁️ ตัวอย่าง SQL Query</h5>
                    <div class="query-preview" id="query-preview">
                        <em>กรุณาเลือกตารางและฟิลด์เพื่อสร้าง Query</em>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-action" onclick="executeQuery()">
                            <i class="fas fa-play"></i> รันคำสั่ง
                        </button>
                        <button class="btn btn-success btn-action" onclick="exportToExcel()" disabled id="btn-excel">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button class="btn btn-danger btn-action" onclick="exportToPDF()" disabled id="btn-pdf">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button class="btn btn-info btn-action" onclick="createChart()" disabled id="btn-chart">
                            <i class="fas fa-chart-line"></i> สร้างกราฟ
                        </button>
                        <button class="btn btn-secondary btn-action" onclick="saveQuery()">
                            <i class="fas fa-save"></i> บันทึก Query
                        </button>
                    </div>
                </div>

                <!-- Loading -->
                <div class="loading" id="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>กำลังประมวลผล...</p>
                </div>

                <!-- Chart Controls -->
                <div class="builder-section" id="chart-controls" style="display:none;">
                    <h5 class="section-title">📊 การตั้งค่ากราฟ</h5>
                    <div class="chart-controls">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">ประเภทกราฟ</label>
                                <select class="form-select" id="chart-type">
                                    <option value="bar">Bar Chart</option>
                                    <option value="line">Line Chart</option>
                                    <option value="pie">Pie Chart</option>
                                    <option value="doughnut">Doughnut Chart</option>
                                    <option value="scatter">Scatter Plot</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ฟิลด์ X-Axis</label>
                                <select class="form-select" id="chart-x-field">
                                    <!-- Options จะถูกเติมโดย JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ฟิลด์ Y-Axis</label>
                                <select class="form-select" id="chart-y-field">
                                    <!-- Options จะถูกเติมโดย JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary w-100" onclick="generateChart()">
                                    <i class="fas fa-chart-bar"></i> สร้างกราฟ
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="customChart"></canvas>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="results-section" id="results-section" style="display:none;">
                    <div class="builder-section">
                        <h5 class="section-title">📊 ผลลัพธ์</h5>
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-striped table-hover" style="width:100%">
                                <!-- DataTable จะถูกสร้างโดย JavaScript -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Save Query Modal -->
    <div class="modal fade" id="saveQueryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">💾 บันทึก Query</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ชื่อ Query</label>
                        <input type="text" class="form-control" id="query-name" placeholder="เช่น รายงานโครงการตามจังหวัด">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="query-description" rows="3" placeholder="อธิบายรายละเอียดของ Query นี้"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" onclick="saveQueryToDB()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Saved Queries Modal -->
    <div class="modal fade" id="savedQueriesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">💾 Query ที่บันทึกไว้</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="saved-queries-modal-content">
                        <p class="text-center">กำลังโหลด...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let selectedTables = [];
        let selectedFields = [];
        let joinConditions = [];
        let whereConditions = [];
        let currentData = [];
        let dataTable = null;
        let currentChart = null;

        // Database table schema (จะถูกโหลดจาก server)
        let tableSchema = {};

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();
            
            // Load tables on page load
            loadTables();
            
            // Load saved queries on page load
            loadSavedQueries();
        });

        // Step navigation functions
        function goToStep(stepNumber) {
            // Validate step requirements
            switch(stepNumber) {
                case 2:
                    if (selectedTables.length === 0) {
                        showError('กรุณาเลือกตารางก่อน');
                        return;
                    }
                    break;
                case 3:
                    if (selectedFields.length === 0) {
                        showError('กรุณาเลือกฟิลด์ก่อน');
                        return;
                    }
                    if (selectedTables.length < 2) {
                        showError('ต้องมีตารางอย่างน้อย 2 ตารางสำหรับ JOIN');
                        return;
                    }
                    break;
                case 4:
                case 5:
                    if (selectedFields.length === 0) {
                        showError('กรุณาเลือกฟิลด์ก่อน');
                        return;
                    }
                    break;
            }
            
            // Update step indicator
            $('.step').removeClass('active').addClass('completed');
            for (let i = stepNumber + 1; i <= 5; i++) {
                $(`#step${i}`).removeClass('completed');
            }
            $(`#step${stepNumber}`).removeClass('completed').addClass('active');
            
            // Show/hide sections based on step
            $('.builder-section').hide();
            switch(stepNumber) {
                case 1:
                    $('#tables-section').show();
                    break;
                case 2:
                    $('#tables-section, #fields-section').show();
                    break;
                case 3:
                    $('#tables-section, #fields-section, #joins-section').show();
                    break;
                case 4:
                    $('#tables-section, #fields-section, #joins-section, #conditions-section').show();
                    break;
                case 5:
                    // แสดงทุก section และ results section
                    $('#tables-section, #fields-section, #joins-section, #conditions-section, #results-section').show();
                    // ไม่เรียก executeQuery ซ้ำ เพราะจะถูกเรียกจากปุ่มอื่น
                    break;
            }
        }

        // Show next step buttons and actions
        function showNextStepButtons() {
            $('#field-actions').show();
            
            // แสดงปุ่ม JOIN หากมีตารางมากกว่า 1 ตาราง
            if (selectedTables.length > 1) {
                $('#btn-configure-joins').show();
            } else {
                $('#btn-configure-joins').hide();
            }
        }

        // Proceed to next step based on current state
        function proceedToNextStep() {
            if (selectedFields.length === 0) {
                showError('กรุณาเลือกฟิลด์อย่างน้อย 1 ฟิลด์');
                return;
            }
            
            // หากมีตารางเดียว ไปที่ WHERE conditions
            if (selectedTables.length === 1) {
                goToStep(4);
                return;
            }
            
            // หากมีหลายตาราง ถามว่าต้องการ JOIN หรือไม่
            if (confirm('คุณมีหลายตาราง ต้องการกำหนด JOIN หรือไม่?\n\nกด OK เพื่อกำหนด JOIN\nกด Cancel เพื่อไปเงื่อนไข WHERE')) {
                goToStep(3);
            } else {
                goToStep(4);
            }
        }

        // Load available tables
        function loadTables() {
            console.log('Loading tables...');
            $.ajax({
                url: 'api/get_tables.php',
                method: 'GET',
                success: function(response) {
                    console.log('Raw response:', response);
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        console.log('Parsed response:', response);
                        if (response.success) {
                            displayTables(response.data);
                            // Store table schema for later use
                            response.data.forEach(table => {
                                tableSchema[table.name] = table.columns;
                            });
                        } else {
                            showError('ไม่สามารถโหลดรายการตารางได้: ' + (response.error || 'Unknown error'));
                        }
                    } catch (e) {
                        console.error('JSON Parse Error:', e);
                        showError('เกิดข้อผิดพลาดในการแปลงข้อมูล: ' + e.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error + '\nResponse: ' + xhr.responseText);
                }
            });
        }

        // Display tables in the UI
        function displayTables(tables) {
            let html = '';
            tables.forEach(table => {
                html += `
                    <div class="table-card" data-table="${table.name}">
                        <div class="d-flex align-items-center">
                            <input type="checkbox" class="form-check-input me-2 table-checkbox" id="table_${table.name}" data-table="${table.name}">
                            <div>
                                <strong>${table.name}</strong>
                                <br><small class="text-muted">${table.columns.length} คอลัมน์</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#table-list').html(html);
            
            // Add click events after HTML is inserted
            $('.table-card').on('click', function(e) {
                e.preventDefault();
                const tableName = $(this).data('table');
                toggleTable(tableName);
            });
            
            $('.table-checkbox').on('click', function(e) {
                e.stopPropagation(); // ป้องกัน event จากการคลิก card
                const tableName = $(this).data('table');
                toggleTable(tableName);
            });
        }

        // Toggle table selection
        function toggleTable(tableName) {
            const checkbox = $(`#table_${tableName}`);
            const card = checkbox.closest('.table-card');
            
            // Toggle checkbox state
            const isCurrentlyChecked = checkbox.prop('checked');
            checkbox.prop('checked', !isCurrentlyChecked);
            
            if (!isCurrentlyChecked) {
                // เพิ่มตารางใหม่
                card.addClass('selected');
                if (!selectedTables.includes(tableName)) {
                    selectedTables.push(tableName);
                }
            } else {
                // ลบตาราง
                card.removeClass('selected');
                selectedTables = selectedTables.filter(t => t !== tableName);
            }
            
            console.log('Selected tables:', selectedTables);
            
            updateFieldsList();
            updateJoinsList();
            updateQueryPreview();
            
            if (selectedTables.length > 0) {
                $('#fields-section').show();
                if ($('#fields-section').is(':hidden')) {
                    goToStep(2);
                }
            } else {
                $('#fields-section').hide();
                selectedFields = [];
                $('.field-checkbox-input').prop('checked', false);
            }
        }

        // Update fields list based on selected tables
        function updateFieldsList() {
            let html = '';
            selectedTables.forEach(tableName => {
                if (tableSchema[tableName]) {
                    html += `
                        <div class="mb-3">
                            <h6 class="text-primary">${tableName}</h6>
                            <div class="row">
                    `;
                    
                    tableSchema[tableName].forEach(field => {
                        const fieldId = `${tableName}.${field.field}`;
                        html += `
                            <div class="col-md-6 field-checkbox">
                                <div class="field-card" data-field="${fieldId}">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-2 field-checkbox-input" 
                                               id="field_${fieldId.replace('.', '_')}" value="${fieldId}" data-field="${fieldId}">
                                        <div>
                                            <span>${field.field}</span>
                                            <br><small class="text-muted">(${field.type})</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `
                            </div>
                        </div>
                    `;
                }
            });
            $('#field-list').html(html);
            
            // Add click events after HTML is inserted
            $('.field-card').on('click', function(e) {
                e.preventDefault();
                const fieldId = $(this).data('field');
                const checkbox = $(this).find('.field-checkbox-input');
                
                // Toggle checkbox state
                checkbox.prop('checked', !checkbox.prop('checked'));
                
                // Update visual state
                if (checkbox.prop('checked')) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }
                
                // Update selected fields
                updateSelectedFields();
            });
            
            $('.field-checkbox-input').on('click', function(e) {
                e.stopPropagation(); // ป้องกัน event จากการคลิก field-card
                
                // Update visual state
                const fieldCard = $(this).closest('.field-card');
                if ($(this).prop('checked')) {
                    fieldCard.addClass('selected');
                } else {
                    fieldCard.removeClass('selected');
                }
                
                // Update selected fields
                updateSelectedFields();
            });
        }

        // Update selected fields array
        function updateSelectedFields() {
            selectedFields = [];
            $('.field-checkbox-input:checked').each(function() {
                selectedFields.push($(this).val());
            });
            updateQueryPreview();
            
            // แสดง JOIN section หากเลือกตารางมากกว่า 1 ตาราง
            if (selectedFields.length > 0 && selectedTables.length > 1) {
                $('#joins-section').show();
            }
            
            // แสดง WHERE section เมื่อเลือกฟิลด์แล้ว
            if (selectedFields.length > 0) {
                $('#conditions-section').show();
                
                // เพิ่มปุ่มสำหรับไปขั้นตอนต่อไป
                showNextStepButtons();
            }
        }

        // Update joins list
        function updateJoinsList() {
            if (selectedTables.length < 2) {
                $('#join-list').html('<p class="text-muted">เลือกตารางอย่างน้อย 2 ตารางเพื่อสร้าง JOIN</p>');
                return;
            }
            
            let html = '';
            joinConditions.forEach((join, index) => {
                html += createJoinHTML(join, index);
            });
            $('#join-list').html(html);
            
            // แสดง section JOIN เมื่อมีตารางมากกว่า 1
            if (selectedTables.length >= 2) {
                $('#joins-section').show();
            }
        }

        // Create JOIN HTML
        function createJoinHTML(join, index) {
            let tableOptions = '';
            selectedTables.forEach(table => {
                tableOptions += `<option value="${table}" ${join.leftTable === table ? 'selected' : ''}>${table}</option>`;
            });
            
            // Get field options for left table
            let leftFieldOptions = '';
            if (join.leftTable && tableSchema[join.leftTable]) {
                tableSchema[join.leftTable].forEach(field => {
                    leftFieldOptions += `<option value="${field.field}" ${join.leftField === field.field ? 'selected' : ''}>${field.field}</option>`;
                });
            }
            
            // Get field options for right table
            let rightFieldOptions = '';
            if (join.rightTable && tableSchema[join.rightTable]) {
                tableSchema[join.rightTable].forEach(field => {
                    rightFieldOptions += `<option value="${field.field}" ${join.rightField === field.field ? 'selected' : ''}>${field.field}</option>`;
                });
            }
            
            return `
                <div class="join-builder" id="join_${index}">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-2">
                            <label class="form-label small">Join Type</label>
                            <select class="form-select form-select-sm" onchange="updateJoinData(${index}, 'type', this.value)">
                                <option value="INNER" ${join.type === 'INNER' ? 'selected' : ''}>INNER</option>
                                <option value="LEFT" ${join.type === 'LEFT' ? 'selected' : ''}>LEFT</option>
                                <option value="RIGHT" ${join.type === 'RIGHT' ? 'selected' : ''}>RIGHT</option>
                                <option value="FULL" ${join.type === 'FULL' ? 'selected' : ''}>FULL</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">ตารางซ้าย</label>
                            <select class="form-select form-select-sm" onchange="updateJoinData(${index}, 'leftTable', this.value)">
                                <option value="">เลือกตาราง</option>
                                ${tableOptions}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">ฟิลด์ซ้าย</label>
                            <select class="form-select form-select-sm" id="leftField_${index}" onchange="updateJoinData(${index}, 'leftField', this.value)">
                                <option value="">เลือกฟิลด์</option>
                                ${leftFieldOptions}
                            </select>
                        </div>
                        <div class="col-md-1 text-center">
                            <span class="fw-bold">=</span>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">ตารางขวา</label>
                            <select class="form-select form-select-sm" onchange="updateJoinData(${index}, 'rightTable', this.value)">
                                <option value="">เลือกตาราง</option>
                                ${tableOptions}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">ฟิลด์ขวา</label>
                            <select class="form-select form-select-sm" id="rightField_${index}" onchange="updateJoinData(${index}, 'rightField', this.value)">
                                <option value="">เลือกฟิลด์</option>
                                ${rightFieldOptions}
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small">&nbsp;</label>
                            <button class="btn btn-danger btn-sm w-100" onclick="removeJoin(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Add new JOIN
        function addJoin() {
            if (selectedTables.length < 2) {
                showError('ต้องเลือกตารางอย่างน้อย 2 ตารางเพื่อสร้าง JOIN');
                return;
            }
            
            const newJoin = {
                type: 'INNER',
                leftTable: selectedTables[0] || '',
                leftField: '',
                rightTable: selectedTables[1] || '',
                rightField: ''
            };
            joinConditions.push(newJoin);
            updateJoinsList();
            updateQueryPreview();
        }

        // Remove JOIN
        function removeJoin(index) {
            joinConditions.splice(index, 1);
            updateJoinsList();
            updateQueryPreview();
        }

        // Add WHERE condition
        function addCondition() {
            const newCondition = {
                field: '',
                operator: '=',
                value: '',
                logic: 'AND'
            };
            whereConditions.push(newCondition);
            updateConditionsList();
            updateQueryPreview();
        }

        // Update conditions list
        function updateConditionsList() {
            let html = '';
            whereConditions.forEach((condition, index) => {
                html += createConditionHTML(condition, index);
            });
            $('#condition-list').html(html);
            
            // แสดง section conditions เมื่อมี conditions
            if (whereConditions.length > 0) {
                $('#conditions-section').show();
            }
        }

        // Create condition HTML
        function createConditionHTML(condition, index) {
            let fieldOptions = '';
            selectedFields.forEach(field => {
                fieldOptions += `<option value="${field}" ${condition.field === field ? 'selected' : ''}>${field}</option>`;
            });
            
            return `
                <div class="condition-builder" id="condition_${index}">
                    <div class="row g-2 align-items-center">
                        ${index > 0 ? `
                        <div class="col-md-1">
                            <label class="form-label small">Logic</label>
                            <select class="form-select form-select-sm" onchange="updateConditionData(${index}, 'logic', this.value)">
                                <option value="AND" ${condition.logic === 'AND' ? 'selected' : ''}>AND</option>
                                <option value="OR" ${condition.logic === 'OR' ? 'selected' : ''}>OR</option>
                            </select>
                        </div>
                        ` : '<div class="col-md-1"></div>'}
                        <div class="col-md-3">
                            <label class="form-label small">ฟิลด์</label>
                            <select class="form-select form-select-sm" onchange="updateConditionData(${index}, 'field', this.value)">
                                <option value="">เลือกฟิลด์</option>
                                ${fieldOptions}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Operator</label>
                            <select class="form-select form-select-sm" onchange="updateConditionData(${index}, 'operator', this.value)">
                                <option value="=" ${condition.operator === '=' ? 'selected' : ''}>=</option>
                                <option value="!=" ${condition.operator === '!=' ? 'selected' : ''}>!=</option>
                                <option value=">" ${condition.operator === '>' ? 'selected' : ''}>></option>
                                <option value="<" ${condition.operator === '<' ? 'selected' : ''}><</option>
                                <option value=">=" ${condition.operator === '>=' ? 'selected' : ''}>>=</option>
                                <option value="<=" ${condition.operator === '<=' ? 'selected' : ''}><=</option>
                                <option value="LIKE" ${condition.operator === 'LIKE' ? 'selected' : ''}>LIKE</option>
                                <option value="IN" ${condition.operator === 'IN' ? 'selected' : ''}>IN</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">ค่า</label>
                            <input type="text" class="form-control form-control-sm" value="${condition.value}" 
                                   onchange="updateConditionData(${index}, 'value', this.value)" placeholder="ค่าที่ต้องการ">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small">&nbsp;</label>
                            <button class="btn btn-danger btn-sm w-100" onclick="removeCondition(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Remove WHERE condition
        function removeCondition(index) {
            whereConditions.splice(index, 1);
            updateConditionsList();
            updateQueryPreview();
        }

        // Update JOIN condition
        function updateJoin(index) {
            // This function would be called when JOIN selects change
            updateQueryPreview();
        }

        // Update JOIN data when select changes
        function updateJoinData(index, field, value) {
            if (joinConditions[index]) {
                joinConditions[index][field] = value;
                
                // ถ้าเปลี่ยนตาราง ให้อัพเดตฟิลด์ options
                if (field === 'leftTable') {
                    updateJoinFieldOptions(index, 'left', value);
                    joinConditions[index].leftField = ''; // รีเซ็ตฟิลด์
                } else if (field === 'rightTable') {
                    updateJoinFieldOptions(index, 'right', value);
                    joinConditions[index].rightField = ''; // รีเซ็ตฟิลด์
                }
                
                updateQueryPreview();
            }
        }

        // Update field options for JOIN
        function updateJoinFieldOptions(joinIndex, side, tableName) {
            let selectId = side === 'left' ? `leftField_${joinIndex}` : `rightField_${joinIndex}`;
            let fieldSelect = $(`#${selectId}`);
            
            if (tableName && tableSchema[tableName]) {
                let options = '<option value="">เลือกฟิลด์</option>';
                tableSchema[tableName].forEach(field => {
                    options += `<option value="${field.field}">${field.field}</option>`;
                });
                fieldSelect.html(options);
            } else {
                fieldSelect.html('<option value="">เลือกฟิลด์</option>');
            }
        }

        // Update WHERE condition
        function updateCondition(index) {
            // This function would be called when condition selects change
            updateQueryPreview();
        }

        // Update WHERE condition data
        function updateConditionData(index, field, value) {
            if (whereConditions[index]) {
                whereConditions[index][field] = value;
                updateQueryPreview();
            }
        }

        // Update query preview
        function updateQueryPreview() {
            if (selectedFields.length === 0) {
                $('#query-preview').html('<em>กรุณาเลือกฟิลด์เพื่อสร้าง Query</em>');
                return;
            }
            
            let query = `SELECT ${selectedFields.join(', ')}\n`;
            query += `FROM ${selectedTables[0]}`;
            
            // Add JOINs - ปรับปรุงการตรวจสอบ JOIN conditions
            joinConditions.forEach(join => {
                if (join.leftTable && join.rightTable && join.leftField && join.rightField) {
                    query += `\n${join.type} JOIN ${join.rightTable} ON ${join.leftTable}.${join.leftField} = ${join.rightTable}.${join.rightField}`;
                }
            });
            
            // Add WHERE conditions - ปรับปรุงการสร้าง WHERE clause
            let hasValidConditions = false;
            let whereClause = '';
            
            whereConditions.forEach((condition, index) => {
                if (condition.field && condition.value) {
                    if (hasValidConditions) {
                        whereClause += ` ${condition.logic} `;
                    }
                    whereClause += `${condition.field} ${condition.operator} '${condition.value}'`;
                    hasValidConditions = true;
                }
            });
            
            if (hasValidConditions) {
                query += '\nWHERE ' + whereClause;
            }
            
            // เพิ่ม LIMIT เพื่อป้องกันข้อมูลมากเกินไป
            query += '\nLIMIT 1000';
            
            $('#query-preview').text(query);
        }

        // Execute the query
        function executeQuery() {
            console.log('🚀 executeQuery() called');
            const query = $('#query-preview').text();
            console.log('📝 Current query:', query);
            
            if (!query || query.includes('กรุณาเลือก')) {
                console.log('❌ Query validation failed: empty or default query');
                showError('กรุณาสร้าง Query ให้สมบูรณ์ก่อน');
                return;
            }
            
            // ตรวจสอบ SQL syntax พื้นฐาน
            if (query.includes('WHERE LIMIT') || query.includes('FROM \n')) {
                console.log('❌ SQL syntax validation failed');
                showError('SQL Query มีรูปแบบไม่ถูกต้อง กรุณาตรวจสอบการเลือกตารางและเงื่อนไข');
                return;
            }
            
            console.log('✅ Basic SQL validation passed');
            
            // ตรวจสอบ JOIN conditions
            let hasInvalidJoin = false;
            joinConditions.forEach(join => {
                if (join.leftTable && join.rightTable && (!join.leftField || !join.rightField)) {
                    hasInvalidJoin = true;
                }
            });
            
            console.log('🔗 JOIN validation - hasInvalidJoin:', hasInvalidJoin, 'joinConditions:', joinConditions);
            
            if (hasInvalidJoin) {
                if (confirm('มี JOIN conditions ที่ไม่สมบูรณ์ ต้องการดำเนินการต่อหรือไม่?\n\n(JOIN ที่ไม่สมบูรณ์จะถูกข้าม)')) {
                    console.log('✅ User confirmed to proceed with invalid JOINs');
                    // อนุญาตให้ดำเนินการต่อ แต่จะข้าม JOIN ที่ไม่สมบูรณ์
                } else {
                    console.log('❌ User cancelled due to invalid JOINs');
                    return;
                }
            }
            
            console.log('🚀 Starting AJAX request...');
            
            $('#loading').show();
            
            $.ajax({
                url: 'api/execute_query.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    sql: query
                }),
                success: function(response) {
                    console.log('📨 Raw execute response:', response);
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        console.log('📋 Parsed execute response:', response);
                        $('#loading').hide();
                        if (response.success) {
                            console.log('✅ Query executed successfully');
                            console.log('📊 Response data type:', typeof response.data);
                            console.log('📊 Response data length:', response.data ? response.data.length : 'undefined');
                            console.log('📊 Response columns:', response.columns);
                            
                            currentData = response.data;
                            displayResults(response.data, response.columns);
                            
                            // แสดงผลลัพธ์โดยไม่เปลี่ยน step
                            $('#results-section').show();
                            
                            // Scroll to results
                            setTimeout(() => {
                                $('#results-section')[0].scrollIntoView({ behavior: 'smooth' });
                            }, 500);
                        } else {
                            console.error('❌ Query execution failed:', response.error);
                            showError('เกิดข้อผิดพลาดในการรัน Query: ' + (response.error || 'Unknown error') + 
                                    (response.sql ? '\n\nSQL: ' + response.sql : ''));
                        }
                    } catch (e) {
                        console.error('❌ JSON Parse Error:', e);
                        $('#loading').hide();
                        showError('เกิดข้อผิดพลาดในการแปลงข้อมูล: ' + e.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Execute AJAX Error:', xhr.responseText);
                    $('#loading').hide();
                    let errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error;
                    
                    // แสดงรายละเอียด error เพิ่มเติม
                    if (xhr.responseText) {
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.error) {
                                errorMessage += '\n\nรายละเอียด: ' + errorResponse.error;
                            }
                            if (errorResponse.sql) {
                                errorMessage += '\n\nSQL: ' + errorResponse.sql;
                            }
                        } catch (e) {
                            errorMessage += '\n\nResponse: ' + xhr.responseText;
                        }
                    }
                    
                    showError(errorMessage);
                }
            });
        }

        // Display results in DataTable
        function displayResults(data, columns) {
            console.log('📊 displayResults() called');
            console.log('📊 Data:', data);
            console.log('📊 Columns:', columns);
            
            // ตรวจสอบข้อมูล
            if (!data || !Array.isArray(data)) {
                console.error('❌ Invalid data format:', data);
                showError('ข้อมูลที่ได้รับไม่ถูกต้อง');
                return;
            }
            
            if (!columns || !Array.isArray(columns)) {
                console.error('❌ Invalid columns format:', columns);
                showError('รูปแบบคอลัมน์ไม่ถูกต้อง');
                return;
            }
            
            console.log(`📊 Data rows: ${data.length}, Columns: ${columns.length}`);
            
            // Destroy existing DataTable
            if (dataTable) {
                console.log('🗑️ Destroying existing DataTable');
                dataTable.destroy();
                dataTable = null;
            }
            
            // Clear table content
            $('#dataTable').empty();
            
            // Create columns configuration
            const columnDefs = columns.map(col => ({
                title: col,
                data: col
            }));
            
            console.log('📋 Column definitions:', columnDefs);
            
            try {
                // Initialize DataTable
                console.log('🚀 Initializing new DataTable...');
                dataTable = $('#dataTable').DataTable({
                    data: data,
                    columns: columnDefs,
                    responsive: true,
                    scrollX: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    pageLength: 25,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                    },
                    // เพิ่ม debug options
                    processing: true,
                    deferRender: true
                });
                
                console.log('✅ DataTable initialized successfully');
                
                // Show results section
                $('#results-section').show();
                
                // Update button states
                $('#btn-excel, #btn-pdf, #btn-chart').prop('disabled', false);
                
                // Populate chart field options
                populateChartFields(columns);
                
                console.log('✅ Results displayed successfully');
                
            } catch (error) {
                console.error('❌ DataTable initialization error:', error);
                showError('เกิดข้อผิดพลาดในการสร้างตาราง: ' + error.message);
                
                // Fallback: แสดงข้อมูลเป็น HTML table แบบง่าย
                createFallbackTable(data, columns);
            }
        }

        // Create fallback HTML table if DataTable fails
        function createFallbackTable(data, columns) {
            console.log('🔄 Creating fallback HTML table');
            
            let html = '<table class="table table-striped table-hover">';
            
            // Header
            html += '<thead><tr>';
            columns.forEach(col => {
                html += `<th>${col}</th>`;
            });
            html += '</tr></thead>';
            
            // Body
            html += '<tbody>';
            data.forEach(row => {
                html += '<tr>';
                columns.forEach(col => {
                    const value = row[col] || '';
                    html += `<td>${value}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody></table>';
            
            $('#dataTable').html(html);
            $('#results-section').show();
            
            console.log('✅ Fallback table created');
        }

        // Populate chart field options
        function populateChartFields(columns) {
            let options = '<option value="">เลือกฟิลด์</option>';
            columns.forEach(col => {
                options += `<option value="${col}">${col}</option>`;
            });
            $('#chart-x-field, #chart-y-field').html(options);
        }

        // Export functions
        function exportToExcel() {
            if (dataTable) {
                dataTable.button('.buttons-excel').trigger();
            }
        }

        function exportToPDF() {
            if (dataTable) {
                dataTable.button('.buttons-pdf').trigger();
            }
        }

        // Chart functions
        function createChart() {
            $('#chart-controls').show();
        }

        function generateChart() {
            const chartType = $('#chart-type').val();
            const xField = $('#chart-x-field').val();
            const yField = $('#chart-y-field').val();
            
            if (!xField || !yField) {
                showError('กรุณาเลือกฟิลด์สำหรับ X และ Y axis');
                return;
            }
            
            // Prepare chart data
            const chartData = prepareChartData(currentData, xField, yField, chartType);
            
            // Destroy existing chart
            if (currentChart) {
                currentChart.destroy();
            }
            
            // Create new chart
            const ctx = document.getElementById('customChart').getContext('2d');
            currentChart = new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `${yField} by ${xField}`
                        }
                    },
                    scales: chartType !== 'pie' && chartType !== 'doughnut' ? {
                        x: {
                            title: {
                                display: true,
                                text: xField
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: yField
                            }
                        }
                    } : {}
                }
            });
        }

        // Prepare chart data
        function prepareChartData(data, xField, yField, chartType) {
            const labels = [];
            const values = [];
            const backgroundColors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
            ];
            
            // Group data if needed
            const groupedData = {};
            data.forEach(row => {
                const xValue = row[xField];
                const yValue = parseFloat(row[yField]) || 0;
                
                if (groupedData[xValue]) {
                    groupedData[xValue] += yValue;
                } else {
                    groupedData[xValue] = yValue;
                }
            });
            
            Object.keys(groupedData).forEach(key => {
                labels.push(key);
                values.push(groupedData[key]);
            });
            
            return {
                labels: labels,
                datasets: [{
                    label: yField,
                    data: values,
                    backgroundColor: chartType === 'pie' || chartType === 'doughnut' ? 
                        backgroundColors.slice(0, labels.length) : 
                        'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };
        }

        // Save query
        function saveQuery() {
            const query = $('#query-preview').text();
            if (!query || query.includes('กรุณาเลือก')) {
                showError('กรุณาสร้าง Query ให้สมบูรณ์ก่อน');
                return;
            }
            $('#saveQueryModal').modal('show');
        }

        function saveQueryToDB() {
            const name = $('#query-name').val().trim();
            const description = $('#query-description').val().trim();
            const query = $('#query-preview').text().trim();
            
            if (!name) {
                showError('กรุณาใส่ชื่อ Query');
                return;
            }
            
            if (!query) {
                showError('ไม่พบ SQL Query ที่จะบันทึก กรุณารัน Query ก่อน');
                return;
            }
            
            console.log('💾 Saving query:', { name, description, query });
            
            $.ajax({
                url: 'api/save_query.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    name: name,
                    description: description,
                    sql: query
                }),
                dataType: 'json',
                success: function(response) {
                    console.log('💾 Save response:', response);
                    if (response.success) {
                        showSuccess('บันทึก Query "' + name + '" เรียบร้อยแล้ว');
                        $('#saveQueryModal').modal('hide');
                        $('#query-name, #query-description').val('');
                        // Reload saved queries list
                        loadSavedQueries();
                    } else {
                        showError('ไม่สามารถบันทึก Query ได้: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('💾 Save error:', xhr.responseText);
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error + '\nResponse: ' + xhr.responseText);
                }
            });
        }

        // Load saved queries
        function loadSavedQueries() {
            console.log('📂 Loading saved queries...');
            $('#saved-queries-list').html('<p class="text-muted"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด Query ที่บันทึกไว้...</p>');
            
            $.ajax({
                url: 'api/save_query.php',
                method: 'GET',
                success: function(response) {
                    console.log('📂 Saved queries response:', response);
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.success) {
                            displaySavedQueries(response.data);
                        } else {
                            console.error('Failed to load saved queries:', response.error);
                            $('#saved-queries-list').html('<p class="text-danger">ไม่สามารถโหลด Query ที่บันทึกไว้ได้: ' + (response.error || 'Unknown error') + '</p>');
                        }
                    } catch (e) {
                        console.error('JSON Parse Error:', e);
                        $('#saved-queries-list').html('<p class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + e.message + '</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    $('#saved-queries-list').html('<p class="text-danger">เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error + '<br><small>' + xhr.responseText + '</small></p>');
                }
            });
        }

        // Display saved queries
        function displaySavedQueries(queries) {
            console.log('📂 Displaying saved queries:', queries);
            
            if (!queries || queries.length === 0) {
                $('#saved-queries-list').html('<p class="text-muted">ยังไม่มี Query ที่บันทึกไว้</p>');
                return;
            }
            
            let html = '<div class="row">';
            queries.slice(0, 3).forEach(query => { // แสดงแค่ 3 อันล่าสุด
                html += `
                    <div class="col-md-12 mb-2">
                        <div class="card border-left-primary">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${query.name}</strong>
                                        <br><small class="text-muted">${query.description || 'ไม่มีคำอธิบาย'}</small>
                                        <br><small class="text-info">${new Date(query.created_at).toLocaleString('th-TH')}</small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-primary" onclick="loadSavedQuery(${query.id})">
                                            <i class="fas fa-play"></i> ใช้งาน
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            if (queries.length > 3) {
                html += `<p class="text-center mt-2"><small class="text-muted">และอีก ${queries.length - 3} รายการ</small></p>`;
            }
            
            $('#saved-queries-list').html(html);
        }

        // Show saved queries modal
        function showSavedQueriesModal() {
            console.log('📂 Opening saved queries modal');
            $.ajax({
                url: 'api/save_query.php',
                method: 'GET',
                success: function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.success) {
                            displaySavedQueriesModal(response.data);
                            $('#savedQueriesModal').modal('show');
                        } else {
                            showError('ไม่สามารถโหลด Query ที่บันทึกไว้ได้');
                        }
                    } catch (e) {
                        showError('เกิดข้อผิดพลาดในการแปลงข้อมูล');
                    }
                },
                error: function(xhr, status, error) {
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                }
            });
        }

        // Display saved queries in modal
        function displaySavedQueriesModal(queries) {
            if (!queries || queries.length === 0) {
                $('#saved-queries-modal-content').html(`
                    <div class="text-center">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">ยังไม่มี Query ที่บันทึกไว้</p>
                    </div>
                `);
                return;
            }
            
            let html = '<div class="list-group">';
            queries.forEach(query => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${query.name}</h6>
                            <small class="text-muted">${new Date(query.created_at).toLocaleString('th-TH')}</small>
                        </div>
                        <p class="mb-1">${query.description || 'ไม่มีคำอธิบาย'}</p>
                        <div class="mb-2">
                            <small class="text-muted">SQL:</small>
                            <pre class="bg-light p-2 rounded" style="font-size: 12px; max-height: 100px; overflow-y: auto;">${query.sql_query}</pre>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary" onclick="loadSavedQuery(${query.id}); $('#savedQueriesModal').modal('hide');">
                                <i class="fas fa-play"></i> ใช้งาน Query นี้
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSavedQuery(${query.id})">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            $('#saved-queries-modal-content').html(html);
        }

        // Load and execute a saved query
        function loadSavedQuery(queryId) {
            console.log('📂 Loading saved query ID:', queryId);
            $.ajax({
                url: 'api/save_query.php',
                method: 'GET',
                success: function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.success) {
                            const query = response.data.find(q => q.id == queryId);
                            if (query) {
                                // แสดง SQL ใน preview
                                $('#query-preview').text(query.sql_query);
                                
                                // รัน query ทันที
                                executeQueryDirect(query.sql_query);
                                
                                showSuccess(`โหลด Query "${query.name}" เรียบร้อยแล้ว`);
                            } else {
                                showError('ไม่พบ Query ที่ระบุ');
                            }
                        } else {
                            showError('ไม่สามารถโหลด Query ได้');
                        }
                    } catch (e) {
                        showError('เกิดข้อผิดพลาดในการแปลงข้อมูล');
                    }
                },
                error: function(xhr, status, error) {
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                }
            });
        }

        // Execute query directly with SQL string
        function executeQueryDirect(sqlQuery) {
            console.log('🚀 executeQueryDirect() called with SQL:', sqlQuery);
            
            $('#loading').show();
            
            $.ajax({
                url: 'api/execute_query.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    sql: sqlQuery
                }),
                success: function(response) {
                    console.log('📨 Direct execute response:', response);
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        $('#loading').hide();
                        if (response.success) {
                            currentData = response.data;
                            displayResults(response.data, response.columns);
                            
                            $('#results-section').show();
                            setTimeout(() => {
                                $('#results-section')[0].scrollIntoView({ behavior: 'smooth' });
                            }, 500);
                        } else {
                            showError('เกิดข้อผิดพลาดในการรัน Query: ' + response.error);
                        }
                    } catch (e) {
                        $('#loading').hide();
                        showError('เกิดข้อผิดพลาดในการแปลงข้อมูล: ' + e.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading').hide();
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error);
                }
            });
        }

        // Delete a saved query
        function deleteSavedQuery(queryId) {
            if (!confirm('คุณแน่ใจหรือไม่ที่จะลบ Query นี้?')) {
                return;
            }
            
            $.ajax({
                url: 'api/save_query.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: queryId
                }),
                success: function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.success) {
                            showSuccess('ลบ Query เรียบร้อยแล้ว');
                            // Reload saved queries
                            loadSavedQueries();
                            // Reload modal if it's open
                            if ($('#savedQueriesModal').hasClass('show')) {
                                showSavedQueriesModal();
                            }
                        } else {
                            showError('ไม่สามารถลบ Query ได้: ' + response.error);
                        }
                    } catch (e) {
                        showError('เกิดข้อผิดพลาดในการแปลงข้อมูล');
                    }
                },
                error: function(xhr, status, error) {
                    showError('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error);
                }
            });
        }

        // Utility functions
        function showError(message) {
            console.error('Error:', message);
            // Create a more user-friendly error display
            const errorDiv = $(`
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>เกิดข้อผิดพลาด:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('.container-fluid').prepend(errorDiv);
            // Auto dismiss after 10 seconds
            setTimeout(() => {
                errorDiv.alert('close');
            }, 10000);
        }

        function showSuccess(message) {
            console.log('Success:', message);
            const successDiv = $(`
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>สำเร็จ:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('.container-fluid').prepend(successDiv);
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                successDiv.alert('close');
            }, 5000);
        }
    </script>
</body>

</html>
