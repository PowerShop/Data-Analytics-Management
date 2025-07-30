
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการโครงการ - หน้าแรก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            padding: 2rem;
        }
        
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
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .action-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .action-content h6 {
            margin-bottom: 0.25rem;
            font-weight: 600;
            color: #212529;
        }
        
        .action-content small {
            color: #6c757d;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tools-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <!-- Page Header -->
        <div class="page-header text-center">
            <h1 class="fw-bold mb-2">📊 ระบบจัดการโครงการ</h1>
            <p class="text-muted mb-0">จัดการข้อมูลโครงการ วิเคราะห์ และติดตามผลการดำเนินงาน</p>
        </div>
        
        <!-- เมนูหลัก -->
        <h3 class="section-title">
            <i class="fas fa-rocket"></i> เมนูหลัก
        </h3>
        <div class="quick-actions">
            <a href="add_project.php" class="action-card">
                <div class="action-icon bg-primary text-white">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-content">
                    <h6>เพิ่มโครงการใหม่</h6>
                    <small>บันทึกข้อมูลโครงการใหม่</small>
                </div>
            </a>
            <a href="projects_list.php" class="action-card">
                <div class="action-icon bg-success text-white">
                    <i class="fas fa-list"></i>
                </div>
                <div class="action-content">
                    <h6>รายการโครงการ</h6>
                    <small>ดูและจัดการโครงการทั้งหมด</small>
                </div>
            </a>
            <a href="analytics.php" class="action-card">
                <div class="action-icon bg-info text-white">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-content">
                    <h6>Analytics</h6>
                    <small>วิเคราะห์ข้อมูลเชิงลึก</small>
                </div>
            </a>
            <a href="projects_table_view.php" class="action-card">
                <div class="action-icon bg-warning text-white">
                    <i class="fas fa-table"></i>
                </div>
                <div class="action-content">
                    <h6>รายงานตาราง</h6>
                    <small>ดูข้อมูลในรูปแบบตาราง</small>
                </div>
            </a>
        </div>

        <!-- เครื่องมือเสริม -->
        <div class="tools-section">
            <h4 class="section-title">
                <i class="fas fa-tools"></i> เครื่องมือเสริม
            </h4>
            <div class="quick-actions">
                <a href="main_projects.php" class="action-card">
                    <div class="action-icon bg-light text-primary">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="action-content">
                        <h6>จัดการโครงการหลัก</h6>
                        <small>เพิ่ม แก้ไข โครงการหลักตาม ทปอ.</small>
                    </div>
                </a>
                <a href="manage_indicators.php" class="action-card">
                    <div class="action-icon bg-light text-success">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h6>จัดการตัวชี้วัด</h6>
                        <small>กำหนดและติดตามตัวชี้วัดโครงการ</small>
                    </div>
                </a>
                <a href="reports.php" class="action-card">
                    <div class="action-icon bg-light text-info">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="action-content">
                        <h6>รายงานและส่งออก</h6>
                        <small>สร้างรายงานในรูปแบบต่างๆ</small>
                    </div>
                </a>
                <a href="backup_manager.php" class="action-card">
                    <div class="action-icon bg-light text-secondary">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <div class="action-content">
                        <h6>จัดการสำรองข้อมูล</h6>
                        <small>สำรองและกู้คืนข้อมูล</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-4 text-center text-muted">
        <div class="container">
            <small>© 2025 ระบบจัดการโครงการ</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
