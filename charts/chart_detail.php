<?php 
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include database connection with error handling
$db_files = [
    '../database/db.php',
    './db.php',
    './database/db.php',
    '../db.php'
];

$db_connected = false;
foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        include $db_file;
        $db_connected = true;
        break;
    }
}

if (!$db_connected) {
    die('Error: Database connection file not found.');
}
include 'navbar.php';

// Get chart parameters
$saved_chart_id = $_GET['saved_chart_id'] ?? null;
$title = '';
$type = 'bar';
$xAxis = '';
$yAxis = '';
$dataSource = 'builder';
$customSQL = '';
$chartFilters = [];
$chartOptions = [];

if ($saved_chart_id) {
    // โหลดข้อมูลกราฟที่บันทึกไว้
    $stmt = $conn->prepare("SELECT * FROM saved_charts WHERE ChartID = ? AND IsActive = 1");
    $stmt->bind_param("i", $saved_chart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($chart = $result->fetch_assoc()) {
        $title = $chart['ChartTitle'];
        $type = $chart['ChartType'];
        $dataSource = $chart['DataSource'];
        $xAxis = $chart['XAxisData'] ?? '';
        $yAxis = $chart['YAxisData'] ?? '';
        $customSQL = $chart['CustomSQL'] ?? '';
        $chartFilters = json_decode($chart['ChartFilters'], true) ?? [];
        $chartOptions = json_decode($chart['ChartOptions'], true) ?? [];
    } else {
        // ถ้าไม่พบกราฟ
        $title = 'ไม่พบกราฟที่ต้องการ';
    }
} else {
    // ถ้าไม่มี saved_chart_id ให้ใช้พารามิเตอร์แบบเดิม
    $title = $_GET['title'] ?? 'กราฟไม่มีชื่อ';
    $type = $_GET['type'] ?? 'bar';
    $xAxis = $_GET['x_axis'] ?? '';
    $yAxis = $_GET['y_axis'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?> - ระบบจัดการโครงการ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Noto Sans Thai Looped -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .chart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            margin-bottom: 30px;
        }
        
        .chart-canvas {
            max-height: 600px;
        }
        
        .chart-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-secondary-action {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .btn-secondary-action:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        
        .btn-success-action {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .btn-success-action:hover {
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .chart-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-action {
                width: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Chart Header -->
        <div class="chart-header">
            <h1><i class="fas fa-chart-area me-3"></i><?php echo htmlspecialchars($title); ?></h1>
            <p class="mb-0">กราหรายละเอียดแบบเต็มหน้าจอ</p>
        </div>

        <!-- Statistics Summary -->
        <div class="stats-grid" id="statsGrid">
            <!-- Stats will be loaded here -->
        </div>

        <!-- Chart Container -->
        <div class="chart-container">
            <canvas id="fullChart" class="chart-canvas"></canvas>
        </div>

        <!-- Chart Actions -->
        <div class="chart-actions">
            <button class="btn btn-action" onclick="refreshChart()">
                <i class="fas fa-refresh me-2"></i>รีเฟรชข้อมูล
            </button>
            <button class="btn btn-action btn-success-action" onclick="downloadChart()">
                <i class="fas fa-download me-2"></i>ดาวน์โหลดรูปภาพ
            </button>
            <a href="index.php" class="btn btn-action btn-secondary-action">
                <i class="fas fa-arrow-left me-2"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let fullChart = null;
        const chartTitle = '<?php echo addslashes($title); ?>';
        const chartType = '<?php echo $type; ?>';
        const xAxis = '<?php echo $xAxis; ?>';
        const yAxis = '<?php echo $yAxis; ?>';
        const dataSource = '<?php echo $dataSource; ?>';
        const customSQL = <?php echo json_encode($customSQL); ?>;
        const savedChartId = <?php echo $saved_chart_id ? $saved_chart_id : 'null'; ?>;
        const chartFilters = <?php echo json_encode($chartFilters); ?>;
        const chartOptions = <?php echo json_encode($chartOptions); ?>;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadChart();
            loadStats();
        });
        
        // Load chart
        function loadChart() {
            const ctx = document.getElementById('fullChart').getContext('2d');
            
            if (savedChartId) {
                // โหลดกราฟที่บันทึกไว้
                loadSavedChart(ctx);
            } else {
                // โหลดกราฟแบบเดิม
                loadRegularChart(ctx);
            }
        }
        
        // Load saved chart
        function loadSavedChart(ctx) {
            let apiUrl;
            let requestData = new FormData();
            
            if (dataSource === 'sql') {
                apiUrl = '../api/custom_chart_api.php';
                requestData.append('data_source', 'sql');
                requestData.append('custom_sql', customSQL);
            } else {
                apiUrl = '../api/chart_data_api.php';
                requestData.append('data_source', 'builder');
                requestData.append('x_axis', xAxis);
                requestData.append('y_axis', yAxis);
            }
            
            // เพิ่ม filters จากกราฟที่บันทึกไว้
            if (chartFilters) {
                Object.keys(chartFilters).forEach(key => {
                    if (chartFilters[key]) {
                        requestData.append(key, chartFilters[key]);
                    }
                });
            }
            
            fetch(apiUrl, {
                method: 'POST',
                body: requestData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    // กรอง PHP errors ออกจาก response
                    let cleanJson = text;
                    const jsonStart = text.indexOf('{');
                    if (jsonStart > 0) {
                        cleanJson = text.substring(jsonStart);
                    }
                    
                    const data = JSON.parse(cleanJson);
                    
                    if (data.success) {
                        createChart(ctx, data, chartTitle, chartType);
                    } else {
                        console.error('Error loading chart data:', data.message);
                        showErrorMessage('ไม่สามารถโหลดข้อมูลกราฟได้: ' + data.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was:', text);
                    showErrorMessage('เกิดข้อผิดพลาดในการประมวลผลข้อมูล');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์');
            });
        }
        
        // Load regular chart (legacy method)
        function loadRegularChart(ctx) {
            // Get URL parameters for filters
            const urlParams = new URLSearchParams(window.location.search);
            const filters = {};
            
            ['project_year_start', 'project_year_end', 'strategy', 'main_project'].forEach(param => {
                if (urlParams.get(param)) {
                    filters[param] = urlParams.get(param);
                }
            });
            
            const formData = new FormData();
            formData.append('x_axis', xAxis);
            formData.append('y_axis', yAxis);
            formData.append('data_source', 'builder');
            
            // Add filters
            Object.keys(filters).forEach(key => {
                formData.append(key, filters[key]);
            });
            
            fetch('../api/chart_data_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    let cleanJson = text;
                    const jsonStart = text.indexOf('{');
                    if (jsonStart > 0) {
                        cleanJson = text.substring(jsonStart);
                    }
                    
                    const data = JSON.parse(cleanJson);
                    
                    if (data.success) {
                        createChart(ctx, data, chartTitle, chartType);
                    } else {
                        console.error('Error loading chart data:', data.message);
                        showErrorMessage('ไม่สามารถโหลดข้อมูลกราฟได้: ' + data.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    showErrorMessage('เกิดข้อผิดพลาดในการประมวลผลข้อมูล');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์');
            });
        }
        
        // Show error message
        function showErrorMessage(message) {
            const ctx = document.getElementById('fullChart').getContext('2d');
            ctx.font = '16px Arial';
            ctx.fillStyle = '#dc3545';
            ctx.textAlign = 'center';
            ctx.fillText(message, ctx.canvas.width / 2, ctx.canvas.height / 2);
        }
        
        // Create chart
        function createChart(ctx, data, title, type) {
            // Destroy existing chart
            if (fullChart) {
                fullChart.destroy();
            }
            
            // Use saved chart options or generate default colors
            let colors;
            if (chartOptions.primaryColor && chartOptions.secondaryColor) {
                colors = generateGradientColors(
                    chartOptions.primaryColor, 
                    chartOptions.secondaryColor, 
                    data.labels.length, 
                    chartOptions.opacity || 0.7
                );
            } else {
                colors = generateColors(data.labels.length);
            }
            
            // คำนวณ stepSize สำหรับแกน Y
            const maxValue = Math.max(...data.values);
            let stepSize = 10; // ค่าเริ่มต้น
            
            if (yAxis === 'budget_sum') {
                // สำหรับงบประมาณ
                if (maxValue <= 10000000) stepSize = 1000000; // 1M
                else if (maxValue <= 50000000) stepSize = 5000000; // 5M
                else stepSize = 10000000; // 10M
            } else {
                // สำหรับข้อมูลอื่นๆ
                if (maxValue <= 100) stepSize = 10;
                else if (maxValue <= 500) stepSize = 50;
                else if (maxValue <= 1000) stepSize = 100;
                else if (maxValue <= 5000) stepSize = 500;
                else stepSize = Math.ceil(maxValue / 10);
            }
            
            const chartConfig = {
                type: type,
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: getAxisLabel(yAxis),
                        data: data.values,
                        backgroundColor: colors.background,
                        borderColor: colors.border,
                        borderWidth: 3,
                        tension: 0.4 // For smoother lines
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: title,
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            padding: 20
                        },
                        legend: {
                            display: chartOptions.showLegend !== undefined ? 
                                chartOptions.showLegend : 
                                (['pie', 'doughnut', 'polarArea', 'radar'].includes(type)),
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    
                                    let value = context.parsed;
                                    if (type === 'pie' || type === 'doughnut') {
                                        value = context.parsed;
                                    } else {
                                        value = context.parsed.y;
                                    }
                                    
                                    // Format numbers
                                    if (yAxis === 'budget_sum') {
                                        label += (value / 1000000).toFixed(2) + ' ล้านบาท';
                                    } else {
                                        label += value.toLocaleString();
                                    }
                                    
                                    return label;
                                }
                            }
                        }
                    },
                    scales: ['bar', 'line', 'scatter'].includes(type) ? {
                        x: {
                            grid: {
                                display: chartOptions.showGrid !== undefined ? 
                                    chartOptions.showGrid : true,
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: chartOptions.showGrid !== undefined ? 
                                    chartOptions.showGrid : true,
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                stepSize: stepSize,
                                callback: function(value) {
                                    if (yAxis === 'budget_sum') {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    }
                                    return value.toLocaleString();
                                }
                            }
                        }
                    } : {},
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            };
            
            fullChart = new Chart(ctx, chartConfig);
        }
        
        // Load statistics
        function loadStats() {
            const urlParams = new URLSearchParams(window.location.search);
            const filters = {};
            
            ['project_year_start', 'project_year_end', 'strategy', 'main_project'].forEach(param => {
                if (urlParams.get(param)) {
                    filters[param] = urlParams.get(param);
                }
            });
            
            const apiUrl = `../api/stats_api.php?${new URLSearchParams(filters).toString()}`;
            
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ปิดการแสดงผลสถิติชั่วคราว
                        // displayStats(data.stats);
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }
        
        // Display statistics
        function displayStats(stats) {
            const statsHtml = `
                <div class="stat-card">
                    <div class="stat-value">${stats.total_projects.toLocaleString()}</div>
                    <div class="stat-label">โครงการทั้งหมด</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${(stats.total_budget / 1000000).toFixed(1)}</div>
                    <div class="stat-label">งบประมาณรวม (ล้านบาท)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${stats.total_targets.toLocaleString()}</div>
                    <div class="stat-label">กลุ่มเป้าหมายรวม</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${stats.avg_sroi ? stats.avg_sroi.toFixed(2) : '-'}</div>
                    <div class="stat-label">SROI เฉลี่ย</div>
                </div>
            `;
            
            document.getElementById('statsGrid').innerHTML = statsHtml;
        }
        
        // Refresh chart
        function refreshChart() {
            loadChart();
            loadStats();
        }
        
        // Download chart as image
        function downloadChart() {
            if (fullChart) {
                const link = document.createElement('a');
                link.download = `${chartTitle}.png`;
                link.href = fullChart.toBase64Image();
                link.click();
            }
        }
        
        // Print chart
        function printChart() {
            if (fullChart) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>${chartTitle}</title>
                        <style>
                            body { margin: 0; padding: 20px; text-align: center; font-family: 'Noto Sans Thai Looped', sans-serif; }
                            h1 { color: #333; margin-bottom: 20px; }
                            img { max-width: 100%; height: auto; }
                        </style>
                    </head>
                    <body>
                        <h1>${chartTitle}</h1>
                        <img src="${fullChart.toBase64Image()}" />
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.print();
            }
        }
        
        // Helper functions
        function getAxisLabel(axis) {
            const labels = {
                'project_count': 'จำนวนโครงการ',
                'budget_sum': 'งบประมาณ (บาท)',
                'target_count': 'จำนวนกลุ่มเป้าหมาย',
                'sroi_avg': 'SROI เฉลี่ย',
                'indicator_count': 'จำนวนตัวชี้วัด'
            };
            return labels[axis] || axis;
        }
        
        function generateColors(count, alpha = 0.7) {
            const baseColors = [
                [102, 126, 234], [118, 75, 162], [40, 167, 69],
                [255, 193, 7], [220, 53, 69], [23, 162, 184],
                [108, 117, 125], [253, 126, 20], [111, 66, 193],
                [214, 51, 132]
            ];
            
            let backgrounds = [];
            let borders = [];
            
            for (let i = 0; i < count; i++) {
                const color = baseColors[i % baseColors.length];
                backgrounds.push(`rgba(${color[0]}, ${color[1]}, ${color[2]}, ${alpha})`);
                borders.push(`rgba(${color[0]}, ${color[1]}, ${color[2]}, 1)`);
            }
            
            return {
                background: backgrounds,
                border: borders
            };
        }
        
        // Generate gradient colors from saved chart options
        function generateGradientColors(color1, color2, count, opacity) {
            const colors = [];
            const borderColors = [];
            
            // Convert hex to RGB
            const rgb1 = hexToRgb(color1);
            const rgb2 = hexToRgb(color2);
            
            for (let i = 0; i < count; i++) {
                const ratio = count === 1 ? 0 : i / (count - 1);
                
                const r = Math.round(rgb1.r + ratio * (rgb2.r - rgb1.r));
                const g = Math.round(rgb1.g + ratio * (rgb2.g - rgb1.g));
                const b = Math.round(rgb1.b + ratio * (rgb2.b - rgb1.b));
                
                colors.push(`rgba(${r}, ${g}, ${b}, ${opacity})`);
                borderColors.push(`rgba(${r}, ${g}, ${b}, 1)`);
            }
            
            return {
                background: colors,
                border: borderColors
            };
        }
        
        // Convert hex to RGB
        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    </script>
</body>
</html>
