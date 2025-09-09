<?php
// เริ่ม output buffering เพื่อป้องกัน headers already sent
ob_start();

include '../db.php';
include 'navbar.php';

// ประมวลผลการเพิ่มโครงการหลัก
if (isset($_POST['add_main_project'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO mainprojects (MainProjectName, MainProjectCode, MainProjectDescription) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_POST['MainProjectName'], $_POST['MainProjectCode'], $_POST['MainProjectDescription']);
        $stmt->execute();

        // ล้าง output buffer และ redirect
        ob_end_clean();
        header("Location: main_projects.php?action=added");
        exit();
    } catch (Exception $e) {
        $error_msg = urlencode("เกิดข้อผิดพลาด: " . $e->getMessage());
        ob_end_clean();
        header("Location: main_projects.php?action=error&msg=$error_msg");
        exit();
    }
}

// ประมวลผลการแก้ไขโครงการหลัก
if (isset($_POST['update_main_project'])) {
    try {
        $stmt = $conn->prepare("UPDATE mainprojects SET MainProjectName=?, MainProjectDescription=? WHERE MainProjectID=?");
        $stmt->bind_param("ssi", $_POST['MainProjectName'], $_POST['MainProjectDescription'], $_POST['MainProjectID']);
        $stmt->execute();

        // ล้าง output buffer และ redirect
        ob_end_clean();
        header("Location: main_projects.php?action=updated");
        exit();
    } catch (Exception $e) {
        $error_msg = urlencode("เกิดข้อผิดพลาด: " . $e->getMessage());
        ob_end_clean();
        header("Location: main_projects.php?action=error&msg=$error_msg");
        exit();
    }
}

// ประมวลผลการลบโครงการหลัก
if (isset($_GET['delete_main_project'])) {
    try {
        $main_project_id = intval($_GET['delete_main_project']);
        // ตรวจสอบว่ามีโครงการย่อยหรือไม่
        $check_result = $conn->query("SELECT COUNT(*) as count FROM projects WHERE MainProjectID = $main_project_id");
        $count = $check_result->fetch_assoc()['count'];

        if ($count > 0) {
            $error_msg = urlencode("ไม่สามารถลบได้! มีโครงการย่อย $count โครงการที่เกี่ยวข้อง");
            ob_end_clean();
            header("Location: main_projects.php?action=error&msg=$error_msg");
            exit();
        } else {
            $stmt = $conn->prepare("DELETE FROM mainprojects WHERE MainProjectID = ?");
            $stmt->bind_param("i", $main_project_id);
            $stmt->execute();

            // ล้าง output buffer และ redirect
            ob_end_clean();
            header("Location: main_projects.php?action=deleted");
            exit();
        }
    } catch (Exception $e) {
        $error_msg = urlencode("เกิดข้อผิดพลาด: " . $e->getMessage());
        ob_end_clean();
        header("Location: main_projects.php?action=error&msg=$error_msg");
        exit();
    }
}

