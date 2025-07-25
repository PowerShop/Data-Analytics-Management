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
                        <li><a class="dropdown-item" href="comprehensive_looker_guide.php"><i class="fas fa-book me-2 text-primary"></i>Looker Studio Guide</a></li>
                        <li><a class="dropdown-item" href="advanced_looker_export.php"><i class="fas fa-chart-line me-2 text-success"></i>Advanced Looker Export</a></li>
                        <li><a class="dropdown-item" href="looker_studio_export.php"><i class="fas fa-download me-2 text-info"></i>Basic Export สำหรับ Looker Studio</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="backup_manager.php"><i class="fas fa-cloud-download-alt me-2"></i>จัดการสำรองข้อมูล</a></li>
                        <li><a class="dropdown-item" href="admin_find_replace.php"><i class="fas fa-search-plus me-2 text-warning"></i>Admin Find & Replace</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-question-circle me-2"></i>ช่วยเหลือ</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- เพิ่ม Font Awesome สำหรับไอคอน -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.navbar-brand {
    font-size: 1.4rem;
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
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.05);
    transform: translateY(-1px);
}

.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.container {
    padding-top: 1rem;
    padding-bottom: 1rem;
}
</style>

