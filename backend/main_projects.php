<?php include '../db.php'; ?>
<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการโครงการหลัก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { 
            background-color: #f8f9fa; 
        }
        
        .page-header {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            margin-bottom: 2rem;
            padding: 2rem;
        }
        
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .project-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .project-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .project-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .project-code {
            font-size: 0.85rem;
            color: #6c757d;
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
        }
        
        .project-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            margin: 1rem 0;
        }
        
        .project-stats {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .stat-badge {
            background: #e7f3ff;
            color: #0066cc;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .project-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .btn-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .btn-action:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
            text-decoration: none;
        }
        
        .btn-action.primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .btn-action.primary:hover {
            background: #0056b3;
            border-color: #0056b3;
            color: white;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
        }
        
        .empty-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .add-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .add-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
            color: white;
        }
        
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 1.5rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-project-diagram text-primary me-2"></i>จัดการโครงการหลัก
                    </h2>
                    <p class="text-muted mb-0">จัดการโครงการหลักตามแผนงาน ทปอ.</p>
                </div>
                <button class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addMainProjectModal">
                    <i class="fas fa-plus"></i> เพิ่มโครงการหลัก
                </button>
            </div>
        </div>

        <!-- รายการโครงการหลัก -->
        <div class="projects-grid">
            <?php
            $result = $conn->query("SELECT * FROM mainprojects ORDER BY CreatedAt DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    // นับจำนวนโครงการย่อย
                    $sub_count_result = $conn->query("SELECT COUNT(*) as count FROM projects WHERE MainProjectID = " . $row['MainProjectID']);
                    $sub_count = $sub_count_result ? $sub_count_result->fetch_assoc()['count'] : 0;
            ?>
            <div class="project-card">
                <div class="project-header">
                    <div class="flex-grow-1">
                        <h5 class="project-title"><?= htmlspecialchars($row['MainProjectName']) ?></h5>
                        <div class="project-code"><?= htmlspecialchars($row['MainProjectCode']) ?></div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" style="border-color: #dee2e6;" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="editMainProject(<?= $row['MainProjectID'] ?>)">
                                <i class="fas fa-edit me-2"></i> แก้ไข
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteMainProject(<?= $row['MainProjectID'] ?>)">
                                <i class="fas fa-trash me-2"></i> ลบ
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <?php if (!empty($row['MainProjectDescription'])): ?>
                <p class="project-description"><?= htmlspecialchars($row['MainProjectDescription']) ?></p>
                <?php endif; ?>
                
                <div class="project-stats">
                    <div class="stat-badge">
                        <i class="fas fa-tasks"></i>
                        โครงการย่อย <?= $sub_count ?> โครงการ
                    </div>
                </div>
                
                <div class="project-actions">
                    <a href="projects_list.php?main_project_id=<?= $row['MainProjectID'] ?>" class="btn-action primary">
                        <i class="fas fa-eye"></i> ดูโครงการย่อย
                    </a>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="mb-2">ยังไม่มีโครงการหลัก</h5>
                    <p class="text-muted mb-3">เริ่มต้นโดยการเพิ่มโครงการหลักแรกของคุณ</p>
                    <button class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addMainProjectModal">
                        <i class="fas fa-plus"></i> เพิ่มโครงการหลัก
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>    <!-- Modal เพิ่มโครงการหลัก -->
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'การลบโครงการหลักจะส่งผลต่อโครงการย่อยที่เกี่ยวข้องด้วย',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?delete_main_project=${id}`;
                }
            });
        }
    </script>

    <?php
    // ประมวลผลการเพิ่มโครงการหลัก
    if (isset($_POST['add_main_project'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO mainprojects (MainProjectName, MainProjectCode, MainProjectDescription) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['MainProjectName'], $_POST['MainProjectCode'], $_POST['MainProjectDescription']);
            $stmt->execute();
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'เพิ่มโครงการหลักสำเร็จ',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location.reload();
                });
            });
            </script>";
        } catch (Exception $e) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '" . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            });
            </script>";
        }
    }

    // ประมวลผลการแก้ไขโครงการหลัก
    if (isset($_POST['update_main_project'])) {
        try {
            $stmt = $conn->prepare("UPDATE mainprojects SET MainProjectName=?, MainProjectDescription=? WHERE MainProjectID=?");
            $stmt->bind_param("ssi", $_POST['MainProjectName'], $_POST['MainProjectDescription'], $_POST['MainProjectID']);
            $stmt->execute();
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: 'แก้ไขโครงการหลักสำเร็จ',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                }).then(function() {
                    window.location.reload();
                });
            });
            </script>";
        } catch (Exception $e) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '" . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            });
            </script>";
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
                echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'ไม่สามารถลบได้!',
                        text: 'มีโครงการย่อย $count โครงการที่เกี่ยวข้อง',
                        icon: 'warning',
                        confirmButtonText: 'ตกลง'
                    });
                });
                </script>";
            } else {
                $stmt = $conn->prepare("DELETE FROM mainprojects WHERE MainProjectID = ?");
                $stmt->bind_param("i", $main_project_id);
                $stmt->execute();
                echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'ลบโครงการหลักสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function() {
                        window.location.href='main_projects.php';
                    });
                });
                </script>";
            }
        } catch (Exception $e) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '" . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            });
            </script>";
        }
    }
    ?>
</body>
</html>
