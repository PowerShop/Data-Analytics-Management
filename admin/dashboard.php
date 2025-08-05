<?php
// session_start();

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการโครงการ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }
        
        body {
            background: #f8f9fa;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: block;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
        }
        
        .feature-desc {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            color: white;
        }
        
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
        }
        
        .stats-overview {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .action-btn {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            color: white;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <!-- <div class="header-section"> -->
        <!-- <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        แดชบอร์ดจัดการโครงการ
                    </h1>
                </div>
                <div class="col-md-4 text-end">
                    <div class="user-info mb-3">
                        <i class="fas fa-user me-2"></i>
                        ยินดีต้อนรับ, <?php echo $_SESSION['admin_username']; ?>
                    </div>
                    <br>
                    <a href="?logout=1" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        ออกจากระบบ
                    </a>
                </div>
            </div>
        </div> -->
    <!-- </div> -->
    
    <div class="container mt-2">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>
                        ยินดีต้อนรับสู่ระบบรายงาน
                    </h3>
                    <p class="text-muted mb-0">
                        เข้าถึงข้อมูลโครงการ สถิติ และรายงานต่าง ๆ ได้อย่างครบถ้วนและสะดวก 
                        พร้อมเครื่องมือวิเคราะห์ข้อมูลและส่งออกรายงานในรูปแบบ Excel
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-chart-pie" style="font-size: 4rem; color: #667eea;"></i>
                </div>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="row text-center">
                <div class="col-md-3">
                    <h3><i class="fas fa-project-diagram me-2"></i>โครงการ</h3>
                    <p class="mb-0">ดูรายละเอียดโครงการทั้งหมด</p>
                </div>
                <!-- <div class="col-md-3">
                    <h3><i class="fas fa-chart-bar me-2"></i>สถิติ</h3>
                    <p class="mb-0">วิเคราะห์ข้อมูลเชิงลึก</p>
                </div> -->
                <div class="col-md-3">
                    <h3><i class="fas fa-file-excel me-2"></i>รายงาน</h3>
                    <p class="mb-0">ส่งออกข้อมูลรูปแบบ Excel</p>
                </div>
                <div class="col-md-3">
                    <h3><i class="fas fa-filter me-2"></i>ตัวกรอง</h3>
                    <p class="mb-0">กรองข้อมูลตามต้องการ</p>
                </div>
            </div>
        </div>
        
        <!-- Feature Cards -->
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-4">
                <a href="projects_table_view.php" class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-table"></i>
                    </div>
                    <h5 class="feature-title">รายงานโครงการแบบตาราง</h5>
                    <p class="feature-desc">
                        ดูข้อมูลโครงการทั้งหมดในรูปแบบตาราง พร้อมรายละเอียดครบถ้วน 
                        สามารถกรองข้อมูลและส่งออก Excel ได้
                    </p>
                </a>
            </div>
            
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="quick-actions">
                    <h5 class="mb-4">
                        <i class="fas fa-bolt me-2"></i>
                        เครื่องมือ
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="action-btn" onclick="window.location.href='projects_table_view.php'">
                            <i class="fas fa-eye me-2"></i>
                            ดูรายการโครงการทั้งหมด
                        </button>
                        
                        <button type="button" class="action-btn" onclick="exportAllData()">
                            <i class="fas fa-download me-2"></i>
                            ส่งออกข้อมูลทั้งหมด
                        </button>
                        
                        <!-- <button type="button" class="action-btn" onclick="openFilterDialog()">
                            <i class="fas fa-filter me-2"></i>
                            ตั้งค่าตัวกรองข้อมูล
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="welcome-card">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        คำแนะนำการใช้งาน
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-search me-2 text-success"></i>การค้นหาข้อมูล</h6>
                            <p class="text-muted small">
                                ใช้ตัวกรองต่าง ๆ เพื่อค้นหาโครงการตามปี พื้นที่ หน่วยงาน หรือเงื่อนไขอื่น ๆ
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-file-export me-2 text-warning"></i>การส่งออกข้อมูล</h6>
                            <p class="text-muted small">
                                ส่งออกรายงานเป็นไฟล์ Excel พร้อมการจัดรูปแบบและฟอนต์ไทย
                            </p>
                        </div>
                        <!-- <div class="col-md-4">
                            <h6><i class="fas fa-chart-line me-2 text-info"></i>การวิเคราะห์</h6>
                            <p class="text-muted small">
                                ดูสถิติสรุปและวิเคราะห์ผลการดำเนินงานโครงการได้ทันที
                            </p>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportAllData() {
            window.open('export_projects_table_detailed_xlsx.php', '_blank');
        }
        
        function openFilterDialog() {
            window.location.href = 'projects_table_view.php#filterForm';
        }
        
        // เอฟเฟกต์เมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            // แสดงข้อความต้อนรับ
            setTimeout(function() {
                if (typeof bootstrap !== 'undefined') {
                    // สามารถเพิ่ม toast notification ได้ที่นี่
                }
            }, 1000);
        });
    </script>
</body>
</html>
