<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!-- SweetAlert2 -->

<?php
// Initialize alert script variable
$alert_script = '';
$should_redirect = false;

if (!isset($_GET['id'])) {
    $alert_script = "
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'ข้อผิดพลาด!',
            text: 'ไม่พบรหัสโครงการ',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            window.location.href = 'projects_list.php';
        });
    });
    ";
    $should_redirect = true;
}

$id = $_GET['id'];

// เมื่อมีการกดบันทึก
if (isset($_POST['save'])) {
    try {
        // เริ่ม transaction
        $conn->autocommit(false);
        
        // อัพเดทข้อมูลโครงการหลัก (รวม mainprojectid)
        $stmt = $conn->prepare("UPDATE projects SET Projectname=?, Projectcode=?, Agencyname=?, Responsibleperson=?, Province=?, Projectyear=?, Strategyid=?, MainprojectID=?, TargetArea=? WHERE Projectid=?");
        $stmt->bind_param("sssssssisi", 
            $_POST['projectname'], 
            $_POST['projectcode'], 
            $_POST['agencyname'], 
            $_POST['responsibleperson'], 
            $_POST['province'], 
            $_POST['ProjectYear'], 
            $_POST['StrategyID'], 
            $_POST['MainProjectID'], 
            $_POST['targetarea'],
            $id
        );
        $stmt->execute();
        
        // ลบข้อมูลเดิมทั้งหมดที่เกี่ยวข้องกับโครงการนี้
        $conn->query("DELETE FROM projecttargetcounts WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectvillages WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectschools WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectuniversities WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectlocaladmins WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectothers WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectnetworks WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectenterprises WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectproducts WHERE ProjectID = $id");
        $conn->query("DELETE FROM projectsroi WHERE ProjectID = $id");
        $conn->query("DELETE FROM budgetitems WHERE ProjectID = $id");
        $conn->query("DELETE FROM project_indicators WHERE ProjectID = $id");
        
        // บันทึกกลุ่มเป้าหมายใหม่
        if (isset($_POST['target_groups']) && is_array($_POST['target_groups'])) {
            $stmt = $conn->prepare("INSERT INTO projecttargetcounts (ProjectID, GroupID, TargetCount) VALUES (?,?,?)");
            foreach ($_POST['target_groups'] as $group_id) {
                $target_count = isset($_POST['target_count_' . $group_id]) ? (int)$_POST['target_count_' . $group_id] : 0;
                $stmt->bind_param("iii", $id, $group_id, $target_count);
                $stmt->execute();
            }
        }
        
        // บันทึกหมู่บ้านใหม่
        if (isset($_POST['village_names']) && is_array($_POST['village_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectvillages (ProjectID, VillageName, Moo, SubDistrict, District, Province, Community) VALUES (?,?,?,?,?,?,?)");
            for ($i = 0; $i < count($_POST['village_names']); $i++) {
                if (!empty($_POST['village_names'][$i])) {
                    $village_moo = $_POST['village_moo'][$i] ?? '';
                    $village_subdistrict = $_POST['village_subdistrict'][$i] ?? '';
                    $village_district = $_POST['village_district'][$i] ?? '';
                    $village_province = $_POST['village_province'][$i] ?? '';
                    $village_community = $_POST['village_community'][$i] ?? '';
                    
                    $stmt->bind_param("issssss", 
                        $id,
                        $_POST['village_names'][$i],
                        $village_moo,
                        $village_subdistrict,
                        $village_district,
                        $village_province,
                        $village_community
                    );
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกโรงเรียนใหม่
        if (isset($_POST['school_names']) && is_array($_POST['school_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectschools (ProjectID, SchoolName) VALUES (?,?)");
            foreach ($_POST['school_names'] as $school_name) {
                if (!empty($school_name)) {
                    $stmt->bind_param("is", $id, $school_name);
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกเครือข่ายใหม่
        if (isset($_POST['network_names']) && is_array($_POST['network_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectnetworks (ProjectID, NetworkName) VALUES (?,?)");
            foreach ($_POST['network_names'] as $network_name) {
                if (!empty($network_name)) {
                    $stmt->bind_param("is", $id, $network_name);
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกวิสาหกิจ/ผู้ประกอบการใหม่
        if (isset($_POST['enterprise_names']) && is_array($_POST['enterprise_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectenterprises (ProjectID, EnterpriseName, EnterpriseType) VALUES (?,?,?)");
            for ($i = 0; $i < count($_POST['enterprise_names']); $i++) {
                if (!empty($_POST['enterprise_names'][$i]) && !empty($_POST['enterprise_types'][$i])) {
                    $stmt->bind_param("iss", 
                        $id,
                        $_POST['enterprise_names'][$i],
                        $_POST['enterprise_types'][$i]
                    );
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกมหาวิทยาลัยใหม่
        if (isset($_POST['university_names']) && is_array($_POST['university_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectuniversities (ProjectID, UniversityName) VALUES (?,?)");
            foreach ($_POST['university_names'] as $university_name) {
                if (!empty($university_name)) {
                    $stmt->bind_param("is", $id, $university_name);
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกองค์กรปกครองส่วนท้องถิ่นใหม่
        if (isset($_POST['localadmin_names']) && is_array($_POST['localadmin_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectlocaladmins (ProjectID, AdminName, AdminType) VALUES (?,?,?)");
            for ($i = 0; $i < count($_POST['localadmin_names']); $i++) {
                if (!empty($_POST['localadmin_names'][$i])) {
                    $localadmin_type = $_POST['localadmin_types'][$i] ?? '';
                    $stmt->bind_param("iss", 
                        $id,
                        $_POST['localadmin_names'][$i],
                        $localadmin_type
                    );
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกองค์กรอื่นๆ ใหม่
        if (isset($_POST['other_names']) && is_array($_POST['other_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectothers (ProjectID, OrganizationName, OrganizationType) VALUES (?,?,?)");
            for ($i = 0; $i < count($_POST['other_names']); $i++) {
                if (!empty($_POST['other_names'][$i])) {
                    $other_type = $_POST['other_types'][$i] ?? '';
                    $stmt->bind_param("iss", 
                        $id,
                        $_POST['other_names'][$i],
                        $other_type
                    );
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกผลิตภัณฑ์ใหม่
        if (isset($_POST['product_names']) && is_array($_POST['product_names'])) {
            $stmt = $conn->prepare("INSERT INTO projectproducts (ProjectID, ProductName, ProductType, Description, StandardNumber) VALUES (?,?,?,?,?)");
            for ($i = 0; $i < count($_POST['product_names']); $i++) {
                if (!empty($_POST['product_names'][$i])) {
                    $product_type = $_POST['product_types'][$i] ?? '';
                    $product_description = $_POST['product_descriptions'][$i] ?? '';
                    $standard_number = $_POST['product_standards'][$i] ?? '';
                    
                    $stmt->bind_param("issss", 
                        $id,
                        $_POST['product_names'][$i],
                        $product_type,
                        $product_description,
                        $standard_number
                    );
                    $stmt->execute();
                }
            }
        }
        
        // บันทึก SROI ใหม่
        if (isset($_POST['sroi_results']) && is_array($_POST['sroi_results'])) {
            $stmt = $conn->prepare("INSERT INTO projectsroi (ProjectID, SROIResult, Description) VALUES (?,?,?)");
            for ($i = 0; $i < count($_POST['sroi_results']); $i++) {
                if (!empty($_POST['sroi_results'][$i]) && is_numeric($_POST['sroi_results'][$i])) {
                    $sroi_value = (float)$_POST['sroi_results'][$i];
                    $description = $_POST['sroi_descriptions'][$i] ?? '';
                    
                    $stmt->bind_param("ids", 
                        $id,
                        $sroi_value,
                        $description
                    );
                    $stmt->execute();
                }
            }
        }
        
        // บันทึกตัวชี้วัดใหม่ (ใช้ API ใหม่)
        if (isset($_POST['indicator_values']) && is_array($_POST['indicator_values'])) {
            $stmt_indicator = $conn->prepare("INSERT INTO project_indicators (ProjectID, IndicatorID, Value) VALUES (?,?,?)");
            $stmt_detail = $conn->prepare("INSERT INTO project_indicator_details (ProjectIndicatorID, DetailText) VALUES (?,?)");

            foreach ($_POST['indicator_values'] as $indicator_id => $values) {
                if (is_array($values)) {
                    $details = isset($_POST['indicator_details'][$indicator_id]) ? $_POST['indicator_details'][$indicator_id] : [];
                    
                    for ($i = 0; $i < count($values); $i++) {
                        if (!empty($values[$i]) && is_numeric($values[$i])) {
                            $value = (float)$values[$i];
                            
                            // บันทึกค่าตัวชี้วัด
                            $stmt_indicator->bind_param("iid", $id, $indicator_id, $value);
                            $stmt_indicator->execute();
                            
                            $projectIndicatorId = $conn->insert_id;
                            
                            // บันทึกรายละเอียดเพิ่มเติม
                            if (isset($details[$i]) && is_array($details[$i])) {
                                foreach ($details[$i] as $detail) {
                                    $detailText = trim($detail);
                                    if (!empty($detailText)) {
                                        $stmt_detail->bind_param("is", $projectIndicatorId, $detailText);
                                        $stmt_detail->execute();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // บันทึกข้อมูลสำเร็จ
            $indicators_saved = true;
        }

        // บันทึกงบประมาณใหม่
        if (isset($_POST['budget_types']) && is_array($_POST['budget_types'])) {
            $stmt = $conn->prepare("INSERT INTO budgetitems (ProjectID, BudgetType, RequestedAmount, ApprovedAmount, Remark) VALUES (?,?,?,?,?)");
            for ($i = 0; $i < count($_POST['budget_types']); $i++) {
                if (!empty($_POST['budget_types'][$i])) {
                    $budget_type = $_POST['budget_types'][$i];
                    $requested_amount = isset($_POST['requested_amounts'][$i]) ? (float)$_POST['requested_amounts'][$i] : 0;
                    $approved_amount = isset($_POST['approved_amounts'][$i]) ? (float)$_POST['approved_amounts'][$i] : 0;
                    $remark = $_POST['budget_remarks'][$i] ?? '';
                    
                    $stmt->bind_param("isdds", 
                        $id,
                        $budget_type,
                        $requested_amount,
                        $approved_amount,
                        $remark
                    );
                    $stmt->execute();
                }
            }
        }
        
        // commit transaction
        $conn->commit();
        
        // แสดงข้อความสำเร็จ
        $success_message = "✅ แก้ไขข้อมูลเรียบร้อยแล้ว";
        if (isset($indicators_saved) && $indicators_saved) {
            $success_message .= " (รวมข้อมูลตัวชี้วัด)";
        }
        
        // เตรียมข้อความสำหรับ SweetAlert2
        $alert_script = "
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'สำเร็จ!',
                text: '$success_message',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            });
        });
        ";
        
    } catch (Exception $e) {
        // rollback transaction
        $conn->rollback();
        $error_message = htmlspecialchars($e->getMessage());
        
        // เตรียมข้อความสำหรับ SweetAlert2
        $alert_script = "
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'เกิดข้อผิดพลาด: $error_message',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        });
        ";
    }
}

// ดึงข้อมูลเดิมมาแสดง
$result = $conn->query("SELECT * FROM projects WHERE ProjectID = $id");
$row = $result->fetch_assoc();

// ตรวจสอบว่าข้อมูลมีอยู่หรือไม่
if (!$row) {
    $alert_script = "
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'ไม่พบข้อมูล!',
            text: 'ไม่พบข้อมูลโครงการที่ระบุ',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        }).then(function() {
            window.location.href = 'projects_list.php';
        });
    });
    ";
    $should_redirect = true;
}

// ดึงข้อมูลกลุ่มเป้าหมายที่เลือก
$selected_targets = [];
$target_counts = [];
$target_result = $conn->query("SELECT ptc.groupid, ptc.targetcount FROM projecttargetcounts ptc WHERE ptc.ProjectID = $id");
while ($target_row = $target_result->fetch_assoc()) {
    $selected_targets[] = $target_row['groupid'];
    $target_counts[$target_row['groupid']] = $target_row['targetcount'];
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

// ดึงข้อมูลมหาวิทยาลัย
$universities = [];
$university_result = $conn->query("SELECT * FROM projectuniversities WHERE ProjectID = $id");
while ($university_row = $university_result->fetch_assoc()) {
    $universities[] = $university_row;
}

// ดึงข้อมูลองค์กรปกครองส่วนท้องถิ่น
$localadmins = [];
$localadmin_result = $conn->query("SELECT * FROM projectlocaladmins WHERE ProjectID = $id");
while ($localadmin_row = $localadmin_result->fetch_assoc()) {
    $localadmins[] = $localadmin_row;
}

// ดึงข้อมูลองค์กรอื่นๆ
$others = [];
$others_result = $conn->query("SELECT * FROM projectothers WHERE ProjectID = $id");
while ($others_row = $others_result->fetch_assoc()) {
    $others[] = $others_row;
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

// ดึงข้อมูล SROI
$sroi_items = [];
$sroi_result = $conn->query("SELECT * FROM projectsroi WHERE ProjectID = $id ORDER BY SROIResult DESC");
while ($sroi_row = $sroi_result->fetch_assoc()) {
    $sroi_items[] = $sroi_row;
}

// ดึงข้อมูลงบประมาณ
$budget_items = [];
$budget_result = $conn->query("SELECT * FROM budgetitems WHERE ProjectID = $id ORDER BY BudgetID");
while ($budget_row = $budget_result->fetch_assoc()) {
    $budget_items[] = $budget_row;
}

// ดึงข้อมูลตัวชี้วัดที่เกี่ยวข้องกับโครงการและข้อมูลที่บันทึกไว้
$available_indicators = [];
$saved_indicators = [];
$project_year = $row['ProjectYear'] ?? null;

if (!empty($row['ProjectYear'])) {
    // ดึงตัวชี้วัดที่เกี่ยวข้องโดยกรองแค่ปี
    $available_result = $conn->query("
        SELECT i.IndicatorID,
               i.IndicatorName,
               i.Unit,
               i.Description,
               i.Year
        FROM indicators i 
        WHERE i.Year = " . $row['ProjectYear'] . "
          AND (i.IsActive IS NULL OR i.IsActive = 1)
        ORDER BY i.IndicatorID ASC
    ");
    
    while ($available_row = $available_result->fetch_assoc()) {
        $available_indicators[] = $available_row;
    }

    // ดึงข้อมูลตัวชี้วัดที่บันทึกไว้แล้ว จัดกลุ่มตาม IndicatorID
    $saved_result = $conn->query("
        SELECT pi.ID as ProjectIndicatorID,
               pi.IndicatorID, 
               pi.Value,
               GROUP_CONCAT(pid.DetailText ORDER BY pid.DetailID SEPARATOR '|||') as Details
        FROM project_indicators pi 
        LEFT JOIN project_indicator_details pid ON pi.ID = pid.ProjectIndicatorID
        WHERE pi.ProjectID = $id
        GROUP BY pi.ID
        ORDER BY pi.IndicatorID, pi.Value
    ");
    
    while ($saved_row = $saved_result->fetch_assoc()) {
        $indicator_id = $saved_row['IndicatorID'];
        $details = [];
        if (!empty($saved_row['Details'])) {
            $details = explode('|||', $saved_row['Details']);
        }
        
        if (!isset($saved_indicators[$indicator_id])) {
            $saved_indicators[$indicator_id] = ['Values' => []];
        }
        
        $saved_indicators[$indicator_id]['Values'][] = [
            'Value' => $saved_row['Value'],
            'Details' => $details
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขโครงการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        max-width: 800px;
        margin: 2rem auto;
    }

    .section-header {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 15px;
        padding-bottom: 10px;
        font-weight: bold;
        color: #495057;
    }
    </style>
</head>

<body>
    <?php if (!$should_redirect): ?>
    <div class="container">
        <div class="card shadow">
        <div class="card-header bg-warning text-dark"><i class="fas fa-edit"></i> แก้ไขข้อมูลโครงการ</div>
        <div class="card-body">
            <form method="post">
                <!-- ข้อมูลโครงการหลัก -->
                <div class="section-header"><i class="fas fa-folder-open"></i> ข้อมูลโครงการหลัก</div>
                
                <!-- ปีโครงการ -->
                <div class="mb-3">
                    <label class="form-label">ปีโครงการ</label>
                    <select class="form-select" id="ProjectYear" name="ProjectYear" required>
                        <option value="">-- เลือกปี --</option>
                        <?php
                        $current_year = date('Y') + 543; // ปี พ.ศ. ปัจจุบัน
                        for ($year = 2565; $year <= $current_year + 5; $year++) {
                            $selected = ($row['ProjectYear'] ?? '') == $year ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">ชื่อโครงการ (โครงการที่ได้รับงบประมาณ)</label>
                    <input name="projectname" class="form-control" value="<?= htmlspecialchars($row['ProjectName']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <!-- <label class="form-label">รหัสโครงการ (ตามเล่มแผนปฏิบัติราชการ)</label> -->
                    <input name="projectcode" class="form-control" value="<?= htmlspecialchars($row['ProjectCode']) ?>" readonly hidden>
                </div>
                
                <!-- โครงการหลัก -->
                <div class="mb-3">
                    <label class="form-label">โครงการหลัก (ตาม ทปอ.)</label>
                    <select name="MainProjectID" class="form-select" required>
                        <option value="">-- เลือกโครงการหลัก --</option>
                        <?php
                        $main_projects = $conn->query("SELECT MainProjectID, MainProjectName, MainProjectCode FROM mainprojects ORDER BY MainProjectID");
                        if ($main_projects && $main_projects->num_rows > 0) {
                            while ($main_row = $main_projects->fetch_assoc()) {
                                $selected = ($row['MainProjectID'] ?? '') == $main_row['MainProjectID'] ? 'selected' : '';
                                echo "<option value='{$main_row['MainProjectID']}' $selected>";
                                echo htmlspecialchars($main_row['MainProjectCode'] . ' - ' . $main_row['MainProjectName']);
                                echo "</option>";
                            }
                        }
                        ?>
                    </select>
                    <div class="form-text">
                        ไม่มีโครงการหลักที่ต้องการ? <a href="main_projects.php" target="_blank">จัดการโครงการหลัก</a>
                    </div>
                </div>

                <!-- ยุทธศาสตร์ -->
                <div class="mb-3">
                    <label class="form-label">ยุทธศาสตร์</label>
                    <?php
                    // ดึงข้อมูลยุทธศาสตร์จากฐานข้อมูล
                    $strategies = [];
                    $strategy_result = $conn->query("SELECT StrategyID, StrategyName FROM strategies ORDER BY StrategyName");
                    if ($strategy_result && $strategy_result->num_rows > 0) {
                        while ($strategy_row = $strategy_result->fetch_assoc()) {
                            $strategies[] = $strategy_row;
                        }
                    }
                    ?>
                    <select name="StrategyID" class="form-select" required>
                        <option value="">-- เลือกยุทธศาสตร์ --</option>
                        <?php foreach ($strategies as $strategy): ?>
                            <option value="<?= $strategy['StrategyID'] ?>" <?= ($row['StrategyID'] ?? '') == $strategy['StrategyID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($strategy['StrategyName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ผู้รับผิดชอบ -->
                <div class="mb-3">
                    <label class="form-label">ผู้รับผิดชอบโครงการ</label>
                    <input name="responsibleperson" class="form-control" value="<?= htmlspecialchars($row['ResponsiblePerson'] ?? '') ?>" placeholder="ผู้รับผิดชอบโครงการ">
                </div>
                <div class="mb-3">
                    <label class="form-label">หน่วยงาน</label>
                    <input name="agencyname" class="form-control" value="<?= htmlspecialchars($row['AgencyName'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <!-- จังหวัดซ่อนไว้เป็น ราชบุรี -->
                    <input type="hidden" name="province" class="form-control" value="ราชบุรี">
                </div>

                <div class="section-header mt-4">พื้นที่ดำเนินโครงการ</div>
                    <!-- <label class="form-label">พื้นที่ดำเนินการ</label> -->
                    <!-- <textarea name="targetarea" class="form-control" rows="3" placeholder="เช่น หมู่บ้านห้วยผาก อำเภอสวนผึ้ง จังหวัดราชบุรี"><?= htmlspecialchars($row['TargetArea'] ?? '') ?></textarea> -->

                <!-- หมู่บ้าน/ชุมชน -->
                <div class="section-header mt-4"><i class="fas fa-home"></i> หมู่บ้าน/ชุมชน</div>
                <div id="villages-container">
                    <?php if (empty($villages)): ?>
                    <div class="village-item border p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">ชื่อหมู่บ้าน</label>
                                <input name="village_names[]" class="form-control" placeholder="เช่น บ้านหนองน้ำ">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">ชุมชน</label>
                                <input name="village_community[]" class="form-control" placeholder="เช่น ชุมชนบ้านบ่อ">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">หมู่ที่</label>
                                <input name="village_moo[]" class="form-control" placeholder="เช่น 3">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">ตำบล</label>
                                <input name="village_subdistrict[]" class="form-control" placeholder="เช่น สวนผึ้ง">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">อำเภอ</label>
                                <input name="village_district[]" class="form-control" placeholder="เช่น สวนผึ้ง">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">จังหวัด</label>
                                <input name="village_province[]" class="form-control" placeholder="เช่น ราชบุรี">
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($villages as $village): ?>
                    <div class="village-item border p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">ชื่อหมู่บ้าน</label>
                                <input name="village_names[]" class="form-control" value="<?= htmlspecialchars($village['VillageName']) ?>" placeholder="เช่น บ้านหนองน้ำ">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">ชุมชน</label>
                                <input name="village_community[]" class="form-control" value="<?= htmlspecialchars($village['Community']) ?>" placeholder="เช่น ชุมชนบ้านบ่อ">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">หมู่ที่</label>
                                <input name="village_moo[]" class="form-control" value="<?= htmlspecialchars($village['Moo']) ?>" placeholder="เช่น 3">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">ตำบล</label>
                                <input name="village_subdistrict[]" class="form-control" value="<?= htmlspecialchars($village['Subdistrict']) ?>" placeholder="เช่น สวนผึ้ง">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">อำเภอ</label>
                                <input name="village_district[]" class="form-control" value="<?= htmlspecialchars($village['District']) ?>" placeholder="เช่น สวนผึ้ง">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">จังหวัด</label>
                                <input name="village_province[]" class="form-control" value="<?= htmlspecialchars($village['Province']) ?>" placeholder="เช่น ราชบุรี">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVillage(this)">ลบ</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addVillage()">+ เพิ่มหมู่บ้าน</button>

                <!-- วิสาหกิจ/ผู้ประกอบการ -->
                <div class="section-header mt-4"><i class="fas fa-store"></i> กลุ่มวิสาหกิจ/ผู้ประกอบการ</div>
                <div id="enterprises-container">
                    <?php if (empty($enterprises)): ?>
                    <div class="enterprise-item row mb-2">
                        <div class="col-md-8">
                            <input name="enterprise_names[]" class="form-control" placeholder="ชื่อวิสาหกิจ/ผู้ประกอบการ">
                        </div>
                        <div class="col-md-4">
                            <select name="enterprise_types[]" class="form-control">
                                <option value="">-- เลือกประเภท --</option>
                                <option value="วิสาหกิจ">วิสาหกิจ</option>
                                <option value="ผู้ประกอบการ">ผู้ประกอบการ</option>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($enterprises as $enterprise): ?>
                    <div class="enterprise-item row mb-2">
                        <div class="col-md-8">
                            <input name="enterprise_names[]" class="form-control" value="<?= htmlspecialchars($enterprise['EnterpriseName']) ?>" placeholder="ชื่อวิสาหกิจ/ผู้ประกอบการ">
                        </div>
                        <div class="col-md-4">
                            <select name="enterprise_types[]" class="form-control">
                                <option value="">-- เลือกประเภท --</option>
                                <option value="วิสาหกิจ" <?= $enterprise['EnterpriseType'] == 'วิสาหกิจ' ? 'selected' : '' ?>>วิสาหกิจ</option>
                                <option value="ผู้ประกอบการ" <?= $enterprise['EnterpriseType'] == 'ผู้ประกอบการ' ? 'selected' : '' ?>>ผู้ประกอบการ</option>
                            </select>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addEnterprise()">+ เพิ่มวิสาหกิจ/ผู้ประกอบการ</button>

                <!-- โรงเรียน -->
                <div class="section-header mt-4"><i class="fas fa-school"></i> โรงเรียน</div>
                <div id="schools-container">
                    <?php if (empty($schools)): ?>
                    <div class="mb-2">
                        <input name="school_names[]" class="form-control" placeholder="ชื่อโรงเรียน">
                    </div>
                    <?php else: ?>
                    <?php foreach ($schools as $school): ?>
                    <div class="mb-2">
                        <input name="school_names[]" class="form-control" value="<?= htmlspecialchars($school['SchoolName']) ?>" placeholder="ชื่อโรงเรียน">
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addSchool()">+ เพิ่มโรงเรียน</button>

                <!-- มหาวิทยาลัย -->
                <div class="section-header mt-4"><i class="fas fa-university"></i> มหาวิทยาลัย</div>
                <div id="universities-container">
                    <?php if (empty($universities)): ?>
                    <div class="university-item row mb-2">
                        <div class="col-md-6">
                            <input name="university_names[]" class="form-control" placeholder="ชื่อมหาวิทยาลัย">
                        </div>
                        <div class="col-md-3">
                            <select name="university_types[]" class="form-control">
                                <option value="">-- เลือกประเภท --</option>
                                <option value="มหาวิทยาลัยรัฐ">มหาวิทยาลัยรัฐ</option>
                                <option value="มหาวิทยาลัยเอกชน">มหาวิทยาลัยเอกชน</option>
                                <option value="ราชภัฏ">มหาวิทยาลัยราชภัฏ</option>
                                <option value="เทคโนโลยีราชมงคล">มหาวิทยาลัยเทคโนโลยีราชมงคล</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input name="university_collaborations[]" class="form-control" placeholder="รูปแบบความร่วมมือ">
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($universities as $university): ?>
                    <div class="university-item row mb-2">
                        <div class="col-md-6">
                            <input name="university_names[]" class="form-control" value="<?= htmlspecialchars($university['UniversityName']) ?>" placeholder="ชื่อมหาวิทยาลัย">
                        </div>
                        <div class="col-md-3">
                            <select name="university_types[]" class="form-control">
                                <option value="">-- เลือกประเภท --</option>
                                <option value="มหาวิทยาลัยรัฐ" <?= ($university['UniversityType'] ?? '') == 'มหาวิทยาลัยรัฐ' ? 'selected' : '' ?>>มหาวิทยาลัยรัฐ</option>
                                <option value="มหาวิทยาลัยเอกชน" <?= ($university['UniversityType'] ?? '') == 'มหาวิทยาลัยเอกชน' ? 'selected' : '' ?>>มหาวิทยาลัยเอกชน</option>
                                <option value="ราชภัฏ" <?= ($university['UniversityType'] ?? '') == 'ราชภัฏ' ? 'selected' : '' ?>>มหาวิทยาลัยราชภัฏ</option>
                                <option value="เทคโนโลยีราชมงคล" <?= ($university['UniversityType'] ?? '') == 'เทคโนโลยีราชมงคล' ? 'selected' : '' ?>>มหาวิทยาลัยเทคโนโลยีราชมงคล</option>
                                <option value="อื่นๆ" <?= ($university['UniversityType'] ?? '') == 'อื่นๆ' ? 'selected' : '' ?>>อื่นๆ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input name="university_collaborations[]" class="form-control" value="<?= htmlspecialchars($university['Collaboration'] ?? '') ?>" placeholder="รูปแบบความร่วมมือ">
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addUniversity()">+ เพิ่มมหาวิทยาลัย</button>

                <!-- อบต./องค์กรปกครองส่วนท้องถิ่น -->
                <div class="section-header mt-4"><i class="fas fa-landmark"></i> องค์กรปกครองส่วนท้องถิ่น</div>
                <div id="localadmins-container">
                    <?php if (empty($localadmins)): ?>
                    <div class="localadmin-item row mb-2">
                        <div class="col-md-4">
                            <input name="localadmin_names[]" class="form-control" placeholder="ชื่อองค์กร">
                        </div>
                        <div class="col-md-2">
                            <select name="localadmin_types[]" class="form-control">
                                <option value="">-- ประเภท --</option>
                                <option value="อบต.">อบต.</option>
                                <option value="เทศบาล">เทศบาล</option>
                                <option value="อปท.อื่นๆ">อปท.อื่นๆ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input name="localadmin_districts[]" class="form-control" placeholder="อำเภอ">
                        </div>
                        <div class="col-md-3">
                            <input name="localadmin_supports[]" class="form-control" placeholder="รูปแบบการสนับสนุน">
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($localadmins as $localadmin): ?>
                    <div class="localadmin-item row mb-2">
                        <div class="col-md-4">
                            <input name="localadmin_names[]" class="form-control" value="<?= htmlspecialchars($localadmin['AdminName']) ?>" placeholder="ชื่อองค์กร">
                        </div>
                        <div class="col-md-2">
                            <select name="localadmin_types[]" class="form-control">
                                <option value="">-- ประเภท --</option>
                                <option value="อบต." <?= $localadmin['AdminType'] == 'อบต.' ? 'selected' : '' ?>>อบต.</option>
                                <option value="เทศบาล" <?= $localadmin['AdminType'] == 'เทศบาล' ? 'selected' : '' ?>>เทศบาล</option>
                                <option value="อปท.อื่นๆ" <?= $localadmin['AdminType'] == 'อปท.อื่นๆ' ? 'selected' : '' ?>>อปท.อื่นๆ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input name="localadmin_districts[]" class="form-control" value="<?= htmlspecialchars($localadmin['District'] ?? '') ?>" placeholder="อำเภอ">
                        </div>
                        <div class="col-md-3">
                            <input name="localadmin_supports[]" class="form-control" value="<?= htmlspecialchars($localadmin['Support'] ?? '') ?>" placeholder="รูปแบบการสนับสนุน">
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addLocalAdmin()">+ เพิ่มองค์กรปกครองส่วนท้องถิ่น</button>

                <!-- องค์กรอื่นๆ -->
                <div class="section-header mt-4"><i class="fas fa-building"></i> องค์กรอื่นๆ ที่เข้าร่วม</div>
                <div id="others-container">
                    <?php if (empty($others)): ?>
                    <div class="others-item row mb-2">
                        <div class="col-md-4">
                            <input name="others_names[]" class="form-control" placeholder="ชื่อองค์กร">
                        </div>
                        <div class="col-md-3">
                            <select name="others_types[]" class="form-control">
                                <option value="">-- ประเภท --</option>
                                <option value="หน่วยงานรัฐ">หน่วยงานรัฐ</option>
                                <option value="เอกชน">เอกชน</option>
                                <option value="รัฐวิสาหกิจ">รัฐวิสาหกิจ</option>
                                <option value="NGO">องค์กรพัฒนาเอกชน (NGO)</option>
                                <option value="มูลนิธิ">มูลนิธิ</option>
                                <option value="สมาคม">สมาคม</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input name="others_roles[]" class="form-control" placeholder="บทบาท">
                        </div>
                        <div class="col-md-3">
                            <input name="others_descriptions[]" class="form-control" placeholder="รายละเอียด">
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($others as $other): ?>
                    <div class="others-item row mb-2">
                        <div class="col-md-4">
                            <input name="others_names[]" class="form-control" value="<?= htmlspecialchars($other['OrganizationName']) ?>" placeholder="ชื่อองค์กร">
                        </div>
                        <div class="col-md-3">
                            <select name="others_types[]" class="form-control">
                                <option value="">-- ประเภท --</option>
                                <option value="หน่วยงานรัฐ" <?= $other['OrganizationType'] == 'หน่วยงานรัฐ' ? 'selected' : '' ?>>หน่วยงานรัฐ</option>
                                <option value="เอกชน" <?= $other['OrganizationType'] == 'เอกชน' ? 'selected' : '' ?>>เอกชน</option>
                                <option value="รัฐวิสาหกิจ" <?= $other['OrganizationType'] == 'รัฐวิสาหกิจ' ? 'selected' : '' ?>>รัฐวิสาหกิจ</option>
                                <option value="NGO" <?= $other['OrganizationType'] == 'NGO' ? 'selected' : '' ?>>องค์กรพัฒนาเอกชน (NGO)</option>
                                <option value="มูลนิธิ" <?= $other['OrganizationType'] == 'มูลนิธิ' ? 'selected' : '' ?>>มูลนิธิ</option>
                                <option value="สมาคม" <?= $other['OrganizationType'] == 'สมาคม' ? 'selected' : '' ?>>สมาคม</option>
                                <option value="อื่นๆ" <?= $other['OrganizationType'] == 'อื่นๆ' ? 'selected' : '' ?>>อื่นๆ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input name="others_roles[]" class="form-control" value="<?= htmlspecialchars($other['Role'] ?? '') ?>" placeholder="บทบาท">
                        </div>
                        <div class="col-md-3">
                            <input name="others_descriptions[]" class="form-control" value="<?= htmlspecialchars($other['Description'] ?? '') ?>" placeholder="รายละเอียด">
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addOthers()">+ เพิ่มองค์กรอื่นๆ</button>

                <!-- เครือข่าย -->
                <div class="section-header mt-4"><i class="fas fa-network-wired"></i> เครือข่ายร่วมดำเนินการ</div>
                <div id="networks-container">
                    <?php if (empty($networks)): ?>
                    <div class="mb-2">
                        <input name="network_names[]" class="form-control" placeholder="ชื่อเครือข่าย">
                    </div>
                    <?php else: ?>
                    <?php foreach ($networks as $network): ?>
                    <div class="mb-2">
                        <input name="network_names[]" class="form-control" value="<?= htmlspecialchars($network['NetworkName']) ?>" placeholder="ชื่อเครือข่าย">
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addNetwork()">+ เพิ่มเครือข่าย</button>

                <!-- กลุ่มเป้าหมาย -->
                <div class="section-header mt-4"><i class="fas fa-users"></i> กลุ่มเป้าหมาย</div>
                <?php
                // ดึงกลุ่มเป้าหมายทั้งหมด
                $target_groups = [];
                $result = $conn->query("SELECT GroupID, GroupName FROM targetgroups ORDER BY GroupName");
                if ($result && $result->num_rows > 0) {
                    while ($tg_row = $result->fetch_assoc()) {
                        $target_groups[] = $tg_row;
                    }
                }
                ?>
                
                <div class="row">
                    <?php foreach ($target_groups as $group): ?>
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="target_groups[]" 
                                   value="<?= $group['GroupID'] ?>" id="group_<?= $group['GroupID'] ?>"
                                   <?= in_array($group['GroupID'], $selected_targets) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="group_<?= $group['GroupID'] ?>">
                                <?= htmlspecialchars($group['GroupName']) ?>
                            </label>
                        </div>
                        <input type="number" name="target_count_<?= $group['GroupID'] ?>" 
                               class="form-control form-control-sm mt-1" placeholder="จำนวน (คน)" min="0"
                               value="<?= isset($target_counts[$group['GroupID']]) ? $target_counts[$group['GroupID']] : '' ?>">
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- ผลิตภัณฑ์ -->
                <div class="section-header mt-4"><i class="fas fa-box"></i> ผลิตภัณฑ์</div>
                <div id="products-container">
                    <?php if (empty($products)): ?>
                    <div class="product-item row mb-2">
                        <div class="col-md-4">
                            <input name="product_names[]" class="form-control" placeholder="ชื่อผลิตภัณฑ์">
                        </div>
                        <div class="col-md-3">
                            <input name="product_types[]" class="form-control" placeholder="ประเภท (เช่น อาหาร)">
                        </div>
                        <div class="col-md-3">
                            <input name="product_standards[]" class="form-control" placeholder="เลขมาตรฐาน (เช่น มอก.1234)">
                        </div>
                        <div class="col-md-2">
                            <input name="product_descriptions[]" class="form-control" placeholder="รายละเอียด">
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <div class="product-item row mb-2">
                        <div class="col-md-4">
                            <input name="product_names[]" class="form-control" value="<?= htmlspecialchars($product['ProductName']) ?>" placeholder="ชื่อผลิตภัณฑ์">
                        </div>
                        <div class="col-md-3">
                            <input name="product_types[]" class="form-control" value="<?= htmlspecialchars($product['ProductType']) ?>" placeholder="ประเภท (เช่น อาหาร)">
                        </div>
                        <div class="col-md-3">
                            <input name="product_standards[]" class="form-control" value="<?= htmlspecialchars($product['StandardNumber'] ?? '') ?>" placeholder="เลขมาตรฐาน (เช่น มอก.1234)">
                        </div>
                        <div class="col-md-2">
                            <input name="product_descriptions[]" class="form-control" value="<?= htmlspecialchars($product['Description']) ?>" placeholder="รายละเอียด">
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addProduct()">+ เพิ่มผลิตภัณฑ์</button>
                
                <!-- SROI -->
                <div class="section-header mt-4"><i class="fas fa-coins"></i> SROI - ผลตอบแทนทางสังคม</div>
                <div id="sroi-container">
                    <?php if (empty($sroi_items)): ?>
                    <div class="sroi-item row mb-2">
                        <div class="col-md-4">
                            <input name="sroi_results[]" type="number" step="0.01" class="form-control" placeholder="ค่า SROI">
                        </div>
                        <div class="col-md-8">
                            <input name="sroi_descriptions[]" class="form-control" placeholder="รายละเอียด (ถ้ามี)">
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($sroi_items as $sroi): ?>
                    <div class="sroi-item row mb-2">
                        <div class="col-md-4">
                            <input name="sroi_results[]" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($sroi['SROIResult']) ?>" placeholder="ค่า SROI">
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input name="sroi_descriptions[]" class="form-control" value="<?= htmlspecialchars($sroi['Description'] ?? '') ?>" placeholder="รายละเอียด (ถ้ามี)">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSROI(this)">ลบ</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addSROI()">+ เพิ่มข้อมูล SROI</button>

                <!-- ตัวชี้วัด -->
                <div class="section-header mt-4" id="indicators-section"><i class="fas fa-chart-bar"></i> ตัวชี้วัดโครงการ</div>
                <div id="indicators-filter">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">ปีโครงการ</label>
                            <select id="indicator-year" class="form-select" disabled>
                                <option value="<?= htmlspecialchars($row['ProjectYear'] ?? '') ?>"><?= htmlspecialchars($row['ProjectYear'] ?? '') ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ยุทธศาสตร์</label>
                            <select id="indicator-strategy" class="form-select" disabled>
                                <option value="<?= htmlspecialchars($row['StrategyID'] ?? '') ?>">
                                    <?php
                                    if ($row['StrategyID']) {
                                        $strategy_name_result = $conn->query("SELECT StrategyName FROM strategies WHERE StrategyID = " . $row['StrategyID']);
                                        if ($strategy_name_result && $strategy_name_result->num_rows > 0) {
                                            $strategy_name = $strategy_name_result->fetch_assoc();
                                            echo htmlspecialchars($strategy_name['StrategyName']);
                                        }
                                    } else {
                                        echo '-- ไม่ระบุ --';
                                    }
                                    ?>
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">โครงการหลัก</label>
                            <select id="indicator-main-project" class="form-select" disabled>
                                <option value="<?= htmlspecialchars($row['MainProjectID'] ?? '') ?>">
                                    <?php
                                    if ($row['MainProjectID']) {
                                        $main_project_name_result = $conn->query("SELECT MainProjectName FROM mainprojects WHERE MainProjectID = " . $row['MainProjectID']);
                                        if ($main_project_name_result && $main_project_name_result->num_rows > 0) {
                                            $main_project_name = $main_project_name_result->fetch_assoc();
                                            echo htmlspecialchars($main_project_name['MainProjectName']);
                                        }
                                    } else {
                                        echo '-- ไม่ระบุ --';
                                    }
                                    ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> ตัวชี้วัดจะแสดงตามปีที่เลือก
                    </div>
                </div>
                <div id="indicators-container">
                    <?php if (empty($available_indicators)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> ไม่พบตัวชี้วัดที่เกี่ยวข้องกับโครงการนี้
                            <br><small>กรุณาตรวจสอบการตั้งค่าปีโครงการ ยุทธศาสตร์ และโครงการหลัก</small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> พบตัวชี้วัดที่เกี่ยวข้อง <?= count($available_indicators) ?> รายการ
                            <?php if (!empty($saved_indicators)): ?>
                                (มีข้อมูลบันทึกไว้แล้ว <?= count($saved_indicators) ?> รายการ)
                            <?php endif; ?>
                        </div>
                        
                        <?php foreach ($available_indicators as $indicator): ?>
                        <div class="indicator-group border p-3 mb-3" data-indicator-id="<?= $indicator['IndicatorID'] ?>">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-bar"></i> <?= htmlspecialchars($indicator['IndicatorName']) ?>
                                <?php if ($indicator['Unit']): ?>
                                    <span class="badge bg-secondary ms-2"><?= htmlspecialchars($indicator['Unit']) ?></span>
                                <?php endif; ?>
                                
                                <!-- แสดงความเกี่ยวข้อง -->
                                <?php if ($indicator['Year'] == $project_year): ?>
                                    <span class="badge bg-info ms-1"><i class="fas fa-calendar"></i> ปี <?= $indicator['Year'] ?></span>
                                <?php endif; ?>
                                
                                <!-- แสดงสถานะการบันทึก -->
                                <?php if (isset($saved_indicators[$indicator['IndicatorID']])): ?>
                                    <span class="badge bg-success ms-1"><i class="fas fa-check"></i> มีข้อมูล</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark ms-1"><i class="fas fa-plus"></i> ยังไม่มีข้อมูล</span>
                                <?php endif; ?>
                            </h6>

                            <?php if ($indicator['Description']): ?>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($indicator['Description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="indicator-values" id="indicator-values-<?= $indicator['IndicatorID'] ?>">
                                <?php 
                                $indicator_id = $indicator['IndicatorID'];
                                $existing_values = isset($saved_indicators[$indicator_id]['Values']) ? $saved_indicators[$indicator_id]['Values'] : [];
                                
                                // ถ้ามีข้อมูลเดิม ให้แสดงข้อมูลเดิม ถ้าไม่มี ให้แสดงฟิลด์ว่างให้กรอก
                                if (!empty($existing_values)) {
                                    foreach ($existing_values as $index => $value_data) { ?>
                                    <div class="indicator-value-item border p-3 mb-3">
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <?php if ($index === 0): ?>
                                                    <label class="form-label">ค่าตัวชี้วัด <?= $indicator['Unit'] ? '(' . $indicator['Unit'] . ')' : '' ?></label>
                                                <?php endif; ?>
                                                <input name="indicator_values[<?= $indicator_id ?>][]" type="number" step="0.01" 
                                                       class="form-control" placeholder="ระบุค่าตัวชี้วัด" 
                                                       value="<?= htmlspecialchars($value_data['Value']) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <?php if ($index === 0): ?>
                                                    <label class="form-label">รายละเอียดเพิ่มเติม</label>
                                                <?php endif; ?>
                                                <div class="details-container">
                                                    <?php if (!empty($value_data['Details'])) {
                                                        foreach ($value_data['Details'] as $detail) { ?>
                                                        <div class="detail-item mb-2">
                                                            <div class="input-group">
                                                                <input name="indicator_details[<?= $indicator_id ?>][<?= $index ?>][]" 
                                                                       class="form-control" placeholder="เช่น ตำบล/หมู่บ้าน" 
                                                                       value="<?= htmlspecialchars($detail) ?>">
                                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailItem(this)">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <?php }
                                                    } ?>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetailItem(<?= $indicator_id ?>, <?= $index ?>)">
                                                        <i class="fas fa-plus"></i> เพิ่มรายละเอียด
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeIndicatorValue(this)">
                                                    <i class="fas fa-trash"></i> ลบ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }
                                } else { ?>
                                    <!-- แสดงฟิลด์ว่างสำหรับตัวชี้วัดที่ยังไม่มีข้อมูล -->
                                    <div class="indicator-value-item border p-3 mb-3">
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <label class="form-label">ค่าตัวชี้วัด <?= $indicator['Unit'] ? '(' . $indicator['Unit'] . ')' : '' ?></label>
                                                <input name="indicator_values[<?= $indicator_id ?>][]" type="number" step="0.01" 
                                                       class="form-control" placeholder="ระบุค่าตัวชี้วัด">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">รายละเอียดเพิ่มเติม</label>
                                                <div class="details-container">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetailItem(<?= $indicator_id ?>, 0)">
                                                        <i class="fas fa-plus"></i> เพิ่มรายละเอียด
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeIndicatorValue(this)">
                                                    <i class="fas fa-trash"></i> ลบ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addIndicatorValue(<?= $indicator_id ?>)">
                                    <i class="fas fa-plus"></i> เพิ่มค่าตัวชี้วัด
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- งบประมาณ -->
                <div class="section-header mt-4"><i class="fas fa-calculator"></i> งบประมาณ</div>
                <div id="budget-container">
                    <?php if (empty($budget_items)): ?>
                    <div class="budget-item row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">ประเภทงบ</label>
                            <input name="budget_types[]" class="form-control" placeholder="เช่น เงินอุดหนุน">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">งบที่ขอ (บาท)</label>
                            <input name="requested_amounts[]" type="number" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">งบที่อนุมัติ (บาท)</label>
                            <input name="approved_amounts[]" type="number" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">หมายเหตุ</label>
                            <input name="budget_remarks[]" class="form-control" placeholder="หมายเหตุ">
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($budget_items as $budget): ?>
                    <div class="budget-item row mb-2">
                        <div class="col-md-4">
                            <input name="budget_types[]" class="form-control" value="<?= htmlspecialchars($budget['BudgetType']) ?>" placeholder="เช่น เงินอุดหนุน">
                        </div>
                        <div class="col-md-3">
                            <input name="requested_amounts[]" type="number" class="form-control" value="<?= htmlspecialchars($budget['RequestedAmount'] ?? '0') ?>" placeholder="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <input name="approved_amounts[]" type="number" class="form-control" value="<?= htmlspecialchars($budget['ApprovedAmount']) ?>" placeholder="0" min="0">
                        </div>
                        <div class="col-md-2">
                            <input name="budget_remarks[]" class="form-control" value="<?= htmlspecialchars($budget['Remark']) ?>" placeholder="หมายเหตุ">
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm mb-3" onclick="addBudget()">+ เพิ่มงบประมาณ</button>

                <div class="d-grid d-none">
                    <button class="btn btn-warning" name="save">💾 บันทึกการแก้ไข</button>
                </div>
                
                <!-- Floating Save Button -->
                <button class="btn floating-save-btn" name="save" type="submit">
                    <i class="fas fa-save"></i> บันทึกการแก้ไข
                </button>
            </form>
        </div>
    </div>
    </div>
    <?php endif; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <?php if (!empty($alert_script)): ?>
    <script><?= $alert_script ?></script>
    <?php endif; ?>
    
    <script>
        // Global variables for project data
        const projectId = <?= $id ?>;
        const currentProjectYear = '<?= htmlspecialchars($row['ProjectYear'] ?? '') ?>';
        const currentStrategyId = '<?= htmlspecialchars($row['StrategyID'] ?? '') ?>';
        const currentMainProjectId = '<?= htmlspecialchars($row['MainProjectID'] ?? '') ?>';

        // Load indicators when selections change
        function checkAndLoadIndicators() {
            const year = $('[name="ProjectYear"]').val();
            console.log('checkAndLoadIndicators called with year:', year);

            // Load indicators if year is selected
            if (year) {
                loadIndicators(year);
            } else {
                $('#indicators-container').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> กรุณาเลือกปีโครงการเพื่อแสดงตัวชี้วัดที่เกี่ยวข้อง</div>');
            }
        }

        function loadIndicators(year) {
            console.log('Loading indicators for year:', year, 'project_id:', projectId);
            $.ajax({
                url: './api/get_project_indicators_with_details.php',
                method: 'GET',
                data: { 
                    year: year,
                    project_id: projectId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('API Response:', response);
                    if (response.success && response.data.length > 0) {
                        let indicatorsHtml = '<div class="alert alert-info">' +
                            '<i class="fas fa-info-circle"></i> พบตัวชี้วัดที่เกี่ยวข้อง ' + response.data.length + ' รายการ' +
                            '</div>';

                        response.data.forEach(function(indicator) {
                            indicatorsHtml += generateIndicatorInputForEdit(indicator);
                        });

                        $('#indicators-container').html(indicatorsHtml);
                    } else {
                        $('#indicators-container').html('<div class="alert alert-warning">' +
                            '<i class="fas fa-exclamation-triangle"></i> ไม่พบตัวชี้วัดที่เกี่ยวข้องกับโครงการนี้' +
                            '<br><small>กรุณาตรวจสอบการตั้งค่าปีโครงการ ยุทธศาสตร์ และโครงการหลัก</small>' +
                            '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('API Error:', xhr.responseText);
                    $('#indicators-container').html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> เกิดข้อผิดพลาดในการโหลดตัวชี้วัด</div>');
                }
            });
        }

        function generateIndicatorInputForEdit(indicator) {
            const currentYear = $('[name="ProjectYear"]').val();
            
            let badges = '';
            // แสดง badge ตามปี
            if (indicator.Year == currentYear) {
                badges += '<span class="badge bg-info ms-1"><i class="fas fa-calendar"></i> ปี ' + indicator.Year + '</span>';
            }
            
            // แสดงสถานะการบันทึก
            if (indicator.Value !== null && indicator.Value !== '') {
                badges += '<span class="badge bg-success ms-1"><i class="fas fa-check"></i> มีข้อมูล</span>';
            } else {
                badges += '<span class="badge bg-light text-dark ms-1"><i class="fas fa-plus"></i> ยังไม่มีข้อมูล</span>';
            }
            
            let valuesHtml = '';
            if (indicator.Value !== null && indicator.Value !== '') {
                // แสดงข้อมูลที่มีอยู่
                valuesHtml += generateIndicatorValueHtml(indicator.IndicatorID, 0, indicator.Value, indicator.Details, indicator.Unit);
            } else {
                // แสดงฟิลด์ว่างสำหรับกรอกข้อมูลใหม่
                valuesHtml += generateIndicatorValueHtml(indicator.IndicatorID, 0, '', [], indicator.Unit);
            }
            
            return '<div class="indicator-group border p-3 mb-3" data-indicator-id="' + indicator.IndicatorID + '">' +
                '<h6 class="text-primary mb-3">' +
                '<i class="fas fa-chart-bar"></i> ' + indicator.IndicatorName +
                (indicator.Unit ? '<span class="badge bg-secondary ms-2">' + indicator.Unit + '</span>' : '') +
                badges +
                '</h6>' +
                (indicator.Description ? '<p class="text-muted small mb-3">' + indicator.Description + '</p>' : '') +
                '<div class="indicator-values" id="indicator-values-' + indicator.IndicatorID + '">' +
                valuesHtml +
                '<button type="button" class="btn btn-outline-primary btn-sm" onclick="addIndicatorValue(' + indicator.IndicatorID + ')">' +
                '<i class="fas fa-plus"></i> เพิ่มค่าตัวชี้วัด' +
                '</button>' +
                '</div>' +
                '</div>';
        }

        function generateIndicatorValueHtml(indicatorId, index, value, details, unit) {
            let detailsHtml = '';
            if (details && details.length > 0) {
                details.forEach(function(detail) {
                    detailsHtml += '<div class="detail-item mb-2">' +
                        '<div class="input-group">' +
                        '<input name="indicator_details[' + indicatorId + '][' + index + '][]" ' +
                        'class="form-control" placeholder="เช่น ตำบล/หมู่บ้าน" value="' + detail + '">' +
                        '<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailItem(this)">' +
                        '<i class="fas fa-trash"></i>' +
                        '</button>' +
                        '</div>' +
                        '</div>';
                });
            }
            
            return '<div class="indicator-value-item border p-3 mb-3">' +
                '<div class="row mb-2">' +
                '<div class="col-md-4">' +
                (index === 0 ? '<label class="form-label">ค่าตัวชี้วัด ' + (unit ? '(' + unit + ')' : '') + '</label>' : '') +
                '<input name="indicator_values[' + indicatorId + '][]" type="number" step="0.01" ' +
                'class="form-control" placeholder="ระบุค่าตัวชี้วัด" value="' + (value || '') + '">' +
                '</div>' +
                '<div class="col-md-6">' +
                (index === 0 ? '<label class="form-label">รายละเอียดเพิ่มเติม</label>' : '') +
                '<div class="details-container">' +
                detailsHtml +
                '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetailItem(' + indicatorId + ', ' + index + ')">' +
                '<i class="fas fa-plus"></i> เพิ่มรายละเอียด' +
                '</button>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-2 d-flex align-items-end">' +
                '<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeIndicatorValue(this)">' +
                '<i class="fas fa-trash"></i> ลบ' +
                '</button>' +
                '</div>' +
                '</div>' +
                '</div>';
        }

        function addIndicatorValue(indicatorId) {
            const container = document.getElementById('indicator-values-' + indicatorId);
            const valueCount = container.querySelectorAll('.indicator-value-item').length;
            
            const valueHtml = generateIndicatorValueHtml(indicatorId, valueCount, '', [], '');
            
            // เพิ่มก่อนปุ่ม "เพิ่มค่าตัวชี้วัด"
            const addButton = container.querySelector('.btn-outline-primary');
            addButton.insertAdjacentHTML('beforebegin', valueHtml);
        }

        function addDetailItem(indicatorId, valueIndex) {
            const container = event.target.closest('.details-container');
            const detailHtml = '<div class="detail-item mb-2">' +
                '<div class="input-group">' +
                '<input name="indicator_details[' + indicatorId + '][' + valueIndex + '][]" ' +
                'class="form-control" placeholder="เช่น ตำบล/หมู่บ้าน">' +
                '<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailItem(this)">' +
                '<i class="fas fa-trash"></i>' +
                '</button>' +
                '</div>' +
                '</div>';
            
            // เพิ่มก่อนปุ่ม "เพิ่มรายละเอียด"
            event.target.insertAdjacentHTML('beforebegin', detailHtml);
        }

        function removeDetailItem(button) {
            button.closest('.detail-item').remove();
        }

        function removeIndicatorValue(button) {
            button.closest('.indicator-value-item').remove();
        }

        // Event handlers สำหรับการเปลี่ยนข้อมูลโครงการ
        $(document).ready(function() {
            console.log('Document ready, loading indicators...');
            
            // โหลดตัวชี้วัดเมื่อเริ่มต้น
            if (currentProjectYear) {
                console.log('Loading indicators for initial year:', currentProjectYear);
                loadIndicators(currentProjectYear);
            }
            
            // เมื่อมีการเปลี่ยนปี - เฉพาะปีเท่านั้น
            $('[name="ProjectYear"]').on('change', function() {
                console.log('Year changed to:', $(this).val());
                checkAndLoadIndicators();
            });
        });

        function addVillage() {
            const container = document.getElementById('villages-container');
            const villageHtml = `
                <div class="village-item border p-3 mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">ชื่อหมู่บ้าน</label>
                            <input name="village_names[]" class="form-control" placeholder="เช่น บ้านหนองน้ำ">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">ชุมชน</label>
                            <input name="village_community[]" class="form-control" placeholder="เช่น ชุมชนบ้านบ่อ">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">หมู่ที่</label>
                            <input name="village_moo[]" class="form-control" placeholder="เช่น 3">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">ตำบล</label>
                            <input name="village_subdistrict[]" class="form-control" placeholder="เช่น สวนผึ้ง">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">อำเภอ</label>
                            <input name="village_district[]" class="form-control" placeholder="เช่น สวนผึ้ง">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">จังหวัด</label>
                            <input name="village_province[]" class="form-control" placeholder="เช่น ราชบุรี">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVillage(this)">ลบ</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', villageHtml);
        }

        function removeVillage(button) {
            button.closest('.village-item').remove();
        }

        function addSchool() {
            const container = document.getElementById('schools-container');
            const schoolHtml = `
                <div class="school-item mb-2">
                    <div class="input-group">
                        <input name="school_names[]" class="form-control" placeholder="ชื่อโรงเรียน">
                        <button type="button" class="btn btn-outline-danger" onclick="removeSchool(this)">ลบ</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', schoolHtml);
        }

        function removeSchool(button) {
            button.closest('.school-item').remove();
        }

        function addNetwork() {
            const container = document.getElementById('networks-container');
            const networkHtml = `
                <div class="network-item mb-2">
                    <div class="input-group">
                        <input name="network_names[]" class="form-control" placeholder="ชื่อเครือข่าย">
                        <button type="button" class="btn btn-outline-danger" onclick="removeNetwork(this)">ลบ</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', networkHtml);
        }

        function removeNetwork(button) {
            button.closest('.network-item').remove();
        }

        function addEnterprise() {
            const container = document.getElementById('enterprises-container');
            const enterpriseHtml = `
                <div class="enterprise-item row mb-2">
                    <div class="col-md-8">
                        <input name="enterprise_names[]" class="form-control" placeholder="ชื่อวิสาหกิจ/ผู้ประกอบการ">
                    </div>
                    <div class="col-md-4">
                        <select name="enterprise_types[]" class="form-control">
                            <option value="">-- เลือกประเภท --</option>
                            <option value="วิสาหกิจ">วิสาหกิจ</option>
                            <option value="ผู้ประกอบการ">ผู้ประกอบการ</option>
                        </select>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', enterpriseHtml);
        }

        function removeEnterprise(button) {
            button.closest('.enterprise-item').remove();
        }

        function addProduct() {
            const container = document.getElementById('products-container');
            const productHtml = `
                <div class="product-item row mb-2">
                    <div class="col-md-4">
                        <input name="product_names[]" class="form-control" placeholder="ชื่อผลิตภัณฑ์">
                    </div>
                    <div class="col-md-3">
                        <input name="product_types[]" class="form-control" placeholder="ประเภท (เช่น อาหาร)">
                    </div>
                    <div class="col-md-3">
                        <input name="product_standards[]" class="form-control" placeholder="เลขมาตรฐาน (เช่น มอก.1234)">
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input name="product_descriptions[]" class="form-control" placeholder="รายละเอียด">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeProduct(this)">ลบ</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', productHtml);
        }

        function removeProduct(button) {
            button.closest('.product-item').remove();
        }

        function addSROI() {
            const container = document.getElementById('sroi-container');
            const sroiHtml = `
                <div class="sroi-item row mb-2">
                    <div class="col-md-4">
                        <input name="sroi_results[]" type="number" step="0.01" class="form-control" placeholder="ค่า SROI">
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input name="sroi_descriptions[]" class="form-control" placeholder="รายละเอียด (ถ้ามี)">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSROI(this)">ลบ</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', sroiHtml);
        }

        function removeSROI(button) {
            button.closest('.sroi-item').remove();
        }

        function addUniversity() {
            const container = document.getElementById('universities-container');
            const universityHtml = `
                <div class="university-item row mb-2">
                    <div class="col-md-6">
                        <input name="university_names[]" class="form-control" placeholder="ชื่อมหาวิทยาลัย">
                    </div>
                    <div class="col-md-3">
                        <select name="university_types[]" class="form-control">
                            <option value="">-- เลือกประเภท --</option>
                            <option value="มหาวิทยาลัยรัฐ">มหาวิทยาลัยรัฐ</option>
                            <option value="มหาวิทยาลัยเอกชน">มหาวิทยาลัยเอกชน</option>
                            <option value="ราชภัฏ">มหาวิทยาลัยราชภัฏ</option>
                            <option value="เทคโนโลยีราชมงคล">มหาวิทยาลัยเทคโนโลยีราชมงคล</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input name="university_collaborations[]" class="form-control" placeholder="รูปแบบความร่วมมือ">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', universityHtml);
        }

        function removeUniversity(button) {
            button.closest('.university-item').remove();
        }

        function addLocalAdmin() {
            const container = document.getElementById('localadmins-container');
            const localadminHtml = `
                <div class="localadmin-item row mb-2">
                    <div class="col-md-4">
                        <input name="localadmin_names[]" class="form-control" placeholder="ชื่อองค์กร">
                    </div>
                    <div class="col-md-2">
                        <select name="localadmin_types[]" class="form-control">
                            <option value="">-- ประเภท --</option>
                            <option value="อบต.">อบต.</option>
                            <option value="เทศบาล">เทศบาล</option>
                            <option value="อปท.อื่นๆ">อปท.อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input name="localadmin_districts[]" class="form-control" placeholder="อำเภอ">
                    </div>
                    <div class="col-md-3">
                        <input name="localadmin_supports[]" class="form-control" placeholder="รูปแบบการสนับสนุน">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', localadminHtml);
        }

        function removeLocalAdmin(button) {
            button.closest('.localadmin-item').remove();
        }

        function addOthers() {
            const container = document.getElementById('others-container');
            const othersHtml = `
                <div class="others-item row mb-2">
                    <div class="col-md-4">
                        <input name="others_names[]" class="form-control" placeholder="ชื่อองค์กร">
                    </div>
                    <div class="col-md-3">
                        <select name="others_types[]" class="form-control">
                            <option value="">-- ประเภท --</option>
                            <option value="หน่วยงานรัฐ">หน่วยงานรัฐ</option>
                            <option value="เอกชน">เอกชน</option>
                            <option value="รัฐวิสาหกิจ">รัฐวิสาหกิจ</option>
                            <option value="NGO">องค์กรพัฒนาเอกชน (NGO)</option>
                            <option value="มูลนิธิ">มูลนิธิ</option>
                            <option value="สมาคม">สมาคม</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input name="others_roles[]" class="form-control" placeholder="บทบาท">
                    </div>
                    <div class="col-md-3">
                        <input name="others_descriptions[]" class="form-control" placeholder="รายละเอียด">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', othersHtml);
        }

        function removeOthers(button) {
            button.closest('.others-item').remove();
        }

        function addBudget() {
            const container = document.getElementById('budget-container');
            const budgetHtml = `
                <div class="budget-item row mb-2">
                    <div class="col-md-4">
                        <input name="budget_types[]" class="form-control" placeholder="ประเภทงบ">
                    </div>
                    <div class="col-md-3">
                        <input name="requested_amounts[]" type="number" class="form-control" placeholder="งบที่ขอ" min="0">
                    </div>
                    <div class="col-md-3">
                        <input name="approved_amounts[]" type="number" class="form-control" placeholder="งบที่อนุมัติ" min="0">
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input name="budget_remarks[]" class="form-control" placeholder="หมายเหตุ">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeBudget(this)">ลบ</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', budgetHtml);
        }

        function removeBudget(button) {
            button.closest('.budget-item').remove();
        }

        function removeDetailItem(button) {
            button.closest('.detail-item').remove();
        }

        function addDetailItem(indicatorId, valueIndex) {
            const container = event.target.closest('.details-container');
            const detailHtml = `
                <div class="detail-item mb-2">
                    <div class="input-group">
                        <input name="indicator_details[${indicatorId}][${valueIndex}][]" 
                               class="form-control" placeholder="เช่น ตำบล/หมู่บ้าน">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailItem(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // เพิ่มก่อนปุ่ม "เพิ่มรายละเอียด"
            event.target.insertAdjacentHTML('beforebegin', detailHtml);
        }

        function removeIndicatorValue(button) {
            button.closest('.indicator-value-item').remove();
        }

        function addIndicatorValue(indicatorId) {
            const container = document.getElementById('indicator-values-' + indicatorId);
            const valueCount = container.querySelectorAll('.indicator-value-item').length;
            
            const valueHtml = `
                <div class="indicator-value-item border p-3 mb-3">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <input name="indicator_values[${indicatorId}][]" type="number" step="0.01" 
                                   class="form-control" placeholder="ระบุค่าตัวชี้วัด">
                        </div>
                        <div class="col-md-6">
                            <div class="details-container">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetailItem(${indicatorId}, ${valueCount})">
                                    <i class="fas fa-plus"></i> เพิ่มรายละเอียด
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeIndicatorValue(this)">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // เพิ่มก่อนปุ่ม "เพิ่มค่าตัวชี้วัด"
            const addButton = container.querySelector('.btn-outline-primary');
            addButton.insertAdjacentHTML('beforebegin', valueHtml);
        }
    </script>
</body>

</html>