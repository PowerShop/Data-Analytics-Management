<?php
// หน้ารายงาน
$title = 'รายงาน';
$page_header = 'รายงานและสถิติ';

// ดึงข้อมูลสำหรับรายงาน
$reports = [];

// รายงานสรุปโครงการ
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_projects,
        SUM(CASE WHEN ProjectYear = YEAR(CURDATE()) THEN 1 ELSE 0 END) as current_year_projects,
        SUM(CASE WHEN ProjectYear = YEAR(CURDATE()) - 1 THEN 1 ELSE 0 END) as last_year_projects,
        COUNT(DISTINCT ProjectYear) as years_span
    FROM projects
");
$reports['project_summary'] = $stmt->fetch(PDO::FETCH_ASSOC);

// รายงานตัวชี้วัด
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_indicators,
        COUNT(DISTINCT Year) as year_span,
        COUNT(CASE WHEN IsActive = 1 THEN 1 END) as active_indicators
    FROM indicators
");
$reports['indicator_summary'] = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<style>
.report-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.report-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.report-item {
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-radius: 8px;
    padding: 1.5rem;
    border-left: 4px solid var(--bs-primary);
    transition: all 0.3s ease;
}

.report-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.report-icon {
    width: 50px;
    height: 50px;
    background: var(--bs-primary);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.report-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--bs-dark);
    margin-bottom: 0.5rem;
}

.report-label {
    color: var(--bs-secondary);
    font-weight: 500;
}

.export-options {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.export-btn {
    flex: 1;
    min-width: 120px;
}

.period-selector {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.chart-preview {
    height: 300px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1rem 0;
}

.report-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><?php echo $page_header; ?></h1>
                <p class="text-muted mt-1">สร้างและดาวน์โหลดรายงานต่างๆ</p>
            </div>
            <div class="export-options">
                <button class="btn btn-outline-success export-btn" onclick="exportExcel()">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </button>
                <button class="btn btn-outline-danger export-btn" onclick="exportPDF()">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </button>
                <button class="btn btn-primary export-btn" onclick="generateReport()">
                    <i class="fas fa-chart-bar me-2"></i>สร้างรายงาน
                </button>
            </div>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="period-selector">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label fw-bold">ช่วงเวลา</label>
                <select class="form-select" id="periodType">
                    <option value="all">ทั้งหมด</option>
                    <option value="year">รายปี</option>
                    <option value="month">รายเดือน</option>
                    <option value="custom">กำหนดเอง</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">จาก</label>
                <input type="date" class="form-control" id="startDate">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">ถึง</label>
                <input type="date" class="form-control" id="endDate">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">&nbsp;</label>
                <button class="btn btn-primary d-block w-100" onclick="updateReports()">
                    <i class="fas fa-search me-2"></i>ค้นหา
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Reports -->
    <div class="report-grid">
        <!-- Project Summary -->
        <div class="report-card">
            <div class="report-header">
                <h5 class="mb-0">
                    <i class="fas fa-project-diagram text-primary me-2"></i>
                    สรุปโครงการ
                </h5>
            </div>
            
            <div class="row">
                <div class="col-6">
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['project_summary']['total_projects']); ?></div>
                        <div class="report-label">โครงการทั้งหมด</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="report-item">
                        <div class="report-icon" style="background: var(--bs-success);">
                            <i class="fas fa-play"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['project_summary']['current_year_projects']); ?></div>
                        <div class="report-label">โครงการปีปัจจุบัน</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="report-item">
                        <div class="report-icon" style="background: var(--bs-info);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['project_summary']['last_year_projects']); ?></div>
                        <div class="report-label">โครงการปีที่แล้ว</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="report-item">
                        <div class="report-icon" style="background: var(--bs-warning);">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['project_summary']['years_span']); ?></div>
                        <div class="report-label">ปีที่มีข้อมูล</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicator Summary -->
        <div class="report-card">
            <div class="report-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    สรุปตัวชี้วัด
                </h5>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="report-item">
                        <div class="report-icon" style="background: var(--bs-purple);">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['indicator_summary']['total_indicators']); ?></div>
                        <div class="report-label">ตัวชี้วัดทั้งหมด</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="report-item">
                        <div class="report-icon" style="background: var(--bs-orange);">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['indicator_summary']['year_span']); ?></div>
                        <div class="report-label">ปีที่มีตัวชี้วัด</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="report-item">
                        <div class="report-icon" style="background: var(--bs-teal);">
                            <i class="fas fa-target"></i>
                        </div>
                        <div class="report-value"><?php echo number_format($reports['indicator_summary']['active_indicators']); ?></div>
                        <div class="report-label">ตัวชี้วัดที่ใช้งาน</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Preview -->
    <div class="report-card">
        <div class="report-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie text-primary me-2"></i>
                ตัวอย่างแผนภูมิ
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshChart()">
                    <i class="fas fa-sync-alt me-1"></i>รีเฟรช
                </button>
            </div>
        </div>
        
        <div class="chart-preview" id="chartPreview">
            <canvas id="reportChart"></canvas>
        </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="report-card">
        <div class="report-header">
            <h5 class="mb-0">
                <i class="fas fa-table text-primary me-2"></i>
                รายงานรายละเอียด
            </h5>
        </div>
        
        <div class="table-responsive report-table">
            <table class="table table-hover mb-0" id="reportTable">
                <thead class="table-dark">
                    <tr>
                        <th>โครงการ</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้าง</th>
                        <th>ตัวชี้วัด</th>
                        <th>ความคืบหน้า</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let reportChart = null;

