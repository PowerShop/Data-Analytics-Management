<?php
// หน้าวิเคราะห์ข้อมูล  
$title = 'วิเคราะห์ข้อมูล';
$page_header = 'วิเคราะห์ข้อมูลโครงการ';

// ดึงข้อมูลสำหรับแผนภูมิ
$chart_data = [];

// ข้อมูลโครงการตามปี
$stmt = $pdo->query("
    SELECT ProjectYear as year, COUNT(*) as count 
    FROM projects 
    WHERE ProjectYear IS NOT NULL
    GROUP BY ProjectYear 
    ORDER BY ProjectYear
");
$chart_data['projects_by_year'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ข้อมูลโครงการตามยุทธศาสตร์
$stmt = $pdo->query("
    SELECT s.StrategyName as strategy_name, COUNT(p.ProjectID) as count 
    FROM strategies s
    LEFT JOIN projects p ON s.StrategyID = p.StrategyID 
    GROUP BY s.StrategyID, s.StrategyName
    HAVING count > 0
");
$chart_data['projects_by_strategy'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ข้อมูลตัวชี้วัดที่ใช้บ่อย
$stmt = $pdo->query("
    SELECT i.IndicatorName as indicator_name, COUNT(pi.ID) as usage_count
    FROM indicators i
    LEFT JOIN project_indicators pi ON i.IndicatorID = pi.IndicatorID
    WHERE i.IsActive = 1
    GROUP BY i.IndicatorID, i.IndicatorName
    HAVING usage_count > 0
    ORDER BY usage_count DESC
    LIMIT 10
");
$chart_data['top_indicators'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.analytics-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.chart-container {
    position: relative;
    height: 400px;
    margin: 1rem 0;
}

.chart-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 2rem;
}

.chart-tab {
    background: none;
    border: none;
    padding: 1rem 1.5rem;
    color: var(--bs-secondary);
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.chart-tab.active {
    color: var(--bs-primary);
    border-bottom-color: var(--bs-primary);
}

.chart-tab:hover {
    color: var(--bs-primary);
    background: rgba(13, 110, 253, 0.05);
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
}

.summary-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.summary-label {
    opacity: 0.9;
    font-size: 0.9rem;
}

.filter-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.export-section {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1rem;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><?php echo $page_header; ?></h1>
                <p class="text-muted mt-1">วิเคราะห์และติดตามประสิทธิภาพโครงการ</p>
            </div>
            <div class="export-section">
                <button class="btn btn-outline-primary" onclick="exportChart()">
                    <i class="fas fa-download me-2"></i>ส่งออกแผนภูมิ
                </button>
                <button class="btn btn-primary" onclick="generateReport()">
                    <i class="fas fa-file-alt me-2"></i>สร้างรายงาน
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-value"><?php echo count($chart_data['projects_by_year']); ?></div>
            <div class="summary-label">ปีที่มีข้อมูล</div>
        </div>
        <div class="summary-card" style="background: linear-gradient(135deg, var(--bs-success), var(--bs-info));">
            <div class="summary-value"><?php echo array_sum(array_column($chart_data['projects_by_status'], 'count')); ?></div>
            <div class="summary-label">โครงการทั้งหมด</div>
        </div>
        <div class="summary-card" style="background: linear-gradient(135deg, var(--bs-warning), var(--bs-orange));">
            <div class="summary-value"><?php echo count($chart_data['top_indicators']); ?></div>
            <div class="summary-label">ตัวชี้วัดที่ใช้</div>
        </div>
        <div class="summary-card" style="background: linear-gradient(135deg, var(--bs-danger), var(--bs-warning));">
            <div class="summary-value"><?php echo !empty($chart_data['top_indicators']) ? max(array_column($chart_data['top_indicators'], 'usage_count')) : 0; ?></div>
            <div class="summary-label">การใช้งานสูงสุด</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">ปี</label>
                <select class="form-select" id="yearFilter">
                    <option value="">ทุกปี</option>
                    <?php foreach ($chart_data['projects_by_year'] as $year_data): ?>
                        <option value="<?php echo $year_data['year']; ?>"><?php echo $year_data['year']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">สถานะ</label>
                <select class="form-select" id="statusFilter">
                    <option value="">ทุกสถานะ</option>
                    <option value="active">ดำเนินการ</option>
                    <option value="completed">เสร็จสิ้น</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">ประเภทแผนภูมิ</label>
                <select class="form-select" id="chartTypeFilter">
                    <option value="bar">แผนภูมิแท่ง</option>
                    <option value="line">แผนภูมิเส้น</option>
                    <option value="pie">แผนภูมิวงกลม</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-primary d-block w-100" onclick="updateCharts()">
                    <i class="fas fa-search me-2"></i>ค้นหา
                </button>
            </div>
        </div>
    </div>

    <!-- Chart Tabs -->
    <div class="analytics-container">
        <div class="chart-tabs">
            <button class="chart-tab active" onclick="showChart('projects-by-year')">
                โครงการตามปี
            </button>
            <button class="chart-tab" onclick="showChart('projects-by-strategy')">
                โครงการตามยุทธศาสตร์
            </button>
            <button class="chart-tab" onclick="showChart('top-indicators')">
                ตัวชี้วัดยอดนิยม
            </button>
        </div>

        <!-- Chart Area -->
        <div id="chartArea" class="chart-container">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="analytics-container">
        <h5 class="mb-3">
            <i class="fas fa-table text-primary me-2"></i>
            ข้อมูลรายละเอียด
        </h5>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>เปอร์เซ็นต์</th>
                        <th>แนวโน้ม</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ข้อมูลสำหรับแผนภูมิ
const chartData = {
    projectsByYear: <?php echo json_encode($chart_data['projects_by_year']); ?>,
    projectsByStrategy: <?php echo json_encode($chart_data['projects_by_strategy']); ?>,
    topIndicators: <?php echo json_encode($chart_data['top_indicators']); ?>
};

let currentChart = null;
let currentChartType = 'projects-by-year';

// สีสำหรับแผนภูมิ
const colors = [
    '#0d6efd', '#6f42c1', '#d63384', '#dc3545', '#fd7e14',
    '#ffc107', '#198754', '#20c997', '#0dcaf0', '#6c757d'
];

function initChart() {
    showChart('projects-by-year');
}

function showChart(type) {
    currentChartType = type;
    
    // Update active tab
    document.querySelectorAll('.chart-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Clear existing chart
    if (currentChart) {
        currentChart.destroy();
    }
    
    const ctx = document.getElementById('mainChart').getContext('2d');
    const chartType = document.getElementById('chartTypeFilter').value || 'bar';
    
    let data, options;
    
    switch(type) {
        case 'projects-by-year':
            data = {
                labels: chartData.projectsByYear.map(item => item.year),
                datasets: [{
                    label: 'จำนวนโครงการ',
                    data: chartData.projectsByYear.map(item => item.count),
                    backgroundColor: colors[0],
                    borderColor: colors[0],
                    borderWidth: 2
                }]
            };
            options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'จำนวนโครงการแยกตามปี'
                    }
                }
            };
            break;
            
        case 'projects-by-strategy':
            data = {
                labels: chartData.projectsByStrategy.map(item => item.strategy_name),
                datasets: [{
                    label: 'จำนวนโครงการ',
                    data: chartData.projectsByStrategy.map(item => item.count),
                    backgroundColor: chartData.projectsByStrategy.map((item, index) => colors[index % colors.length]),
                    borderWidth: 2
                }]
            };
            options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'จำนวนโครงการแยกตามยุทธศาสตร์'
                    }
                }
            };
            break;
            
        case 'top-indicators':
            data = {
                labels: chartData.topIndicators.map(item => item.indicator_name),
                datasets: [{
                    label: 'จำนวนการใช้งาน',
                    data: chartData.topIndicators.map(item => item.usage_count),
                    backgroundColor: chartData.topIndicators.map((item, index) => colors[index % colors.length]),
                    borderWidth: 2
                }]
            };
            options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'ตัวชี้วัดที่ใช้งานบ่อยที่สุด'
                    }
                }
            };
            break;
    }
    
    currentChart = new Chart(ctx, {
        type: chartType === 'pie' && type !== 'projects-by-year' ? 'pie' : 
              chartType === 'line' ? 'line' : 'bar',
        data: data,
        options: options
    });
    
    updateDataTable(type);
}

