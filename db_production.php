<?php
// ไฟล์สำหรับการตั้งค่าสำหรับ production server
// คัดลอกไฟล์นี้เป็น db.php เมื่อขึ้น server จริง

// ปิดการแสดง error ใน production
ini_set('display_errors', 0);
error_reporting(0);

// ตั้งค่าการเชื่อมต่อฐานข้อมูล (แก้ไขให้ตรงกับ server)
$host = "localhost"; // หรือ IP ของฐานข้อมูล
$user = "your_db_username"; // username ของฐานข้อมูล
$pass = "your_db_password"; // password ของฐานข้อมูล  
$dbname = "your_db_name"; // ชื่อฐานข้อมูล

$conn = new mysqli($host, $user, $pass, $dbname);

// ตั้งค่า charset เป็น utf8 เพื่อรองรับภาษาไทย
$conn->set_charset("utf8");

if ($conn->connect_error) {
    // Log error ลงไฟล์แทนการแสดงบนหน้าเว็บ
    error_log("Database connection failed: " . $conn->connect_error);
    
    // แสดงข้อความที่เหมาะสมกับผู้ใช้
    die("ระบบขัดข้อง กรุณาลองใหม่อีกครั้งหรือติดต่อผู้ดูแลระบบ");
}

// ไม่ต้อง echo อะไรเมื่อเชื่อมต่อสำเร็จ
?>
