<?php
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../portal/');
    exit();
} else {
    // พาไป index.php
    header('Location: dashboard.php');
}
?>
