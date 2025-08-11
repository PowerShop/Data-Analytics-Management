<?php
// หน้าแรก - หน้าหลักของระบบ
$title = 'หน้าหลัก';
$page_header = 'ยินดีต้อนรับสู่ระบบจัดการข้อมูลการวิเคราะห์';

// ดึงสถิติพื้นฐาน
try {
    // นับจำนวนโครงการทั้งหมด
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM projects");
    $total_projects = $stmt->fetch()['total'];
    
    // นับจำนวนตัวชี้วัดที่ใช้งาน
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM indicators WHERE IsActive = 1");
    $total_indicators = $stmt->fetch()['total'];
    
    // ดึงโครงการล่าสุด
    $stmt = $pdo->query("SELECT ProjectID, ProjectName, ProjectYear, CreateAt FROM projects ORDER BY CreateAt DESC LIMIT 5");
    $recent_projects = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_projects = 0;
    $total_indicators = 0;
    $recent_projects = [];
}
?>

<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="page-header slide-up">
        <h1><i class="fas fa-home me-3"></i><?= $_config['app_name'] ?></h1>
        <p class="mb-0">ระบบจัดการข้อมูลการวิเคราะห์โครงการเพื่อการพัฒนาชุมชนอย่างยั่งยืน</p>
        <small class="d-block mt-2 opacity-75">เวอร์ชัน <?= $_config['app_version'] ?></small>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <i class="fas fa-project-diagram fa-2x mb-2"></i>
                <h3><?= number_format($total_projects) ?></h3>
                <p class="mb-0">โครงการทั้งหมด</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card success">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <h3><?= number_format($total_indicators) ?></h3>
                <p class="mb-0">ตัวชี้วัดทั้งหมด</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card info">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                <h3><?= date('Y') ?></h3>
                <p class="mb-0">ปีปัจจุบัน</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card warning">
                <i class="fas fa-clock fa-2x mb-2"></i>
                <h3><?= date('H:i') ?></h3>
                <p class="mb-0">เวลาปัจจุบัน</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card danger">
                <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                <h3 id="totalLocations">-</h3>
                <p class="mb-0">พื้นที่ดำเนินการ</p>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Quick Access Menu -->
        <div class="col-lg-8 mb-4">
            <div class="content-section">
                <h4><i class="fas fa-rocket me-2 text-primary-custom"></i>เมนูเข้าใช้งานหลัก</h4>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <a href="?page=dashboard" class="btn btn-primary w-100 p-3 text-start">
                            <i class="fas fa-tachometer-alt fa-2x d-block mb-2"></i>
                            <strong>แดชบอร์ด</strong>
                            <small class="d-block text-white-50">ภาพรวมข้อมูลทั้งหมด</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=projects" class="btn btn-success w-100 p-3 text-start">
                            <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
                            <strong>จัดการโครงการ</strong>
                            <small class="d-block text-white-50">เพิ่ม แก้ไข ดูข้อมูลโครงการ</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=analytics" class="btn btn-info w-100 p-3 text-start">
                            <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                            <strong>วิเคราะห์ข้อมูล</strong>
                            <small class="d-block text-white-50">รายงานและกราฟแผนภูมิ</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=indicators" class="btn btn-warning w-100 p-3 text-start">
                            <i class="fas fa-tasks fa-2x d-block mb-2"></i>
                            <strong>ตัวชี้วัด</strong>
                            <small class="d-block text-white-50">จัดการตัวชี้วัดโครงการ</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=reports" class="btn btn-danger w-100 p-3 text-start">
                            <i class="fas fa-file-alt fa-2x d-block mb-2"></i>
                            <strong>รายงาน</strong>
                            <small class="d-block text-white-50">สร้างและดาวน์โหลดรายงาน</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=settings" class="btn btn-secondary w-100 p-3 text-start">
                            <i class="fas fa-cog fa-2x d-block mb-2"></i>
                            <strong>ตั้งค่า</strong>
                            <small class="d-block text-white-50">การตั้งค่าระบบ</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="col-lg-4 mb-4">
            <div class="content-section">
                <h5><i class="fas fa-clock me-2 text-info"></i>โครงการล่าสุด</h5>
                <?php if (count($recent_projects) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_projects as $project): ?>
                            <a href="new_index.php?page=project_detail&ProjectID=<?= $project['ProjectID'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= h($project['ProjectName']) ?></h6>
                                    <small><?= formatDateThai($project['CreateAt']) ?></small>
                                </div>
                                <small class="text-muted">ปี <?= $project['ProjectYear'] ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="new_index.php?page=projects" class="btn btn-outline-primary btn-sm">ดูทั้งหมด</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted">ยังไม่มีโครงการ</p>
                    <a href="new_index.php?page=add_project" class="btn btn-primary btn-sm">เพิ่มโครงการแรก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row">
        <!-- System Information -->
        <div class="col-lg-4 mb-4">
            <div class="content-section">
                <h5><i class="fas fa-info-circle me-2 text-info"></i>ข้อมูลระบบ</h5>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>เวอร์ชัน:</span>
                        <strong><?= config('app_version') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>วันที่อัปเดตล่าสุด:</span>
                        <strong><?= getThaiDate('short') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>สถานะระบบ:</span>
                        <span class="badge bg-success">ออนไลน์</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>ผู้ใช้งานออนไลน์:</span>
                        <strong id="onlineUsers">-</strong>
                    </div>
                </div>

                <hr>

                <h6><i class="fas fa-clock me-2"></i>กิจกรรมล่าสุด</h6>
                <div class="mt-2" id="recentActivity">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary rounded-circle p-2 me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-plus text-white small"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted">กำลังโหลดกิจกรรมล่าสุด...</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="content-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4><i class="fas fa-history me-2 text-warning"></i>โครงการล่าสุด</h4>
            <a href="?page=projects" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-right me-1"></i>ดูทั้งหมด
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover" id="recentProjectsTable">
                <thead>
                    <tr>
                        <th>ชื่อโครงการ</th>
                        <th>ยุทธศาสตร์</th>
                        <th>งบประมาณ</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้าง</th>
                        <th class="no-sort text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="recentProjectsBody">
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">กำลังโหลด...</span>
                            </div>
                            <p class="mt-2 text-muted">กำลังโหลดข้อมูลโครงการ...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Load dashboard data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadRecentActivity();
    loadRecentProjects();
});

