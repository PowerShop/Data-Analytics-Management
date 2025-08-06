<?php
/**
 * URL Helper Functions for Clean URL Routing
 * ฟังก์ชันช่วยเหลือสำหรับการจัดการ URL routing
 */

/**
 * สร้าง URL แบบ clean URL
 * @param string $route เส้นทาง route
 * @param array $params พารามิเตอร์เพิ่มเติม
 * @return string URL ที่สร้างขึ้น
 */
function url($route = '', $params = []) {
    $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    if ($script_dir === '/') {
        $script_dir = '';
    }
    
    // สร้าง clean URL
    $url = $base_url . $script_dir . '/' . ltrim($route, '/');
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * Redirect ไปยัง route ที่กำหนด
 * @param string $route เส้นทาง route
 * @param array $params พารามิเตอร์เพิ่มเติม
 */
function redirect($route = '', $params = []) {
    header('Location: ' . url($route, $params));
    exit();
}

/**
 * ตรวจสอบว่าอยู่ใน route ปัจจุบันหรือไม่
 * @param string $route เส้นทาง route ที่ต้องการตรวจสอบ
 * @return bool true ถ้าตรงกับ route ปัจจุบัน
 */
function is_current_route($route) {
    $current = $GLOBALS['current_route'] ?? '';
    return $current === $route;
}

/**
 * ตรวจสอบว่า route ปัจจุบันเริ่มต้นด้วยเส้นทางที่กำหนดหรือไม่
 * @param string $route_prefix คำนำหน้าของ route
 * @return bool true ถ้า route ปัจจุบันเริ่มต้นด้วยคำนำหน้าที่กำหนด
 */
function route_starts_with($route_prefix) {
    $current = $GLOBALS['current_route'] ?? '';
    return strpos($current, $route_prefix) === 0;
}

/**
 * สร้าง active class สำหรับ navigation
 * @param string $route เส้นทาง route
 * @param string $active_class CSS class ที่ต้องการเมื่อ active
 * @return string CSS class
 */
function nav_active($route, $active_class = 'active') {
    return is_current_route($route) ? $active_class : '';
}

/**
 * สร้าง active class สำหรับ navigation แบบ prefix
 * @param string $route_prefix คำนำหน้าของ route
 * @param string $active_class CSS class ที่ต้องการเมื่อ active
 * @return string CSS class
 */
function nav_active_prefix($route_prefix, $active_class = 'active') {
    return route_starts_with($route_prefix) ? $active_class : '';
}

/**
 * ดึงพารามิเตอร์จาก URL
 * @param string $key ชื่อพารามิเตอร์
 * @param mixed $default ค่า default ถ้าไม่พบ
 * @return mixed ค่าพารามิเตอร์
 */
function get_param($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * ดึงพารามิเตอร์จาก POST
 * @param string $key ชื่อพารามิเตอร์
 * @param mixed $default ค่า default ถ้าไม่พบ
 * @return mixed ค่าพารามิเตอร์
 */
function get_post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * สร้าง breadcrumb จาก route ปัจจุบัน
 * @return array รายการ breadcrumb
 */
function get_breadcrumb() {
    $current = $GLOBALS['current_route'] ?? '';
    $parts = explode('/', $current);
    
    $breadcrumbs = [
        ['title' => 'หน้าหลัก', 'url' => '/home']
    ];
    
    $route_titles = [
        'projects' => 'โครงการ',
        'projects/add' => 'เพิ่มโครงการ',
        'projects/edit' => 'แก้ไขโครงการ',
        'projects/detail' => 'รายละเอียดโครงการ',
        'projects/list' => 'รายการโครงการ',
        'projects/view' => 'ดูโครงการ',
        'projects/stats' => 'สถิติโครงการ',
        'main-projects' => 'โครงการหลัก',
        'indicators' => 'ตัวชี้วัด',
        'reports' => 'รายงาน',
        'analytics' => 'การวิเคราะห์',
        'admin' => 'ผู้ดูแลระบบ',
        'dashboard' => 'แดชบอร์ด',
    ];
    
    if (!empty($current) && $current !== 'home') {
        $breadcrumbs[] = [
            'title' => $route_titles[$current] ?? ucfirst(str_replace('-', ' ', $current)),
            'url' => '/' . $current
        ];
    }
    
    return $breadcrumbs;
}

/**
 * แสดง breadcrumb ในรูปแบบ HTML
 * @return string HTML ของ breadcrumb
 */
function render_breadcrumb() {
    $breadcrumbs = get_breadcrumb();
    
    if (count($breadcrumbs) <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($breadcrumbs as $index => $crumb) {
        $is_last = ($index === count($breadcrumbs) - 1);
        
        if ($is_last) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($crumb['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['title']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}

/**
 * ตรวจสอบสิทธิ์การเข้าถึง route
 * @param string $route เส้นทาง route
 * @return bool true ถ้ามีสิทธิ์เข้าถึง
 */
function can_access_route($route) {
    // สามารถปรับแต่งตรรกะการตรวจสอบสิทธิ์ได้ที่นี่
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        return false;
    }
    
    // ตรวจสอบสิทธิ์ admin สำหรับ admin routes
    $admin_routes = ['admin', 'tools/find-replace', 'tools/duplicate-fix'];
    $admin_users = ['admin', 'manager', 'director'];
    
    if (in_array($route, $admin_routes) || strpos($route, 'admin/') === 0) {
        return in_array($_SESSION['username'] ?? '', $admin_users);
    }
    
    return true;
}

/**
 * สร้าง CSRF token
 * @return string CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * ตรวจสอบ CSRF token
 * @param string $token Token ที่ต้องการตรวจสอบ
 * @return bool true ถ้า token ถูกต้อง
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * สร้าง hidden input field สำหรับ CSRF token
 * @return string HTML ของ input field
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}
?>
