<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการโครงการหลัก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { margin: 1rem 0; }
        .main-project-card { border-left: 4px solid #007bff; }
        .main-project-card:hover { transform: translateY(-2px); transition: all 0.2s; }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-project-diagram"></i> จัดการโครงการหลัก</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMainProjectModal">
                        <i class="fas fa-plus"></i> เพิ่มโครงการหลัก
                    </button>
                </div>

                <!-- รายการโครงการหลัก -->
                <div class="row">
                    <?php
                    $result = $conn->query("SELECT * FROM mainprojects ORDER BY CreatedAt DESC");
                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            // นับจำนวนโครงการย่อย
                            $sub_count_result = $conn->query("SELECT COUNT(*) as count FROM projects WHERE MainProjectID = " . $row['MainProjectID']);
                            $sub_count = $sub_count_result ? $sub_count_result->fetch_assoc()['count'] : 0;
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card main-project-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title text-primary"><?= htmlspecialchars($row['MainProjectName']) ?></h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editMainProject(<?= $row['MainProjectID'] ?>)">
                                                <i class="fas fa-edit"></i> แก้ไข
                                            </a></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteMainProject(<?= $row['MainProjectID'] ?>)">
                                                <i class="fas fa-trash"></i> ลบ
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-muted small mb-2">รหัส: <?= htmlspecialchars($row['MainProjectCode']) ?></p>
                                <p class="card-text"><?= htmlspecialchars($row['MainProjectDescription'] ?? 'ไม่มีคำอธิบาย') ?></p>
                                <div class="mt-3">
                                    <span class="badge bg-info">
                                        <i class="fas fa-tasks"></i> โครงการย่อย: <?= $sub_count ?> โครงการ
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <a href="projects_list.php?main_project_id=<?= $row['MainProjectID'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> ดูโครงการย่อย
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> ยังไม่มีโครงการหลัก
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal เพิ่มโครงการหลัก -->
    <div class="modal fade" id="addMainProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มโครงการหลัก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อโครงการหลัก</label>
                            <input name="MainProjectName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รหัสโครงการหลัก</label>
                            <?php
                            // สร้างรหัสอัตโนมัติ
                            $next_code = "MAIN001";
                            $result = $conn->query("SELECT MainProjectCode FROM mainprojects ORDER BY MainProjectID DESC LIMIT 1");
                            if ($result && $result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $last_code = $row['MainProjectCode'];
                                if (preg_match('/MAIN(\d+)/', $last_code, $matches)) {
                                    $next_number = intval($matches[1]) + 1;
                                    $next_code = "MAIN" . str_pad($next_number, 3, "0", STR_PAD_LEFT);
                                }
                            }
                            ?>
                            <input name="MainProjectCode" class="form-control" value="<?= $next_code ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="MainProjectDescription" class="form-control" rows="3" placeholder="คำอธิบายเกี่ยวกับโครงการหลักนี้"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="add_main_project" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal แก้ไขโครงการหลัก -->
    <div class="modal fade" id="editMainProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขโครงการหลัก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="MainProjectID" id="edit_main_project_id">
                        <div class="mb-3">
                            <label class="form-label">ชื่อโครงการหลัก</label>
                            <input name="MainProjectName" id="edit_main_project_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รหัสโครงการหลัก</label>
                            <input name="MainProjectCode" id="edit_main_project_code" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">คำอธิบาย</label>
                            <textarea name="MainProjectDescription" id="edit_main_project_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="update_main_project" class="btn btn-warning">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editMainProject(id) {
            // ดึงข้อมูลโครงการหลักและแสดงใน modal
            fetch(`api/get_main_project.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_main_project_id').value = data.data.MainProjectID;
                        document.getElementById('edit_main_project_name').value = data.data.MainProjectName;
                        document.getElementById('edit_main_project_code').value = data.data.MainProjectCode;
                        document.getElementById('edit_main_project_description').value = data.data.MainProjectDescription || '';
                        new bootstrap.Modal(document.getElementById('editMainProjectModal')).show();
                    }
                });
        }

        function deleteMainProject(id) {
            if (confirm('คุณแน่ใจหรือไม่ที่จะลบโครงการหลักนี้?\n\nการลบจะส่งผลต่อโครงการย่อยที่เกี่ยวข้องด้วย')) {
                window.location.href = `?delete_main_project=${id}`;
            }
        }
    </script>

    <?php
    // ประมวลผลการเพิ่มโครงการหลัก
    if (isset($_POST['add_main_project'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO mainprojects (MainProjectName, MainProjectCode, MainProjectDescription) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['MainProjectName'], $_POST['MainProjectCode'], $_POST['MainProjectDescription']);
            $stmt->execute();
            echo "<script>alert('เพิ่มโครงการหลักสำเร็จ!'); window.location.reload();</script>";
        } catch (Exception $e) {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');</script>";
        }
    }

    // ประมวลผลการแก้ไขโครงการหลัก
    if (isset($_POST['update_main_project'])) {
        try {
            $stmt = $conn->prepare("UPDATE mainprojects SET MainProjectName=?, MainProjectDescription=? WHERE MainProjectID=?");
            $stmt->bind_param("ssi", $_POST['MainProjectName'], $_POST['MainProjectDescription'], $_POST['MainProjectID']);
            $stmt->execute();
            echo "<script>alert('แก้ไขโครงการหลักสำเร็จ!'); window.location.reload();</script>";
        } catch (Exception $e) {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');</script>";
        }
    }

    // ประมวลผลการลบโครงการหลัก
    if (isset($_GET['delete_main_project'])) {
        try {
            $main_project_id = $_GET['delete_main_project'];
            // ตรวจสอบว่ามีโครงการย่อยหรือไม่
            $check_result = $conn->query("SELECT COUNT(*) as count FROM projects WHERE MainProjectID = $main_project_id");
            $count = $check_result->fetch_assoc()['count'];
            
            if ($count > 0) {
                echo "<script>alert('ไม่สามารถลบได้ เนื่องจากมีโครงการย่อย $count โครงการที่เกี่ยวข้อง');</script>";
            } else {
                $stmt = $conn->prepare("DELETE FROM mainprojects WHERE MainProjectID = ?");
                $stmt->bind_param("i", $main_project_id);
                $stmt->execute();
                echo "<script>alert('ลบโครงการหลักสำเร็จ!'); window.location.href='main_projects.php';</script>";
            }
        } catch (Exception $e) {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');</script>";
        }
    }
    ?>
</body>
</html>
