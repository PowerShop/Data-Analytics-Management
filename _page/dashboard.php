<?php
// หน้า Dashboard หลัก
$title = 'หน้าแรก - ระบบจัดการโครงการ';
$page_header = 'ภาพรวมระบบจัดการโครงการ';

// ดึงสถิติต่างๆ
$stats = [];

// จำนวนโครงการทั้งหมด
$stmt = $pdo->query("SELECT COUNT(*) FROM projects");
$stats['total_projects'] = $stmt->fetchColumn();

// จำนวนโครงการที่ดำเนินการ (สมมติใช้ปีปัจจุบัน)
$current_year = date('Y');
$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE ProjectYear = $current_year");
$stats['active_projects'] = $stmt->fetchColumn();

// จำนวนโครงการที่เสร็จสิ้น (ปีที่แล้ว)
$last_year = $current_year - 1;
$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE ProjectYear = $last_year");
$stats['completed_projects'] = $stmt->fetchColumn();

// จำนวนตัวชี้วัดทั้งหมด
$stmt = $pdo->query("SELECT COUNT(*) FROM indicators WHERE IsActive = 1");
$stats['total_indicators'] = $stmt->fetchColumn();

// โครงการล่าสุด
$stmt = $pdo->query("SELECT ProjectID as id, ProjectName as project_name, ProjectYear, CreateAt as created_date FROM projects ORDER BY CreateAt DESC LIMIT 5");
$recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    text-align: center;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--bs-primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--bs-dark);
}

.stat-label {
    color: var(--bs-secondary);
    font-weight: 500;
}

.recent-projects-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.project-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.project-item:last-child {
    border-bottom: none;
}

.project-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
}

.project-name {
    font-weight: 600;
    color: var(--bs-dark);
    text-decoration: none;
}

.project-date {
    font-size: 0.875rem;
    color: var(--bs-secondary);
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.action-btn {
    background: white;
    border: 2px solid var(--bs-primary);
    color: var(--bs-primary);
    padding: 1rem;
    border-radius: 12px;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s ease;
    font-weight: 600;
}

.action-btn:hover {
    background: var(--bs-primary);
    color: white;
    transform: translateY(-2px);
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><?php echo $page_header; ?></h1>
                <p class="text-muted mt-1">ข้อมูลสถิติและภาพรวมของระบบ</p>
            </div>
            <div class="text-end">
                <small class="text-muted">อัปเดตล่าสุด: <?php echo date('d/m/Y H:i:s'); ?></small>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-value"><?php echo number_format($stats['total_projects']); ?></div>
            <div class="stat-label">โครงการทั้งหมด</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: var(--bs-success);">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-value"><?php echo number_format($stats['active_projects']); ?></div>
            <div class="stat-label">โครงการที่ดำเนินการ</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: var(--bs-info);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?php echo number_format($stats['completed_projects']); ?></div>
            <div class="stat-label">โครงการที่เสร็จสิ้น</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: var(--bs-warning);">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-value"><?php echo number_format($stats['total_indicators']); ?></div>
            <div class="stat-label">ตัวชี้วัดทั้งหมด</div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Projects -->
        <div class="col-lg-8">
            <div class="recent-projects-card">
                <h5 class="mb-3">
                    <i class="fas fa-history text-primary me-2"></i>
                    โครงการล่าสุด
                </h5>
                <?php if (!empty($recent_projects)): ?>
                    <?php foreach ($recent_projects as $project): ?>
                        <div class="project-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="new_index.php?page=project_detail&id=<?php echo $project['id']; ?>" 
                                       class="project-name">
                                        <?php echo htmlspecialchars($project['project_name']); ?>
                                    </a>
                                    <div class="project-date">
                                        สร้างเมื่อ: <?php echo date('d/m/Y', strtotime($project['created_date'])); ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-<?php echo $project['ProjectYear'] == date('Y') ? 'success' : 'secondary'; ?>">
                                        <?php echo $project['ProjectYear'] == date('Y') ? 'ปีปัจจุบัน' : 'ปี ' . $project['ProjectYear']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>ยังไม่มีโครงการในระบบ</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="recent-projects-card">
                <h5 class="mb-3">
                    <i class="fas fa-bolt text-primary me-2"></i>
                    เมนูด่วน
                </h5>
                <div class="d-grid gap-2">
                    <a href="new_index.php?page=projects" class="action-btn">
                        <i class="fas fa-project-diagram me-2"></i>
                        จัดการโครงการ
                    </a>
                    <a href="new_index.php?page=analytics" class="action-btn">
                        <i class="fas fa-chart-line me-2"></i>
                        วิเคราะห์ข้อมูล
                    </a>
                    <a href="new_index.php?page=reports" class="action-btn">
                        <i class="fas fa-file-alt me-2"></i>
                        รายงาน
                    </a>
                    <a href="admin/" class="action-btn">
                        <i class="fas fa-cog me-2"></i>
                        ตั้งค่าระบบ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// เพิ่ม animation เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card, .recent-projects-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
