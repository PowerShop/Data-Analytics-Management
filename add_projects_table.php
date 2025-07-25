<?php 
include 'db.php'; 
include 'navbar.php'; 

// ดึงข้อมูลสำหรับ dropdown
$strategies = [];
$result = $conn->query("SELECT strategyid, strategyname FROM strategies ORDER BY StrategyName");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $strategies[] = $row;
    }
}

$main_projects = [];
$result = $conn->query("SELECT mainprojectid, mainprojectname, mainprojectcode FROM mainprojects ORDER BY mainprojectname");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $main_projects[] = $row;
    }
}

$target_groups = [];
$result = $conn->query("SELECT GroupID, GroupName FROM TargetGroups ORDER BY GroupName");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $target_groups[] = $row;
    }
}

// ถ้าไม่มีกลุ่มเป้าหมายในฐานข้อมูล ให้เพิ่มกลุ่มเป้าหมายพื้นฐาน
if (empty($target_groups)) {
    $default_groups = ['เกษตรกร', 'ผู้ประกอบการ', 'ชุมชน', 'นักเรียน/นักศึกษา', 'ผู้สูงอายุ', 'เยาวชน'];
    foreach ($default_groups as $group) {
        $stmt = $conn->prepare("INSERT IGNORE INTO TargetGroups (GroupName) VALUES (?)");
        $stmt->bind_param("s", $group);
        $stmt->execute();
    }
    // ดึงข้อมูลใหม่
    $result = $conn->query("SELECT GroupID, GroupName FROM TargetGroups ORDER BY GroupName");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $target_groups[] = $row;
        }
    }
}

