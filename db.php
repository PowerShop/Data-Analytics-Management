<?php
$host = "localhost";
$user = "root";
$pass = "Kittisak644245001";
$dbname = "data analytics";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("[DEBUG] เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
} else {
    // echo "[DEBUG] เชื่อมต่อฐานข้อมูลสำเร็จ";
}
?>
