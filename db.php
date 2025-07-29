<?php
// เปิดการแสดง error สำหรับ development (ปิดใน production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// $host = "localhost";
// $user = "kittisak";
// $pass = "SkJ3cm@001";
// $dbname = "kittisak";
$host = "localhost";
$user = "root";
$pass = "Kittisak644245001";
$dbname = "data analytics";

$conn = new mysqli($host, $user, $pass, $dbname);

// ตั้งค่า charset เป็น utf8
$conn->set_charset("utf8");

if ($conn->connect_error) {
    // Log error แทนการใช้ die() เพื่อไม่ให้หยุดการทำงาน
    error_log("Database connection failed: " . $conn->connect_error);
    die("เชื่อมต่อฐานข้อมูลล้มเหลว กรุณาติดต่อผู้ดูแลระบบ");
}

// ลบ echo debug message ออก เพื่อไม่ให้แสดงในหน้าเว็บ
// Connection successful - no need to echo anything
?>