// สร้างรหัสโครงการใหม่
function generateNextProjectCode($conn, $prefix = "P") {
    $result = $conn->query("SELECT ProjectCode FROM projects ORDER BY ProjectID DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_code = $row['ProjectCode'];
        if (preg_match('/P(\d+)/', $last_code, $matches)) {
            $next_number = intval($matches[1]) + 1;
            return $prefix . str_pad($next_number, 4, "0", STR_PAD_LEFT);
        }
    }
    return $prefix . "001";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มโครงการ (แบบตาราง) - ระบบจัดการโครงการ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .header-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        .projects-table {
            min-width: 2000px;
            font-size: 14px;
        }
        
        .projects-table th {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: 1px solid #dee2e6;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            vertical-align: middle;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .projects-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
        }
        
        .projects-table input,
        .projects-table select,
        .projects-table textarea {
            border: none;
            background: transparent;
            width: 100%;
            padding: 5px;
            font-size: 13px;
        }
        
        .projects-table input:focus,
        .projects-table select:focus,
        .projects-table textarea:focus {
            background: #fff3cd;
            outline: 2px solid #ffc107;
            border-radius: 4px;
        }
        
        .row-number {
            background: #e9ecef;
            font-weight: bold;
            text-align: center;
            width: 50px;
            position: sticky;
            left: 0;
            z-index: 5;
        }
        
        .control-panel {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #28a745;
        }
        
        .btn-add-row {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-add-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .btn-save-all {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .btn-save-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
            color: white;
        }
        
        .btn-remove-row {
            background: #dc3545;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            color: white;
            font-size: 12px;
            padding: 0;
        }
        
        .btn-remove-row:hover {
            background: #c82333;
            color: white;
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
        
        .instruction-box {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .section-divider {
            background: #28a745;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .required-field {
            background-color: #fff9e6 !important;
        }
        
        /* ปรับขนาดคอลัมน์ */
        .col-project-code { width: 120px; }
        .col-project-name { width: 200px; }
        .col-year { width: 80px; }
        .col-strategy { width: 150px; }
        .col-main-project { width: 200px; }
        .col-agency { width: 150px; }
        .col-responsible { width: 150px; }
        .col-village { width: 120px; }
        .col-school { width: 150px; }
        .col-enterprise { width: 150px; }
        .col-network { width: 150px; }
        .col-product { width: 130px; }
        .col-budget { width: 110px; }
        .col-target-group { width: 90px; }
        .col-sroi { width: 100px; }
        .col-action { width: 80px; }
        
        /* เพิ่มสีพื้นหลังแยกแยะส่วน */
        .projects-table tr:nth-child(odd) {
            background-color: #f8f9fa;
        }
        
        .projects-table tr:hover {
            background-color: #e9ecef;
        }
        
        /* ปรับแต่ง input ให้สวยขึ้น */
        .projects-table input:hover,
        .projects-table select:hover {
            background-color: #f8f9fa;
        }
        
        /* สีเฉพาะสำหรับฟิลด์ที่จำเป็น */
        .required-field {
            background-color: #fff9e6 !important;
            border-left: 3px solid #ffc107 !important;
        }
        
        .required-field:focus {
            background-color: #fff3cd !important;
            border-left: 3px solid #f57c00 !important;
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center text-white">
            <div class="spinner-border spinner-border-lg text-light" role="status">
                <span class="visually-hidden">กำลังประมวลผล...</span>
            </div>
            <p class="mt-3 mb-0">กำลังบันทึกข้อมูล...</p>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Header -->
        <div class="header-section">
            <h2 class="fw-bold mb-3">
                <i class="fas fa-table me-3"></i>เพิ่มโครงการ (แบบตาราง)
            </h2>
            <p class="mb-0">กรอกข้อมูลโครงการหลายโครงการพร้อมกันในรูปแบบตาราง</p>
            <div class="mt-3">
                <span class="badge bg-info fs-6">
                    <i class="fas fa-lightbulb me-1"></i>
                    กรอกได้หลายโครงการในครั้งเดียว
                </span>
            </div>
        </div>

        <!-- Control Panel -->
        <div class="control-panel">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">
                        <i class="fas fa-cogs me-2"></i>แผงควบคุม
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn-add-row" onclick="addProjectRow()">
                            <i class="fas fa-plus me-2"></i>เพิ่มแถวโครงการ
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="clearAllRows()">
                            <i class="fas fa-eraser me-2"></i>เคลียร์ทั้งหมด
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="fillSampleData()">
                            <i class="fas fa-magic me-2"></i>เติมข้อมูลตัวอย่าง
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>ส่งออก Excel
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="validateAllData()">
                            <i class="fas fa-check-circle me-2"></i>ตรวจสอบข้อมูล
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn-save-all" onclick="saveAllProjects()">
                        <i class="fas fa-save me-2"></i>บันทึกทั้งหมด
                    </button>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="instruction-box">
            <h6 class="mb-2">
                <i class="fas fa-info-circle me-2"></i>วิธีการใช้งาน
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li>คลิก "เพิ่มแถวโครงการ" เพื่อเพิ่มโครงการใหม่</li>
                        <li>กรอกข้อมูลในแต่ละคอลัมน์ตามต้องการ</li>
                        <li>ใช้ Tab เพื่อเลื่อนไปยังช่องถัดไป</li>
                        <li>คลิกปุ่ม X สีแดงเพื่อลบแถว</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li>ช่องที่มีพื้นหลังสีเหลืองอ่อนคือช่องที่จำเป็น</li>
                        <li>สามารถเลื่อนตารางไปซ้าย-ขวาได้</li>
                        <li>คลิก "บันทึกทั้งหมด" เมื่อกรอกเสร็จ</li>
                        <li>ข้อมูลจะถูกตรวจสอบก่อนบันทึก</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Warning -->
        <div class="warning-box">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                <strong>หมายเหตุ:</strong> กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนบันทึก เมื่อบันทึกแล้วจะไม่สามารถแก้ไขได้ในหน้านี้
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-bordered projects-table" id="projectsTable">
                    <thead>
                        <tr>
                            <th class="row-number">#</th>
                            <th class="col-project-code">รหัสโครงการ*</th>
                            <th class="col-project-name">ชื่อโครงการ*</th>
                            <th class="col-year">ปี พ.ศ.*</th>
                            <th class="col-strategy">ยุทธศาสตร์*</th>
                            <th class="col-main-project">โครงการหลัก*</th>
                            <th class="col-agency">หน่วยงาน</th>
                            <th class="col-responsible">ผู้รับผิดชอบ</th>
                            <th class="section-divider" colspan="6">พื้นที่ดำเนินการ</th>
                            <th class="section-divider" colspan="2">เครือข่าย</th>
                            <th class="section-divider" colspan="3">ผลิตภัณฑ์</th>
                            <th class="section-divider" colspan="3">งบประมาณ</th>
                            <th class="section-divider" colspan="<?= count($target_groups) ?>">กลุ่มเป้าหมาย (คน)</th>
                            <th class="section-divider">SROI</th>
                            <th class="col-action">จัดการ</th>
                        </tr>
                        <tr>
                            <th class="row-number"></th>
                            <th class="col-project-code"></th>
                            <th class="col-project-name"></th>
                            <th class="col-year"></th>
                            <th class="col-strategy"></th>
                            <th class="col-main-project"></th>
                            <th class="col-agency"></th>
                            <th class="col-responsible"></th>
                            <th class="col-village">หมู่บ้าน</th>
                            <th class="col-village">หมู่ที่</th>
                            <th class="col-village">ตำบล</th>
                            <th class="col-village">อำเภอ</th>
                            <th class="col-school">โรงเรียน</th>
                            <th class="col-enterprise">วิสาหกิจ</th>
                            <th class="col-network">เครือข่าย</th>
                            <th class="col-enterprise">ประเภท</th>
                            <th class="col-product">ชื่อผลิตภัณฑ์</th>
                            <th class="col-product">ประเภท</th>
                            <th class="col-product">มาตรฐาน</th>
                            <th class="col-budget">ประเภทงบ</th>
                            <th class="col-budget">งบที่ขอ</th>
                            <th class="col-budget">งบที่อนุมัติ</th>
                            <?php foreach ($target_groups as $group): ?>
                                <th class="col-target-group"><?= htmlspecialchars($group['GroupName']) ?></th>
                            <?php endforeach; ?>
                            <th class="col-sroi">ค่า SROI</th>
                            <th class="col-action"></th>
                        </tr>
                    </thead>
                    <tbody id="projectsTableBody">
                        <!-- แถวจะถูกเพิ่มด้วย JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-database me-2"></i>ข้อมูลอ้างอิง - ยุทธศาสตร์
                        </h6>
                    </div>
                    <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach ($strategies as $strategy): ?>
                            <small class="badge bg-info me-1 mb-1"><?= $strategy['StrategyID'] ?>: <?= htmlspecialchars($strategy['StrategyName']) ?></small>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-project-diagram me-2"></i>โครงการหลัก
                        </h6>
                    </div>
                    <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach ($main_projects as $project): ?>
                            <small class="badge bg-success me-1 mb-1"><?= $project['MainProjectID'] ?>: <?= htmlspecialchars($project['MainProjectName']) ?></small>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ข้อมูลอ้างอิงจาก PHP
        const strategies = <?= json_encode($strategies) ?>;
        const mainProjects = <?= json_encode($main_projects) ?>;
        const targetGroups = <?= json_encode($target_groups) ?>;
        
        let rowCounter = 0;
        let lastProjectCode = "<?= generateNextProjectCode($conn) ?>";

        $(document).ready(function() {
            // เพิ่มแถวแรกอัตโนมัติ
            addProjectRow();
        });

        function addProjectRow() {
            rowCounter++;
            const newCode = generateNextCode();
            const currentYear = new Date().getFullYear() + 543;
            
            let targetGroupCells = '';
            targetGroups.forEach(group => {
                targetGroupCells += `<td><input type="number" name="target_count_${group.GroupID}_${rowCounter}" min="0" placeholder="0"></td>`;
            });

            const row = `
                <tr data-row="${rowCounter}">
                    <td class="row-number">${rowCounter}</td>
                    <td><input type="text" name="project_code_${rowCounter}" value="${newCode}" class="required-field" readonly></td>
                    <td><input type="text" name="project_name_${rowCounter}" class="required-field" placeholder="ชื่อโครงการ"></td>
                    <td>
                        <select name="project_year_${rowCounter}" class="required-field">
                            ${generateYearOptions(currentYear)}
                        </select>
                    </td>
                    <td>
                        <select name="strategy_id_${rowCounter}" class="required-field">
                            <option value="">-- เลือก --</option>
                            ${strategies.map(s => `<option value="${s.StrategyID}">${s.StrategyName}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <select name="main_project_id_${rowCounter}" class="required-field">
                            <option value="">-- เลือก --</option>
                            ${mainProjects.map(p => `<option value="${p.MainProjectID}">${p.MainProjectCode} - ${p.MainProjectName}</option>`).join('')}
                        </select>
                    </td>
                    <td><input type="text" name="agency_name_${rowCounter}" placeholder="หน่วยงาน"></td>
                    <td><input type="text" name="responsible_person_${rowCounter}" placeholder="ผู้รับผิดชอบ"></td>
                    <td><input type="text" name="village_name_${rowCounter}" placeholder="ชื่อหมู่บ้าน"></td>
                    <td><input type="text" name="village_moo_${rowCounter}" placeholder="หมู่ที่"></td>
                    <td><input type="text" name="village_subdistrict_${rowCounter}" placeholder="ตำบล"></td>
                    <td><input type="text" name="village_district_${rowCounter}" placeholder="อำเภอ"></td>
                    <td><input type="text" name="school_name_${rowCounter}" placeholder="ชื่อโรงเรียน"></td>
                    <td><input type="text" name="enterprise_name_${rowCounter}" placeholder="ชื่อวิสาหกิจ"></td>
                    <td><input type="text" name="network_name_${rowCounter}" placeholder="ชื่อเครือข่าย"></td>
                    <td>
                        <select name="enterprise_type_${rowCounter}">
                            <option value="">-- เลือก --</option>
                            <option value="วิสาหกิจ">วิสาหกิจ</option>
                            <option value="ผู้ประกอบการ">ผู้ประกอบการ</option>
                        </select>
                    </td>
                    <td><input type="text" name="product_name_${rowCounter}" placeholder="ชื่อผลิตภัณฑ์"></td>
                    <td><input type="text" name="product_type_${rowCounter}" placeholder="ประเภท"></td>
                    <td><input type="text" name="product_standard_${rowCounter}" placeholder="เลขมาตรฐาน"></td>
                    <td>
                        <select name="budget_type_${rowCounter}">
                            <option value="">-- เลือก --</option>
                            <option value="เงินอุดหนุน">เงินอุดหนุน</option>
                            <option value="งบดำเนินงาน">งบดำเนินงาน</option>
                            <option value="งบลงทุน">งบลงทุน</option>
                            <option value="งบบุคลากร">งบบุคลากร</option>
                        </select>
                    </td>
                    <td><input type="number" name="requested_amount_${rowCounter}" min="0" placeholder="0"></td>
                    <td><input type="number" name="approved_amount_${rowCounter}" min="0" placeholder="0"></td>
                    ${targetGroupCells}
                    <td><input type="number" name="sroi_result_${rowCounter}" step="0.01" min="0" placeholder="0.00"></td>
                    <td>
                        <button type="button" class="btn-remove-row" onclick="removeProjectRow(${rowCounter})" title="ลบแถว">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#projectsTableBody').append(row);
            
            // แสดงข้อความแจ้งเตือน
            Swal.fire({
                title: 'เพิ่มแถวแล้ว',
                text: `เพิ่มแถวโครงการที่ ${rowCounter} เรียบร้อยแล้ว`,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }

        function removeProjectRow(rowNum) {
            Swal.fire({
                title: 'ต้องการลบแถวนี้?',
                text: `แถวที่ ${rowNum} จะถูกลบและไม่สามารถกู้คืนได้`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`tr[data-row="${rowNum}"]`).remove();
                    Swal.fire('ลบแล้ว!', 'แถวถูกลบเรียบร้อยแล้ว', 'success');
                }
            });
        }

        function clearAllRows() {
            Swal.fire({
                title: 'ต้องการเคลียร์ทั้งหมด?',
                text: 'ข้อมูลทั้งหมดจะถูกลบและไม่สามารถกู้คืนได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, เคลียร์เลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#projectsTableBody').empty();
                    rowCounter = 0;
                    Swal.fire('เคลียร์แล้ว!', 'ข้อมูลทั้งหมดถูกลบแล้ว', 'success');
                }
            });
        }

        function fillSampleData() {
            if (rowCounter === 0) {
                addProjectRow();
            }
            
            // เติมข้อมูลตัวอย่างในแถวแรก
            const firstRow = $('#projectsTableBody tr').first();
            firstRow.find('input[name*="project_name"]').val('โครงการพัฒนาชุมชนต้นแบบ');
            firstRow.find('select[name*="strategy_id"]').val(strategies[0]?.StrategyID || '');
            firstRow.find('select[name*="main_project_id"]').val(mainProjects[0]?.MainProjectID || '');
            firstRow.find('input[name*="agency_name"]').val('สำนักงานจังหวัดราชบุรี');
            firstRow.find('input[name*="responsible_person"]').val('นายตัวอย่าง ใจดี');
            firstRow.find('input[name*="village_name"]').val('บ้านตัวอย่าง');
            firstRow.find('input[name*="village_moo"]').val('1');
            firstRow.find('input[name*="village_subdistrict"]').val('สวนผึ้ง');
            firstRow.find('input[name*="village_district"]').val('สวนผึ้ง');
            firstRow.find('input[name*="school_name"]').val('โรงเรียนตัวอย่าง');
            firstRow.find('input[name*="enterprise_name"]').val('วิสาหกิจชุมชนตัวอย่าง');
            firstRow.find('select[name*="enterprise_type"]').val('วิสาหกิจ');
            firstRow.find('input[name*="network_name"]').val('เครือข่ายเกษตรกรตัวอย่าง');
            firstRow.find('input[name*="product_name"]').val('ผลิตภัณฑ์ตัวอย่าง');
            firstRow.find('input[name*="product_type"]').val('อาหาร');
            firstRow.find('input[name*="product_standard"]').val('มอก.1234');
            firstRow.find('select[name*="budget_type"]').val('เงินอุดหนุน');
            firstRow.find('input[name*="requested_amount"]').val('100000');
            firstRow.find('input[name*="approved_amount"]').val('80000');
            firstRow.find('input[name*="sroi_result"]').val('1.5');
            
            // เติมกลุ่มเป้าหมายตัวอย่าง
            if (targetGroups.length > 0) {
                firstRow.find(`input[name*="target_count_${targetGroups[0].GroupID}"]`).val('50');
            }
            
            Swal.fire('เติมข้อมูลตัวอย่างแล้ว', 'ข้อมูลตัวอย่างถูกเติมในแถวแรก', 'success');
        }

        function generateNextCode() {
            const match = lastProjectCode.match(/P(\d+)/);
            if (match) {
                const nextNum = parseInt(match[1]) + rowCounter;
                lastProjectCode = 'P' + nextNum.toString().padStart(4, '0');
                return lastProjectCode;
            }
            return 'P' + (rowCounter + 1).toString().padStart(4, '0');
        }

        function generateYearOptions(currentYear) {
            let options = '';
            for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                const selected = year === currentYear ? 'selected' : '';
                options += `<option value="${year}" ${selected}>${year}</option>`;
            }
            return options;
        }

        function saveAllProjects() {
            const projects = [];
            const rows = $('#projectsTableBody tr');
            
            if (rows.length === 0) {
                Swal.fire('ไม่มีข้อมูล', 'กรุณาเพิ่มโครงการก่อนบันทึก', 'warning');
                return;
            }

            let hasErrors = false;
            const errors = [];

            rows.each(function(index) {
                const row = $(this);
                const rowNum = row.data('row');
                
                const project = {
                    projectCode: row.find(`input[name="project_code_${rowNum}"]`).val(),
                    projectName: row.find(`input[name="project_name_${rowNum}"]`).val(),
                    projectYear: row.find(`select[name="project_year_${rowNum}"]`).val(),
                    strategyId: row.find(`select[name="strategy_id_${rowNum}"]`).val(),
                    mainProjectId: row.find(`select[name="main_project_id_${rowNum}"]`).val(),
                    agencyName: row.find(`input[name="agency_name_${rowNum}"]`).val(),
                    responsiblePerson: row.find(`input[name="responsible_person_${rowNum}"]`).val(),
                    villageName: row.find(`input[name="village_name_${rowNum}"]`).val(),
                    villageMoo: row.find(`input[name="village_moo_${rowNum}"]`).val(),
                    villageSubdistrict: row.find(`input[name="village_subdistrict_${rowNum}"]`).val(),
                    villageDistrict: row.find(`input[name="village_district_${rowNum}"]`).val(),
                    schoolName: row.find(`input[name="school_name_${rowNum}"]`).val(),
                    enterpriseName: row.find(`input[name="enterprise_name_${rowNum}"]`).val(),
                    enterpriseType: row.find(`select[name="enterprise_type_${rowNum}"]`).val(),
                    networkName: row.find(`input[name="network_name_${rowNum}"]`).val(),
                    productName: row.find(`input[name="product_name_${rowNum}"]`).val(),
                    productType: row.find(`input[name="product_type_${rowNum}"]`).val(),
                    productStandard: row.find(`input[name="product_standard_${rowNum}"]`).val(),
                    budgetType: row.find(`select[name="budget_type_${rowNum}"]`).val(),
                    requestedAmount: row.find(`input[name="requested_amount_${rowNum}"]`).val(),
                    approvedAmount: row.find(`input[name="approved_amount_${rowNum}"]`).val(),
                    sroiResult: row.find(`input[name="sroi_result_${rowNum}"]`).val(),
                    targetGroups: {}
                };

                // ตรวจสอบฟิลด์ที่จำเป็น
                if (!project.projectName) {
                    errors.push(`แถวที่ ${rowNum}: กรุณากรอกชื่อโครงการ`);
                    hasErrors = true;
                }
                if (!project.strategyId) {
                    errors.push(`แถวที่ ${rowNum}: กรุณาเลือกยุทธศาสตร์`);
                    hasErrors = true;
                }
                if (!project.mainProjectId) {
                    errors.push(`แถวที่ ${rowNum}: กรุณาเลือกโครงการหลัก`);
                    hasErrors = true;
                }

                // เก็บข้อมูลกลุ่มเป้าหมาย
                targetGroups.forEach(group => {
                    const count = row.find(`input[name="target_count_${group.GroupID}_${rowNum}"]`).val();
                    if (count && count > 0) {
                        project.targetGroups[group.GroupID] = count;
                    }
                });

                projects.push(project);
            });

            if (hasErrors) {
                Swal.fire({
                    title: 'ข้อมูลไม่ครบถ้วน',
                    html: errors.join('<br>'),
                    icon: 'error'
                });
                return;
            }

            // แสดง loading
            showLoading();

            // ส่งข้อมูลไปยัง server
            $.ajax({
                url: 'api/save_projects_table.php',
                type: 'POST',
                data: {
                    projects: JSON.stringify(projects)
                },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({
                            title: 'บันทึกสำเร็จ!',
                            text: `บันทึกโครงการทั้งหมด ${response.saved_count} โครงการ`,
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            if (confirm('ต้องการเริ่มกรอกใหม่หรือไม่?')) {
                                clearAllRows();
                                setTimeout(() => addProjectRow(), 500);
                            }
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }

        function showLoading() {
            $('#loadingOverlay').css('display', 'flex');
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        function exportToExcel() {
            const rows = $('#projectsTableBody tr');
            
            if (rows.length === 0) {
                Swal.fire('ไม่มีข้อมูล', 'กรุณาเพิ่มโครงการก่อนส่งออก', 'warning');
                return;
            }

            // สร้างข้อมูล CSV
            let csvContent = "data:text/csv;charset=utf-8,";
            
            // Header
            let headers = [
                "รหัสโครงการ", "ชื่อโครงการ", "ปี พ.ศ.", "ยุทธศาสตร์", "โครงการหลัก", 
                "หน่วยงาน", "ผู้รับผิดชอบ", "หมู่บ้าน", "หมู่ที่", "ตำบล", "อำเภอ", 
                "โรงเรียน", "วิสาหกิจ", "เครือข่าย", "ประเภทวิสาหกิจ", "ผลิตภัณฑ์", 
                "ประเภทผลิตภัณฑ์", "มาตรฐาน", "ประเภทงบ", "งบที่ขอ", "งบที่อนุมัติ"
            ];
            
            // เพิ่มกลุ่มเป้าหมาย
            targetGroups.forEach(group => {
                headers.push(group.GroupName);
            });
            headers.push("SROI");
            
            csvContent += headers.join(",") + "\r\n";
            
            // Data rows
            rows.each(function() {
                const row = $(this);
                const rowNum = row.data('row');
                
                let rowData = [
                    row.find(`input[name="project_code_${rowNum}"]`).val() || '',
                    '"' + (row.find(`input[name="project_name_${rowNum}"]`).val() || '') + '"',
                    row.find(`select[name="project_year_${rowNum}"]`).val() || '',
                    '"' + (row.find(`select[name="strategy_id_${rowNum}"] option:selected`).text() || '') + '"',
                    '"' + (row.find(`select[name="main_project_id_${rowNum}"] option:selected`).text() || '') + '"',
                    '"' + (row.find(`input[name="agency_name_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="responsible_person_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="village_name_${rowNum}"]`).val() || '') + '"',
                    row.find(`input[name="village_moo_${rowNum}"]`).val() || '',
                    '"' + (row.find(`input[name="village_subdistrict_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="village_district_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="school_name_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="enterprise_name_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="network_name_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`select[name="enterprise_type_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="product_name_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="product_type_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`input[name="product_standard_${rowNum}"]`).val() || '') + '"',
                    '"' + (row.find(`select[name="budget_type_${rowNum}"]`).val() || '') + '"',
                    row.find(`input[name="requested_amount_${rowNum}"]`).val() || '0',
                    row.find(`input[name="approved_amount_${rowNum}"]`).val() || '0'
                ];
                
                // เพิ่มข้อมูลกลุ่มเป้าหมาย
                targetGroups.forEach(group => {
                    rowData.push(row.find(`input[name="target_count_${group.GroupID}_${rowNum}"]`).val() || '0');
                });
                
                rowData.push(row.find(`input[name="sroi_result_${rowNum}"]`).val() || '0');
                
                csvContent += rowData.join(",") + "\r\n";
            });
            
            // ดาวน์โหลดไฟล์
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `projects_data_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            Swal.fire('ส่งออกสำเร็จ', 'ไฟล์ CSV ถูกดาวน์โหลดแล้ว', 'success');
        }

        function validateAllData() {
            const rows = $('#projectsTableBody tr');
            
            if (rows.length === 0) {
                Swal.fire('ไม่มีข้อมูล', 'กรุณาเพิ่มโครงการก่อนตรวจสอบ', 'warning');
                return;
            }

            let errors = [];
            let warnings = [];
            
            rows.each(function(index) {
                const row = $(this);
                const rowNum = row.data('row');
                const projectName = row.find(`input[name="project_name_${rowNum}"]`).val();
                const strategyId = row.find(`select[name="strategy_id_${rowNum}"]`).val();
                const mainProjectId = row.find(`select[name="main_project_id_${rowNum}"]`).val();
                
                // ตรวจสอบฟิลด์ที่จำเป็น
                if (!projectName) {
                    errors.push(`แถวที่ ${rowNum}: ต้องมีชื่อโครงการ`);
                }
                if (!strategyId) {
                    errors.push(`แถวที่ ${rowNum}: ต้องเลือกยุทธศาสตร์`);
                }
                if (!mainProjectId) {
                    errors.push(`แถวที่ ${rowNum}: ต้องเลือกโครงการหลัก`);
                }
                
                // ตรวจสอบข้อมูลเพิ่มเติม
                const agencyName = row.find(`input[name="agency_name_${rowNum}"]`).val();
                const responsiblePerson = row.find(`input[name="responsible_person_${rowNum}"]`).val();
                
                if (!agencyName && !responsiblePerson) {
                    warnings.push(`แถวที่ ${rowNum}: ควรมีข้อมูลหน่วยงานหรือผู้รับผิดชอบ`);
                }
            });

            // แสดงผลการตรวจสอบ
            if (errors.length > 0) {
                Swal.fire({
                    title: 'พบข้อผิดพลาด',
                    html: '<div style="text-align: left;">' + errors.join('<br>') + '</div>',
                    icon: 'error',
                    width: '600px'
                });
            } else if (warnings.length > 0) {
                Swal.fire({
                    title: 'คำเตือน',
                    html: '<div style="text-align: left;">' + warnings.join('<br>') + '</div>',
                    icon: 'warning',
                    width: '600px'
                });
            } else {
                Swal.fire('ข้อมูลถูกต้อง', 'ข้อมูลทั้งหมดผ่านการตรวจสอบ พร้อมบันทึกได้', 'success');
            }
        }
    </script>
</body>
</html>
