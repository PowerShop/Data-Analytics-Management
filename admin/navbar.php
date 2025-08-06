<?php
// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    // ถ้าไม่ได้ login และไม่ได้อยู่ในหน้า login ให้ redirect
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        header('Location: login.php');
        exit();
    }
}

// logout 
if (isset($_GET['logout'])) {
    // ล้าง session ทั้งหมด
    session_start();
    session_unset();
    session_destroy();

    // ลบ cookie session (ถ้ามี)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // เริ่ม session ใหม่
    session_start();

    // แสดงข้อความแจ้งเตือน
    $_SESSION['logout_message'] = 'ออกจากระบบเรียบร้อยแล้ว';

    // Redirect ไปหน้า login
    header('Location: /login');
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        แดชบอร์ด
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'projects_table_view.php') ? 'active' : ''; ?>" href="projects_table_view.php">
                        <i class="fas fa-table me-1"></i>
                        รายงานโครงการ
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'charts.php') ? 'active' : ''; ?>" href="charts/charts.php">
                        <i class="fas fa-chart-bar me-1"></i>
                        กราฟและแผนภูมิ
                    </a>
                </li> -->
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'chart_builder.php') ? 'active' : ''; ?>" href="chart_builder.php">
                        <i class="fas fa-magic me-1"></i>
                        ตัวสร้างกราฟขั้นสูง
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="/kittisak/backend/index.php">
                        <i class="fas fa-cog me-1"></i>
                        หลังบ้าน
                    </a>
                </li>
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
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
                        <li><hr class="dropdown-divider"></li>
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
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
