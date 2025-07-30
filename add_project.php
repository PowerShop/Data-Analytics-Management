<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มโครงการ</title>
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
    <div class="container">
        <div class="card shadow">
        <div class="card-header bg-primary text-white">เพิ่มข้อมูลโครงการ</div>
        <div class="card-body">
            <form method="post">
                <!-- ข้อมูลโครงการหลัก -->
                <div class="section-header"><i class="fas fa-folder-open"></i> ข้อมูลโครงการหลัก</div>
                <!-- ปีโครงการ -->
                <div class="mb-3">
                    <label class="form-label">ปีโครงการ</label>
                    <!-- <input name="ProjectYear" id="ProjectYear" type="number" class="form-control" placeholder="เช่น 2568" min="2550" max="2599" required> -->
                    <select class="form-select" id="ProjectYear" name="ProjectYear" required>
                        <?php
                            $currentYear = date('Y') + 543; // ปี พ.ศ.
                            for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                $selected = $year == $currentYear ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ชื่อโครงการ (โครงการที่ได้รับงบประมาณ)</label>
                    <input name="projectname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสโครงการ (ตามเล่มแผนปฏิบัติราชการ)</label>
                    <?php
                                             // ดึงรหัสโครงการล่าสุดและสร้างรหัสใหม่
                        $next_code = "P001"; // ค่าเริ่มต้น
                        $result    = $conn->query("SELECT ProjectCode FROM projects ORDER BY ProjectID DESC LIMIT 1");
                        if ($result && $result->num_rows > 0) {
                            $row       = $result->fetch_assoc();
                            $last_code = $row['ProjectCode'];
                            // ดึงตัวเลขจากรหัสล่าสุด (สมมติรูปแบบ P001, P002, ...)
                            if (preg_match('/P(\d+)/', $last_code, $matches)) {
                                $next_number = intval($matches[1]) + 1;
                                $next_code   = "P" . str_pad($next_number, 4, "0", STR_PAD_LEFT);
                            }
                        }
                    ?>
                    <input name="projectcode" class="form-control" value="<?php echo $next_code ?>" readonly>
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
                                    echo "<option value='{$main_row['MainProjectID']}'>";
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
                        $result     = $conn->query("SELECT StrategyID, StrategyName FROM strategies ORDER BY StrategyName");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $strategies[] = $row;
                            }
                        }
                    ?>
                    <select name="StrategyID" class="form-select" required>
                        <option value="">-- เลือกยุทธศาสตร์ --</option>
                        <?php foreach ($strategies as $strategy): ?>
                            <option value="<?php echo $strategy['StrategyID'] ?>">
                                <?php echo htmlspecialchars($strategy['StrategyName']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- ผู้รับผิดชอบ -->
                <div class="mb-3">
                    <label class="form-label">ผู้รับผิดชอบโครงการ</label>
                    <input name="responsibleperson" class="form-control" placeholder="ผู้รับผิดชอบโครงการ">
                </div>
                <div class="mb-3">
                    <label class="form-label">หน่วยงาน</label>
                    <input name="agencyname" class="form-control">
                </div>
                <div class="mb-3">
                    <!-- <label class="form-label">จังหวัด</label> -->
                    <input type="hidden" name="province" class="form-control" placeholder="เช่น ราชบุรี" value="ราชบุรี">
                </div>

                
                <!-- พื้นที่ดำเนินการ TargetArea -->
                <div class="mb-3">
                    <!-- <label class="form-label">พื้นที่ดำเนินโครงการ</label> -->
                    <!-- <textarea name="targetarea" class="form-control" rows="3" placeholder="เช่น หมู่บ้านห้วยผาก อำเภอสวนผึ้ง จังหวัดราชบุรี"></textarea> -->
                </div>

                <div class="section-header mt-4">พื้นที่ดำเนินโครงการ</div>

                <!-- หมู่บ้าน/ชุมชน -->
                <div class="section-header mt-4"><i class="fas fa-home"></i> หมู่บ้าน/ชุมชน</div>
                <div id="villages-container">
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
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addVillage()">+ เพิ่มหมู่บ้าน</button>
                
                <!-- วิสาหกิจ/ผู้ประกอบการ -->
                <div class="section-header mt-4"><i class="fas fa-store"></i> กลุ่มวิสาหกิจ/ผู้ประกอบการ</div>
                <div id="enterprises-container">
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
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addEnterprise()">+ เพิ่มวิสาหกิจ/ผู้ประกอบการ</button>

                <!-- โรงเรียน -->
                <div class="section-header mt-4"><i class="fas fa-school"></i> โรงเรียน</div>
                <div id="schools-container">
                    <div class="mb-2">
                        <input name="school_names[]" class="form-control" placeholder="ชื่อโรงเรียน">
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addSchool()">+ เพิ่มโรงเรียน</button>

                <!-- มหาวิทยาลัย -->
                <div class="section-header mt-4"><i class="fas fa-university"></i> มหาวิทยาลัย</div>
                <div id="universities-container">
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
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addUniversity()">+ เพิ่มมหาวิทยาลัย</button>

                <!-- อบต./องค์กรปกครองส่วนท้องถิ่น -->
                <div class="section-header mt-4"><i class="fas fa-landmark"></i> องค์กรปกครองส่วนท้องถิ่น</div>
                <div id="localadmins-container">
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
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addLocalAdmin()">+ เพิ่มองค์กรปกครองส่วนท้องถิ่น</button>

                <!-- องค์กรอื่นๆ -->
                <div class="section-header mt-4"><i class="fas fa-building"></i> องค์กรอื่นๆ ที่เข้าร่วม</div>
                <div id="others-container">
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
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addOthers()">+ เพิ่มองค์กรอื่นๆ</button>

                <!-- เครือข่าย -->
                <div class="section-header mt-4"><i class="fas fa-network-wired"></i> เครือข่ายร่วมดำเนินการ</div>
                <div id="networks-container">
                    <div class="mb-2">
                        <input name="network_names[]" class="form-control" placeholder="ชื่อเครือข่าย">
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addNetwork()">+ เพิ่มเครือข่าย</button>

                <!-- กลุ่มเป้าหมาย -->
                <div class="section-header mt-4"><i class="fas fa-users"></i> กลุ่มเป้าหมาย</div>
                <?php
                    // ดึงกลุ่มเป้าหมายจากฐานข้อมูล
                    $target_groups = [];
                    $result        = $conn->query("SELECT GroupID, GroupName FROM targetgroups ORDER BY GroupName");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $target_groups[] = $row;
                        }
                    }

                    // ถ้าไม่มีกลุ่มเป้าหมายในฐานข้อมูล ให้เพิ่มกลุ่มเป้าหมายพื้นฐาน
                    if (empty($target_groups)) {
                        $default_groups = ['เกษตรกร', 'ผู้ประกอบการ', 'ชุมชน', 'นักเรียน/นักศึกษา', 'ผู้สูงอายุ', 'เยาวชน'];
                        foreach ($default_groups as $group) {
                            $stmt = $conn->prepare("INSERT IGNORE INTO targetgroups (GroupName) VALUES (?)");
                            $stmt->bind_param("s", $group);
                            $stmt->execute();
                        }
                        // ดึงข้อมูลใหม่
                        $result = $conn->query("SELECT GroupID, GroupName FROM targetgroups ORDER BY GroupName");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $target_groups[] = $row;
                            }
                        }
                    }
                ?>

                <div class="row">
                    <?php foreach ($target_groups as $group): ?>
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="target_groups[]" value="<?php echo $group['GroupID'] ?>" id="group_<?php echo $group['GroupID'] ?>">
                            <label class="form-check-label" for="group_<?php echo $group['GroupID'] ?>">
                                <?php echo htmlspecialchars($group['GroupName']) ?>
                            </label>
                        </div>
                        <input type="number" name="target_count_<?php echo $group['GroupID'] ?>" class="form-control form-control-sm mt-1" placeholder="จำนวน (คน)" min="0">
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- ผลิตภัณฑ์ -->
                <div class="section-header mt-4"><i class="fas fa-box"></i> ผลิตภัณฑ์</div>
                <div id="products-container">
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
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addProduct()">+ เพิ่มผลิตภัณฑ์</button>

                <!-- GVH (การประเมินผลการดำเนินงาน) -->
                <!-- <div class="section-header mt-4"><i class="fas fa-chart-line"></i> GVH</div>
                <div id="gvh-container">
                    <div class="gvh-item row mb-2">
                        <div class="col-md-4">
                            <input name="gvh_village_names[]" class="form-control" placeholder="ชื่อหมู่บ้าน">
                        </div>
                        <div class="col-md-4">
                            <input name="gvh_community_names[]" class="form-control" placeholder="ชื่อชุมชน">
                        </div>
                        <div class="col-md-4">
                            <input name="gvh_performance_results[]" type="number" step="0.01" class="form-control" placeholder="ผลการประเมิน">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addGVH()">+ เพิ่มข้อมูล GVH</button> -->

                <!-- SROI (Social Return on Investment) -->
                <div class="section-header mt-4"><i class="fas fa-coins"></i> SROI - ผลตอบแทนทางสังคม</div>
                <div id="sroi-container">
                    <div class="sroi-item row mb-2">
                        <div class="col-md-4">
                            <input name="sroi_results[]" type="number" step="0.01" class="form-control" placeholder="ค่า SROI">
                        </div>
                        <div class="col-md-8">
                            <input name="sroi_descriptions[]" class="form-control" placeholder="รายละเอียด (ถ้ามี)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addSROI()">+ เพิ่มข้อมูล SROI</button>

                <!-- Soft Power -->
                <!-- <div class="section-header mt-4"><i class="fas fa-heart"></i> ชุมชน Soft Power</div>
                <div id="softpower-container">
                    <div class="softpower-item border p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">ชื่อหมู่บ้าน</label>
                                <input name="softpower_village_names[]" class="form-control" placeholder="เช่น บ้านหนองน้ำ">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">หมู่ที่</label>
                                <input name="softpower_moo[]" class="form-control" placeholder="เช่น 3">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">ชื่อชุมชน</label>
                                <input name="softpower_community_names[]" class="form-control" placeholder="เช่น ชุมชนบ้านบ่อ">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">ตำบล</label>
                                <input name="softpower_subdistrict[]" class="form-control" placeholder="เช่น สวนผึ้ง">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">อำเภอ</label>
                                <input name="softpower_district[]" class="form-control" placeholder="เช่น สวนผึ้ง">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">จังหวัด</label>
                                <input name="softpower_province[]" class="form-control" placeholder="เช่น ราชบุรี">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addSoftPower()">+ เพิ่มข้อมูล Soft Power</button> -->
                <div class="section-header"></div>

                <!-- ตัวชี้วัด -->
                <div class="section-header mt-4" id="indicators-section" style="display: none;"><i class="fas fa-chart-bar"></i> ตัวชี้วัดโครงการ</div>
                <div id="indicators-filter" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">ปีโครงการ</label>
                            <select id="indicator-year" class="form-select" disabled>
                                <option value="">-- จะเลือกอัตโนมัติตามปีโครงการ --</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> ตัวชี้วัดจะแสดงตามปีโครงการที่เลือกข้างต้น
                    </div>
                </div>
                <div id="indicators-container" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> กรุณาเลือกปีโครงการก่อน เพื่อแสดงตัวชี้วัดที่เกี่ยวข้อง
                    </div>
                </div>

                <!-- งบประมาณ -->
                <div class="section-header mt-4"><i class="fas fa-calculator"></i> งบประมาณ</div>
                <div id="budget-container">
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
                </div>
                <button type="button" class="btn btn-outline-success btn-sm mb-3" onclick="addBudget()">+ เพิ่มงบประมาณ</button>

                <!-- ปุ่มบันทึกลอย -->
                <button class="btn floating-save-btn" name="save" type="submit">
                    <i class="fas fa-save"></i>บันทึก
                </button>
            </form>

            <?php
                if (isset($_POST['save'])) {
                    try {
                        // เริ่ม transaction
                        $conn->autocommit(false);

                        // บันทึกข้อมูลโครงการหลัก
                        $stmt = $conn->prepare("INSERT INTO projects (ProjectName, ProjectCode, AgencyName, ResponsiblePerson, Province, ProjectYear, StrategyID, MainProjectID, TargetArea) VALUES (?,?,?,?,?,?,?,?,?)");
                        $stmt->bind_param(
                            "sssssssis",
                            $_POST['projectname'],
                            $_POST['projectcode'],
                            $_POST['agencyname'],
                            $_POST['responsibleperson'],
                            $_POST['province'],
                            $_POST['ProjectYear'],
                            $_POST['StrategyID'],
                            $_POST['MainProjectID'],
                            $_POST['targetarea']
                        );
                        $stmt->execute();
                        $project_id = $conn->insert_id;

                        // บันทึกกลุ่มเป้าหมาย
                        if (isset($_POST['target_groups']) && is_array($_POST['target_groups'])) {
                            $stmt = $conn->prepare("INSERT INTO projecttargetcounts (ProjectID, GroupID, TargetCount) VALUES (?,?,?)");
                            foreach ($_POST['target_groups'] as $group_id) {
                                $target_count = isset($_POST['target_count_' . $group_id]) ? (int) $_POST['target_count_' . $group_id] : 0;
                                $stmt->bind_param("iii", $project_id, $group_id, $target_count);
                                $stmt->execute();
                            }
                        }

                        // บันทึกหมู่บ้าน
                        if (isset($_POST['village_names']) && is_array($_POST['village_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectvillages (ProjectID, VillageName, Moo, SubDistrict, District, Province, Community) VALUES (?,?,?,?,?,?,?)");
                            for ($i = 0; $i < count($_POST['village_names']); $i++) {
                                if (! empty($_POST['village_names'][$i])) {
                                    $village_moo         = $_POST['village_moo'][$i] ?? '';
                                    $village_subdistrict = $_POST['village_subdistrict'][$i] ?? '';
                                    $village_district    = $_POST['village_district'][$i] ?? '';
                                    $village_province    = $_POST['village_province'][$i] ?? '';
                                    $village_community   = $_POST['village_community'][$i] ?? '';

                                    $stmt->bind_param("issssss",
                                        $project_id,
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

                        // บันทึกโรงเรียน
                        if (isset($_POST['school_names']) && is_array($_POST['school_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectschools (ProjectID, SchoolName) VALUES (?,?)");
                            foreach ($_POST['school_names'] as $school_name) {
                                if (! empty($school_name)) {
                                    $stmt->bind_param("is", $project_id, $school_name);
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกเครือข่าย
                        if (isset($_POST['network_names']) && is_array($_POST['network_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectnetworks (ProjectID, NetworkName) VALUES (?,?)");
                            foreach ($_POST['network_names'] as $network_name) {
                                if (! empty($network_name)) {
                                    $stmt->bind_param("is", $project_id, $network_name);
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกวิสาหกิจ/ผู้ประกอบการ
                        if (isset($_POST['enterprise_names']) && is_array($_POST['enterprise_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectenterprises (ProjectID, EnterpriseName, EnterpriseType) VALUES (?,?,?)");
                            for ($i = 0; $i < count($_POST['enterprise_names']); $i++) {
                                if (! empty($_POST['enterprise_names'][$i]) && ! empty($_POST['enterprise_types'][$i])) {
                                    $stmt->bind_param("iss",
                                        $project_id,
                                        $_POST['enterprise_names'][$i],
                                        $_POST['enterprise_types'][$i]
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกผลิตภัณฑ์ (รวมเลขมาตรฐาน)
                        if (isset($_POST['product_names']) && is_array($_POST['product_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectproducts (ProjectID, ProductName, ProductType, Description, StandardNumber) VALUES (?,?,?,?,?)");
                            for ($i = 0; $i < count($_POST['product_names']); $i++) {
                                if (! empty($_POST['product_names'][$i])) {
                                    $product_type        = $_POST['product_types'][$i] ?? '';
                                    $product_description = $_POST['product_descriptions'][$i] ?? '';
                                    $product_standard    = $_POST['product_standards'][$i] ?? '';

                                    $stmt->bind_param("issss",
                                        $project_id,
                                        $_POST['product_names'][$i],
                                        $product_type,
                                        $product_description,
                                        $product_standard
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกมหาวิทยาลัย
                        if (isset($_POST['university_names']) && is_array($_POST['university_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectuniversities (ProjectID, UniversityName, UniversityType, Collaboration) VALUES (?,?,?,?)");
                            for ($i = 0; $i < count($_POST['university_names']); $i++) {
                                if (! empty($_POST['university_names'][$i])) {
                                    $university_type         = $_POST['university_types'][$i] ?? '';
                                    $university_collaboration = $_POST['university_collaborations'][$i] ?? '';

                                    $stmt->bind_param("isss",
                                        $project_id,
                                        $_POST['university_names'][$i],
                                        $university_type,
                                        $university_collaboration
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกองค์กรปกครองส่วนท้องถิ่น
                        if (isset($_POST['localadmin_names']) && is_array($_POST['localadmin_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectlocaladmins (ProjectID, AdminName, AdminType, District, SupportType) VALUES (?,?,?,?,?)");
                            for ($i = 0; $i < count($_POST['localadmin_names']); $i++) {
                                if (! empty($_POST['localadmin_names'][$i])) {
                                    $admin_type      = $_POST['localadmin_types'][$i] ?? '';
                                    $admin_district  = $_POST['localadmin_districts'][$i] ?? '';
                                    $support_type    = $_POST['localadmin_supports'][$i] ?? '';

                                    $stmt->bind_param("issss",
                                        $project_id,
                                        $_POST['localadmin_names'][$i],
                                        $admin_type,
                                        $admin_district,
                                        $support_type
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกองค์กรอื่นๆ
                        if (isset($_POST['others_names']) && is_array($_POST['others_names'])) {
                            $stmt = $conn->prepare("INSERT INTO projectothers (ProjectID, OrganizationName, OrganizationType, Role, Description) VALUES (?,?,?,?,?)");
                            for ($i = 0; $i < count($_POST['others_names']); $i++) {
                                if (! empty($_POST['others_names'][$i])) {
                                    $org_type        = $_POST['others_types'][$i] ?? '';
                                    $org_role        = $_POST['others_roles'][$i] ?? '';
                                    $org_description = $_POST['others_descriptions'][$i] ?? '';

                                    $stmt->bind_param("issss",
                                        $project_id,
                                        $_POST['others_names'][$i],
                                        $org_type,
                                        $org_role,
                                        $org_description
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึกตัวชี้วัด (ใช้โครงสร้างใหม่)
                        if (isset($_POST['indicator_values']) && is_array($_POST['indicator_values'])) {
                            $stmt_indicator = $conn->prepare("INSERT INTO project_indicators (ProjectID, IndicatorID, Value) VALUES (?,?,?)");
                            $stmt_detail = $conn->prepare("INSERT INTO project_indicator_details (ProjectIndicatorID, DetailText) VALUES (?,?)");

                            foreach ($_POST['indicator_values'] as $indicator_id => $values) {
                                if (is_array($values)) {
                                    $details = isset($_POST['indicator_details'][$indicator_id]) ? $_POST['indicator_details'][$indicator_id] : [];

                                    for ($i = 0; $i < count($values); $i++) {
                                        if (! empty($values[$i]) && is_numeric($values[$i])) {
                                            $value = (float) $values[$i];

                                            // บันทึกค่าตัวชี้วัด
                                            $stmt_indicator->bind_param("iid", $project_id, $indicator_id, $value);
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
                        }

                        // บันทึกงบประมาณ
                        if (isset($_POST['budget_types']) && is_array($_POST['budget_types'])) {
                            $stmt = $conn->prepare("INSERT INTO budgetitems (ProjectID, BudgetType, RequestedAmount, ApprovedAmount, Remark) VALUES (?,?,?,?,?)");
                            for ($i = 0; $i < count($_POST['budget_types']); $i++) {
                                if (! empty($_POST['budget_types'][$i])) {
                                    $requested_amount = ! empty($_POST['requested_amounts'][$i]) ? (float) $_POST['requested_amounts'][$i] : 0;
                                    $approved_amount  = ! empty($_POST['approved_amounts'][$i]) ? (float) $_POST['approved_amounts'][$i] : 0;
                                    $remark           = $_POST['budget_remarks'][$i] ?? '';

                                    $stmt->bind_param("isdds",
                                        $project_id,
                                        $_POST['budget_types'][$i],
                                        $requested_amount,
                                        $approved_amount,
                                        $remark
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // บันทึก SROI
                        if (isset($_POST['sroi_results']) && is_array($_POST['sroi_results'])) {
                            $stmt = $conn->prepare("INSERT INTO projectsroi (ProjectID, SROIResult, Description) VALUES (?,?,?)");
                            for ($i = 0; $i < count($_POST['sroi_results']); $i++) {
                                if (! empty($_POST['sroi_results'][$i]) && is_numeric($_POST['sroi_results'][$i])) {
                                    $sroi_value = (float) $_POST['sroi_results'][$i];
                                    $description = $_POST['sroi_descriptions'][$i] ?? '';

                                    $stmt->bind_param("ids",
                                        $project_id,
                                        $sroi_value,
                                        $description
                                    );
                                    $stmt->execute();
                                }
                            }
                        }

                        // commit transaction
                        $conn->commit();
                        
                        // แสดงข้อความสำเร็จด้วย SweetAlert2
                        echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: '✅ บันทึกโครงการสำเร็จ!',
                                icon: 'success',
                                confirmButtonText: 'ตกลง'
                            }).then(function() {
                                // เคลียร์ฟอร์มหลังจากบันทึกสำเร็จ
                                document.querySelector('form').reset();
                                // รีเฟรชหน้าเพื่อให้ฟอร์มกลับสู่สถานะเริ่มต้น
                                window.location.reload();
                            });
                        });
                        </script>";

                    } catch (Exception $e) {
                        // rollback transaction
                        $conn->rollback();
                        $error_message = htmlspecialchars($e->getMessage());
                        
                        // แสดงข้อความข้อผิดพลาดด้วย SweetAlert2
                        echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'เกิดข้อผิดพลาด: $error_message',
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        });
                        </script>";
                    }
                }
            ?>
        </div>
    </div>
</div>

    <!-- jQuery for AJAX - ต้องโหลดก่อนใช้งาน -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Load indicators when selections change
        function checkAndLoadIndicators() {
            const year = $('#ProjectYear').val();
            
            // Update indicator filters display
            updateIndicatorFilters(year);
            
            // Load indicators if year is selected
            if (year) {
                loadIndicators(year);
            } else {
                $('#indicators-section').hide();
                $('#indicators-container').hide().html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> กรุณาเลือกปีโครงการก่อน เพื่อแสดงตัวชี้วัดที่เกี่ยวข้อง</div>');
            }
        }

        // Event handler for form field changes
        $('#ProjectYear').change(function() {
            checkAndLoadIndicators();
        });

        // Load indicators on page load
        $(document).ready(function() {
            checkAndLoadIndicators();
        });

        function updateIndicatorFilters(year) {
            // Update filter display values
            $('#indicator-year').val(year);
            
            // Show/hide indicators section based on year selection
            if (year) {
                $('#indicators-section').show();
            } else {
                $('#indicators-section').hide();
            }
        }

        function loadIndicators(year) {
            $.ajax({
                url: 'api/get_indicators.php',
                method: 'GET',
                data: { 
                    year: year
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let indicatorsHtml = '<div class="alert alert-info">' +
                            '<i class="fas fa-info-circle"></i> พบตัวชี้วัดที่เกี่ยวข้อง ' + response.data.length + ' รายการ' +
                            '</div>';

                        response.data.forEach(function(indicator) {
                            indicatorsHtml += generateIndicatorInput(indicator);
                        });

                        $('#indicators-container').html(indicatorsHtml);
                        $('#indicators-section').show();
                        $('#indicators-container').show();
                    } else {
                        $('#indicators-container').html('<div class="alert alert-warning">' +
                            '<i class="fas fa-exclamation-triangle"></i> ไม่พบตัวชี้วัดในปีที่เลือก' +
                            '<br><small>กรุณาตรวจสอบการตั้งค่าปีโครงการ</small>' +
                            '</div>');
                        $('#indicators-section').show();
                        $('#indicators-container').show();
                    }
                },
                error: function() {
                    $('#indicators-container').html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> เกิดข้อผิดพลาดในการโหลดตัวชี้วัด</div>');
                    $('#indicators-section').show();
                    $('#indicators-container').show();
                }
            });
        }

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
                            <label class="form-label">หมู่ที่</label>
                            <input name="village_moo[]" class="form-control" placeholder="เช่น 3">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">ชุมชน</label>
                            <input name="village_community[]" class="form-control" placeholder="เช่น ชุมชนบ้านบ่อ">
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
                    <div class="col-md-6">
                        <input name="enterprise_names[]" class="form-control" placeholder="ชื่อวิสาหกิจ/ผู้ประกอบการ">
                    </div>
                    <div class="col-md-4">
                        <select name="enterprise_types[]" class="form-control">
                            <option value="">-- เลือกประเภท --</option>
                            <option value="วิสาหกิจ">วิสาหกิจ</option>
                            <option value="ผู้ประกอบการ">ผู้ประกอบการ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger" onclick="removeEnterprise(this)">ลบ</button>
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

        function addUniversity() {
            const container = document.getElementById('universities-container');
            const universityHtml = `
                <div class="university-item row mb-2">
                    <div class="col-md-5">
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
                    <div class="col-md-2">
                        <input name="university_collaborations[]" class="form-control" placeholder="รูปแบบความร่วมมือ">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeUniversity(this)">ลบ</button>
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
                    <div class="col-md-2">
                        <input name="localadmin_districts[]" class="form-control" placeholder="อำเภอ">
                    </div>
                    <div class="col-md-2">
                        <input name="localadmin_supports[]" class="form-control" placeholder="รูปแบบการสนับสนุน">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeLocalAdmin(this)">ลบ</button>
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
                    <div class="col-md-3">
                        <input name="others_names[]" class="form-control" placeholder="ชื่อองค์กร">
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOthers(this)">ลบ</button>
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



        function generateIndicatorInput(indicator) {
            const currentYear = $('#ProjectYear').val();
            const currentStrategy = $('[name="StrategyID"]').val();
            const currentMainProject = $('[name="MainProjectID"]').val();
            
            let badges = '';
            // แสดง badge ตามความเกี่ยวข้อง
            if (indicator.Year == currentYear) {
                badges += '<span class="badge bg-info ms-1"><i class="fas fa-calendar"></i> ปี ' + indicator.Year + '</span>';
            }
            if (indicator.StrategyID == currentStrategy) {
                badges += '<span class="badge bg-warning ms-1"><i class="fas fa-chess"></i> ยุทธศาสตร์</span>';
            }
            if (indicator.MainProjectID == currentMainProject) {
                badges += '<span class="badge bg-primary ms-1"><i class="fas fa-folder"></i> โครงการหลัก</span>';
            }
            
            // แสดงสถานะการบันทึก - สำหรับโครงการใหม่จะเป็น "ยังไม่มีข้อมูล"
            badges += '<span class="badge bg-light text-dark ms-1"><i class="fas fa-plus"></i> ยังไม่มีข้อมูล</span>';
            
            return '<div class="indicator-group border p-3 mb-3" data-indicator-id="' + indicator.IndicatorID + '">' +
                '<h6 class="text-primary mb-3">' +
                '<i class="fas fa-chart-bar"></i> ' + indicator.IndicatorName +
                (indicator.Unit ? '<span class="badge bg-secondary ms-2">' + indicator.Unit + '</span>' : '') +
                badges +
                '</h6>' +
                (indicator.Description ? '<p class="text-muted small mb-3">' + indicator.Description + '</p>' : '') +
                '<div class="indicator-values" id="indicator-values-' + indicator.IndicatorID + '">' +
                '<div class="indicator-value-item border p-3 mb-3">' +
                '<div class="row mb-2">' +
                '<div class="col-md-4">' +
                '<label class="form-label">ค่าตัวชี้วัด ' + (indicator.Unit ? '(' + indicator.Unit + ')' : '') + '</label>' +
                '<input name="indicator_values[' + indicator.IndicatorID + '][]" type="number" step="0.01" class="form-control" placeholder="ระบุค่าตัวชี้วัด">' +
                '</div>' +
                '<div class="col-md-6">' +
                '<label class="form-label">รายละเอียดเพิ่มเติม</label>' +
                '<div class="details-container">' +
                '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetailItem(' + indicator.IndicatorID + ', 0)">' +
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
                '</div>' +
                '<button type="button" class="btn btn-outline-primary btn-sm" onclick="addIndicatorValue(' + indicator.IndicatorID + ')">' +
                '<i class="fas fa-plus"></i> เพิ่มค่าตัวชี้วัด' +
                '</button>' +
                '</div>' +
                '</div>';
        }

        function addIndicatorValue(indicatorId) {
            const container = document.getElementById('indicator-values-' + indicatorId);
            const valueCount = container.querySelectorAll('.indicator-value-item').length;
            
            const valueHtml = '<div class="indicator-value-item border p-3 mb-3">' +
                '<div class="row mb-2">' +
                '<div class="col-md-4">' +
                '<input name="indicator_values[' + indicatorId + '][]" type="number" step="0.01" ' +
                'class="form-control" placeholder="ระบุค่าตัวชี้วัด">' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="details-container">' +
                '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDetailItem(' + indicatorId + ', ' + valueCount + ')">' +
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

        // Event listener สำหรับเปลี่ยนปีโครงการ, ยุทธศาสตร์, และโครงการหลัก
        $(document).ready(function() {
            // Set up change handlers for all relevant fields
            $('#ProjectYear, [name="StrategyID"], [name="MainProjectID"]').on('change', function() {
                const year = $('#ProjectYear').val();
                const strategyId = $('[name="StrategyID"]').val();
                const mainProjectId = $('[name="MainProjectID"]').val();
                
                updateIndicatorFilters(year, strategyId, mainProjectId);
                
                if (year && strategyId && mainProjectId) {
                    loadIndicators(year, strategyId, mainProjectId);
                } else {
                    $('#indicators-section').hide();
                    $('#indicators-container').hide().html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> กรุณาเลือกปีโครงการ ยุทธศาสตร์ และโครงการหลักก่อน เพื่อแสดงตัวชี้วัดที่เกี่ยวข้อง</div>');
                }
            });
            
            // Initial load if form has pre-selected values
            const initialYear = $('#ProjectYear').val();
            const initialStrategy = $('[name="StrategyID"]').val();
            const initialMainProject = $('[name="MainProjectID"]').val();
            
            if (initialYear && initialStrategy && initialMainProject) {
                loadIndicators(initialYear, initialStrategy, initialMainProject);
            }
            
            // Form submission with SweetAlert2 confirmation
            $('form').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'ยืนยันการบันทึก?',
                    text: 'คุณต้องการบันทึกข้อมูลโครงการนี้หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'บันทึก',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // แสดง loading
                        Swal.fire({
                            title: 'กำลังบันทึก...',
                            text: 'กรุณารอสักครู่',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form
                        this.submit();
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>