<?php
// หน้าจัดการโครงการหลัก
$title = 'จัดการโครงการ';
$page_header = 'จัดการโครงการหลัก';

// ดึงข้อมูลโครงการทั้งหมด
$sql = "SELECT p.ProjectID as id, p.ProjectName as project_name, p.ProjectYear, p.CreateAt as created_date,
               p.AgencyName, p.ResponsiblePerson, p.TargetArea,
               COUNT(pi.ID) as indicator_count,
               (SELECT COUNT(*) FROM project_indicators pi2 WHERE pi2.ProjectID = p.ProjectID AND pi2.Value IS NOT NULL AND pi2.Value > 0) as filled_indicators
        FROM projects p 
        LEFT JOIN project_indicators pi ON p.ProjectID = pi.ProjectID 
        GROUP BY p.ProjectID 
        ORDER BY p.CreateAt DESC";
$stmt = $pdo->query($sql);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
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
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.project-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-dark);
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.project-meta {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    margin-bottom: 1rem;
}

.project-description {
    color: var(--bs-dark);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.project-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-weight: 600;
    color: var(--bs-dark);
}

.stat-label {
    color: var(--bs-secondary);
    font-size: 0.75rem;
}

.project-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-sm {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
}

.progress-ring {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
}

.add-project-card {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    text-decoration: none;
    min-height: 200px;
}

.add-project-card:hover {
    color: white;
    opacity: 0.9;
}

.add-project-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><?php echo $page_header; ?></h1>
                <p class="text-muted mt-1">จัดการและติดตามโครงการต่างๆ ในระบบ</p>
            </div>
            <div>
                <a href="new_index.php?page=add_project" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>เพิ่มโครงการใหม่
                </a>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="projects-grid">
        <!-- Add New Project Card -->
        <a href="new_index.php?page=add_project" class="project-card add-project-card">
            <div class="add-project-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h5 class="mb-2">เพิ่มโครงการใหม่</h5>
            <p class="mb-0 opacity-75">คลิกเพื่อสร้างโครงการใหม่</p>
        </a>

        <!-- Existing Projects -->
        <?php foreach ($projects as $project): ?>
            <?php 
            $progress = $project['indicator_count'] > 0 ? 
                        ($project['filled_indicators'] / $project['indicator_count']) * 100 : 0;
            ?>
            <div class="project-card">
                <!-- Progress Ring -->
                <div class="progress-ring">
                    <svg width="40" height="40" class="progress-svg">
                        <circle cx="20" cy="20" r="15" stroke="#e9ecef" stroke-width="3" fill="none"/>
                        <circle cx="20" cy="20" r="15" stroke="var(--bs-success)" stroke-width="3" 
                                fill="none" stroke-linecap="round"
                                stroke-dasharray="<?php echo 2 * pi() * 15; ?>"
                                stroke-dashoffset="<?php echo 2 * pi() * 15 * (1 - $progress/100); ?>"
                                transform="rotate(-90 20 20)"/>
                    </svg>
                    <div class="progress-text position-absolute top-50 start-50 translate-middle">
                        <small class="fw-bold"><?php echo round($progress); ?>%</small>
                    </div>
                </div>

                <div class="project-header">
                    <div class="flex-grow-1">
                        <h6 class="project-title">
                            <?php echo htmlspecialchars($project['project_name']); ?>
                        </h6>
                        <div class="project-meta">
                            <i class="fas fa-calendar me-1"></i>
                            สร้างเมื่อ: <?php echo date('d/m/Y', strtotime($project['created_date'])); ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($project['TargetArea'])): ?>
                <div class="project-description">
                    <?php echo htmlspecialchars($project['TargetArea']); ?>
                </div>
                <?php endif; ?>

                <!-- Project Statistics -->
                <div class="project-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $project['indicator_count']; ?></div>
                        <div class="stat-label">ตัวชี้วัด</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $project['filled_indicators']; ?></div>
                        <div class="stat-label">กรอกแล้ว</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">
                            <span class="badge bg-<?php echo $project['ProjectYear'] == date('Y') ? 'success' : 'secondary'; ?>">
                                <?php echo $project['ProjectYear'] == date('Y') ? 'ปีปัจจุบัน' : 'ปี ' . $project['ProjectYear']; ?>
                            </span>
                        </div>
                        <div class="stat-label">ปีโครงการ</div>
                    </div>
                </div>

                <!-- Project Actions -->
                <div class="project-actions">
                    <a href="new_index.php?page=project_detail&id=<?php echo $project['id']; ?>" 
                       class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-eye me-1"></i>ดูรายละเอียด
                    </a>
                    <a href="new_index.php?page=edit_project&id=<?php echo $project['id']; ?>" 
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-outline-danger btn-sm" 
                            onclick="deleteProject(<?php echo $project['id']; ?>)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($projects)): ?>
    <div class="text-center py-5">
        <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">ยังไม่มีโครงการในระบบ</h5>
        <p class="text-muted">เริ่มต้นด้วยการสร้างโครงการแรกของคุณ</p>
        <a href="new_index.php?page=add_project" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>สร้างโครงการใหม่
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
function deleteProject(projectId) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: 'คุณต้องการลบโครงการนี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // ส่งคำขอลบไปยัง API
            fetch('_sys/_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete_project',
                    project_id: projectId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'ลบสำเร็จ',
                        text: 'โครงการถูกลบออกจากระบบแล้ว',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบโครงการได้', 'error');
            });
        }
    });
}

// Animation เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.project-card');
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