function initReportChart() {
    const ctx = document.getElementById('reportChart').getContext('2d');
    
    reportChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['โครงการปีปัจจุบัน', 'โครงการปีที่แล้ว'],
            datasets: [{
                data: [
                    <?php echo $reports['project_summary']['current_year_projects']; ?>,
                    <?php echo $reports['project_summary']['last_year_projects']; ?>
                ],
                backgroundColor: [
                    '#198754',
                    '#0dcaf0'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'สัดส่วนโครงการตามปี'
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateReports() {
    const periodType = document.getElementById('periodType').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    // แสดง loading
    Swal.fire({
        title: 'กำลังอัปเดตรายงาน...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // ส่งข้อมูลไป API
    fetch('_sys/_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'update_reports',
            period_type: periodType,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            location.reload();
        } else {
            Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถอัปเดตรายงานได้', 'error');
    });
}

function exportExcel() {
    window.open('_sys/_api.php?action=export_excel', '_blank');
}

function exportPDF() {
    window.open('_sys/_api.php?action=export_pdf', '_blank');
}

function generateReport() {
    Swal.fire({
        title: 'สร้างรายงาน',
        text: 'เลือกประเภทรายงานที่ต้องการ',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'รายงานสรุป',
        cancelButtonText: 'รายงานรายละเอียด'
    }).then((result) => {
        if (result.isConfirmed) {
            window.open('_sys/_api.php?action=generate_summary_report', '_blank');
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.open('_sys/_api.php?action=generate_detailed_report', '_blank');
        }
    });
}

function refreshChart() {
    if (reportChart) {
        reportChart.update();
    }
}

// เริ่มต้นเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    initReportChart();
    
    // เริ่มต้น DataTable
    $('#reportTable').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
        },
        ajax: {
            url: '_sys/_api.php',
            type: 'POST',
            data: {
                action: 'get_report_data'
            }
        },
        columns: [
            { data: 'project_name' },
            { 
                data: 'status',
                render: function(data) {
                    return data === 'active' ? 
                        '<span class="badge bg-success">กำลังดำเนินการ</span>' :
                        '<span class="badge bg-info">เสร็จสิ้นแล้ว</span>';
                }
            },
            { data: 'created_date' },
            { data: 'indicator_count' },
            { 
                data: 'progress',
                render: function(data) {
                    return `<div class="progress">
                                <div class="progress-bar" style="width: ${data}%">${data}%</div>
                            </div>`;
                }
            },
            {
                data: 'id',
                render: function(data) {
                    return `<a href="new_index.php?page=project_detail&id=${data}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>`;
                }
            }
        ]
    });
});
</script>
