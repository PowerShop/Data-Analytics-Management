<?php
// การตั้งค่าฐานข้อมูล Production (Primary)
$prod_host = "localhost";
$prod_user = "kittisak";
$prod_pass = "SkJ3cm@001";
$prod_dbname = "kittisak";

// การตั้งค่าฐานข้อมูล Local (Fallback)
$local_host = "localhost";
$local_user = "root";
$local_pass = "Kittisak644245001";
$local_dbname = "data analytics";

$connection_type = "";

// พยายามเชื่อมต่อ Production Database ก่อน
try {
    $conn = new mysqli($prod_host, $prod_user, $prod_pass, $prod_dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Production DB connection failed: " . $conn->connect_error);
    }
    
    $connection_type = "Production";
    // echo "[DEBUG] เชื่อมต่อฐานข้อมูล Production สำเร็จ";
    
} catch (Exception $e) {
    // หากเชื่อมต่อ Production ไม่ได้ ให้ fallback ไป Local
    try {
        $conn = new mysqli($local_host, $local_user, $local_pass, $local_dbname);
        
        if ($conn->connect_error) {
            throw new Exception("Local DB connection failed: " . $conn->connect_error);
        }
        
        $connection_type = "Local (Fallback)";
        // echo "[DEBUG] เชื่อมต่อฐานข้อมูล Local (Fallback) สำเร็จ";
        
    } catch (Exception $e2) {
        // หากทั้งสองไม่ได้ ให้แสดงข้อผิดพลาด
        die("
        <div style='
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
            text-align: center;
            font-family: Arial, sans-serif;
        '>
            <h3><i class='fas fa-exclamation-triangle'></i> ข้อผิดพลาดการเชื่อมต่อฐานข้อมูล</h3>
            <p>ไม่สามารถเชื่อมต่อฐานข้อมูลได้ทั้ง Production และ Local</p>
            <p><strong>Production Error:</strong> " . $e->getMessage() . "</p>
            <p><strong>Local Error:</strong> " . $e2->getMessage() . "</p>
            <p><small>กรุณาตรวจสอบการตั้งค่าฐานข้อมูลและลองใหม่อีกครั้ง</small></p>
        </div>");
    }
}

// ตั้งค่า charset เป็น UTF-8
if ($conn && $conn instanceof mysqli) {
    $conn->set_charset("utf8");
    
    // สำหรับ debug - แสดงประเภทการเชื่อมต่อ (สามารถปิดได้)
    // echo "[DEBUG] ใช้การเชื่อมต่อ: " . $connection_type;
}
?>
