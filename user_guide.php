<?php
// session_start();

include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คู่มือการใช้งานระบบ</title>
    
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
        
        .guide-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .content-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .feature-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .feature-desc {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .login-info {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .step-list {
            counter-reset: step-counter;
            list-style: none;
            padding-left: 0;
        }
        
        .step-list li {
            counter-increment: step-counter;
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            position: relative;
            padding-left: 60px;
        }
        
        .step-list li:before {
            content: counter(step-counter);
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: #667eea;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .column-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .column-item {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }
        
        .warning-box {
            background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .error-box {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .contact-box {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        
        .toc {
            background: #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .toc ul {
            list-style: none;
            padding-left: 0;
        }
        
        .toc li {
            margin-bottom: 8px;
        }
        
        .toc a {
            color: #495057;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .toc a:hover {
            color: #667eea;
        }
        
        kbd {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            color: #495057;
            display: inline-block;
            font-family: 'Courier New', monospace;
            font-size: 0.8em;
            font-weight: 600;
            line-height: 1;
            padding: 2px 6px;
            margin: 0 2px;
        }
        
        .version-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .update-badge {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-1">
        <!-- Header -->
        <div class="guide-header text-center">
            <div class="container">
                <h1 class="fw-bold mb-3">
                    <i class="fas fa-book-open me-3"></i>คู่มือการใช้งานระบบ
                </h1>
                <p class="mb-0 fs-5">เรียนรู้การใช้งานระบบจัดการโครงการและวิเคราะห์ข้อมูลอย่างครบถ้วน</p>
                <div class="mt-3">
                    <span class="version-badge">
                        <i class="fas fa-code-branch me-1"></i>v2.0
                    </span>
                    <span class="update-badge">
                        <i class="fas fa-check-circle me-1"></i>อัปเดทล่าสุด
                    </span>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- สารบัญ -->
            <div class="content-section">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>สารบัญ
                </h2>
                <div class="toc">
                    <ul>
                        <!-- <li><a href="#overview">🔐 ภาพรวมระบบ</a></li> -->
                        <li><a href="#login">🔑 หน้าเข้าสู่ระบบ</a></li>
                        <li><a href="#dashboard">🏠 หน้าแดชบอร์ดหลัก</a></li>
                        <li><a href="#main-projects">📋 จัดการโครงการหลัก</a></li>
                        <li><a href="#charts-dashboard">📊 แดชบอร์ดกราฟและแผนภูมิ</a></li>
                        <li><a href="#table-report">📋 รายงานโครงการแบบตาราง</a></li>
                        <li><a href="#project-detail">📄 รายละเอียดโครงการ</a></li>
                        <!-- <li><a href="#backend-system">🔧 ระบบจัดการหลังบ้าน</a></li> -->
                        <li><a href="#support-files">⚙️ ไฟล์สนับสนุน</a></li>
                        <li><a href="#usage">🚀 วิธีการใช้งานทั่วไป</a></li>
                        <li><a href="#shortcuts">⌨️ คีย์ลัดและทิปการใช้งาน</a></li>
                        <li><a href="#warnings">⚠️ ข้อควรระวัง</a></li>
                        <li><a href="#troubleshooting">🔧 การแก้ไขปัญหา</a></li>
                        <li><a href="#contact">📞 การติดต่อสนับสนุน</a></li>
                    </ul>
                </div>
            </div>

            <!-- ภาพรวมระบบ -->
            <!-- <div class="content-section" id="overview">
                <h2 class="section-title">
                    <i class="fas fa-shield-alt"></i>ภาพรวมระบบ
                </h2>
                <p>ระบบจัดการโครงการและวิเคราะห์ข้อมูล เป็นแพลตฟอร์มครบครันสำหรับการจัดการและวิเคราะห์ข้อมูลโครงการอย่างมีประสิทธิภาพ</p>

                <div class="highlight">
                    <strong><i class="fas fa-star me-2"></i>จุดเด่นของระบบ:</strong>
                    <ul class="mt-2 mb-0">
                        <li>📊 <strong>แดชบอร์ดกราฟและแผนภูมิ</strong> - วิเคราะห์ข้อมูลด้วยกราฟแบบเรียลไทม์</li>
                        <li>🔄 <strong>การกู้คืนกราฟที่ถูกลบ</strong> - ป้องกันการสูญเสียข้อมูลกราฟ</li>
                        <li>📋 <strong>จัดการโครงการหลัก</strong> - ระบบ CRUD แบบครบถ้วน</li>
                        <li>🎯 <strong>กรองข้อมูลขั้นสูง</strong> - ฟิลเตอร์แบบ Cascading</li>
                        <li>📤 <strong>ส่งออกข้อมูลหลากรูปแบบ</strong> - Excel, PNG, JPG, PDF</li>
                        <li>🔐 <strong>ระบบรักษาความปลอดภัย</strong> - การยืนยันตัวตนและการจัดการสิทธิ์</li>
                        <li>📱 <strong>Responsive Design</strong> - รองรับทุกอุปกรณ์</li>
                        <li>⚡ <strong>ประสิทธิภาพสูง</strong> - โหลดข้อมูลเร็วและราบรื่น</li>
                    </ul>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🎨 ส่วนติดต่อผู้ใช้</div>
                            <div class="feature-desc">
                                <ul class="mb-0">
                                    <li>Bootstrap 5.3.0 Framework</li>
                                    <li>Font Awesome 6.4.0 Icons</li>
                                    <li>Noto Sans Thai Looped Font</li>
                                    <li>Modern UI/UX Design</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🛠️ เทคโนโลยีที่ใช้</div>
                            <div class="feature-desc">
                                <ul class="mb-0">
                                    <li>PHP 8+ Backend</li>
                                    <li>MySQL Database</li>
                                    <li>Chart.js สำหรับกราฟ</li>
                                    <li>JSZip สำหรับส่งออก</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- หน้าเข้าสู่ระบบ -->
            <div class="content-section" id="login">
                <h2 class="section-title">
                    <i class="fas fa-sign-in-alt"></i>หน้าเข้าสู่ระบบ (<code>login.php</code>)
                </h2>
                
                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">ระบบรักษาความปลอดภัยสำหรับการเข้าถึง Admin Panel</div>
                </div>

                <div class="login-info">
                    <h5><i class="fas fa-key me-2"></i>ข้อมูลการเข้าสู่ระบบ</h5>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <strong>admin</strong> / <strong>[รหัสผ่าน]</strong><br>
                            <small>ผู้ดูแลระบบหลัก</small>
                        </div>
                        <div class="col-md-4">
                            <strong>manager</strong> / <strong>[รหัสผ่าน]</strong><br>
                            <small>ผู้จัดการโครงการ</small>
                        </div>
                        <div class="col-md-4">
                            <strong>director</strong> / <strong>[รหัสผ่าน]</strong><br>
                            <small>ผู้อำนวยการ</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-light">
                            <i class="fas fa-info-circle me-1"></i>
                            สำหรับข้อมูลการเข้าสู่ระบบจริง กรุณาติดต่อผู้ดูแลระบบ
                        </small>
                    </div>
                </div>

                <!-- <h5>✨ ฟีเจอร์</h5>
                <ul>
                    <li>✅ ระบบยืนยันตัวตนแบบปลอดภัย</li>
                    <li>✅ UI/UX สวยงามด้วย Bootstrap และ Font Awesome</li>
                    <li>✅ Responsive Design รองรับทุกอุปกรณ์</li>
                    <li>✅ Auto-redirect เมื่อ Login สำเร็จ</li>
                </ul> -->

                <h5>📝 วิธีใช้งาน</h5>
                <ol class="step-list">
                    <li>เข้าไปยัง <code>/admin/login.php</code></li>
                    <li>กรอกชื่อผู้ใช้และรหัสผ่าน</li>
                    <li>คลิก "เข้าสู่ระบบ"</li>
                    <li>ระบบจะพาไปยังหน้า Dashboard อัตโนมัติ</li>
                </ol>
            </div>

            <!-- หน้าแดชบอร์ด -->
            <div class="content-section" id="dashboard">
                <h2 class="section-title">
                    <i class="fas fa-tachometer-alt"></i>หน้าแดชบอร์ด (<code>dashboard.php</code>)
                </h2>
                
                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">หน้าหลักของระบบ Admin ที่แสดงเมนูและฟีเจอร์ต่างๆ</div>
                </div>

                <h5>🚀 ฟีเจอร์หลัก</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">📊 รายงานโครงการแบบตาราง</div>
                            <div class="feature-desc">ดูข้อมูลโครงการในรูปแบบตารางแบบละเอียด</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">🔧 หลังบ้าน</div>
                            <div class="feature-desc">เข้าสู่ระบบจัดการหลัก</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">👤 ข้อมูลผู้ใช้</div>
                            <div class="feature-desc">แสดงชื่อผู้ใช้งานและเมนู Logout</div>
                        </div>
                    </div>
                </div>

                <h5>🧭 การนำทาง</h5>
                <ul>
                    <li>Navigation Bar ด้านบนสำหรับเปลี่ยนหน้า</li>
                    <li>User Dropdown สำหรับ Logout</li>
                    <li>Cards สำหรับเข้าถึงฟีเจอร์ต่างๆ</li>
                </ul>
            </div>

            <!-- จัดการโครงการหลัก -->
            <div class="content-section" id="main-projects">
                <h2 class="section-title">
                    <i class="fas fa-project-diagram"></i>จัดการโครงการหลัก (<code>main_projects.php</code>)
                </h2>

                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">ระบบจัดการโครงการหลักแบบครบถ้วน (CRUD) พร้อมฟีเจอร์การแก้ไขและลบชั่วคราว</div>
                </div>

                <h5>🚀 ฟีเจอร์หลัก</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">➕ เพิ่มโครงการใหม่</div>
                            <div class="feature-desc">สร้างโครงการหลักใหม่พร้อมรายละเอียดครบถ้วน</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">✏️ แก้ไขโครงการ</div>
                            <div class="feature-desc">แก้ไขข้อมูลโครงการที่มีอยู่</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">🗑️ ลบชั่วคราว</div>
                            <div class="feature-desc">ลบโครงการแบบชั่วคราว สามารถกู้คืนได้</div>
                        </div>
                    </div>
                </div>

                <h5>📋 ข้อมูลโครงการหลัก</h5>
                <div class="column-list">
                    <div class="column-item">🏷️ <strong>รหัสโครงการ</strong> - รหัสประจำโครงการ</div>
                    <div class="column-item">📝 <strong>ชื่อโครงการ</strong> - ชื่อเต็มของโครงการหลัก</div>
                    <div class="column-item">📅 <strong>ปีที่ดำเนินการ</strong> - ปีงบประมาณ</div>
                    <div class="column-item">💰 <strong>งบประมาณ</strong> - จำนวนเงินที่ได้รับจัดสรร</div>
                    <div class="column-item">🎯 <strong>วัตถุประสงค์</strong> - เป้าหมายหลักของโครงการ</div>
                    <div class="column-item">📊 <strong>ตัวชี้วัด</strong> - ตัวชี้วัดความสำเร็จ</div>
                    <div class="column-item">👥 <strong>กลุ่มเป้าหมาย</strong> - ผู้รับประโยชน์</div>
                    <div class="column-item">📍 <strong>พื้นที่ดำเนินการ</strong> - สถานที่ดำเนินโครงการ</div>
                </div>

                <h5>🔄 การทำงานของปุ่มแก้ไข</h5>
                <div class="feature-card">
                    <div class="feature-title">⚡ กระบวนการแก้ไขอัตโนมัติ</div>
                    <ul class="mb-0">
                        <li>คลิกปุ่ม "แก้ไข" ที่แถวโครงการ</li>
                        <li>ระบบจะ redirect ไปหน้าแก้ไขอัตโนมัติ</li>
                        <li>แสดงฟอร์มพร้อมข้อมูลเดิม</li>
                        <li>บันทึกการเปลี่ยนแปลงและ redirect กลับ</li>
                        <li>แสดงข้อความยืนยันการบันทึกสำเร็จ</li>
                    </ul>
                </div>

                <h5>🗑️ ระบบลบชั่วคราว</h5>
                <div class="warning-box">
                    <h6><i class="fas fa-info-circle me-2"></i>การลบชั่วคราว</h6>
                    <ul class="mb-0">
                        <li>ข้อมูลจะไม่ถูกลบถาวรจากฐานข้อมูล</li>
                        <li>สามารถกู้คืนได้โดยการ refresh หน้าเว็บ</li>
                        <li>ป้องกันการสูญเสียข้อมูลโดยไม่ตั้งใจ</li>
                        <li>แสดงข้อความเตือนก่อนการลบ</li>
                    </ul>
                </div>
            </div>

            <!-- แดชบอร์ดกราฟและแผนภูมิ -->
            <div class="content-section" id="charts-dashboard">
                <h2 class="section-title">
                    <i class="fas fa-chart-line"></i>แดชบอร์ดกราฟและแผนภูมิ (<code>charts/index.php</code>)
                </h2>

                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">ระบบวิเคราะห์ข้อมูลด้วยกราฟและแผนภูมิแบบเรียลไทม์ พร้อมฟีเจอร์การส่งออกขั้นสูง</div>
                </div>

                <h5>🚀 ฟีเจอร์เด่น</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔄 การกู้คืนกราฟที่ถูกลบ</div>
                            <div class="feature-desc">
                                <ul class="mb-0">
                                    <li>เก็บข้อมูลกราฟที่ถูกลบชั่วคราว</li>
                                    <li>กู้คืนได้โดยไม่ต้องรีเฟรชหน้า</li>
                                    <li>แสดงจำนวนกราฟที่ถูกลบ</li>
                                    <li>กู้คืนรายการเดียวหรือทั้งหมด</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📤 ส่งออกกราฟหลากรูปแบบ</div>
                            <div class="feature-desc">
                                <ul class="mb-0">
                                    <li>PNG, JPG, PDF สำหรับกราฟเดียว</li>
                                    <li>ส่งออกกราฟทั้งหมดเป็น ZIP</li>
                                    <li>ชื่อไฟล์เป็นภาษาไทย</li>
                                    <li>ปรับคุณภาพได้</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <h5>📊 ประเภทกราฟที่มี</h5>
                <div class="column-list">
                    <div class="column-item">📈 <strong>Bar Chart</strong> - แผนภูมิแท่ง</div>
                    <div class="column-item">🥧 <strong>Pie Chart</strong> - แผนภูมิวงกลม</div>
                    <div class="column-item">📊 <strong>Line Chart</strong> - แผนภูมิเส้น</div>
                    <div class="column-item">⭕ <strong>Doughnut Chart</strong> - แผนภูมิโดนัท</div>
                    <div class="column-item">📉 <strong>Area Chart</strong> - แผนภูมิพื้นที่</div>
                    <div class="column-item">🔺 <strong>Radar Chart</strong> - แผนภูมิเรดาร์</div>
                </div>

                <h5>🎛️ การจัดการกราฟ</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">🔍 กรองข้อมูล</div>
                            <div class="feature-desc">ฟิลเตอร์ขั้นสูงแบบ Cascading</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">📐 จัดเรียงกราฟ</div>
                            <div class="feature-desc">1x1, 2x2, 3x3, 4x4 Layout</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-title">⌨️ คีย์ลัด</div>
                            <div class="feature-desc">Ctrl+R สำหรับกู้คืนกราฟ</div>
                        </div>
                    </div>
                </div>

                <h5>📋 การ์ดสถิติ</h5>
                <div class="column-list">
                    <div class="column-item">🏗️ <strong>โครงการทั้งหมด</strong> - จำนวนโครงการ</div>
                    <div class="column-item">💰 <strong>งบประมาณรวม</strong> - ยอดงบประมาณ</div>
                    <div class="column-item">📊 <strong>ตัวชี้วัด</strong> - จำนวนตัวชี้วัด</div>
                    <div class="column-item">📍 <strong>พื้นที่</strong> - จำนวนพื้นที่ดำเนินการ</div>
                    <div class="column-item">👥 <strong>กลุ่มเป้าหมาย</strong> - จำนวนผู้รับประโยชน์</div>
                    <div class="column-item">📦 <strong>ผลิตภัณฑ์</strong> - จำนวนผลิตภัณฑ์</div>
                    <div class="column-item">🏫 <strong>โรงเรียน</strong> - สถานศึกษาที่เข้าร่วม</div>
                    <div class="column-item">🏢 <strong>หน่วยงาน</strong> - หน่วยงานที่เกี่ยวข้อง</div>
                </div>

                <h5>🔄 ระบบกู้คืนกราฟ</h5>
                <div class="feature-card">
                    <div class="feature-title">⚡ วิธีการใช้งาน</div>
                    <ol class="step-list mb-0">
                        <li>ลบกราฟโดยคลิกปุ่มถังขยะ</li>
                        <li>ปุ่ม "กราฟที่ถูกลบ" จะปรากฏขึ้น</li>
                        <li>คลิกปุ่มหรือกด Ctrl+R</li>
                        <li>เลือกกราฟที่ต้องการกู้คืน</li>
                        <li>คลิก "กู้คืน" หรือ "กู้คืนทั้งหมด"</li>
                    </ol>
                </div>
            </div>
            <div class="content-section" id="table-report">
                <h2 class="section-title">
                    <i class="fas fa-table"></i>หน้ารายงานโครงการแบบตาราง (<code>projects_table_view.php</code>)
                </h2>
                
                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">หน้าหลักสำหรับดูและวิเคราะห์ข้อมูลโครงการในรูปแบบตารางแบบครอบคลุม</div>
                </div>

                <!-- ส่วนฟิลเตอร์ -->
                <hr class="my-4" style="border-top: 2px solid #667eea; opacity: 0.3;">
                <h4><i class="fas fa-filter me-2"></i>ส่วนฟิลเตอร์ข้อมูล</h4>
                
                <h5>🎛️ ฟิลเตอร์หลัก</h5>
                <div class="column-list">
                    <div class="column-item">📅 <strong>ปีโครงการ (เริ่มต้น/สิ้นสุด)</strong> - กรองตามช่วงปี</div>
                    <div class="column-item">🗺️ <strong>ตำบล/อำเภอ/จังหวัด</strong> - กรองตามพื้นที่ (แบบ Cascading)</div>
                    <div class="column-item">🎯 <strong>โครงการหลัก</strong> - กรองตามโครงการใหญ่</div>
                    <div class="column-item">⚡ <strong>ยุทธศาสตร์</strong> - กรองตามแผนยุทธศาสตร์</div>
                    <div class="column-item">🏢 <strong>หน่วยงาน</strong> - กรองตามหน่วยงานที่รับผิดชอบ</div>
                    <div class="column-item">👥 <strong>กลุ่มเป้าหมาย</strong> - กรองตามกลุ่มเป้าหมาย</div>
                </div>

                <h5>📝 วิธีใช้ฟิลเตอร์</h5>
                <ol class="step-list">
                    <li>เลือกเงื่อนไขในแต่ละ Dropdown</li>
                    <li>ระบบจะอัพเดทข้อมูลอัตโนมัติ</li>
                    <li>สามารถใช้หลายเงื่อนไขพร้อมกันได้</li>
                    <li>กดปุ่ม "ล้างตัวกรอง" เพื่อรีเซ็ตทั้งหมด</li>
                </ol>

                <!-- การ์ดสถิติ -->
                <hr class="my-4" style="border-top: 2px solid #667eea; opacity: 0.3;">
                <h4><i class="fas fa-chart-bar me-2"></i>การ์ดสถิติ</h4>
                
                <h5>📊 แถวที่ 1</h5>
                <div class="column-list">
                    <div class="column-item">🏗️ <strong>โครงการทั้งหมด</strong> - จำนวนโครงการรวม</div>
                    <div class="column-item">🪙 <strong>งบประมาณรวม</strong> - งบประมาณทั้งหมด (ล้านบาท)</div>
                    <div class="column-item">📈 <strong>ตัวชี้วัดทั้งหมด</strong> - จำนวนตัวชี้วัด</div>
                    <div class="column-item">📍 <strong>พื้นที่ดำเนินการ</strong> - จำนวนพื้นที่</div>
                    <div class="column-item">👥 <strong>กลุ่มเป้าหมาย (คน)</strong> - จำนวนผู้เข้าร่วม</div>
                </div>

                <h5>📊 แถวที่ 2</h5>
                <div class="column-list">
                    <div class="column-item">📦 <strong>ผลิตภัณฑ์ทั้งหมด</strong> - จำนวนผลิตภัณฑ์</div>
                    <div class="column-item">🏫 <strong>โรงเรียนที่เข้าร่วม</strong> - จำนวนสถานศึกษา</div>
                    <div class="column-item">👨‍👩‍👧‍👦 <strong>กลุ่มเป้าหมายทั้งหมด</strong> - จำนวนกลุ่ม</div>
                    <div class="column-item">🏢 <strong>หน่วยงานที่เข้าร่วม</strong> - จำนวนหน่วยงาน</div>
                </div>

                <!-- ตารางข้อมูล -->
                <hr class="my-4" style="border-top: 2px solid #667eea; opacity: 0.3;">
                <h4><i class="fas fa-table me-2"></i>ตารางข้อมูลโครงการ</h4>
                
                <h5>📋 คอลัมน์ข้อมูลหลัก</h5>
                <div class="column-list">
                    <div class="column-item">🏷️ <strong>รหัสโครงการ</strong> - รหัสประจำโครงการ</div>
                    <div class="column-item">📁 <strong>ชื่อโครงการ</strong> - ชื่อเต็มของโครงการ</div>
                    <div class="column-item">📅 <strong>ปีโครงการ</strong> - ปีที่ดำเนินการ</div>
                    <div class="column-item">👔 <strong>ผู้รับผิดชอบ</strong> - เจ้าหน้าที่รับผิดชอบ</div>
                    <div class="column-item">💰 <strong>งบประมาณอนุมัติ</strong> - จำนวนเงินที่ได้รับอนุมัติ</div>
                    <div class="column-item">🎯 <strong>โครงการหลัก</strong> - โครงการหลักที่เชื่อมโยง</div>
                    <div class="column-item">⚡ <strong>ยุทธศาสตร์</strong> - ยุทธศาสตร์ที่เกี่ยวข้อง</div>
                    <div class="column-item">🏢 <strong>หน่วยงาน</strong> - หน่วยงานที่รับผิดชอบ</div>
                </div>

                <h5>📊 คอลัมน์รายละเอียด</h5>
                <div class="column-list">
                    <div class="column-item">📊 <strong>ตัวชี้วัด (รายละเอียด)</strong> - ตัวชี้วัดพร้อมค่าเป้าหมาย</div>
                    <div class="column-item">📦 <strong>ผลิตภัณฑ์ (รายละเอียด)</strong> - รายการผลิตภัณฑ์</div>
                    <div class="column-item">🏫 <strong>โรงเรียน (รายละเอียด)</strong> - สถานศึกษาที่เข้าร่วม</div>
                    <div class="column-item">👥 <strong>กลุ่มเป้าหมาย (รายละเอียด)</strong> - กลุ่มเป้าหมายและจำนวน</div>
                    <div class="column-item">📍 <strong>พื้นที่ดำเนินการ (รายละเอียด)</strong> - รายการพื้นที่</div>
                    <div class="column-item">📈 <strong>SROI</strong> - ข้อมูล Social Return on Investment</div>
                    <div class="column-item">🏭 <strong>วิสาหกิจ/ผู้ประกอบการ</strong> - รายการวิสาหกิจ</div>
                    <div class="column-item">🤝 <strong>องค์กรอื่นๆ</strong> - องค์กรที่ร่วมมือ</div>
                    <div class="column-item">🌐 <strong>เครือข่าย</strong> - เครือข่ายที่เกี่ยวข้อง</div>
                    <div class="column-item">🎓 <strong>มหาวิทยาลัย</strong> - สถาบันการศึกษาที่ร่วมมือ</div>
                    <div class="column-item">🏛️ <strong>องค์กรปกครองส่วนท้องถิ่น</strong> - อปท.ที่เข้าร่วม</div>
                    <div class="column-item">⚙️ <strong>การดำเนินการ</strong> - ปุ่มสำหรับจัดการข้อมูล</div>
                </div>

                <!-- ฟีเจอร์การค้นหา -->
                <hr class="my-4" style="border-top: 2px solid #667eea; opacity: 0.3;">
                <h4><i class="fas fa-search me-2"></i>ฟีเจอร์การค้นหา</h4>
                
                <h5>🔍 การค้นหาทั่วไป</h5>
                <ul>
                    <li>ช่องค้นหาด้านขวาบนของตาราง</li>
                    <li>ค้นหาได้ในทุกคอลัมน์พร้อมกัน</li>
                    <li>รองรับการ Highlight คำที่ค้นหา</li>
                    <li>ค้นหาแบบ Real-time</li>
                </ul>

                <h5>⚙️ การจัดการตาราง</h5>
                <ul>
                    <li>📄 <strong>Pagination</strong> - แบ่งหน้าข้อมูล (25, 50, 75, 100 รายการ หรือทั้งหมด)</li>
                    <li>🔄 <strong>Sorting</strong> - เรียงข้อมูลตามคอลัมน์</li>
                    <li>📱 <strong>Responsive</strong> - ปรับตัวตามขนาดหน้าจอ</li>
                    <li>🎨 <strong>Hover Effects</strong> - เอฟเฟกต์เมื่อเลื่อนเมาส์</li>
                </ul>

                <!-- การส่งออกข้อมูล -->
                <hr class="my-4" style="border-top: 2px solid #667eea; opacity: 0.3;">
                <h4><i class="fas fa-download me-2"></i>การส่งออกข้อมูล</h4>
                
                <div class="feature-card">
                    <div class="feature-title">📤 ปุ่ม "ส่งออกข้อมูล"</div>
                    <ul class="mb-0">
                        <li>ส่งออกเป็นไฟล์ Excel (.xlsx)</li>
                        <li>รวมข้อมูลที่ผ่านการกรองแล้ว</li>
                        <li>ครอบคลุมข้อมูลทั้งหมดในตาราง</li>
                        <li>การจัดรูปแบบที่สวยงาม</li>
                    </ul>
                </div>
            </div>

            <!-- หน้ารายละเอียดโครงการ -->
            <div class="content-section" id="project-detail">
                <h2 class="section-title">
                    <i class="fas fa-file-alt"></i>หน้ารายละเอียดโครงการ (<code>project_detail.php</code>)
                </h2>
                
                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">แสดงข้อมูลโครงการแบบละเอียดครบถ้วน</div>
                </div>

                <h5>📋 ข้อมูลที่แสดง</h5>
                <div class="column-list">
                    <div class="column-item">📋 <strong>ข้อมูลพื้นฐานโครงการ</strong></div>
                    <div class="column-item">💰 <strong>รายละเอียดงบประมาณ</strong></div>
                    <div class="column-item">👥 <strong>กลุ่มเป้าหมายและจำนวน</strong></div>
                    <div class="column-item">🗺️ <strong>หมู่บ้านและพื้นที่ดำเนินการ</strong></div>
                    <div class="column-item">🏫 <strong>โรงเรียนที่เข้าร่วม</strong></div>
                    <div class="column-item">🌐 <strong>เครือข่ายความร่วมมือ</strong></div>
                    <div class="column-item">🏭 <strong>วิสาหกิจที่เกี่ยวข้อง</strong></div>
                    <div class="column-item">📦 <strong>ผลิตภัณฑ์และผลผลิต</strong></div>
                    <div class="column-item">📊 <strong>ตัวชี้วัดและค่าเป้าหมาย</strong></div>
                </div>

                <h5>🚪 วิธีเข้าถึง</h5>
                <ul>
                    <li>จากหน้ารายงานโครงการ คลิกปุ่ม "ดูรายละเอียด"</li>
                    <li>หรือเข้าผ่าน URL ที่กำหนดโดยระบบ</li>
                </ul>
            </div>

            <!-- ระบบจัดการหลังบ้าน -->
            <!-- <div class="content-section" id="backend-system">
                <h2 class="section-title">
                    <i class="fas fa-server"></i>ระบบจัดการหลังบ้าน
                </h2>

                <div class="feature-card">
                    <div class="feature-title">🎯 วัตถุประสงค์</div>
                    <div class="feature-desc">ระบบจัดการข้อมูลและการตั้งค่าหลังบ้านสำหรับผู้ดูแลระบบ</div>
                </div>

                <h5>🔧 ฟีเจอร์หลัก</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📊 จัดการตัวชี้วัด</div>
                            <div class="feature-desc">เพิ่ม แก้ไข ลบตัวชี้วัดของโครงการ</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📋 จัดการข้อมูลหลัก</div>
                            <div class="feature-desc">จัดการข้อมูลพื้นฐานของระบบ</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">💾 สำรองข้อมูล</div>
                            <div class="feature-desc">ระบบสำรองและกู้คืนข้อมูล</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📈 รายงานขั้นสูง</div>
                            <div class="feature-desc">รายงานและสถิติระดับผู้ดูแล</div>
                        </div>
                    </div>
                </div>

                <h5>📁 โฟลเดอร์หลัก</h5>
                <div class="column-list">
                    <div class="column-item">🔧 <strong>backend/</strong> - ไฟล์หลักของระบบหลังบ้าน</div>
                    <div class="column-item">📊 <strong>api/</strong> - API endpoints สำหรับการเรียกข้อมูล</div>
                    <div class="column-item">💾 <strong>backup_manager/</strong> - ระบบจัดการการสำรองข้อมูล</div>
                    <div class="column-item">📈 <strong>charts/</strong> - แดชบอร์ดกราฟและแผนภูมิ</div>
                    <div class="column-item">📋 <strong>portal/</strong> - พอร์ทัลสำหรับผู้ใช้ทั่วไป</div>
                </div>

                <h5>🔗 API Endpoints หลัก</h5>
                <div class="column-list">
                    <div class="column-item"><code>/api/chart_data_api.php</code> - ข้อมูลสำหรับกราฟและแผนภูมิ</div>
                    <div class="column-item"><code>/api/stats_api.php</code> - สถิติและข้อมูลการ์ด</div>
                    <div class="column-item"><code>/api/get_filtered_data.php</code> - ข้อมูลที่ผ่านการกรอง</div>
                    <div class="column-item"><code>/backend/manage_indicators.php</code> - จัดการตัวชี้วัด</div>
                    <div class="column-item"><code>/backend/main_projects.php</code> - จัดการโครงการหลัก</div>
                </div>

                <div class="warning-box">
                    <h6><i class="fas fa-shield-alt me-2"></i>คำเตือนความปลอดภัย</h6>
                    <ul class="mb-0">
                        <li>API endpoints เหล่านี้เป็นข้อมูลภายในระบบ</li>
                        <li>กรุณาอย่าเปิดเผยหรือใช้งานโดยไม่ได้รับอนุญาต</li>
                        <li>สำหรับนักพัฒนา: ตรวจสอบสิทธิ์การเข้าถึงก่อนใช้งาน</li>
                    </ul>
                </div>
            </div> -->

            <div class="content-section" id="support-files">
                <h2 class="section-title">
                    <i class="fas fa-cogs"></i>ไฟล์สนับสนุน
                </h2>

                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🧭 Navigation Bar (<code>navbar.php</code>)</div>
                            <ul class="mb-0">
                                <li>เมนูนำทางแบบ Responsive</li>
                                <li>แสดงสถานะการ Login</li>
                                <li>Dropdown สำหรับจัดการบัญชี</li>
                                <li>ลิงก์ไปยังระบบหลังบ้าน</li>
                            </ul>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📊 ข้อมูลตาราง (<code>projects_table_data.php</code>)</div>
                            <ul class="mb-0">
                                <li>API สำหรับโหลดข้อมูลตาราง</li>
                                <li>รองรับ Server-side Processing</li>
                                <li>ประมวลผลฟิลเตอร์และการค้นหา</li>
                                <li>ส่งข้อมูลในรูปแบบ JSON</li>
                            </ul>
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📈 สถิติตาราง (<code>projects_table_stats.php</code>)</div>
                            <ul class="mb-0">
                                <li>คำนวณสถิติสำหรับการ์ดด้านบน</li>
                                <li>อัพเดทตามเงื่อนไขการกรอง</li>
                                <li>ส่งข้อมูลสถิติแบบ Real-time</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📤 ส่งออก Excel (<code>export_projects_table_detailed_xlsx.php</code>)</div>
                            <ul class="mb-0">
                                <li>สร้างไฟล์ Excel ที่จัดรูปแบบสวยงาม</li>
                                <li>รวมข้อมูลที่ผ่านการกรองแล้ว</li>
                                <li>ใช้ PhpSpreadsheet Library</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- วิธีการใช้งานทั่วไป -->
            <div class="content-section" id="usage">
                <h2 class="section-title">
                    <i class="fas fa-rocket"></i>วิธีการใช้งานทั่วไป
                </h2>

                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔐 การเข้าสู่ระบบ</div>
                            <ol class="step-list mb-0">
                                <li>ไปที่หน้าเข้าสู่ระบบของระบบ</li>
                                <li>กรอกชื่อผู้ใช้และรหัสผ่าน</li>
                                <li>เข้าสู่แดชบอร์ดหลัก</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📊 การใช้งานแดชบอร์ดกราฟ</div>
                            <ol class="step-list mb-0">
                                <li>จากเมนูหลักเลือก "แดชบอร์ดกราฟ"</li>
                                <li>ใช้ฟิลเตอร์เพื่อกรองข้อมูล</li>
                                <li>ดูสถิติจากการ์ดด้านบน</li>
                                <li>วิเคราะห์ข้อมูลจากกราฟต่างๆ</li>
                                <li>ส่งออกกราฟตามต้องการ</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📋 จัดการโครงการหลัก</div>
                            <ol class="step-list mb-0">
                                <li>เลือกเมนู "จัดการโครงการหลัก"</li>
                                <li>ดูรายการโครงการที่มีอยู่</li>
                                <li>คลิก "แก้ไข" เพื่อปรับปรุงข้อมูล</li>
                                <li>คลิก "ลบ" สำหรับลบชั่วคราว</li>
                                <li>คลิก "เพิ่มใหม่" สำหรับโครงการใหม่</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔄 การกู้คืนกราฟที่ถูกลบ</div>
                            <ol class="step-list mb-0">
                                <li>ลบกราฟโดยคลิกปุ่มถังขยะ</li>
                                <li>ปุ่ม "กราฟที่ถูกลบ" จะปรากฏ</li>
                                <li>คลิกปุ่มหรือกด Ctrl+R</li>
                                <li>เลือกกราฟที่ต้องการกู้คืน</li>
                                <li>คลิก "กู้คืน" หรือ "กู้คืนทั้งหมด"</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔍 การค้นหาและกรองข้อมูล</div>
                            <ol class="step-list mb-0">
                                <li>ใช้ช่องค้นหาด้านขวาบน</li>
                                <li>หรือใช้ฟิลเตอร์เพื่อกรองข้อมูล</li>
                                <li>ฟิลเตอร์แบบ Cascading</li>
                                <li>สามารถรวมการค้นหาและกรองได้</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📤 การส่งออกข้อมูล</div>
                            <ol class="step-list mb-0">
                                <li>กรองข้อมูลตามต้องการ</li>
                                <li>เลือกประเภทการส่งออก</li>
                                <li>สำหรับกราฟ: PNG, JPG, PDF</li>
                                <li>สำหรับตาราง: Excel (.xlsx)</li>
                                <li>ไฟล์จะถูกดาวน์โหลดอัตโนมัติ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- คีย์ลัดและทิปการใช้งาน -->
            <div class="content-section" id="shortcuts">
                <h2 class="section-title">
                    <i class="fas fa-keyboard"></i>คีย์ลัดและทิปการใช้งาน
                </h2>

                <h5>⌨️ คีย์ลัดในแดชบอร์ดกราฟ</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🎛️ จัดเรียงกราฟ</div>
                            <ul class="mb-0">
                                <li><kbd>Ctrl</kbd> + <kbd>1</kbd> - จัดเรียง 1x1</li>
                                <li><kbd>Ctrl</kbd> + <kbd>2</kbd> - จัดเรียง 2x2</li>
                                <li><kbd>Ctrl</kbd> + <kbd>3</kbd> - จัดเรียง 3x3</li>
                                <li><kbd>Ctrl</kbd> + <kbd>4</kbd> - จัดเรียง 4x4</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔄 การกู้คืน</div>
                            <ul class="mb-0">
                                <li><kbd>Ctrl</kbd> + <kbd>R</kbd> - เปิดเมนูกู้คืนกราฟ</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h5>💡 ทิปการใช้งาน</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">⚡ ประสิทธิภาพ</div>
                            <ul class="mb-0">
                                <li>ใช้ฟิลเตอร์เพื่อลดปริมาณข้อมูล</li>
                                <li>ส่งออกข้อมูลที่กรองแล้วเท่านั้น</li>
                                <li>ใช้การจัดเรียงกราฟที่เหมาะสม</li>
                                <li>ปิดแท็บที่ไม่ได้ใช้งาน</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔒 ความปลอดภัย</div>
                            <ul class="mb-0">
                                <li>ออกจากระบบเมื่อใช้งานเสร็จ</li>
                                <li>ไม่แชร์ข้อมูลการเข้าสู่ระบบ</li>
                                <li>ตรวจสอบ URL ก่อนเข้าสู่ระบบ</li>
                                <li>ใช้รหัสผ่านที่ซับซ้อน</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h5>🎨 การปรับแต่งการแสดงผล</h5>
                <div class="column-list">
                    <!-- <div class="column-item">🌙 <strong>โหมดมืด</strong> - ปรับความสว่างของธีม</div> -->
                    <div class="column-item">📱 <strong>Responsive</strong> - ปรับตามขนาดหน้าจออัตโนมัติ</div>
                    <div class="column-item">🎨 <strong>สีธีม</strong> - ธีมสีน้ำเงินและเขียว</div>
                    <div class="column-item">🔤 <strong>ฟอนต์</strong> - Noto Sans Thai Looped</div>
                    <div class="column-item">⚡ <strong>แอนิเมชัน</strong> - Smooth transitions และ hover effects</div>
                </div>
            </div>

            <!-- ข้อควรระวัง -->
            <div class="content-section" id="warnings">
                <h2 class="section-title">
                    <i class="fas fa-exclamation-triangle"></i>ข้อควรระวัง
                </h2>

                <div class="warning-box">
                    <h5><i class="fas fa-shield-alt me-2"></i>ความปลอดภัย</h5>
                    <ul class="mb-0">
                        <li>ออกจากระบบเมื่อใช้งานเสร็จ</li>
                        <li>ไม่แชร์ข้อมูลการเข้าสู่ระบบ</li>
                    </ul>
                </div>

                <div class="warning-box">
                    <h5><i class="fas fa-tachometer-alt me-2"></i>ประสิทธิภาพ</h5>
                    <ul class="mb-0">
                        <li>ใช้ฟิลเตอร์เพื่อลดข้อมูลเมื่อมีข้อมูลมาก</li>
                        <li>ส่งออกข้อมูลที่กรองแล้วเท่านั้น</li>
                    </ul>
                </div>

                <div class="warning-box">
                    <h5><i class="fas fa-user-check me-2"></i>การใช้งาน</h5>
                    <ul class="mb-0">
                        <li>รอให้ข้อมูลโหลดเสร็จก่อนทำงานต่อ</li>
                        <li>ตรวจสอบฟิลเตอร์ให้ถูกต้อง</li>
                    </ul>
                </div>
            </div>

            <!-- การแก้ไขปัญหา -->
            <div class="content-section" id="troubleshooting">
                <h2 class="section-title">
                    <i class="fas fa-tools"></i>การแก้ไขปัญหาเบื้องต้น
                </h2>

                <div class="row">
                    <div class="col-md-4">
                        <div class="error-box">
                            <h6><i class="fas fa-sign-in-alt me-2"></i>ไม่สามารถเข้าสู่ระบบได้</h6>
                            <ul class="mb-0">
                                <li>ตรวจสอบ username/password</li>
                                <li>ล้าง Cache และ Cookies</li>
                                <li>ตรวจสอบการเชื่อมต่อฐานข้อมูล</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="error-box">
                            <h6><i class="fas fa-eye-slash me-2"></i>ข้อมูลไม่แสดง</h6>
                            <ul class="mb-0">
                                <li>ตรวจสอบการเชื่อมต่อฐานข้อมูล</li>
                                <li>ลองล้างฟิลเตอร์</li>
                                <li>Refresh หน้าเว็บ</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="error-box">
                            <h6><i class="fas fa-download me-2"></i>ส่งออกไม่ได้</h6>
                            <ul class="mb-0">
                                <li>ตรวจสอบข้อมูลที่กรอง</li>
                                <li>ตรวจสอบ PhpSpreadsheet Library</li>
                                <li>ลองส่งออกข้อมูลที่น้อยกว่า</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- การติดต่อสนับสนุน -->
            <div class="content-section" id="contact">
                <h2 class="section-title">
                    <i class="fas fa-headset"></i>การติดต่อสนับสนุน
                </h2>

                <div class="contact-box">
                    <h5><i class="fas fa-phone me-2"></i>หากพบปัญหาการใช้งานหรือต้องการความช่วยเหลือ สามารถติดต่อทีมพัฒนาระบบได้ที่:</h5>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <h6><i class="fas fa-envelope me-2"></i>อีเมลหลัก</h6>
                            <p><a href="mailto:k.sakmeang@gmail.com" class="text-white text-decoration-none">k.sakmeang@gmail.com</a></p>
                            <small class="text-light">สำหรับปัญหาทางเทคนิคและการพัฒนาระบบ</small>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-phone me-2"></i>โทรศัพท์</h6>
                            <p>092-945-8830</p>
                            <small class="text-light">เวลาทำการ จันทร์-ศุกร์ 08:30-21:00 น.</small>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fab fa-line me-2"></i>Line Official</h6>
                            <p>ไอดีไลน์: ridp_</p>
                            <p>หรือเบอร์: 0929458830</p>
                            <small class="text-light">สำหรับการติดต่อด่วน</small>
                        </div>
                    </div>

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center py-4">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>อัปเดทล่าสุด: 9 กันยายน 2025 |
                    <i class="fas fa-code-branch me-1"></i>เวอร์ชัน 2.0
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
                    scrollBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px;';
                    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
                    scrollBtn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
                    document.body.appendChild(scrollBtn);
                }
            } else {
                const scrollBtn = document.querySelector('.scroll-to-top');
                if (scrollBtn) scrollBtn.remove();
            }
        });
    </script>
</body>
</html>
