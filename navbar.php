<?php
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: portal/');
    exit();
}

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    // บันทึก logout log ถ้าเชื่อมต่อฐานข้อมูลได้
    if (isset($_SESSION['use_database']) && $_SESSION['use_database'] && isset($_SESSION['admin_user_id'])) {
        include_once 'backend/includes/user_functions.php';
        $db_files = ['database/db.php', './db.php'];
        foreach ($db_files as $db_file) {
            if (file_exists($db_file)) {
                try {
                    include $db_file;
                    if (isset($conn) && $conn->ping()) {
                        logActivity($conn, $_SESSION['admin_user_id'], 'LOGOUT', 'User logged out');
                        break;
                    }
                } catch (Exception $e) {
                    // ไม่ต้อง error ถ้า log ไม่ได้
                }
            }
        }
    }
    
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<link rel="icon" type="image/x-icon" href="favicon.ico">
<nav class="navbar navbar-expand-lg navbar-dark sticky-top"
    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fas fa-user-shield me-2"></i>
            ระบบจัดการโครงการ
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            แดชบอร์ด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'projects_table_view.php') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=admin-projects">
                            <i class="fas fa-table me-1"></i>
                            รายงานโครงการ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>"
                            href="charts/index.php">
                            <i class="fas fa-chart-bar me-1"></i>
                            กราฟและแผนภูมิ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'user_guide.php') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=user-guide">
                            <i class="fas fa-chart-bar me-1"></i>
                            คู่มือการใช้งาน
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'chart_builder.php') ? 'active' : ''; ?>" href="chart_builder.php">
                        <i class="fas fa-magic me-1"></i>
                        ตัวสร้างกราฟขั้นสูง
                    </a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'backend') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=backend">
                            <i class="fas fa-cog me-1"></i>
                            หลังบ้าน
                        </a>
                    </li>
                    <!-- <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'user_management.php') ? 'active' : ''; ?>"
                            href="user_management.php">
                            <i class="fas fa-users me-1"></i>
                            จัดการผู้ใช้
                        </a>
                    </li>
                    <?php endif; ?> -->
                    <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="otherDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-h me-1"></i>
                        อื่น ๆ
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'user_guide.php') ? 'active' : ''; ?>" href="user_guide.php">
                                <i class="fas fa-book me-2"></i>
                                คู่มือการใช้งาน
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-muted disabled" href="#">
                                <i class="fas fa-info-circle me-2"></i>
                                เพิ่มเติมในอนาคต
                            </a>
                        </li>
                    </ul>
                </li> -->
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?php echo $_SESSION['admin_username']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="dashboard.php">
                                    <i class="fas fa-home me-2"></i>
                                    หน้าหลัก
                                </a>
                            </li>
                            <!-- <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
                            <li>
                                <a class="dropdown-item" href="user_management.php">
                                    <i class="fas fa-users me-2"></i>
                                    จัดการผู้ใช้
                                </a>
                            </li>
                            <?php endif; ?> -->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="?logout=1">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    .navbar-nav .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
    }

    .navbar-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .dropdown-item.active {
        background: #667eea;
        color: white;
    }
</style>