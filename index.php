
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
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: none;
            border-radius: 15px;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .quick-action-btn {
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body class="bg-light">
    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">📊 ระบบจัดการโครงการ</h1>
            <p class="lead mb-4">ระบบจัดการข้อมูลโครงการ สำหรับการวิเคราะห์และติดตามผลการดำเนินงาน</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="add_project.php" class="btn btn-light btn-lg shadow">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มโครงการใหม่
                </a>
                <a href="projects_list.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-list me-2"></i>ดูโครงการทั้งหมด
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- สถิติเบื้องต้น -->
        <?php 
        include 'db.php';
        
        // นับจำนวนโครงการ
        $project_count = $conn->query("SELECT COUNT(*) as count FROM Projects")->fetch_assoc()['count'];
        
        // นับจำนวนหมู่บ้าน
        $village_count = $conn->query("SELECT COUNT(*) as count FROM ProjectVillages")->fetch_assoc()['count'];
        
        // นับจำนวนผลิตภัณฑ์
        $product_count = $conn->query("SELECT COUNT(*) as count FROM ProjectProducts")->fetch_assoc()['count'];
        
        // นับจำนวนวิสาหกิจ
        $enterprise_count = $conn->query("SELECT COUNT(*) as count FROM ProjectEnterprises")->fetch_assoc()['count'];
        ?>
        
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <div class="feature-icon text-primary">📊</div>
                    <h3 class="fw-bold text-dark"><?= number_format($project_count) ?></h3>
                    <p class="mb-0 text-muted">โครงการทั้งหมด</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <div class="feature-icon text-success">🏘️</div>
                    <h3 class="fw-bold text-dark"><?= number_format($village_count) ?></h3>
                    <p class="mb-0 text-muted">หมู่บ้านที่เข้าร่วม</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <div class="feature-icon text-warning">🏭</div>
                    <h3 class="fw-bold text-dark"><?= number_format($enterprise_count) ?></h3>
                    <p class="mb-0 text-muted">วิสาหกิจ/ผู้ประกอบการ</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card text-center">
                    <div class="feature-icon text-info">📦</div>
                    <h3 class="fw-bold text-dark"><?= number_format($product_count) ?></h3>
                    <p class="mb-0 text-muted">ผลิตภัณฑ์</p>
                </div>
            </div>
        </div>

        <!-- เมนูด่วน -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h3 class="fw-bold">⚡ เมนูหลัก</h3>
                <p class="text-muted">เลือกฟังก์ชันที่ต้องการใช้งาน</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="add_project.php" class="btn btn-outline-primary w-100 quick-action-btn">
                    <i class="fas fa-plus-circle fa-2x mb-3 d-block text-primary"></i>
                    <h6 class="fw-bold">เพิ่มโครงการใหม่</h6>
                    <small class="text-muted">บันทึกข้อมูลโครงการใหม่</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="projects_list.php" class="btn btn-outline-success w-100 quick-action-btn">
                    <i class="fas fa-list fa-2x mb-3 d-block text-success"></i>
                    <h6 class="fw-bold">รายการโครงการ</h6>
                    <small class="text-muted">ดูและจัดการโครงการ</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="dashboard.php" class="btn btn-outline-info w-100 quick-action-btn">
                    <i class="fas fa-chart-pie fa-2x mb-3 d-block text-info"></i>
                    <h6 class="fw-bold">Dashboard</h6>
                    <small class="text-muted">ภาพรวมและสถิติ</small>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <a href="analytics.php" class="btn btn-outline-warning w-100 quick-action-btn">
                    <i class="fas fa-chart-line fa-2x mb-3 d-block text-warning"></i>
                    <h6 class="fw-bold">Analytics</h6>
                    <small class="text-muted">วิเคราะห์เชิงลึก</small>
                </a>
            </div>
        </div>

        <!-- เครื่องมือเสริม -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h4 class="fw-bold">🛠️ เครื่องมือเสริม</h4>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon text-primary">�</div>
                        <h5 class="card-title fw-bold">จัดการโครงการหลัก</h5>
                        <p class="card-text text-muted">เพิ่ม แก้ไข โครงการหลักตาม ทปอ.</p>
                        <a href="main_projects.php" class="btn btn-outline-primary">จัดการ</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon text-success">�</div>
                        <h5 class="card-title fw-bold">จัดการตัวชี้วัด</h5>
                        <p class="card-text text-muted">กำหนดและติดตามตัวชี้วัดโครงการ</p>
                        <a href="manage_indicators.php" class="btn btn-outline-success">จัดการ</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon text-info">📄</div>
                        <h5 class="card-title fw-bold">รายงานและส่งออก</h5>
                        <p class="card-text text-muted">สร้างรายงานในรูปแบบต่างๆ</p>
                        <a href="reports.php" class="btn btn-outline-info">สร้างรายงาน</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">© 2025 ระบบจัดการโครงการ</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
