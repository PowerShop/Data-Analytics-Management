<?php
// Database connection for API endpoints
// This file handles database connections without HTML output

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
$conn = null;

// พยายามเชื่อมต่อ Production Database ก่อน
try {
    $conn = new mysqli($prod_host, $prod_user, $prod_pass, $prod_dbname);

    if ($conn->connect_error) {
        throw new Exception("Production DB connection failed: " . $conn->connect_error);
    }

    $connection_type = "Production";

} catch (Exception $e) {
    // หากเชื่อมต่อ Production ไม่ได้ ให้ fallback ไป Local
    try {
        $conn = new mysqli($local_host, $local_user, $local_pass, $local_dbname);

        if ($conn->connect_error) {
            throw new Exception("Local DB connection failed: " . $conn->connect_error);
        }

        $connection_type = "Local (Fallback)";

    } catch (Exception $e2) {
        // หากทั้งสองไม่ได้ ให้ throw exception แทน die()
        throw new Exception("Database connection failed. Production: " . $e->getMessage() . ". Local: " . $e2->getMessage());
    }
}

// ตั้งค่า charset เป็น UTF-8
if ($conn && $conn instanceof mysqli) {
    $conn->set_charset("utf8");
}
?>
