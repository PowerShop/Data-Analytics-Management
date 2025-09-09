<?php
// session_start();

// ตรวจสอบการ login และสิทธิ์การเข้าถึง
function checkLogin($required_roles = ['admin', 'director', 'manager']) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ../portal/');
        exit();
    }
    
    // ตรวจสอบ session timeout (2 ชั่วโมง)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 7200) {
        session_destroy();
        header('Location: ../login.php?timeout=1');
        exit();
    }
    
    // ตรวจสอบสิทธิ์การเข้าถึงตาม role
    if (isset($_SESSION['admin_role']) && !in_array($_SESSION['admin_role'], $required_roles)) {
        // ถ้าไม่มีสิทธิ์ ให้ไปหน้า login พร้อม error
        header('Location: ../login.php?error=access_denied');
        exit();
    }
    
    // อัพเดทเวลาล่าสุดที่ใช้งาน
    $_SESSION['last_activity'] = time();
}

// ฟังก์ชันสำหรับตรวจสอบสิทธิ์ Admin เท่านั้น
function checkAdminOnly() {
    checkLogin(['admin']);
}

// ฟังก์ชันสำหรับตรวจสอบสิทธิ์ Manager ขึ้นไป
function checkManagerAccess() {
    checkLogin(['admin', 'manager']);
}

// ฟังก์ชันสำหรับตรวจสอบสิทธิ์ Director ขึ้นไป
function checkDirectorAccess() {
    checkLogin(['admin', 'director', 'manager']);
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

// เรียกใช้ฟังก์ชันตรวจสอบ login และสิทธิ์การเข้าถึง
// Default: อนุญาตให้ admin, director, manager เข้าถึงได้
checkLogin();

// สำหรับหน้าที่ต้องการสิทธิ์เฉพาะ ให้เรียกใช้:
// checkAdminOnly();        // สำหรับ admin เท่านั้น
// checkManagerAccess();    // สำหรับ admin, manager
// checkDirectorAccess();   // สำหรับ admin, director, manager
?>
