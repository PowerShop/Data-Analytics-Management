<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการโครงการ - หน้าแรก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .stats-card {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <!-- Hero Section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fw-bold mb-4">🎯 ระบบจัดการโครงการ</h1>
                <p class="lead mb-4">ระบบจัดการโครงการ สำหรับประการวิเคราะห์ข้อมูลของโครงการ</p>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <a href="add_project.php" class="btn btn-light btn-lg me-3 shadow">
                            <i class="fas fa-plus-circle me-2"></i>เริ่มต้นโครงการใหม่
                        </a>
                        <a href="projects_list.php" class="btn btn-outline-light btn-lg shadow">
                            <i class="fas fa-list me-2"></i>ดูโครงการทั้งหมด
                        </a>
                    </div>
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
                        <div class="feature-icon">📊</div>
                        <h3 class="fw-bold"><?= number_format($project_count) ?></h3>
                        <p class="mb-0">โครงการทั้งหมด</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card text-center">
                        <div class="feature-icon">🏘️</div>
                        <h3 class="fw-bold"><?= number_format($village_count) ?></h3>
                        <p class="mb-0">หมู่บ้านที่เข้าร่วม</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card text-center">
                        <div class="feature-icon">🏭</div>
                        <h3 class="fw-bold"><?= number_format($enterprise_count) ?></h3>
                        <p class="mb-0">วิสาหกิจ/ผู้ประกอบการ</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card text-center">
                        <div class="feature-icon">📦</div>
                        <h3 class="fw-bold"><?= number_format($product_count) ?></h3>
                        <p class="mb-0">ผลิตภัณฑ์</p>
                    </div>
                </div>
            </div>

            <!-- ฟีเจอร์หลัก -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">🚀 ฟีเจอร์หลักของระบบ</h2>
                    <p class="text-muted">สำหรับการจัดการโครงการ</p>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon text-primary">📝</div>
                            <h5 class="card-title fw-bold">จัดการโครงการ</h5>
                            <p class="card-text text-muted">เพิ่ม แก้ไข ลบ และจัดการข้อมูลโครงการได้อย่างครบถ้วน พร้อมระบบติดตามผล</p>
                            <a href="add_project.php" class="btn btn-primary">เริ่มต้นใช้งาน</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon text-success">🎯</div>
                            <h5 class="card-title fw-bold">กลุ่มเป้าหมาย</h5>
                            <p class="card-text text-muted">บันทึกและจัดการกลุ่มเป้าหมายได้หลากหลาย พร้อมระบุจำนวนคนในแต่ละกลุ่ม</p>
                            <a href="projects_list.php" class="btn btn-success">ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon text-info">🏘️</div>
                            <h5 class="card-title fw-bold">พื้นที่ดำเนินงาน</h5>
                            <p class="card-text text-muted">บันทึกข้อมูลหมู่บ้าน ชุมชน และพื้นที่ที่โครงการดำเนินการได้อย่างละเอียด</p>
                            <a href="add_project.php" class="btn btn-info">เพิ่มข้อมูล</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon text-warning">💰</div>
                            <h5 class="card-title fw-bold">จัดการงบประมาณ</h5>
                            <p class="card-text text-muted">ติดตามงบประมาณที่ขอและที่ได้รับอนุมัติ พร้อมระบบรายงานที่ชัดเจน</p>
                            <a href="add_budget.php" class="btn btn-warning">จัดการงบ</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon text-danger">📊</div>
                            <h5 class="card-title fw-bold">Dashboard & Analytics</h5>
                            <p class="card-text text-muted">ดูสถิติ แผนภูมิ และการวิเคราะห์เชิงลึกของโครงการ</p>
                            <a href="dashboard.php" class="btn btn-danger me-2">Dashboard</a>
                            <a href="analytics.php" class="btn btn-outline-danger">Analytics</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card border-0 shadow">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon text-secondary">📄</div>
                            <h5 class="card-title fw-bold">รายงานและส่งออก</h5>
                            <p class="card-text text-muted">สร้างรายงานต่างๆ และส่งออกในรูปแบบที่ต้องการ</p>
                            <a href="reports.php" class="btn btn-secondary">สร้างรายงาน</a>
                        </div>
                    </div>
                </div>
                    </div>
                </div>
            </div>

            <!-- เมนูด่วน -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-4">
                    <h3 class="fw-bold">⚡ เมนูด่วน</h3>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="add_project.php" class="btn btn-outline-primary w-100 p-3">
                        <i class="fas fa-plus fa-2x mb-2 d-block"></i>
                        เพิ่มโครงการใหม่
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="projects_list.php" class="btn btn-outline-success w-100 p-3">
                        <i class="fas fa-list fa-2x mb-2 d-block"></i>
                        ดูรายการโครงการ
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="add_budget.php" class="btn btn-outline-warning w-100 p-3">
                        <i class="fas fa-money-bill-wave fa-2x mb-2 d-block"></i>
                        เพิ่มงบประมาณ
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="dashboard.php" class="btn btn-outline-info w-100 p-3">
                        <i class="fas fa-chart-pie fa-2x mb-2 d-block"></i>
                        ดู Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <!-- <p class="mb-0">© 2025 ระบบจัดการโครงการ | พัฒนาด้วย ❤️ สำหรับการพัฒนาชุมชน</p> -->
            <p class="mb-0">© 2025 ระบบจัดการโครงการ</p>
        </div>
    </footer>
</body>
</html>
