<?php
// logout.php - ไฟล์สำหรับออกจากระบบ
session_start();

// ล้าง session ทั้งหมด
session_unset();
session_destroy();

// ลบ cookie session (ถ้ามี)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// เริ่ม session ใหม่
session_start();

// แสดงข้อความแจ้งเตือน
$_SESSION['logout_message'] = 'ออกจากระบบเรียบร้อยแล้ว';

// Redirect ไปหน้า login
header('Location: /login');
exit();
?>
