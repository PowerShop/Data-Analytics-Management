
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น - หน้าแรก</title>
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
            line-height: 1.7;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .action-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .action-card:hover::before {
            transform: scaleX(1);
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .action-content h6 {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #212529;
            font-size: 1.1rem;
        }
        
        .action-content small {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1px;
        }
        
        .tools-section {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }
        
        .tools-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .version-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 1rem;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
        }
        
        .feature-highlight {
            background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
        }
        
        .feature-highlight h5 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .feature-highlight p {
            margin-bottom: 0;
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-1">
        <!-- Page Header -->
        <div class="page-header text-center">
            <div class="container">
                <h1 class="fw-bold mb-3">
                    <i class="fas fa-chart-line me-3"></i>ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น
                </h1>
                <p class="mb-3 fs-5">จัดการข้อมูลโครงการ เพิ่ม ลบ แก้ไขข้อมูล และวิเคราะห์ข้อมูลอย่างครบถ้วน</p>
                
                <div class="mt-3">
                    <span class="version-badge">
                        <i class="fas fa-code-branch me-1"></i>v2.0
                    </span>
                </div>
            </div>
        </div>
        
        <div class="container">
            <!-- Welcome Message -->
            <div class="welcome-text">
                <p>ยินดีต้อนรับเข้าสู่ระบบจัดการฐานข้อมูลโครงการพัฒนาท้องถิ่น 
                เลือกฟีเจอร์ที่ต้องการใช้งานจากเมนูด้านล่าง</p>
            </div>
            
            <!-- Feature Highlight -->
            <!-- <div class="feature-highlight">
                <h5><i class="fas fa-star me-2"></i>ฟีเจอร์เด่นของระบบ</h5>
                <p>แดชบอร์ดกราฟและแผนภูมิ • จัดการโครงการหลักแบบครบถ้วน • รายงานตารางละเอียด • ส่งออกข้อมูลหลากรูปแบบ</p>
            </div> -->
            
            <!-- Stats Overview -->
            <!-- <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> สถิติระบบ
            </h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stat-number">150+</div>
                    <div class="stat-label">โครงการทั้งหมด</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">2,500+</div>
                    <div class="stat-label">กลุ่มเป้าหมาย</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">25+</div>
                    <div class="stat-label">ตัวชี้วัด</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-number">85</div>
                    <div class="stat-label">พื้นที่ดำเนินการ</div>
                </div>
            </div> -->
        
        <!-- เมนูหลัก -->
        <h2 class="section-title">
            <i class="fas fa-rocket"></i> เมนูหลัก
        </h2>
        <div class="quick-actions">
            <a href="add_project.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-content">
                    <h6>เพิ่มโครงการใหม่</h6>
                    <small>บันทึกข้อมูลโครงการใหม่พร้อมรายละเอียดครบถ้วน</small>
                </div>
            </a>
            <a href="projects_list.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="action-content">
                    <h6>รายการโครงการ</h6>
                    <small>ดูและจัดการโครงการทั้งหมดที่มีอยู่ในระบบ</small>
                </div>
            </a>
            <!-- <a href="../charts/index.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="action-content">
                    <h6>แดชบอร์ดกราฟและแผนภูมิ</h6>
                    <small>วิเคราะห์ข้อมูลด้วยกราฟและแผนภูมิแบบเรียลไทม์</small>
                </div>
            </a>
            <a href="projects_table_view.php" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-table"></i>
                </div>
                <div class="action-content">
                    <h6>รายงานโครงการแบบตาราง</h6>
                    <small>ดูข้อมูลโครงการในรูปแบบตารางพร้อมฟิลเตอร์ขั้นสูง</small>
                </div>
            </a> -->
        </div>

        <!-- เครื่องมือเสริม -->
        <div class="tools-section">
            <h3 class="section-title">
                <i class="fas fa-tools"></i> เครื่องมือเสริม
            </h3>
            <div class="quick-actions">
                <a href="main_projects.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="action-content">
                        <h6>จัดการโครงการหลัก</h6>
                        <small>เพิ่ม แก้ไข โครงการหลักตาม ทปอ. แบบครบถ้วน</small>
                    </div>
                </a>
                <a href="manage_indicators.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h6>จัดการตัวชี้วัด</h6>
                        <small>กำหนดและติดตามตัวชี้วัดความสำเร็จของโครงการ</small>
                    </div>
                </a>
                <!-- <a href="../backup_manager/backup_manager.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <div class="action-content">
                        <h6>จัดการสำรองข้อมูล</h6>
                        <small>สำรองและกู้คืนข้อมูลระบบอย่างปลอดภัย</small>
                    </div>
                </a> -->
                <!-- <a href="../user_guide.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="action-content">
                        <h6>คู่มือการใช้งาน</h6>
                        <small>เรียนรู้การใช้งานระบบอย่างครบถ้วน</small>
                    </div>
                </a> -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll to top functionality
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                if (!document.querySelector('.scroll-to-top')) {
                    const scrollBtn = document.createElement('button');
                    scrollBtn.className = 'btn btn-primary scroll-to-top position-fixed';
                    scrollBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);';
                    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                    scrollBtn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
                    document.body.appendChild(scrollBtn);
                }
            } else {
                const scrollBtn = document.querySelector('.scroll-to-top');
                if (scrollBtn) scrollBtn.remove();
            }
        });

        // Add loading animation for cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.action-card, .stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