// หากไม่มีการ redirect ให้ส่ง output buffer ออกไป
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการโครงการหลัก - ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }

        body {
            background: #f8f9fa;
            line-height: 1.7;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .page-header .container {
            padding: 0 2rem;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .project-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .project-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .project-code {
            font-size: 0.85rem;
            color: #6c757d;
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            display: inline-block;
            border: 1px solid #dee2e6;
        }

        .project-description {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 1.25rem 0;
        }

        .project-stats {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.25rem 0;
        }

        .stat-badge {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.2);
        }

        .project-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-action:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn-action.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }

        .btn-action.primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            border-color: #5a67d8;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .dropdown-item {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            border: 2px dashed #dee2e6;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .empty-icon {
            font-size: 4rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .add-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .add-btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1px;
        }

        .version-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 1rem;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-1">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="fw-bold mb-3">
                    <i class="fas fa-project-diagram me-3"></i>จัดการโครงการหลัก
                </h1>
                <p class="mb-3 fs-5">จัดการโครงการหลักตามแผนงาน ทปอ. แบบครบถ้วนและมีประสิทธิภาพ</p>

                <div class="mt-3">
                    <span class="version-badge">
                        <i class="fas fa-code-branch me-1"></i>v2.0
                    </span>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Welcome Message -->
            <div class="welcome-text">
                <p>จัดการโครงการหลักทั้งหมดในระบบ เพิ่ม แก้ไข และติดตามโครงการย่อยที่เกี่ยวข้อง</p>
            </div>

            <!-- Action Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <i class="fas fa-list"></i> รายการโครงการหลัก
                </h2>
                <button class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addMainProjectModal">
                    <i class="fas fa-plus"></i> เพิ่มโครงการหลัก
                </button>
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
                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" style="border-color: #dee2e6;"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"
                                                onclick="editMainProject(<?= $row['MainProjectID'] ?>)">
                                                <i class="fas fa-edit me-2"></i> แก้ไข
                                            </a></li>
                                        <li><a class="dropdown-item text-danger" href="#"
                                                onclick="deleteMainProject(<?= $row['MainProjectID'] ?>)">
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
        </div> <!-- Modal เพิ่มโครงการหลัก -->
        <div class="modal fade" id="addMainProjectModal" tabindex="-1" aria-labelledby="addMainProjectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMainProjectModalLabel">เพิ่มโครงการหลัก</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <form method="post" action="" role="form">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="add_main_project_name">ชื่อโครงการหลัก</label>
                                <input name="MainProjectName" id="add_main_project_name" class="form-control" required
                                    aria-describedby="add_main_project_name_help">
                                <div id="add_main_project_name_help" class="form-text">กรุณาใส่ชื่อโครงการหลัก</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="add_main_project_code">รหัสโครงการหลัก</label>
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
                                <input name="MainProjectCode" id="add_main_project_code" class="form-control"
                                    value="<?= $next_code ?>" readonly aria-describedby="add_main_project_code_help">
                                <div id="add_main_project_code_help" class="form-text">รหัสโครงการจะถูกสร้างอัตโนมัติ
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="add_main_project_description">คำอธิบาย</label>
                                <textarea name="MainProjectDescription" id="add_main_project_description"
                                    class="form-control" rows="3" placeholder="คำอธิบายเกี่ยวกับโครงการหลักนี้"
                                    aria-describedby="add_main_project_description_help"></textarea>
                                <div id="add_main_project_description_help" class="form-text">
                                    คำอธิบายเพิ่มเติมเกี่ยวกับโครงการ (ไม่บังคับ)</div>
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
        <div class="modal fade" id="editMainProjectModal" tabindex="-1" aria-labelledby="editMainProjectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMainProjectModalLabel">แก้ไขโครงการหลัก</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <form method="post" action="" role="form">
                        <div class="modal-body">
                            <input type="hidden" name="MainProjectID" id="edit_main_project_id">
                            <div class="mb-3">
                                <label class="form-label" for="edit_main_project_name">ชื่อโครงการหลัก</label>
                                <input name="MainProjectName" id="edit_main_project_name" class="form-control" required
                                    aria-describedby="edit_main_project_name_help">
                                <div id="edit_main_project_name_help" class="form-text">กรุณาใส่ชื่อโครงการหลัก</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_main_project_code">รหัสโครงการหลัก</label>
                                <input name="MainProjectCode" id="edit_main_project_code" class="form-control" readonly
                                    aria-describedby="edit_main_project_code_help">
                                <div id="edit_main_project_code_help" class="form-text">รหัสโครงการไม่สามารถแก้ไขได้
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="edit_main_project_description">คำอธิบาย</label>
                                <textarea name="MainProjectDescription" id="edit_main_project_description"
                                    class="form-control" rows="3"
                                    aria-describedby="edit_main_project_description_help"></textarea>
                                <div id="edit_main_project_description_help" class="form-text">
                                    คำอธิบายเพิ่มเติมเกี่ยวกับโครงการ (ไม่บังคับ)</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" name="update_main_project"
                                class="btn btn-warning">บันทึกการแก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add scroll to top functionality
            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) {
                    if (!document.querySelector('.scroll-to-top')) {
                        const scrollBtn = document.createElement('button');
                        scrollBtn.className = 'btn btn-primary scroll-to-top position-fixed';
                        scrollBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);';
                        scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                        scrollBtn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
                        document.body.appendChild(scrollBtn);
                    }
                } else {
                    const scrollBtn = document.querySelector('.scroll-to-top');
                    if (scrollBtn) scrollBtn.remove();
                }
            });

            // Add loading animation for cards
            document.addEventListener('DOMContentLoaded', function () {
                const cards = document.querySelectorAll('.project-card');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            });

            // ฟังก์ชันแสดง notification ตาม URL parameters
            document.addEventListener('DOMContentLoaded', function () {
                const urlParams = new URLSearchParams(window.location.search);
                const action = urlParams.get('action');
                const msg = urlParams.get('msg');

                if (action === 'added') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'เพิ่มโครงการหลักสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function () {
                        // ลบ parameters จาก URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                } else if (action === 'updated') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'แก้ไขโครงการหลักสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function () {
                        // ลบ parameters จาก URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                } else if (action === 'deleted') {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: 'ลบโครงการหลักสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then(function () {
                        // ลบ parameters จาก URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                } else if (action === 'error' && msg) {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!',
                        text: decodeURIComponent(msg),
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    }).then(function () {
                        // ลบ parameters จาก URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                }

                // จัดการ focus สำหรับ modal เพิ่มโครงการหลัก
                const addModal = document.getElementById('addMainProjectModal');
                addModal.addEventListener('shown.bs.modal', function () {
                    document.getElementById('add_main_project_name').focus();
                });
            });

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

                            // แสดง modal อย่างถูกต้องด้วย Bootstrap API
                            const modalElement = document.getElementById('editMainProjectModal');
                            const modal = new bootstrap.Modal(modalElement, {
                                backdrop: true,
                                keyboard: true,
                                focus: true
                            });

                            // เมื่อ modal แสดงแล้ว ให้ focus ไปที่ input แรก
                            modalElement.addEventListener('shown.bs.modal', function () {
                                document.getElementById('edit_main_project_name').focus();
                            }, { once: true });

                            modal.show();
                        } else {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: data.error || 'ไม่สามารถดึงข้อมูลได้',
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'เกิดปัญหาในการเชื่อมต่อ',
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
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

</body>

</html>