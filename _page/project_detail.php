<?php
// หน้ารายละเอียดโครงการ
$title = 'รายละเอียดโครงการ';
$project_id = $_GET['ProjectID'] ?? $_GET['id'] ?? 0;

if (empty($project_id)) {
    header('Location: new_index.php?page=projects');
    exit;
}

// ดึงข้อมูลโครงการ
$stmt = $pdo->prepare("SELECT * FROM projects WHERE ProjectID = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: new_index.php?page=404');
    exit;
}

$page_header = 'รายละเอียดโครงการ: ' . $project['ProjectName'];

// ดึงข้อมูลตัวชี้วัดของโครงการ
$stmt = $pdo->prepare("
    SELECT pi.*, i.IndicatorName, i.Unit
    FROM project_indicators pi
    JOIN indicators i ON pi.IndicatorID = i.IndicatorID
    WHERE pi.ProjectID = ? AND i.IsActive = 1
    ORDER BY i.IndicatorName
");
$stmt->execute([$project_id]);
$project_indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณความคืบหน้า
$total_indicators = count($project_indicators);
$filled_indicators = 0;
foreach ($project_indicators as $indicator) {
    if (!empty($indicator['Value'])) {
        $filled_indicators++;
    }
}
$progress = $total_indicators > 0 ? ($filled_indicators / $total_indicators) * 100 : 0;
?>

<style>
.project-header {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
    color: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.project-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: rgba(255,255,255,0.1);
    transform: rotate(45deg);
}

.project-info {
    position: relative;
    z-index: 1;
}

.project-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.meta-item {
    background: rgba(255,255,255,0.1);
    padding: 1rem;
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.meta-label {
    font-size: 0.875rem;
    opacity: 0.8;
    margin-bottom: 0.25rem;
}

.meta-value {
    font-size: 1.1rem;
    font-weight: 600;
}

.content-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.progress-circle {
    width: 100px;
    height: 100px;
    position: relative;
    margin: 0 auto 1rem;
}

.progress-circle svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.progress-circle .circle-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 8;
}

.progress-circle .circle-progress {
    fill: none;
    stroke: var(--bs-success);
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--bs-dark);
}

.indicator-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}

.indicator-card:hover {
    border-color: var(--bs-primary);
    background: rgba(13, 110, 253, 0.05);
}

.indicator-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.indicator-name {
    font-weight: 600;
    color: var(--bs-dark);
    margin-bottom: 0.25rem;
}

.indicator-desc {
    font-size: 0.875rem;
    color: var(--bs-secondary);
}

.indicator-value {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.value-input {
    border: none;
    background: none;
    font-weight: 600;
    font-size: 1.1rem;
    flex: 1;
    color: var(--bs-dark);
}

.value-input:focus {
    outline: none;
}

.value-unit {
    color: var(--bs-secondary);
    font-size: 0.9rem;
}

.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 2;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-box {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--bs-primary);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--bs-secondary);
    font-size: 0.875rem;
}
</style>

