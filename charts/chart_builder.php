<?php 
session_start(); 
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
include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตัวสร้างกราฟขั้นสูง - ระบบจัดการโครงการ</title>
    
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
        
        .builder-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .preview-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            min-height: 500px;
        }
        
        .form-section {
            border-left: 4px solid #667eea;
            padding-left: 20px;
            margin-bottom: 25px;
        }
        
        .btn-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .chart-preview {
            max-height: 400px;
            display: none;
        }
        
        .no-preview {
            text-align: center;
            padding: 100px 20px;
            color: #999;
        }
        
        .no-preview i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .advanced-options {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            background: #f8f9fa;
        }
        
        .color-picker {
            width: 50px;
            height: 35px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .sql-editor {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            background: #2d3748;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            min-height: 200px;
        }
        
        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-magic me-3"></i>ตัวสร้างกราฟขั้นสูง</h1>
            <p class="mb-0">สร้างกราฟและแผนภูมิแบบกำหนดเองด้วยเครื่องมือขั้นสูง</p>
        </div>

        <div class="row">
            <!-- Chart Builder -->
            <div class="col-lg-6">
                <div class="builder-section">
                    <h4><i class="fas fa-cogs me-2"></i>ตั้งค่ากราฟ</h4>
                    
                    <form id="chartBuilderForm">
                        <!-- Basic Settings -->
                        <div class="form-section">
                            <h6><i class="fas fa-info-circle me-1"></i>ข้อมูลพื้นฐาน</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อกราฟ</label>
                                    <input type="text" class="form-control" id="chartTitle" placeholder="ระบุชื่อกราฟ" required>
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
                                        <option value="polarArea">แผนภูมิพื้นที่เชิงขั้ว (Polar Area)</option>
                                        <option value="scatter">แผนภูมิกระจาย (Scatter Plot)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Data Source -->
                        <div class="form-section">
                            <h6><i class="fas fa-database me-1"></i>แหล่งข้อมูล</h6>
                            <div class="mb-3">
                                <label class="form-label">วิธีการเลือกข้อมูล</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="dataSource" id="useBuilder" value="builder" checked>
                                    <label class="form-check-label" for="useBuilder">
                                        ใช้ตัวสร้างแบบง่าย
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="dataSource" id="useSQL" value="sql">
                                    <label class="form-check-label" for="useSQL">
                                        เขียน SQL เอง (สำหรับผู้เชี่ยวชาญ)
                                    </label>
                                </div>
                            </div>

                            <!-- Simple Builder -->
                            <div id="simpleBuilder">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ข้อมูลแกน X</label>
                                        <select class="form-select" id="xAxisData">
                                            <option value="">เลือกข้อมูลแกน X</option>
                                            <option value="project_year">ปีโครงการ</option>
                                            <option value="strategy">ยุทธศาสตร์</option>
                                            <option value="main_project">โครงการหลัก</option>
                                            <option value="agency">หน่วยงาน</option>
                                            <option value="province">จังหวัด</option>
                                            <option value="target_group">กลุ่มเป้าหมาย</option>
                                            <option value="month">เดือน (สำหรับแผนภูมิเส้น)</option>
                                            <option value="quarter">ไตรมาส</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ข้อมูลแกน Y</label>
                                        <select class="form-select" id="yAxisData">
                                            <option value="">เลือกข้อมูลแกน Y</option>
                                            <option value="project_count">จำนวนโครงการ</option>
                                            <option value="budget_sum">งบประมาณรวม</option>
                                            <option value="target_count">จำนวนกลุ่มเป้าหมาย</option>
                                            <option value="sroi_avg">SROI เฉลี่ย</option>
                                            <option value="indicator_count">จำนวนตัวชี้วัด</option>
                                            <option value="project_per_agency">โครงการต่อหน่วยงาน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- SQL Editor -->
                            <div id="sqlEditor" style="display: none;">
                                <label class="form-label">SQL Query</label>
                                <textarea class="form-control sql-editor" id="customSQL" rows="8" placeholder="SELECT label, value FROM ...">-- ตัวอย่าง SQL Query
SELECT 
    p.ProjectYear as label,
    COUNT(*) as value
FROM projects p
WHERE p.ProjectYear IS NOT NULL
GROUP BY p.ProjectYear
ORDER BY p.ProjectYear</textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Query ต้องส่งคืนคอลัมน์ 'label' และ 'value' เท่านั้น
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="form-section">
                            <h6><i class="fas fa-filter me-1"></i>ตัวกรองข้อมูล</h6>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">ปีเริ่ม</label>
                                    <select class="form-select" id="yearStart" name="project_year_start">
                                        <option value="">ทั้งหมด</option>
                                        <?php
                                        $years = $conn->query("SELECT DISTINCT ProjectYear FROM projects WHERE ProjectYear IS NOT NULL ORDER BY ProjectYear DESC");
                                        while ($year = $years->fetch_assoc()) {
                                            echo "<option value='{$year['ProjectYear']}'>{$year['ProjectYear']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">ปีสิ้นสุด</label>
                                    <select class="form-select" id="yearEnd" name="project_year_end">
                                        <option value="">ทั้งหมด</option>
                                        <?php
                                        $years->data_seek(0);
                                        while ($year = $years->fetch_assoc()) {
                                            echo "<option value='{$year['ProjectYear']}'>{$year['ProjectYear']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">ยุทธศาสตร์</label>
                                    <select class="form-select" id="strategyFilter" name="strategy">
                                        <option value="">ทั้งหมด</option>
                                        <?php
                                        $strategies = $conn->query("SELECT DISTINCT s.StrategyID, s.StrategyName FROM strategies s INNER JOIN projects p ON s.StrategyID = p.StrategyID ORDER BY s.StrategyName");
                                        while ($strategy = $strategies->fetch_assoc()) {
                                            echo "<option value='{$strategy['StrategyID']}'>" . htmlspecialchars($strategy['StrategyName']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">โครงการหลัก</label>
                                    <select class="form-select" id="mainProjectFilter" name="main_project">
                                        <option value="">ทั้งหมด</option>
                                        <?php
                                        $mainProjects = $conn->query("SELECT DISTINCT mp.MainProjectID, mp.MainProjectName FROM mainprojects mp INNER JOIN projects p ON mp.MainProjectID = p.MainProjectID ORDER BY mp.MainProjectName");
                                        while ($mainProject = $mainProjects->fetch_assoc()) {
                                            echo "<option value='{$mainProject['MainProjectID']}'>" . htmlspecialchars($mainProject['MainProjectName']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Options -->
                        <div class="advanced-options">
                            <h6><i class="fas fa-palette me-1"></i>ตั้งค่าขั้นสูง</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">สีหลัก</label>
                                    <input type="color" class="form-control color-picker" id="primaryColor" value="#667eea">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">สีรอง</label>
                                    <input type="color" class="form-control color-picker" id="secondaryColor" value="#764ba2">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ความโปร่งใส</label>
                                    <input type="range" class="form-range" id="opacity" min="0.1" max="1" step="0.1" value="0.6">
                                    <span id="opacityValue">0.6</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showLegend" checked>
                                        <label class="form-check-label" for="showLegend">
                                            แสดง Legend
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showGrid" checked>
                                        <label class="form-check-label" for="showGrid">
                                            แสดงเส้นกริด
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" class="btn btn-generate me-3" onclick="generateChart()">
                                <i class="fas fa-chart-bar me-1"></i>สร้างกราฟ
                            </button>
                            <button type="button" class="btn btn-save" onclick="saveChart()" disabled id="saveBtn">
                                <i class="fas fa-save me-1"></i>บันทึกกราฟ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Chart Preview -->
            <div class="col-lg-6">
                <div class="preview-section sticky-sidebar">
                    <h4><i class="fas fa-eye me-2"></i>ตัวอย่างกราฟ</h4>
                    
                    <div class="no-preview" id="noPreview">
                        <i class="fas fa-chart-area"></i>
                        <h5>ยังไม่มีตัวอย่าง</h5>
                        <p>กรอกข้อมูลและคลิก "สร้างกราฟ" เพื่อดูตัวอย่าง</p>
                    </div>
                    
                    <canvas id="chartPreview" class="chart-preview"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let previewChart = null;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle data source type
            document.querySelectorAll('input[name="dataSource"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'sql') {
                        document.getElementById('simpleBuilder').style.display = 'none';
                        document.getElementById('sqlEditor').style.display = 'block';
                    } else {
                        document.getElementById('simpleBuilder').style.display = 'block';
                        document.getElementById('sqlEditor').style.display = 'none';
                    }
                });
            });
            
            // Update opacity display
            document.getElementById('opacity').addEventListener('input', function() {
                document.getElementById('opacityValue').textContent = this.value;
            });
        });
        
        // Generate chart
        function generateChart() {
            const title = document.getElementById('chartTitle').value;
            const type = document.getElementById('chartType').value;
            const dataSource = document.querySelector('input[name="dataSource"]:checked').value;
            
            if (!title || !type) {
                Swal.fire('ข้อผิดพลาด', 'กรุณากรอกชื่อกราฟและเลือกประเภทกราฟ', 'error');
                return;
            }
            
            let apiUrl;
            
            if (dataSource === 'sql') {
                const customSQL = document.getElementById('customSQL').value;
                if (!customSQL.trim()) {
                    Swal.fire('ข้อผิดพลาด', 'กรุณาใส่ SQL Query', 'error');
                    return;
                }
                apiUrl = '../api/custom_chart_api.php';
            } else {
                const xAxis = document.getElementById('xAxisData').value;
                const yAxis = document.getElementById('yAxisData').value;
                
                if (!xAxis || !yAxis) {
                    Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกข้อมูลแกน X และ Y', 'error');
                    return;
                }
                
                apiUrl = '../api/chart_data_api.php';
            }
            
            // Show loading
            Swal.fire({
                title: 'กำลังสร้างกราฟ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Prepare data
            const formData = new FormData();
            formData.append('title', title);
            formData.append('type', type);
            formData.append('data_source', dataSource);
            
            if (dataSource === 'sql') {
                formData.append('custom_sql', document.getElementById('customSQL').value);
            } else {
                formData.append('x_axis', document.getElementById('xAxisData').value);
                formData.append('y_axis', document.getElementById('yAxisData').value);
            }
            
            // Add filters
            const filters = ['yearStart', 'yearEnd', 'strategyFilter', 'mainProjectFilter'];
            filters.forEach(filterId => {
                const value = document.getElementById(filterId).value;
                if (value) {
                    formData.append(filterId, value);
                }
            });
            
            // Debug: แสดงข้อมูลที่จะส่ง
            console.log('Sending data to API:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            
            // Fetch data
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                Swal.close();
                
                try {
                    // กรอง PHP errors ออกจาก response ก่อน parse JSON
                    let cleanJson = text;
                    const jsonStart = text.indexOf('{');
                    if (jsonStart > 0) {
                        cleanJson = text.substring(jsonStart);
                        console.log('Cleaned JSON:', cleanJson);
                    }
                    
                    const data = JSON.parse(cleanJson);
                    
                    if (data.success) {
                        createPreviewChart(data, title, type);
                        document.getElementById('saveBtn').disabled = false;
                    } else {
                        Swal.fire('ข้อผิดพลาด', data.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Original response:', text);
                    
                    let errorPreview = text.substring(0, 500);
                    if (text.length > 500) errorPreview += '...';
                    
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        html: `ไม่สามารถสร้างกราฟได้<br><br><code style="font-size:10px;">${errorPreview}</code>`,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Fetch error:', error);
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์', 'error');
            });
        }
        
        // Create preview chart
        function createPreviewChart(data, title, type) {
            const ctx = document.getElementById('chartPreview').getContext('2d');
            
            // Destroy existing chart
            if (previewChart) {
                previewChart.destroy();
            }
            
            // Get style settings
            const primaryColor = document.getElementById('primaryColor').value;
            const secondaryColor = document.getElementById('secondaryColor').value;
            const opacity = document.getElementById('opacity').value;
            const showLegend = document.getElementById('showLegend').checked;
            const showGrid = document.getElementById('showGrid').checked;
            
            // Generate colors
            const colors = generateGradientColors(primaryColor, secondaryColor, data.labels.length, opacity);
            
            const chartConfig = {
                type: type,
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: title,
                        data: data.values,
                        backgroundColor: colors.background,
                        borderColor: colors.border,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: showLegend && ['pie', 'doughnut', 'polarArea', 'radar'].includes(type)
                        }
                    },
                    scales: ['bar', 'line', 'scatter'].includes(type) ? {
                        x: {
                            grid: {
                                display: showGrid
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: showGrid
                            }
                        }
                    } : {}
                }
            };
            
            previewChart = new Chart(ctx, chartConfig);
            
            // Show preview
            document.getElementById('noPreview').style.display = 'none';
            document.getElementById('chartPreview').style.display = 'block';
        }
        
        // Save chart
        function saveChart() {
            Swal.fire({
                title: 'บันทึกกราฟ',
                text: 'คุณต้องการบันทึกกราฟนี้หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'กำลังบันทึก...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // เตรียมข้อมูลสำหรับบันทึก
                    const formData = new FormData();
                    formData.append('title', document.getElementById('chartTitle').value);
                    formData.append('type', document.getElementById('chartType').value);
                    formData.append('data_source', document.querySelector('input[name="dataSource"]:checked').value);
                    
                    // ข้อมูลแหล่งข้อมูล
                    if (document.querySelector('input[name="dataSource"]:checked').value === 'sql') {
                        formData.append('custom_sql', document.getElementById('customSQL').value);
                    } else {
                        formData.append('x_axis', document.getElementById('xAxisData').value);
                        formData.append('y_axis', document.getElementById('yAxisData').value);
                    }
                    
                    // ตัวกรอง
                    const filters = ['yearStart', 'yearEnd', 'strategyFilter', 'mainProjectFilter'];
                    filters.forEach(filterId => {
                        const value = document.getElementById(filterId).value;
                        if (value) {
                            formData.append(filterId, value);
                        }
                    });
                    
                    // ตั้งค่าขั้นสูง
                    formData.append('primaryColor', document.getElementById('primaryColor').value);
                    formData.append('secondaryColor', document.getElementById('secondaryColor').value);
                    formData.append('opacity', document.getElementById('opacity').value);
                    if (document.getElementById('showLegend').checked) {
                        formData.append('showLegend', 'on');
                    }
                    if (document.getElementById('showGrid').checked) {
                        formData.append('showGrid', 'on');
                    }
                    
                    // บันทึกลงฐานข้อมูล
                    fetch('../api/save_chart_api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers.get('content-type'));
                        
                        // อ่าน response เป็น text ก่อน
                        return response.text();
                    })
                    .then(text => {
                        console.log('Raw response:', text);
                        Swal.close();
                        
                        try {
                            // กรอง PHP errors ออกจาก response ก่อน parse JSON
                            let cleanJson = text;
                            
                            // ลบ PHP notices/warnings ที่อาจปนมา
                            const jsonStart = text.indexOf('{');
                            if (jsonStart > 0) {
                                cleanJson = text.substring(jsonStart);
                                console.log('Cleaned JSON:', cleanJson);
                            }
                            
                            // พยายาม parse เป็น JSON
                            const data = JSON.parse(cleanJson);
                            
                            if (data.success) {
                                Swal.fire('บันทึกแล้ว!', 'กราฟถูกบันทึกเรียบร้อยแล้ว', 'success')
                                .then(() => {
                                    // Redirect to charts page
                                    window.location.href = 'charts.php';
                                });
                            } else {
                                Swal.fire('เกิดข้อผิดพลาด!', data.message, 'error');
                            }
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.error('Original response:', text);
                            
                            // แสดงข้อผิดพลาดพร้อมส่วนหนึ่งของ response
                            let errorPreview = text.substring(0, 300);
                            if (text.length > 300) errorPreview += '...';
                            
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                html: `ไม่สามารถประมวลผลข้อมูลได้<br><br><code style="font-size:10px;">${errorPreview}</code>`,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Fetch error:', error);
                        Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                    });
                }
            });
        }
        
        // Helper function to generate gradient colors
        function generateGradientColors(color1, color2, count, opacity) {
            const colors = [];
            const borderColors = [];
            
            // Convert hex to RGB
            const rgb1 = hexToRgb(color1);
            const rgb2 = hexToRgb(color2);
            
            for (let i = 0; i < count; i++) {
                const ratio = count === 1 ? 0 : i / (count - 1);
                
                const r = Math.round(rgb1.r + ratio * (rgb2.r - rgb1.r));
                const g = Math.round(rgb1.g + ratio * (rgb2.g - rgb1.g));
                const b = Math.round(rgb1.b + ratio * (rgb2.b - rgb1.b));
                
                colors.push(`rgba(${r}, ${g}, ${b}, ${opacity})`);
                borderColors.push(`rgba(${r}, ${g}, ${b}, 1)`);
            }
            
            return {
                background: colors,
                border: borderColors
            };
        }
        
        // Convert hex to RGB
        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    </script>
</body>
</html>
