<?php include 'auth.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-chart-bar me-2"></i>📊 ระบบจัดการโครงการ
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home me-1"></i>หน้าแรก
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_project.php') ? 'active' : ''; ?>" href="add_project.php">
                        <i class="fas fa-plus-circle me-1"></i>เพิ่มโครงการ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'projects_list.php') ? 'active' : ''; ?>" href="projects_list.php">
                        <i class="fas fa-list me-1"></i>รายการโครงการ
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_projects_table.php') ? 'active' : ''; ?>" href="add_projects_table.php">
                        <i class="fas fa-table me-1"></i>เพิ่มโครงการ (แบบตาราง)
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'main_projects.php') ? 'active' : ''; ?>" href="main_projects.php">
                        <i class="fas fa-project-diagram me-1"></i>โครงการหลัก
                    </a>
                </li>
                
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-chart-pie me-1"></i>Dashboard
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_indicators.php') ? 'active' : ''; ?>" href="manage_indicators.php">
                        <i class="fas fa-chart-bar me-1"></i>จัดการตัวชี้วัด
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : ''; ?>" href="analytics.php">
                        <i class="fas fa-chart-line me-1"></i>Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'projects_table_view.php') ? 'active' : ''; ?>" href="projects_table_view.php">
                        <i class="fas fa-table me-1"></i>รายงานตาราง
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>เมนูอื่นๆ
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="reports.php"><i class="fas fa-file-alt me-2"></i>รายงาน</a></li>
                        <li><a class="dropdown-item" href="custom_report.php"><i class="fas fa-chart-bar me-2"></i>Custom Report Builder</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="backup_manager.php"><i class="fas fa-cloud-download-alt me-2"></i>จัดการสำรองข้อมูล</a></li>
                        <li><a class="dropdown-item" href="admin_find_replace.php"><i class="fas fa-search-plus me-2 text-warning"></i>Admin Find & Replace</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-question-circle me-2"></i>ช่วยเหลือ</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i><?php echo $_SESSION['username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header">ผู้ใช้: <?php echo $_SESSION['username']; ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="?logout=1">
                            <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- เพิ่ม Font Awesome สำหรับไอคอน -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- เพิ่มฟอนต์ Thai -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
* {
    font-family: 'Noto Sans Thai Looped', sans-serif;
}

body {
    font-family: 'Noto Sans Thai Looped', sans-serif;
}

.navbar-brand {
    font-size: 1.4rem;
}

/* ปรับแต่งสำหรับหน้าจอเล็ก - navbar brand */
@media (max-width: 991.98px) {
    .navbar-brand {
        font-size: 1.1rem;
        line-height: 1.3;
    }
    
    .navbar-brand i {
        display: inline-block;
        margin-right: 0.25rem;
    }
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 0.375rem;
    font-weight: 500;
}

.nav-link {
    transition: all 0.3s ease;
    border-radius: 0.375rem;
    margin: 0 2px;
    white-space: nowrap;
    padding: 0.5rem 0.75rem;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.05);
    transform: translateY(-1px);
}

/* ปรับแต่งสำหรับหน้าจอเล็ก */
@media (max-width: 991.98px) {
    .navbar-nav .nav-link {
        white-space: normal;
        padding: 0.75rem 1rem;
        margin: 0;
        border-radius: 0;
        text-align: left;
    }
    
    .navbar-nav .nav-link:hover {
        transform: none;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .navbar-nav .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15);
        border-radius: 0;
    }
    
    .navbar-collapse {
        margin-top: 0.5rem;
    }
    
    .dropdown-menu {
        border-radius: 0;
        margin-top: 0;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
        background-color: rgba(0, 0, 0, 0.1);
    }
    
    .dropdown-item {
        padding: 0.5rem 1.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.container {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

/* ปรับแต่งสำหรับหน้าจอเล็ก - container */
@media (max-width: 991.98px) {
    .container {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    
    .navbar {
        padding: 0.5rem 0;
    }
}

/* Floating Save Button */
.floating-save-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
    border-radius: 50px;
    padding: 15px 25px;
    font-size: 16px;
    font-weight: 600;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    border: none;
    background: linear-gradient(45deg, #ffc107, #ff8f00);
    color: #000;
    min-width: 150px;
}

.floating-save-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    background: linear-gradient(45deg, #ffb300, #ff6f00);
    color: #000;
}

.floating-save-btn:active {
    transform: translateY(-1px);
}

.floating-save-btn i {
    margin-right: 8px;
}

/* สำหรับหน้าจอเล็ก */
@media (max-width: 768px) {
    .floating-save-btn {
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        font-size: 14px;
        min-width: 120px;
    }
}
</style>