function updateDataTable(type) {
    const tbody = document.getElementById('dataTableBody');
    tbody.innerHTML = '';
    
    let data;
    switch(type) {
        case 'projects-by-year':
            data = chartData.projectsByYear;
            data.forEach(item => {
                const total = chartData.projectsByYear.reduce((sum, i) => sum + parseInt(i.count), 0);
                const percentage = ((item.count / total) * 100).toFixed(1);
                tbody.innerHTML += `
                    <tr>
                        <td>ปี ${item.year}</td>
                        <td>${item.count}</td>
                        <td>${percentage}%</td>
                        <td><i class="fas fa-chart-line text-success"></i></td>
                    </tr>
                `;
            });
            break;
            
        case 'projects-by-strategy':
            data = chartData.projectsByStrategy;
            data.forEach(item => {
                const total = chartData.projectsByStrategy.reduce((sum, i) => sum + parseInt(i.count), 0);
                const percentage = ((item.count / total) * 100).toFixed(1);
                tbody.innerHTML += `
                    <tr>
                        <td>${item.strategy_name}</td>
                        <td>${item.count}</td>
                        <td>${percentage}%</td>
                        <td><i class="fas fa-chart-pie text-info"></i></td>
                    </tr>
                `;
            });
            break;
            
        case 'top-indicators':
            data = chartData.topIndicators;
            data.forEach(item => {
                const total = chartData.topIndicators.reduce((sum, i) => sum + parseInt(i.usage_count), 0);
                const percentage = ((item.usage_count / total) * 100).toFixed(1);
                tbody.innerHTML += `
                    <tr>
                        <td>${item.indicator_name}</td>
                        <td>${item.usage_count}</td>
                        <td>${percentage}%</td>
                        <td><i class="fas fa-chart-bar text-warning"></i></td>
                    </tr>
                `;
            });
            break;
    }
}

function updateCharts() {
    // อัปเดตแผนภูมิตามตัวกรอง
    showChart(currentChartType);
}

function exportChart() {
    if (currentChart) {
        const link = document.createElement('a');
        link.download = 'chart.png';
        link.href = currentChart.toBase64Image();
        link.click();
    }
}

function generateReport() {
    window.open('new_index.php?page=reports', '_blank');
}

// เริ่มต้นแผนภูมิเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});
</script>