<div class="container-fluid">
    <!-- Project Header -->
    <div class="project-header">
        <div class="status-badge">
            <span class="badge bg-<?php echo $project['status'] == 'active' ? 'success' : 'light'; ?> fs-6">
                <?php echo $project['status'] == 'active' ? 'กำลังดำเนินการ' : 'เสร็จสิ้นแล้ว'; ?>
            </span>
        </div>
        
        <div class="project-info">
            <h1 class="h3 mb-3"><?php echo htmlspecialchars($project['project_name']); ?></h1>
            
            <?php if (!empty($project['description'])): ?>
                <p class="mb-3 opacity-90"><?php echo htmlspecialchars($project['description']); ?></p>
            <?php endif; ?>
            
            <div class="project-meta">
                <div class="meta-item">
                    <div class="meta-label">วันที่สร้าง</div>
                    <div class="meta-value"><?php echo date('d/m/Y', strtotime($project['created_date'])); ?></div>
                </div>
                
                <?php if (!empty($project['start_date'])): ?>
                <div class="meta-item">
                    <div class="meta-label">วันที่เริ่มต้น</div>
                    <div class="meta-value"><?php echo date('d/m/Y', strtotime($project['start_date'])); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($project['end_date'])): ?>
                <div class="meta-item">
                    <div class="meta-label">วันที่สิ้นสุด</div>
                    <div class="meta-value"><?php echo date('d/m/Y', strtotime($project['end_date'])); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($project['budget'])): ?>
                <div class="meta-item">
                    <div class="meta-label">งบประมาณ</div>
                    <div class="meta-value"><?php echo number_format($project['budget'], 2); ?> บาท</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Progress Section -->
        <div class="col-lg-4">
            <div class="content-section text-center">
                <h5 class="mb-3">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    ความคืบหน้า
                </h5>
                
                <div class="progress-circle">
                    <svg>
                        <circle class="circle-bg" cx="50" cy="50" r="40"></circle>
                        <circle class="circle-progress" cx="50" cy="50" r="40"
                                stroke-dasharray="<?php echo 2 * pi() * 40; ?>"
                                stroke-dashoffset="<?php echo 2 * pi() * 40 * (1 - $progress/100); ?>">
                        </circle>
                    </svg>
                    <div class="progress-text"><?php echo round($progress); ?>%</div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $total_indicators; ?></div>
                        <div class="stat-label">ตัวชี้วัดทั้งหมด</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $filled_indicators; ?></div>
                        <div class="stat-label">กรอกแล้ว</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicators Section -->
        <div class="col-lg-8">
            <div class="content-section">
                <div class="section-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        ตัวชี้วัดโครงการ
                    </h5>
                    <button class="btn btn-primary btn-sm" onclick="saveAllIndicators()">
                        <i class="fas fa-save me-2"></i>บันทึกทั้งหมด
                    </button>
                </div>
                
                <?php if (!empty($project_indicators)): ?>
                    <form id="indicatorsForm">
                        <?php foreach ($project_indicators as $indicator): ?>
                            <div class="indicator-card">
                                <div class="indicator-header">
                                    <div>
                                        <div class="indicator-name"><?php echo htmlspecialchars($indicator['indicator_name']); ?></div>
                                        <?php if (!empty($indicator['indicator_description'])): ?>
                                            <div class="indicator-desc"><?php echo htmlspecialchars($indicator['indicator_description']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($indicator['value'])): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>กรอกแล้ว
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="indicator-value">
                                    <input type="number" 
                                           class="value-input" 
                                           name="indicator_<?php echo $indicator['id']; ?>"
                                           value="<?php echo htmlspecialchars($indicator['value'] ?? ''); ?>"
                                           placeholder="กรอกค่า..."
                                           step="0.01">
                                    <span class="value-unit"><?php echo htmlspecialchars($indicator['unit'] ?? ''); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </form>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>โครงการนี้ยังไม่มีตัวชี้วัด</p>
                        <a href="new_index.php?page=edit_project&id=<?php echo $project_id; ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>เพิ่มตัวชี้วัด
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="new_index.php?page=projects" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>กลับไปรายการโครงการ
        </a>
        <a href="new_index.php?page=edit_project&id=<?php echo $project_id; ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>แก้ไขโครงการ
        </a>
        <button class="btn btn-danger" onclick="deleteProject(<?php echo $project_id; ?>)">
            <i class="fas fa-trash me-2"></i>ลบโครงการ
        </button>
    </div>
</div>

<script>
function saveAllIndicators() {
    const form = document.getElementById('indicatorsForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const data = {
        action: 'save_project_indicators',
        project_id: <?php echo $project_id; ?>,
        indicators: {}
    };
    
    // แปลงข้อมูลจาก FormData
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('indicator_')) {
            const indicatorId = key.replace('indicator_', '');
            data.indicators[indicatorId] = value;
        }
    }
    
    // ส่งข้อมูลไป API
    fetch('_sys/_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                title: 'บันทึกสำเร็จ',
                text: 'ข้อมูลตัวชี้วัดถูกบันทึกแล้ว',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('เกิดข้อผิดพลาด', result.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
    });
}

function deleteProject(projectId) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: 'คุณต้องการลบโครงการนี้ใช่หรือไม่? การดำเนินการนี้ไม่สามารถย้อนกลับได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
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
                        window.location.href = 'new_index.php?page=projects';
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

// Auto-save เมื่อมีการเปลี่ยนแปลงค่า
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.value-input');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value !== this.defaultValue) {
                // บันทึกอัตโนมัติ
                const indicatorId = this.name.replace('indicator_', '');
                const data = {
                    action: 'save_single_indicator',
                    project_id: <?php echo $project_id; ?>,
                    indicator_id: indicatorId,
                    value: this.value
                };
                
                fetch('_sys/_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        this.defaultValue = this.value;
                        // แสดงสัญลักษณ์บันทึกสำเร็จ
                        this.style.borderColor = '#198754';
                        setTimeout(() => {
                            this.style.borderColor = '';
                        }, 1000);
                    }
                });
            }
        });
    });
});
</script>
