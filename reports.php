<?php include 'db.php'; ?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Reports - ระบบจัดการโครงการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .report-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .report-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #667eea;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .summary-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            height: 350px;
            position: relative;
            overflow: hidden;
        }
        
        .chart-wrapper {
            position: relative;
            height: 250px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Report Header -->
        <div class="report-header">
            <h2 class="fw-bold mb-3">📄 Reports - ระบบรายงาน</h2>
            <p class="mb-0">สร้างและส่งออกรายงานในรูปแบบต่างๆ</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h5 class="mb-3">🔍 กรองข้อมูลสำหรับรายงาน</h5>
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">จังหวัด</label>
                        <select class="form-select" name="province">
                            <option value="">ทั้งหมด</option>
                            <?php
                            $provinces = $conn->query("SELECT DISTINCT Province FROM ProjectVillages WHERE Province IS NOT NULL ORDER BY Province");
                            while ($province = $provinces->fetch_assoc()) {
                                $selected = ($_GET['province'] ?? '') == $province['Province'] ? 'selected' : '';
                                echo "<option value='{$province['Province']}' $selected>{$province['Province']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">งบประมาณขั้นต่ำ</label>
                        <input type="number" class="form-control" name="budget_min" value="<?= $_GET['budget_min'] ?? '' ?>" placeholder="เช่น 100000">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>กรองข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php
            // Build WHERE clause based on filters
            $where_conditions = [];
            $params           = [];

            if (!empty($_GET['province'])) {
                $where_conditions[] = "pv.Province = ?";
                $params[]           = $_GET['province'];
            }

            if (!empty($_GET['budget_min'])) {
                $where_conditions[] = "b.ApprovedAmount >= ?";
                $params[]           = $_GET['budget_min'];
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Prepare and execute filtered queries
            $filtered_projects = "
                SELECT COUNT(DISTINCT p.ProjectID) as count 
                FROM Projects p
                LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
                LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                $where_clause
            ";
            $stmt              = $conn->prepare($filtered_projects);
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $total_filtered_projects = $stmt->get_result()->fetch_assoc()['count'];
        ?>

        <script>
            // Report generation functions
        document.addEventListener('DOMContentLoaded', function() {
            window.generateProjectSummary = function() {
                const params = new URLSearchParams(window.location.search);
                window.open('export_report.php?type=project_summary&' + params.toString(), '_blank');
            }

            window.generateBudgetReport = function() {
                const params = new URLSearchParams(window.location.search);
                window.open('export_report.php?type=budget&' + params.toString(), '_blank');
            }

            window.generatePerformanceReport = function() {
                const params = new URLSearchParams(window.location.search);
                window.open('export_report.php?type=performance&' + params.toString(), '_blank');
            }

            window.generateAreaReport = function() {
                const params = new URLSearchParams(window.location.search);
                window.open('export_report.php?type=area&' + params.toString(), '_blank');
            }

            window.generateEnterpriseReport = function() {
                const params = new URLSearchParams(window.location.search);
                window.open('export_report.php?type=enterprise&' + params.toString(), '_blank');
            }

            window.openCustomReport = function() {
                // Open modal or redirect to custom report builder
                alert('ฟีเจอร์รายงานแบบกำหนดเองจะเปิดให้ใช้งานในเร็วๆ นี้');
            }
        });
        </script>

        <!-- Report Types -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-card text-center">
                    <div class="report-icon">📊</div>
                    <h5>รายงานสรุปโครงการ</h5>
                    <p class="text-muted">สรุปข้อมูลโครงการทั้งหมด รวมถึงสถิติและการวิเคราะห์</p>
                    <button class="btn btn-primary" onclick="generateProjectSummary()">
                        <i class="fas fa-download me-2"></i>สร้างรายงาน
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-card text-center">
                    <div class="report-icon">💰</div>
                    <h5>รายงานงบประมาณ</h5>
                    <p class="text-muted">รายงานการใช้งบประมาณ การอนุมัติ และประสิทธิภาพ</p>
                    <button class="btn btn-success" onclick="generateBudgetReport()">
                        <i class="fas fa-download me-2"></i>สร้างรายงาน
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-card text-center">
                    <div class="report-icon">📈</div>
                    <h5>รายงานประสิทธิภาพ</h5>
                    <p class="text-muted">วิเคราะห์ SROI, GVH และตัวชี้วัดความสำเร็จ</p>
                    <button class="btn btn-info" onclick="generatePerformanceReport()">
                        <i class="fas fa-download me-2"></i>สร้างรายงาน
                    </button>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="report-card text-center">
                    <div class="report-icon">🗺️</div>
                    <h5>รายงานพื้นที่</h5>
                    <p class="text-muted">การกระจายโครงการตามพื้นที่ จังหวัด และหมู่บ้าน</p>
                    <button class="btn btn-warning" onclick="generateAreaReport()">
                        <i class="fas fa-download me-2"></i>สร้างรายงาน
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-card text-center">
                    <div class="report-icon">🏭</div>
                    <h5>รายงานวิสาหกิจ</h5>
                    <p class="text-muted">ข้อมูลวิสาหกิจ ผลิตภัณฑ์ และผลการดำเนินงาน</p>
                    <button class="btn btn-secondary" onclick="generateEnterpriseReport()">
                        <i class="fas fa-download me-2"></i>สร้างรายงาน
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="report-card text-center">
                    <div class="report-icon">📋</div>
                    <h5>รายงานแบบกำหนดเอง</h5>
                    <p class="text-muted">สร้างรายงานตามความต้องการเฉพาะ</p>
                    <button class="btn btn-dark" onclick="openCustomReport()">
                        <i class="fas fa-cog me-2"></i>กำหนดเอง
                    </button>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="row">
            <div class="col-12">
                <div class="summary-table">
                    <h5 class="mb-3">📋 ตัวอย่างข้อมูลที่กรองแล้ว (<?php echo number_format($total_filtered_projects)?> โครงการ)</h5>

                    <?php
                        // Get sample data for preview
                        $preview_query = "
                        SELECT p.ProjectName, p.AgencyName,
                               COUNT(DISTINCT pv.ID) as village_count,
                               COUNT(DISTINCT pe.ID) as enterprise_count,
                               COUNT(DISTINCT pp.ID) as product_count,
                               COALESCE(SUM(b.ApprovedAmount), 0) as total_budget,
                               COALESCE(AVG(ps.SROIResult), 0) as avg_sroi
                        FROM Projects p
                        LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
                        LEFT JOIN ProjectEnterprises pe ON p.ProjectID = pe.ProjectID
                        LEFT JOIN ProjectProducts pp ON p.ProjectID = pp.ProjectID
                        LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                        LEFT JOIN ProjectSROI ps ON p.ProjectID = ps.ProjectID
                        $where_clause
                        GROUP BY p.ProjectID, p.ProjectName, p.AgencyName
                        ORDER BY p.ProjectName
                        LIMIT 10
                    ";

                        $stmt = $conn->prepare($preview_query);
                        if (!empty($params)) {
                            $types = str_repeat('s', count($params));
                            $stmt->bind_param($types, ...$params);
                        }
                        $stmt->execute();
                        $preview_data = $stmt->get_result();
                    ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>โครงการ</th>
                                    <th>หน่วยงาน</th>
                                    <th>หมู่บ้าน</th>
                                    <th>วิสาหกิจ</th>
                                    <th>ผลิตภัณฑ์</th>
                                    <th>งบประมาณ</th>
                                    <th>SROI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $preview_data->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['ProjectName'])?></td>
                                    <td><?php echo htmlspecialchars($row['AgencyName'] ?: 'ไม่ระบุ')?></td>
                                    <td><span class="badge bg-info"><?php echo $row['village_count']?></span></td>
                                    <td><span class="badge bg-success"><?php echo $row['enterprise_count']?></span></td>
                                    <td><span class="badge bg-warning"><?php echo $row['product_count']?></span></td>
                                    <td><?php echo number_format($row['total_budget'])?></td>
                                    <td><strong><?php echo number_format($row['avg_sroi'], 2)?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Preview -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="summary-table chart-container">
                    <h5>📊 การกระจายตามจังหวัด (ข้อมูลที่กรอง)</h5>
                    <div class="chart-wrapper">
                        <canvas id="provinceChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-table chart-container">
                    <h5>💰 การกระจายงบประมาณ</h5>
                    <div class="chart-wrapper">
                        <canvas id="budgetChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <?php
            // Chart data for filtered projects
            $province_data = [];
            $budget_ranges = ['< 100K' => 0, '100K-500K' => 0, '500K-1M' => 0, '1M-5M' => 0, '> 5M' => 0];

            // Province data
            $province_query = "
                SELECT pv.Province, COUNT(DISTINCT p.ProjectID) as count
                FROM Projects p
                JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
                LEFT JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                $where_clause
                GROUP BY pv.Province
                ORDER BY count DESC
                LIMIT 10
            ";
            $stmt = $conn->prepare($province_query);
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $province_result = $stmt->get_result();

            while ($row = $province_result->fetch_assoc()) {
                $province_data[$row['Province']] = $row['count'];
            }

            // Budget data
            $budget_query = "
                SELECT SUM(b.ApprovedAmount) as total_budget
                FROM Projects p
                LEFT JOIN ProjectVillages pv ON p.ProjectID = pv.ProjectID
                JOIN BudgetItems b ON p.ProjectID = b.ProjectID
                $where_clause
                GROUP BY p.ProjectID
            ";
            $stmt = $conn->prepare($budget_query);
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $budget_result = $stmt->get_result();

            while ($row = $budget_result->fetch_assoc()) {
                $budget = $row['total_budget'];
                if ($budget < 100000) {
                    $budget_ranges['< 100K']++;
                } elseif ($budget < 500000) {
                    $budget_ranges['100K-500K']++;
                } elseif ($budget < 1000000) {
                    $budget_ranges['500K-1M']++;
                } elseif ($budget < 5000000) {
                    $budget_ranges['1M-5M']++;
                } else {
                    $budget_ranges['> 5M']++;
                }
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
            // Province Chart
            try {
                const provinceCanvas = document.getElementById('provinceChart');
                if (provinceCanvas) {
                    new Chart(provinceCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode(array_keys($province_data))?>,
                            datasets: [{
                                label: 'จำนวนโครงการ',
                                data: <?php echo json_encode(array_values($province_data))?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating province chart:', e); }

            // Budget Chart
            try {
                const budgetCanvas = document.getElementById('budgetChart');
                if (budgetCanvas) {
                    new Chart(budgetCanvas, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode(array_keys($budget_ranges))?>,
                            datasets: [{
                                label: 'จำนวนโครงการ',
                                data: <?php echo json_encode(array_values($budget_ranges))?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            } catch(e) { console.log('Error creating budget chart:', e); }
        }
        </script>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
