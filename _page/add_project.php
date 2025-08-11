<?php
// หน้าเพิ่มโครงการใหม่
$title = 'เพิ่มโครงการใหม่';
$page_header = 'เพิ่มโครงการใหม่';

// ตรวจสอบการส่งข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ประมวลผลการบันทึกข้อมูล
    $project_name = $_POST['project_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $budget = $_POST['budget'] ?? 0;
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    if (!empty($project_name)) {
        try {
            // ใช้การเชื่อมต่อฐานข้อมูลส่วนกลาง
            $sql = "INSERT INTO projects (ProjectName, TargetArea, ProjectYear, CreateAt, AgencyName, ResponsiblePerson) 
                    VALUES (?, ?, ?, NOW(), ?, ?)";
            $stmt = $pdo->prepare($sql);
            $project_year = !empty($start_date) ? date('Y', strtotime($start_date)) : date('Y');
            $stmt->execute([$project_name, $description, $project_year, $budget, $end_date]);
            
            $project_id = $pdo->lastInsertId();
            
            echo "<script>
                Swal.fire({
                    title: 'บันทึกสำเร็จ',
                    text: 'โครงการใหม่ถูกเพิ่มเรียบร้อยแล้ว',
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

// ดึงข้อมูลตัวชี้วัดที่มีอยู่
try {
    $stmt = $pdo->query("SELECT * FROM indicators WHERE IsActive = 1 ORDER BY IndicatorName");
    $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $indicators = [];
}
?>

<style>
.form-container {
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
    background: var(--bs-primary);
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

.required {
    color: var(--bs-danger);
}

.form-floating label {
    font-weight: 500;
}

.indicator-selector {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.indicator-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
}

.indicator-item:hover {
    border-color: var(--bs-primary);
    background: rgba(13, 110, 253, 0.05);
}

.indicator-item.selected {
    border-color: var(--bs-primary);
    background: rgba(13, 110, 253, 0.1);
}

.progress-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.step {
    flex: 1;
    text-align: center;
    position: relative;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.step.active .step-number {
    background: var(--bs-primary);
    color: white;
}

.step.completed .step-number {
    background: var(--bs-success);
    color: white;
}

.step-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.step.active .step-label {
    color: var(--bs-primary);
    font-weight: 600;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-weight: 600;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><?php echo $page_header; ?></h1>
                <p class="text-muted mt-1">กรอกข้อมูลโครงการใหม่ในระบบ</p>
            </div>
            <div>
                <a href="new_index.php?page=projects" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>กลับไปรายการโครงการ
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="step active">
            <div class="step-number">1</div>
            <div class="step-label">ข้อมูลพื้นฐาน</div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-label">ตัวชี้วัด</div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-label">ยืนยัน</div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form id="projectForm" method="POST" novalidate>
            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    ข้อมูลพื้นฐานโครงการ
                </div>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="project_name" name="project_name" 
                                   placeholder="ชื่อโครงการ" required>
                            <label for="project_name">ชื่อโครงการ <span class="required">*</span></label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="status" name="status">
                                <option value="active">กำลังดำเนินการ</option>
                                <option value="planning">อยู่ระหว่างวางแผน</option>
                                <option value="completed">เสร็จสิ้นแล้ว</option>
                                <option value="cancelled">ยกเลิก</option>
                            </select>
                            <label for="status">สถานะโครงการ</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <textarea class="form-control" id="description" name="description" 
                              style="height: 120px" placeholder="รายละเอียดโครงการ"></textarea>
                    <label for="description">รายละเอียดโครงการ</label>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="budget" name="budget" 
                                   placeholder="งบประมาณ" min="0" step="0.01">
                            <label for="budget">งบประมาณ (บาท)</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="start_date" name="start_date">
                            <label for="start_date">วันที่เริ่มต้น</label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="end_date" name="end_date">
                            <label for="end_date">วันที่สิ้นสุด</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicators Section -->
            <div class="form-section">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    เลือกตัวชี้วัด
                </div>
                
                <div class="indicator-selector">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            เลือกตัวชี้วัดที่ต้องการใช้ในโครงการนี้
                        </label>
                        <div class="form-text">
                            คลิกที่ตัวชี้วัดเพื่อเลือก/ยกเลิก หรือค้นหาจากช่องด้านล่าง
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" id="indicatorSearch" 
                               placeholder="ค้นหาตัวชี้วัด...">
                    </div>
                    
                    <div id="indicatorList" class="row">
                        <?php foreach ($indicators as $indicator): ?>
                            <div class="col-lg-6 indicator-option" data-name="<?php echo strtolower($indicator['indicator_name']); ?>">
                                <div class="indicator-item" data-id="<?php echo $indicator['id']; ?>">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="indicators[]" value="<?php echo $indicator['id']; ?>"
                                               id="indicator_<?php echo $indicator['id']; ?>">
                                        <label class="form-check-label w-100" for="indicator_<?php echo $indicator['id']; ?>">
                                            <div class="fw-bold"><?php echo htmlspecialchars($indicator['indicator_name']); ?></div>
                                            <?php if (!empty($indicator['description'])): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($indicator['description']); ?></small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (empty($indicators)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>ยังไม่มีตัวชี้วัดในระบบ</p>
                            <a href="new_index.php?page=manage_indicators" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>เพิ่มตัวชี้วัด
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="window.history.back()">
                    <i class="fas fa-times me-2"></i>ยกเลิก
                </button>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>บันทึกโครงการ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('projectForm');
    const indicatorSearch = document.getElementById('indicatorSearch');
    const indicatorItems = document.querySelectorAll('.indicator-item');
    const checkboxes = document.querySelectorAll('input[name="indicators[]"]');

    // Indicator search functionality
    indicatorSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.indicator-option').forEach(option => {
            const name = option.dataset.name;
            if (name.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Indicator selection
    indicatorItems.forEach(item => {
        item.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            updateIndicatorSelection(this, checkbox.checked);
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const item = this.closest('.indicator-item');
            updateIndicatorSelection(item, this.checked);
        });
    });

    function updateIndicatorSelection(item, isSelected) {
        if (isSelected) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
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
        const selectedIndicators = document.querySelectorAll('input[name="indicators[]"]:checked').length;
        
        Swal.fire({
            title: 'ยืนยันการบันทึก',
            html: `
                <div class="text-start">
                    <p><strong>ชื่อโครงการ:</strong> ${projectName}</p>
                    <p><strong>ตัวชี้วัดที่เลือก:</strong> ${selectedIndicators} รายการ</p>
                    <p class="text-muted">คุณต้องการบันทึกโครงการนี้ใช่หรือไม่?</p>
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

    // Auto-set end date to one year from start date
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        if (startDate) {
            const endDate = new Date(startDate);
            endDate.setFullYear(endDate.getFullYear() + 1);
            document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
        }
    });
});
</script>