function loadDashboardStats() {
    // Simulate loading stats (replace with actual API call)
    setTimeout(() => {
        document.getElementById('totalProjects').textContent = '<?= formatNumber(1234) ?>';
        document.getElementById('totalBudget').textContent = '<?= formatCurrency(125487250) ?>';
        document.getElementById('totalIndicators').textContent = '<?= formatNumber(567) ?>';
        document.getElementById('totalLocations').textContent = '<?= formatNumber(89) ?>';
        document.getElementById('onlineUsers').textContent = '<?= formatNumber(12) ?>';
    }, 1000);
}

function loadRecentActivity() {
    setTimeout(() => {
        const activities = [
            { icon: 'fas fa-plus', color: 'success', text: 'เพิ่มโครงการใหม่ "โครงการพัฒนาชุมชน"', time: '5 นาทีที่แล้ว' },
            { icon: 'fas fa-edit', color: 'warning', text: 'แก้ไขข้อมูลโครงการ "โครงการเรียนรู้ดิจิทัล"', time: '15 นาทีที่แล้ว' },
            { icon: 'fas fa-chart-bar', color: 'info', text: 'สร้างรายงานสรุปผล Q4', time: '1 ชั่วโมงที่แล้ว' }
        ];

        const activityHtml = activities.map(activity => `
            <div class="d-flex align-items-center mb-2">
                <div class="bg-${activity.color} rounded-circle p-2 me-3" style="width: 35px; height: 35px;">
                    <i class="${activity.icon} text-white small"></i>
                </div>
                <div class="flex-grow-1">
                    <small class="d-block">${activity.text}</small>
                    <small class="text-muted">${activity.time}</small>
                </div>
            </div>
        `).join('');

        document.getElementById('recentActivity').innerHTML = activityHtml;
    }, 1500);
}

function loadRecentProjects() {
    setTimeout(() => {
        const projects = [
            { name: 'โครงการพัฒนาชุมชนดิจิทัล', strategy: 'เทคโนโลยี', budget: 2500000, status: 'กำลังดำเนินการ', date: '15 ม.ค. 2568' },
            { name: 'โครงการเรียนรู้ตลอดชีวิต', strategy: 'การศึกษา', budget: 1800000, status: 'เสร็จสิ้น', date: '10 ม.ค. 2568' },
            { name: 'โครงการเกษตรยั่งยืน', strategy: 'เกษตรกรรม', budget: 3200000, status: 'กำลังดำเนินการ', date: '8 ม.ค. 2568' }
        ];

        const tableHtml = projects.map(project => `
            <tr>
                <td>
                    <strong>${project.name}</strong>
                </td>
                <td>
                    <span class="badge bg-primary">${project.strategy}</span>
                </td>
                <td>${DataAnalytics.formatCurrency(project.budget)}</td>
                <td>
                    <span class="badge bg-${project.status === 'เสร็จสิ้น' ? 'success' : 'warning'}">${project.status}</span>
                </td>
                <td>${project.date}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-view" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning btn-edit" data-bs-toggle="tooltip" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('recentProjectsBody').innerHTML = tableHtml;
        DataAnalytics.initializeTooltips();
    }, 2000);
}
</script>
