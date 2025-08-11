<?php
// หน้าแก้ไขโครงการ
$title = 'แก้ไขโครงการ';
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

$page_header = 'แก้ไขโครงการ: ' . $project['ProjectName'];

// ตรวจสอบการส่งข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = $_POST['project_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $budget = $_POST['budget'] ?? 0;
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    if (!empty($project_name)) {
        try {
            $sql = "UPDATE projects SET ProjectName = ?, TargetArea = ?, AgencyName = ?, ResponsiblePerson = ?, ProjectYear = ? WHERE ProjectID = ?";
            $stmt = $pdo->prepare($sql);
            $project_year = !empty($start_date) ? date('Y', strtotime($start_date)) : $project['ProjectYear'];
            $stmt->execute([$project_name, $description, $budget, $end_date, $project_year, $project_id]);
            
            // อัปเดตตัวชี้วัด (ถ้ามี)
            if (isset($_POST['indicators']) && is_array($_POST['indicators'])) {
                // ลบตัวชี้วัดเก่าที่ไม่ได้เลือก
                $selected_indicators = $_POST['indicators'];
                $placeholders = str_repeat('?,', count($selected_indicators) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM project_indicators WHERE ProjectID = ? AND IndicatorID NOT IN ($placeholders)");
                $stmt->execute(array_merge([$project_id], $selected_indicators));
                
                // เพิ่มตัวชี้วัดใหม่
                foreach ($selected_indicators as $indicator_id) {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO project_indicators (ProjectID, IndicatorID) VALUES (?, ?)");
                    $stmt->execute([$project_id, $indicator_id]);
                }
            }
            
            echo "<script>
                Swal.fire({
                    title: 'บันทึกสำเร็จ',
                    text: 'ข้อมูลโครงการถูกอัปเดตเรียบร้อยแล้ว',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'new_index.php?page=project_detail&ProjectID=$project_id';
                });
            </script>";
        } catch (PDOException $e) {
            echo "<script>
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            </script>";
        }
    }
}

// ดึงข้อมูลตัวชี้วัดทั้งหมด
$stmt = $pdo->query("SELECT * FROM indicators WHERE IsActive = 1 ORDER BY IndicatorName");
$all_indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลตัวชี้วัดที่เลือกไว้แล้ว
$stmt = $pdo->prepare("SELECT IndicatorID FROM project_indicators WHERE ProjectID = ?");
$stmt->execute([$project_id]);
$selected_indicators = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<style>
.edit-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-dark);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.section-icon {
    background: var(--bs-warning);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-size: 0.875rem;
}

.project-status {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 2rem;
    border-left: 4px solid var(--bs-warning);
}

