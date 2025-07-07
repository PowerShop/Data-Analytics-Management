<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - ระบบจัดการโครงการ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 450px;
            position: relative;
            overflow: hidden;
        }
        
        .chart-wrapper {
            position: relative;
            height: 350px;
            width: 100%;
        }
        
        .small-chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 350px;
            position: relative;
            overflow: hidden;
        }
        
        .small-chart-wrapper {
            position: relative;
            height: 250px;
            width: 100%;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .chart-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .export-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h2 class="fw-bold mb-3">📊 Dashboard - ระบบจัดการโครงการ</h2>
            <p class="mb-0">ข้อมูล ณ วันที่ <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-0">📈 ส่งออกรายงาน</h6>
                    <small class="text-muted">ดาวน์โหลดข้อมูลสถิติในรูปแบบต่างๆ</small>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-outline-success btn-sm me-2" onclick="exportToPDF()">
                        📄 PDF
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportToExcel()">
                        📊 Excel
                    </button>
                </div>
            </div>
        </div>

        <?php
        // ดึงข้อมูลสถิติต่างๆ
        
        // จำนวนโครงการทั้งหมด
        $total_projects = $conn->query("SELECT COUNT(*) as count FROM Projects")->fetch_assoc()['count'];
        
        // จำนวนหมู่บ้านที่เข้าร่วม
        $total_villages = $conn->query("SELECT COUNT(*) as count FROM ProjectVillages")->fetch_assoc()['count'];
        
        // จำนวนกลุ่มเป้าหมายทั้งหมด
        $total_target_people = $conn->query("SELECT SUM(TargetCount) as total FROM ProjectTargetCounts")->fetch_assoc()['total'] ?: 0;
        
        // จำนวนวิสาหกิจ/ผู้ประกอบการ
        $total_enterprises = $conn->query("SELECT COUNT(*) as count FROM ProjectEnterprises")->fetch_assoc()['count'];
        
        // จำนวนผลิตภัณฑ์
        $total_products = $conn->query("SELECT COUNT(*) as count FROM ProjectProducts")->fetch_assoc()['count'];
        
        // จำนวนโรงเรียน
        $total_schools = $conn->query("SELECT COUNT(*) as count FROM ProjectSchools")->fetch_assoc()['count'];
        
        // จำนวนเครือข่าย
        $total_networks = $conn->query("SELECT COUNT(*) as count FROM ProjectNetworks")->fetch_assoc()['count'];
        
        // งบประมาณรวม
        $budget_data = $conn->query("SELECT SUM(RequestedAmount) as total_requested, SUM(ApprovedAmount) as total_approved FROM BudgetItems")->fetch_assoc();
        $total_requested = $budget_data['total_requested'] ?: 0;
        $total_approved = $budget_data['total_approved'] ?: 0;
        ?>

        <!-- สถิติหลัก -->
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">📊</div>
                    <h3 class="fw-bold"><?= number_format($total_projects) ?></h3>
                    <p class="mb-0">โครงการทั้งหมด</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">👥</div>
                    <h3 class="fw-bold"><?= number_format($total_target_people) ?></h3>
                    <p class="mb-0">กลุ่มเป้าหมาย (คน)</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">🏘️</div>
                    <h3 class="fw-bold"><?= number_format($total_villages) ?></h3>
                    <p class="mb-0">หมู่บ้านที่เข้าร่วม</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-icon">💰</div>
                    <h3 class="fw-bold"><?= number_format($total_approved / 1000000, 1) ?>M</h3>
                    <p class="mb-0">งบอนุมัติ (ล้านบาท)</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart 1: งบประมาณต่อโครงการ -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">💰 งบประมาณแต่ละโครงการ</h4>
                    <div class="chart-wrapper">
                        <canvas id="budgetChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 2: กลุ่มเป้าหมาย -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">🎯 กลุ่มเป้าหมาย</h4>
                    <div class="chart-wrapper">
                        <canvas id="targetGroupChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart 3: จำนวนหมู่บ้านต่อจังหวัด -->
            <div class="col-lg-8 mb-4">
                <div class="small-chart-container">
                    <h4 class="chart-title">🗺️ จำนวนหมู่บ้านต่อจังหวัด</h4>
                    <div class="small-chart-wrapper">
                        <canvas id="provinceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="col-lg-4 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">📈 สถิติเพิ่มเติม</h4>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="bg-primary text-white p-3 rounded">
                                <h5><?= number_format($total_enterprises) ?></h5>
                                <small>วิสาหกิจ</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-success text-white p-3 rounded">
                                <h5><?= number_format($total_products) ?></h5>
                                <small>ผลิตภัณฑ์</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-warning text-white p-3 rounded">
                                <h5><?= number_format($total_schools) ?></h5>
                                <small>โรงเรียน</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-info text-white p-3 rounded">
                                <h5><?= number_format($total_networks) ?></h5>
                                <small>เครือข่าย</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="bg-light p-3 rounded">
                            <strong>งบประมาณรวม:</strong><br>
                            <span class="text-danger">ขอ: <?= number_format($total_requested) ?> บาท</span><br>
                            <span class="text-success">อนุมัติ: <?= number_format($total_approved) ?> บาท</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Chart 4: ประเภทวิสาหกิจ -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">🏭 ประเภทวิสาหกิจ</h4>
                    <div class="chart-wrapper">
                        <canvas id="enterpriseChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 5: SROI Values -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="chart-title">📊 ค่า SROI ของโครงการ</h4>
                    <div class="chart-wrapper">
                        <canvas id="sroiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // ดึงข้อมูลสำหรับ Charts
        
        // 1. งบประมาณต่อโครงการ
        $budget_chart_data = $conn->query("
            SELECT P.ProjectName, 
                   COALESCE(SUM(B.RequestedAmount), 0) AS TotalRequest,
                   COALESCE(SUM(B.ApprovedAmount), 0) AS TotalApprove
            FROM Projects P
            LEFT JOIN BudgetItems B ON P.ProjectID = B.ProjectID
            GROUP BY P.ProjectID, P.ProjectName
            ORDER BY TotalApprove DESC
            LIMIT 10
        ");
        
        $budget_labels = [];
        $budget_requested = [];
        $budget_approved = [];
        
        while ($row = $budget_chart_data->fetch_assoc()) {
            $budget_labels[] = mb_substr($row['ProjectName'], 0, 20) . (mb_strlen($row['ProjectName']) > 20 ? '...' : '');
            $budget_requested[] = $row['TotalRequest'];
            $budget_approved[] = $row['TotalApprove'];
        }
        
        // 2. กลุ่มเป้าหมาย
        $target_data = $conn->query("
            SELECT tg.GroupName, SUM(ptc.TargetCount) as TotalCount
            FROM TargetGroups tg
            LEFT JOIN ProjectTargetCounts ptc ON tg.GroupID = ptc.GroupID
            GROUP BY tg.GroupID, tg.GroupName
            HAVING TotalCount > 0
            ORDER BY TotalCount DESC
        ");
        
        $target_labels = [];
        $target_counts = [];
        
        while ($row = $target_data->fetch_assoc()) {
            $target_labels[] = $row['GroupName'];
            $target_counts[] = $row['TotalCount'];
        }
        
        // 3. จำนวนหมู่บ้านต่อจังหวัด
        $province_data = $conn->query("
            SELECT Province, COUNT(*) as VillageCount
            FROM ProjectVillages
            WHERE Province IS NOT NULL AND Province != ''
            GROUP BY Province
            ORDER BY VillageCount DESC
            LIMIT 8
        ");
        
        $province_labels = [];
        $province_counts = [];
        
        while ($row = $province_data->fetch_assoc()) {
            $province_labels[] = $row['Province'];
            $province_counts[] = $row['VillageCount'];
        }
        
        // 4. ประเภทวิสาหกิจ
        $enterprise_data = $conn->query("
            SELECT EnterpriseType, COUNT(*) as Count
            FROM ProjectEnterprises
            GROUP BY EnterpriseType
            LIMIT 5
        ");
        
        $enterprise_labels = [];
        $enterprise_counts = [];
        
        while ($row = $enterprise_data->fetch_assoc()) {
            $enterprise_labels[] = $row['EnterpriseType'];
            $enterprise_counts[] = $row['Count'];
        }
        
        // 5. SROI Values
        $sroi_data = $conn->query("
            SELECT p.ProjectName, ps.SROIResult
            FROM Projects p
            JOIN ProjectSROI ps ON p.ProjectID = ps.ProjectID
            ORDER BY ps.SROIResult DESC
            LIMIT 8
        ");
        
        $sroi_labels = [];
        $sroi_values = [];
        
        while ($row = $sroi_data->fetch_assoc()) {
            $sroi_labels[] = mb_substr($row['ProjectName'], 0, 12) . '...';
            $sroi_values[] = $row['SROIResult'];
        }
        ?>

        <script>
        // กำหนดค่าเริ่มต้นสำหรับ Chart.js
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.animation.duration = 600;
        
        // รอให้หน้าเว็บโหลดเสร็จก่อนสร้างกราฟ
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initCharts, 100);
        });
        
        function initCharts() {
            // Chart 1: งบประมาณ
            try {
                const budgetCanvas = document.getElementById('budgetChart');
                if (budgetCanvas) {
                    new Chart(budgetCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($budget_labels) ?>,
                            datasets: [{
                                label: 'งบที่ขอ',
                                data: <?= json_encode($budget_requested) ?>,
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }, {
                                label: 'งบอนุมัติ',
                                data: <?= json_encode($budget_approved) ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'top' } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString() + ' บาท';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating budget chart:', e); }

            // Chart 2: กลุ่มเป้าหมาย
            try {
                const targetCanvas = document.getElementById('targetGroupChart');
                if (targetCanvas) {
                    new Chart(targetCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: <?= json_encode($target_labels) ?>,
                            datasets: [{
                                data: <?= json_encode($target_counts) ?>,
                                backgroundColor: [
                                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating target chart:', e); }

            // Chart 3: จังหวัด
            try {
                const provinceCanvas = document.getElementById('provinceChart');
                if (provinceCanvas) {
                    new Chart(provinceCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($province_labels) ?>,
                            datasets: [{
                                label: 'จำนวนหมู่บ้าน',
                                data: <?= json_encode($province_counts) ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating province chart:', e); }

            // Chart 4: วิสาหกิจ
            <?php if (!empty($enterprise_labels)): ?>
            try {
                const enterpriseCanvas = document.getElementById('enterpriseChart');
                if (enterpriseCanvas) {
                    new Chart(enterpriseCanvas, {
                        type: 'pie',
                        data: {
                            labels: <?= json_encode($enterprise_labels) ?>,
                            datasets: [{
                                data: <?= json_encode($enterprise_counts) ?>,
                                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating enterprise chart:', e); }
            <?php endif; ?>

            // Chart 5: SROI
            <?php if (!empty($sroi_labels)): ?>
            try {
                const sroiCanvas = document.getElementById('sroiChart');
                if (sroiCanvas) {
                    new Chart(sroiCanvas, {
                        type: 'line',
                        data: {
                            labels: <?= json_encode($sroi_labels) ?>,
                            datasets: [{
                                label: 'ค่า SROI',
                                data: <?= json_encode($sroi_values) ?>,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            } catch(e) { console.log('Error creating SROI chart:', e); }
            <?php endif; ?>
        }

        // Export functions
        function exportToPDF() {
            window.print();
        }

        function exportToExcel() {
            const data = [
                ['สถิติ', 'จำนวน'],
                ['โครงการทั้งหมด', '<?= $total_projects ?>'],
                ['กลุ่มเป้าหมาย (คน)', '<?= $total_target_people ?>'],
                ['หมู่บ้านที่เข้าร่วม', '<?= $total_villages ?>'],
                ['งบประมาณอนุมัติ', '<?= $total_approved ?>'],
                ['วิสาหกิจ', '<?= $total_enterprises ?>'],
                ['ผลิตภัณฑ์', '<?= $total_products ?>'],
                ['โรงเรียน', '<?= $total_schools ?>'],
                ['เครือข่าย', '<?= $total_networks ?>']
            ];
            
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
            data.forEach(function(rowArray) {
                let row = rowArray.join(",");
                csvContent += row + "\r\n";
            });
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "dashboard_data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Auto refresh every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
        </script>

        <!-- Refresh Button -->
        <button class="btn btn-primary refresh-btn" onclick="location.reload()" title="รีเฟรชข้อมูล">
            🔄
        </button>

        <style>
        @media print {
            .refresh-btn, .export-section {
                display: none !important;
            }
        }
        </style>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
