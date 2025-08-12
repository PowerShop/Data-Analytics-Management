<?php
session_start();

// ถ้ามี session แล้วให้ redirect ไปหน้าหลัก
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการข้อมูลเชิงวิเคราะห์</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .portal-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        
        .portal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .portal-body {
            padding: 40px;
        }
        
        .portal-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 20px;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .portal-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.2);
            color: inherit;
            text-decoration: none;
        }
        
        .portal-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .portal-card h4 {
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .portal-card p {
            color: #666;
            margin-bottom: 0;
            font-size: 14px;
        }
        
        .portal-card.frontend {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%);
        }
        
        .portal-card.frontend:hover {
            border-color: #28a745;
            box-shadow: 0 15px 30px rgba(40, 167, 69, 0.2);
        }
        
        .portal-card.frontend i {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .portal-card.backend {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(238, 90, 82, 0.1) 100%);
        }
        
        .portal-card.backend:hover {
            border-color: #dc3545;
            box-shadow: 0 15px 30px rgba(220, 53, 69, 0.2);
        }
        
        .portal-card.backend i {
            background: linear-gradient(135deg, #dc3545 0%, #ee5a52 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .min-vh-100 {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .portal-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .system-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .system-info h6 {
            color: #495057;
            margin-bottom: 10px;
        }
        
        .system-info p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 13px;
        }
        
        .version-badge {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="min-vh-100">
        <div class="portal-wrapper">
            <div class="portal-container">
                <div class="portal-header">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        ระบบจัดการข้อมูล
                    </h3>
                    <p class="mb-0 mt-2 opacity-75">เลือกส่วนที่ต้องการเข้าใช้งาน</p>
                </div>
                
                <div class="portal-body">
                    <a href="../login.php" class="portal-card frontend">
                        <i class="fas fa-users"></i>
                        <h4>หน้าบ้าน</h4>
                        <p>สำหรับผู้ใช้ทั่วไป ดูข้อมูลโครงการและสถิติ และส่งออกข้อมูล</p>
                    </a>
                    
                    <a href="../backend/login.php" class="portal-card backend">
                        <i class="fas fa-cogs"></i>
                        <h4>หลังบ้าน</h4>
                        <p>สำหรับผู้ดูแลระบบ จัดการข้อมูลและการตั้งค่า</p>
                    </a>
                    
                    <div class="system-info">
                        <h6><i class="fas fa-info-circle me-2"></i>ข้อมูลระบบ</h6>
                        <p>ระบบจัดการโครงการและวิเคราะห์ข้อมูล</p>
                        <!-- <p>สำหรับสถาบันการศึกษาและหน่วยงานภาครัฐ</p> -->
                        <span class="version-badge">เวอร์ชัน 2.0.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // เพิ่ม animation เมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.portal-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>