.current-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-item {
    background: white;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.info-label {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    margin-bottom: 0.25rem;
}

.info-value {
    font-weight: 600;
    color: var(--bs-dark);
}

.indicator-management {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
}

.indicator-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.indicator-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.indicator-card.selected {
    border-color: var(--bs-success);
    background: rgba(25, 135, 84, 0.05);
}

.indicator-card:hover {
    border-color: var(--bs-primary);
    background: rgba(13, 110, 253, 0.05);
}

.indicator-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.indicator-name {
    font-weight: 600;
    color: var(--bs-dark);
}

.indicator-check {
    width: 20px;
    height: 20px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.btn-group-left {
    display: flex;
    gap: 1rem;
}

.btn-group-right {
    display: flex;
    gap: 1rem;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><?php echo $page_header; ?></h1>
                <p class="text-muted mt-1">แก้ไขข้อมูลโครงการและตัวชี้วัด</p>
            </div>
            <div>
                <a href="new_index.php?page=project_detail&id=<?php echo $project_id; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปรายละเอียด
                </a>
            </div>
        </div>
    </div>

    <!-- Current Project Status -->
    <div class="project-status">
        <h6 class="mb-3">
            <i class="fas fa-info-circle text-warning me-2"></i>
            ข้อมูลปัจจุบัน
        </h6>
        <div class="current-info">
            <div class="info-item">
                <div class="info-label">สถานะ</div>
                <div class="info-value">
                    <span class="badge bg-<?php echo $project['status'] == 'active' ? 'success' : 'secondary'; ?>">
                        <?php echo $project['status'] == 'active' ? 'กำลังดำเนินการ' : 'เสร็จสิ้นแล้ว'; ?>
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">วันที่สร้าง</div>
                <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($project['created_date'])); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">ตัวชี้วัดที่เลือก</div>
                <div class="info-value"><?php echo count($selected_indicators); ?> รายการ</div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="edit-container">
        <form id="editProjectForm" method="POST" novalidate>
            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    แก้ไขข้อมูลพื้นฐาน
                </div>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="project_name" name="project_name" 
                                   value="<?php echo htmlspecialchars($project['project_name']); ?>" 
                                   placeholder="ชื่อโครงการ" required>
                            <label for="project_name">ชื่อโครงการ <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?php echo $project['status'] == 'active' ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                <option value="planning" <?php echo $project['status'] == 'planning' ? 'selected' : ''; ?>>อยู่ระหว่างวางแผน</option>
                                <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>เสร็จสิ้นแล้ว</option>
                                <option value="cancelled" <?php echo $project['status'] == 'cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                            </select>
                            <label for="status">สถานะโครงการ</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <textarea class="form-control" id="description" name="description" 
                              style="height: 120px" placeholder="รายละเอียดโครงการ"><?php echo htmlspecialchars($project['description']); ?></textarea>
                    <label for="description">รายละเอียดโครงการ</label>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="budget" name="budget" 
                                   value="<?php echo $project['budget']; ?>"
                                   placeholder="งบประมาณ" min="0" step="0.01">
                            <label for="budget">งบประมาณ (บาท)</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="<?php echo $project['start_date']; ?>">
                            <label for="start_date">วันที่เริ่มต้น</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="<?php echo $project['end_date']; ?>">
                            <label for="end_date">วันที่สิ้นสุด</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicators Management Section -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    จัดการตัวชี้วัด
                </div>
                
                <div class="indicator-management">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">เลือกตัวชี้วัดสำหรับโครงการนี้</h6>
                            <small class="text-muted">คลิกที่การ์ดเพื่อเลือก/ยกเลิก ตัวชี้วัด</small>
                        </div>
                        <div>
                            <span class="badge bg-info" id="selectedCount"><?php echo count($selected_indicators); ?> รายการ</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" id="indicatorSearch" 
                               placeholder="ค้นหาตัวชี้วัด...">
                    </div>
                    
                    <div class="indicator-grid" id="indicatorGrid">
                        <?php foreach ($all_indicators as $indicator): ?>
                            <?php $isSelected = in_array($indicator['id'], $selected_indicators); ?>
                            <div class="indicator-card <?php echo $isSelected ? 'selected' : ''; ?>" 
                                 data-id="<?php echo $indicator['id']; ?>"
                                 data-name="<?php echo strtolower($indicator['indicator_name']); ?>">
                                <div class="indicator-header">
                                    <div class="indicator-name"><?php echo htmlspecialchars($indicator['indicator_name']); ?></div>
                                    <input type="checkbox" class="indicator-check" 
                                           name="indicators[]" value="<?php echo $indicator['id']; ?>"
                                           <?php echo $isSelected ? 'checked' : ''; ?>>
                                </div>
                                <?php if (!empty($indicator['description'])): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($indicator['description']); ?></small>
                                <?php endif; ?>
                                <?php if (!empty($indicator['unit'])): ?>
                                    <div class="mt-2">
                                        <span class="badge bg-light text-dark">หน่วย: <?php echo htmlspecialchars($indicator['unit']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="btn-group-left">
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>ลบโครงการ
                    </button>
                </div>
                <div class="btn-group-right">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProjectForm');
    const indicatorCards = document.querySelectorAll('.indicator-card');
    const indicatorSearch = document.getElementById('indicatorSearch');
    const selectedCount = document.getElementById('selectedCount');

    // Indicator search
    indicatorSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        indicatorCards.forEach(card => {
            const name = card.dataset.name;
            if (name.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Indicator selection
    indicatorCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('.indicator-check');
                checkbox.checked = !checkbox.checked;
                updateIndicatorSelection(this, checkbox.checked);
            }
        });

        const checkbox = card.querySelector('.indicator-check');
        checkbox.addEventListener('change', function() {
            updateIndicatorSelection(card, this.checked);
        });
    });

    function updateIndicatorSelection(card, isSelected) {
        if (isSelected) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCheckboxes = document.querySelectorAll('.indicator-check:checked');
        selectedCount.textContent = selectedCheckboxes.length + ' รายการ';
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const projectName = document.getElementById('project_name').value.trim();
        if (!projectName) {
            Swal.fire('กรุณากรอกข้อมูล', 'ชื่อโครงการเป็นข้อมูลที่จำเป็น', 'warning');
            document.getElementById('project_name').focus();
            return;
        }

        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate && startDate > endDate) {
            Swal.fire('วันที่ไม่ถูกต้อง', 'วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด', 'warning');
            return;
        }

        // Show confirmation
        const selectedIndicators = document.querySelectorAll('.indicator-check:checked').length;
        
        Swal.fire({
            title: 'ยืนยันการแก้ไข',
            html: `
                <div class="text-start">
                    <p><strong>ชื่อโครงการ:</strong> ${projectName}</p>
                    <p><strong>ตัวชี้วัดที่เลือก:</strong> ${selectedIndicators} รายการ</p>
                    <p class="text-muted">คุณต้องการบันทึกการแก้ไขใช่หรือไม่?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

function confirmDelete() {
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
                    project_id: <?php echo $project_id; ?>
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
</script>
