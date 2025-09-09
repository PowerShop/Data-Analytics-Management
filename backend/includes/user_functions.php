<?php
/**
 * User Management Utility Functions
 * ฟังก์ชันช่วยเหลือสำหรับการจัดการผู้ใช้
 * Backend Version - Updated paths for backend folder
 */

// ตรวจสอบสิทธิ์ของผู้ใช้
function checkUserPermission($required_role = 'admin') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        return false;
    }
    
    if (!isset($_SESSION['admin_role'])) {
        return false;
    }
    
    $role_hierarchy = [
        'viewer' => 1,
        'director' => 2,
        'manager' => 3,
        'admin' => 4
    ];
    
    $user_level = $role_hierarchy[$_SESSION['admin_role']] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 0;
    
    return $user_level >= $required_level;
}

// สร้างรหัสผ่านแบบสุ่ม
function generateRandomPassword($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// ตรวจสอบความแข็งแกร่งของรหัสผ่าน
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 6) {
        $errors[] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'รหัสผ่านต้องมีตัวอักษรพิมพ์เล็กอย่างน้อย 1 ตัว';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'รหัสผ่านต้องมีตัวอักษรพิมพ์ใหญ่อย่างน้อย 1 ตัว';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว';
    }
    
    return $errors;
}

// ตรวจสอบรูปแบบอีเมล
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ตรวจสอบรูปแบบชื่อผู้ใช้
function validateUsername($username) {
    // ชื่อผู้ใช้ต้องมีความยาว 3-50 ตัวอักษร และใช้ a-z, A-Z, 0-9, _, - เท่านั้น
    return preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $username);
}

// แปลงบทบาทเป็นชื่อภาษาไทย
function getRoleDisplayName($role) {
    $role_names = [
        'admin' => 'ผู้ดูแลระบบ',
        'manager' => 'ผู้จัดการ',
        'director' => 'ผู้อำนวยการ',
        'viewer' => 'ผู้ดู'
    ];
    
    return $role_names[$role] ?? $role;
}

// แปลงบทบาทเป็นสีของ badge
function getRoleBadgeColor($role) {
    $role_colors = [
        'admin' => 'bg-danger',
        'manager' => 'bg-warning text-dark',
        'director' => 'bg-info',
        'viewer' => 'bg-secondary'
    ];
    
    return $role_colors[$role] ?? 'bg-secondary';
}

// บันทึกกิจกรรมของผู้ใช้
function logActivity($conn, $user_id, $action, $description = null) {
    if (!$conn) return false;
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $conn->prepare("INSERT INTO user_activity_log (UserID, Action, Description, IPAddress, UserAgent) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $action, $description, $ip_address, $user_agent);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        return false;
    }
}

// ตรวจสอบว่าผู้ใช้สามารถแก้ไขผู้ใช้อื่นได้หรือไม่
function canEditUser($editor_role, $target_role, $editor_id, $target_id) {
    // ไม่สามารถแก้ไขตัวเองได้ (ยกเว้นข้อมูลส่วนตัว)
    if ($editor_id == $target_id) {
        return false;
    }
    
    $role_hierarchy = [
        'viewer' => 1,
        'director' => 2,
        'manager' => 3,
        'admin' => 4
    ];
    
    $editor_level = $role_hierarchy[$editor_role] ?? 0;
    $target_level = $role_hierarchy[$target_role] ?? 0;
    
    // ต้องมีระดับสูงกว่าเพื่อแก้ไข
    return $editor_level > $target_level;
}

// สร้าง session ID ที่ปลอดภัย
function generateSecureSessionId() {
    return bin2hex(random_bytes(32));
}

// ตรวจสอบและทำความสะอาดข้อมูลนำเข้า
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// ตรวจสอบ CSRF Token
function generateCSRFToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ดึงข้อมูลผู้ใช้ปัจจุบัน
function getCurrentUser() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['admin_user_id'] ?? 0,
        'username' => $_SESSION['admin_username'] ?? '',
        'role' => $_SESSION['admin_role'] ?? 'viewer',
        'name' => $_SESSION['admin_name'] ?? '',
        'email' => $_SESSION['admin_email'] ?? '',
        'use_database' => $_SESSION['use_database'] ?? false
    ];
}

// ตรวจสอบการล็อกอิน - Updated for backend
function requireLogin($redirect_to = '../login.php') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        header("Location: $redirect_to");
        exit();
    }
}

// ตรวจสอบสิทธิ์และ redirect หากไม่มีสิทธิ์ - Updated for backend
function requireRole($required_role, $redirect_to = '../login.php?error=access_denied') {
    requireLogin();
    
    if (!checkUserPermission($required_role)) {
        header("Location: $redirect_to");
        exit();
    }
}
?>
