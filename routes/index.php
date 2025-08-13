<?php
/**
 * Router System - จัดการการเปลี่ยนเส้นทางตาม GET parameter
 * ใช้สำหรับ redirect ไปยังหน้าต่างๆ ในระบบ
 */

// เริ่ม session
session_start();

// กำหนดเส้นทางที่อนุญาต (Route Map)
$routes = [
    // Admin Routes
    'dashboard' => '../dashboard.php',
    'admin' => '../index.php',
    'admin-dashboard' => '../dashboard.php',
    'admin-projects' => '../projects_table_view.php',
    'admin-stats' => '../projects_table_stats.php',
    'admin-charts' => '../charts/index.php',
    'admin-users' => '../user_guide.php',
    
    // Main System Routes  
    'home' => 'index.php',
    'projects' => 'main_projects.php',
    'analytics' => 'analytics.php',
    'reports' => 'custom_report.php',
    'indicators' => 'manage_indicators.php',
    'project-list' => 'projects_list.php',
    
    // Auth Routes
    'login' => 'login.php',
    'logout' => 'logout.php',
    'portal' => 'portal/index.php',
    
    // API Routes
    'api-projects' => 'api/get_main_project.php',
    'api-indicators' => 'api/get_indicators.php',
    'api-charts' => 'admin/api/chart_data_api.php',
    
    // Tools & Utilities
    'test-db' => 'test-fallback.php',
    'test-connection' => 'test-connection.php',
    'export' => 'export_projects_table_detailed_xlsx.php',
    
    // Special Pages
    '404' => '404.html',
    'error' => '404.html',

    // Backend
    'backend' => '../backend/' 
];

// กำหนดเส้นทางที่ต้องการ authentication
$protected_routes = [
    'dashboard', 'admin', 'admin-dashboard', 'admin-projects', 
    'admin-stats', 'admin-charts', 'admin-users', 'projects', 
    'analytics', 'reports', 'indicators', 'project-list', 'export'
];

/**
 * ฟังก์ชันตรวจสอบ authentication
 */
function checkAuthentication($route) {
    global $protected_routes;
    
    if (in_array($route, $protected_routes)) {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            return false;
        }
    }
    return true;
}

/**
 * ฟังก์ชันสร้าง URL สำหรับ redirect
 */
function buildRedirectUrl($path) {
    $base_url = dirname($_SERVER['PHP_SELF']);
    if ($base_url === '/' || $base_url === '\\') {
        $base_url = '';
    }
    return $base_url . '/' . ltrim($path, '/');
}

/**
 * ฟังก์ชัน log การเข้าถึง (สำหรับ debug)
 */
function logAccess($route, $target, $user_ip) {
    $timestamp = date('Y-m-d H:i:s');
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $log_entry = "[{$timestamp}] Route: {$route} -> {$target} | IP: {$user_ip} | UA: {$user_agent}\n";
    
    // เขียน log ไฟล์ (optional)
    // file_put_contents('logs/route_access.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// รับค่า redirect parameter
$redirect = $_GET['redirect'] ?? $_GET['r'] ?? $_GET['page'] ?? '';

// ถ้าไม่มีการส่ง redirect parameter
if (empty($redirect)) {
    // ตรวจสอบว่า login แล้วหรือยัง
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $redirect = 'home';  // ไปหน้าหลัก
    } else {
        $redirect = 'portal'; // ไปหน้า portal
    }
}

// ทำความสะอาด input
$redirect = trim(strtolower($redirect));
$redirect = preg_replace('/[^a-z0-9\-_]/', '', $redirect);

// ตรวจสอบว่า route ที่ร้องขอมีอยู่หรือไม่
if (!array_key_exists($redirect, $routes)) {
    // หาก route ไม่มีอยู่ ให้ redirect ไป 404
    header('Location: ../404.html');
    exit();
}

// รับเส้นทางปลายทาง
$target_path = $routes[$redirect];

// ตรวจสอบ authentication
if (!checkAuthentication($redirect)) {
    // หากไม่มีสิทธิ์ ให้ไป login หรือ portal
    if (strpos($redirect, 'admin') === 0) {
        header('Location: /login.php?redirect=' . urlencode($redirect));
    } else {
        header('Location: /portal/index.php?redirect=' . urlencode($redirect));
    }
    exit();
}

// Log การเข้าถึง
$user_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
logAccess($redirect, $target_path, $user_ip);

// สร้าง URL สำหรับ redirect
$redirect_url = buildRedirectUrl($target_path);

// Debug information (แสดงเมื่อมี debug parameter)
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    echo "<div style='background:#f8f9fa; padding:15px; margin:10px; border-radius:5px; font-family:monospace;'>";
    echo "<h4>🔍 Route Debug Information</h4>";
    echo "<p><strong>Requested Route:</strong> {$redirect}</p>";
    echo "<p><strong>Target Path:</strong> {$target_path}</p>";
    echo "<p><strong>Full URL:</strong> {$redirect_url}</p>";
    echo "<p><strong>Authentication Required:</strong> " . (in_array($redirect, $protected_routes) ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>User Logged In:</strong> " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>User IP:</strong> {$user_ip}</p>";
    echo "<p><a href='{$redirect_url}'>➜ Continue to destination</a></p>";
    echo "</div>";
    exit();
}

// ทำการ redirect
header('Location: ' . $redirect_url);
exit();

/**
 * ตัวอย่างการใช้งาน:
 * 
 * /routes/?redirect=dashboard       -> /admin/dashboard.php
 * /routes/?redirect=projects        -> /main_projects.php  
 * /routes/?redirect=login           -> /login.php
 * /routes/?redirect=admin-charts    -> /admin/charts/index.php
 * /routes/?r=analytics              -> /analytics.php
 * /routes/?page=home                -> /index.php
 * /routes/?redirect=test-db&debug=1 -> แสดง debug info
 */
?>