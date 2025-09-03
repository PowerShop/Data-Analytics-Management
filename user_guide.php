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
        
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #495057;
        }
        
        .highlight {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-1">
        <!-- Header -->
        <div class="guide-header text-center">
            <div class="container">
                <h1 class="fw-bold mb-3">
                    <i class="fas fa-book me-3"></i>คู่มือการใช้งานระบบ
                </h1>
                <p class="mb-0 fs-5">เรียนรู้การใช้งานระบบจัดการโครงการอย่างมีประสิทธิภาพ</p>
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
                        <li><a href="#overview">🔐 ภาพรวมระบบ Admin</a></li>
                        <li><a href="#login">🔑 หน้าเข้าสู่ระบบ</a></li>
                        <li><a href="#dashboard">🏠 หน้าแดชบอร์ด</a></li>
                        <li><a href="#table-report">📋 หน้ารายงานโครงการแบบตาราง</a></li>
                        <li><a href="#project-detail">📄 หน้ารายละเอียดโครงการ</a></li>
                        <li><a href="#support-files">🔧 ไฟล์สนับสนุน</a></li>
                        <li><a href="#usage">🚀 วิธีการใช้งานทั่วไป</a></li>
                        <li><a href="#warnings">⚠️ ข้อควรระวัง</a></li>
                        <li><a href="#troubleshooting">🔧 การแก้ไขปัญหาเบื้องต้น</a></li>
                        <li><a href="#contact">📞 การติดต่อสนับสนุน</a></li>
                    </ul>
                </div>
            </div>

            <!-- ภาพรวมระบบ -->
            <div class="content-section" id="overview">
                <h2 class="section-title">
                    <i class="fas fa-shield-alt"></i>ภาพรวมระบบ Admin
                </h2>
                <p>ระบบ Admin Panel เป็นส่วนจัดการหลักของระบบจัดการโครงการ ที่ช่วยให้ผู้ดูแลระบบสามารถเข้าถึงข้อมูลและรายงานขั้นสูงได้อย่างปลอดภัย</p>
                
                <div class="highlight">
                    <strong><i class="fas fa-lightbulb me-2"></i>จุดเด่นของระบบ:</strong>
                    <ul class="mt-2 mb-0">
                        <!-- <li>ระบบรักษาความปลอดภัยขั้นสูง</li> -->
                        <li>รายงานแบบ Real-time</li>
                        <li>การกรองข้อมูลแบบละเอียด</li>
                        <li>ส่งออกข้อมูลเป็น Excel</li>
                        <li>ออกแบบให้ใช้งานง่าย</li>
                    </ul>
                </div>
            </div>

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
                            <strong>admin</strong> / <strong>admin123</strong><br>
                            <small>ผู้ดูแลระบบหลัก</small>
                        </div>
                        <div class="col-md-4">
                            <strong>manager</strong> / <strong>manager123</strong><br>
                            <small>ผู้จัดการโครงการ</small>
                        </div>
                        <div class="col-md-4">
                            <strong>director</strong> / <strong>director123</strong><br>
                            <small>ผู้อำนวยการ</small>
                        </div>
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

            <!-- หน้ารายงานโครงการแบบตาราง -->
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
                    <li>หรือใส่ URL: <code>/admin/project_detail.php?id=[PROJECT_ID]</code></li>
                </ul>
            </div>

            <!-- ไฟล์สนับสนุน -->
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
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📊 ข้อมูลตาราง (<code>projects_table_data.php</code>)</div>
                            <ul class="mb-0">
                                <li>API สำหรับโหลดข้อมูลตาราง</li>
                                <li>รองรับ Server-side Processing</li>
                                <li>ประมวลผลฟิลเตอร์และการค้นหา</li>
                                <li>ส่งข้อมูลในรูปแบบ JSON</li>
                            </ul>
                        </div>
                    </div>
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
                                <li>ไปที่ /admin/</li>
                                <li>ระบบจะ redirect ไป login.php</li>
                                <li>กรอก username/password</li>
                                <li>เข้าสู่ Dashboard</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📊 การดูรายงานโครงการ</div>
                            <ol class="step-list mb-0">
                                <li>จาก Dashboard คลิก "รายงานโครงการ"</li>
                                <li>ใช้ฟิลเตอร์เพื่อกรองข้อมูล</li>
                                <li>ดูสถิติจากการ์ด</li>
                                <li>วิเคราะห์ข้อมูลจากตาราง</li>
                                <li>ส่งออกข้อมูลเมื่อต้องการ</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">🔍 การค้นหาข้อมูล</div>
                            <ol class="step-list mb-0">
                                <li>ใช้ช่องค้นหาด้านขวาบน</li>
                                <li>หรือใช้ฟิลเตอร์เพื่อกรองข้อมูล</li>
                                <li>คำที่ค้นหาจะถูก Highlight</li>
                                <li>สามารถรวมกันได้</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-title">📤 การส่งออกข้อมูล</div>
                            <ol class="step-list mb-0">
                                <li>กรองข้อมูลตามต้องการ</li>
                                <li>คลิกปุ่ม "ส่งออกข้อมูล"</li>
                                <li>ไฟล์ Excel จะถูกดาวน์โหลด</li>
                            </ol>
                        </div>
                    </div>
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
                    <h5><i class="fas fa-phone me-2"></i>หากพบปัญหาการใช้งาน สามารถติดต่อทีมพัฒนาระบบได้ที่:</h5>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <h6><i class="fas fa-envelope me-2"></i>Email</h6>
                            <p><a href="mailto:k.sakmeang@gmail.com" class="text-white text-decoration-none">k.sakmeang@gmail.com</a></p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-phone me-2"></i>โทรศัพท์</h6>
                            <p>092-945-8830</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fab fa-line me-2"></i>Line</h6>
                            <p>ไอดีไลน์ ridp_ หรือเบอร์ 0929458830</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center py-4">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>อัพเดทล่าสุด: 30 กรกฎาคม 2568
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
