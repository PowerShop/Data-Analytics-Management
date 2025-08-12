<?php
// session_start();

// ตรวจสอบการ login
function checkLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ../portal/');
        exit();
    }
    
    // ตรวจสอบ session timeout (2 ชั่วโมง)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 7200) {
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }
    
    // อัพเดทเวลาล่าสุดที่ใช้งาน
    $_SESSION['last_activity'] = time();
}

// ฟังก์ชัน logout
function logout() {
    session_destroy();
    header('Location: ../portal/');
    exit();
}

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    logout();
}

// เรียกใช้ฟังก์ชันตรวจสอบ login
checkLogin();
?>
