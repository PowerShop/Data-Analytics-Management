<?php
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect ไปหน้า login
header('Location: login.php');
exit();
?>
