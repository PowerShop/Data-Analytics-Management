<?php
session_start();

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: portal/');
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .menu-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .menu-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: white;
        }
        
        .menu-icon.projects {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .menu-icon.reports {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .menu-icon.charts {
            background: linear-gradient(135deg, #fd7e14 0%, #e74c3c 100%);
        }
        
        .menu-icon.tools {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }
        
        .menu-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .menu-desc {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .menu-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .menu-features li {
            color: #95a5a6;
            font-size: 0.9rem;
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }
        
        .menu-features li::before {
            content: '•';
            color: #667eea;
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        
        .stats-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 25px;
        }
        
        .quick-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .quick-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 15px;
            }
            
            .header-card {
                padding: 25px;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        /* btn-disabled */
        .btn-disabled {
            pointer-events: none;
            opacity: 0.5;
            position: relative;
        }
        
        .btn-disabled::after {
            content: 'คุณสมบัตินี้ยังไม่พร้อมใช้งาน';
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: auto;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }
        
        .btn-disabled:hover::after {
            opacity: 1;
        }
        
        .btn-disabled::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: rgba(0, 0, 0, 0.8);
            opacity: 0;
            pointer-events: auto;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }
        
        .btn-disabled:hover::before {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="welcome-section">
                <h1 class="welcome-title">
                    <i class="fas fa-chart-line me-3"></i>
                    ระบบจัดการโครงการ
                </h1>
                <p class="welcome-subtitle">
                    เข้าถึงข้อมูลโครงการ วิเคราะห์สถิติ และจัดทำรายงาน
                </p>
                
                <div class="quick-actions">
                    <a href="projects_table_view.php" class="quick-btn">
                        <i class="fas fa-table me-2"></i>ดูโครงการทั้งหมด
                    </a>
                    <!-- <a href="#" onclick="exportAllData()" class="quick-btn">
                        <i class="fas fa-download me-2"></i>ส่งออกข้อมูล
                    </a> -->
                    <a href="/kittisak/routes/?redirect=admin-charts" class="quick-btn">
                        <i class="fas fa-chart-pie me-2"></i>แผนภูมิ
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- โครงการ -->
            <a href="projects_table_view.php" class="menu-card">
                <div class="menu-icon projects">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3 class="menu-title">รายการโครงการ</h3>
                <p class="menu-desc">
                    ดูรายละเอียดโครงการทั้งหมด กรองข้อมูล และจัดการข้อมูลโครงการต่าง ๆ รวมถึงส่งออกไฟล์รายงาน Excel
                </p>
                <ul class="menu-features">
                    <li>ดูรายการโครงการแบบตาราง</li>
                    <li>กรองข้อมูลตามเงื่อนไข</li>
                    <li>ค้นหาโครงการ</li>
                    <li>ส่งออกรายงานเป็นไฟล์ Excel</li>
                </ul>
            </a>
            
            <!-- รายงาน -->
            <!-- <a href="export_projects_table_detailed_xlsx.php" class="menu-card">
                <div class="menu-icon reports">
                    <i class="fas fa-file-excel"></i>
                </div>
                <h3 class="menu-title">รายงาน Excel</h3>
                <p class="menu-desc">
                    ส่งออกรายงานเป็นไฟล์ Excel พร้อมการจัดรูปแบบและข้อมูลครบถ้วน
                </p>
                <ul class="menu-features">
                    <li>รายงานแบบรายละเอียด</li>
                    <li>แยกตามยุทธศาสตร์</li>
                    <li>จัดกลุ่มตามปี</li>
                </ul>
            </a> -->
            
            <!-- แผนภูมิ -->
            <a href="/kittisak/routes/?redirect=admin-charts" class="menu-card">
                <div class="menu-icon charts">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="menu-title">แผนภูมิและสถิติ</h3>
                <p class="menu-desc">
                    วิเคราะห์ข้อมูลด้วยแผนภูมิและกราฟต่าง ๆ
                </p>
                <ul class="menu-features">
                    <li>แผนภูมิแท่ง แผนภูมิวงกลม</li>
                    <li>สถิติตามพื้นที่</li>
                    <!-- <li>วิเคราะห์แนวโน้ม</li> -->
                </ul>
            </a>
            
            <!-- เครื่องมือ -->
            <a href="/kittisak/routes/?redirect=user-guide" class="menu-card">
                <div class="menu-icon tools">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="menu-title">เครื่องมือและช่วยเหลือ</h3>
                <p class="menu-desc">
                    คู่มือการใช้งาน เครื่องมือช่วยเหลือ
                </p>
                <ul class="menu-features">
                    <li>คู่มือการใช้งาน</li>
                    <!-- <li>FAQ คำถามที่พบบ่อย</li> -->
                    <!-- <li>การแก้ไขปัญหา</li> -->
                </ul>
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportAllData() {
            window.open('export_projects_table_detailed_xlsx.php', '_blank');
        }
        
        // เอฟเฟกต์แอนิเมชันเมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            // แอนิเมชันปรากฏของเมนู
            const menuCards = document.querySelectorAll('.menu-card');
            menuCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });
            
            // เอฟเฟกต์ hover ของ header card
            const headerCard = document.querySelector('.header-card');
            headerCard.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            headerCard.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // ป้องกันการคลิกขวา (ถ้าต้องการ)
        document.addEventListener('contextmenu', function(e) {
            // e.preventDefault(); // ยกเลิกการใช้งานถ้าไม่ต้องการ
        });
    </script>
</body>
</html>
