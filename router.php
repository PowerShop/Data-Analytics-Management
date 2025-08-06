<?php
// ป้องกัน direct access ถ้าไม่มี route parameter
if (!isset($_GET['route'])) {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>
    <html><head><title>403 Forbidden</title></head>
    <body><h1>403 Forbidden</h1><p>Direct access to router is not allowed.</p></body>
    </html>';
    exit();
}

// เริ่ม session หากยังไม่ได้เริ่ม
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// กำหนด routes และไฟล์ที่ต้องการ
$routes = [
    // หน้าหลัก
    '' => 'welcome.html',
    'home' => 'index.php',
    'dashboard' => 'dashboard.php',
    
    // การจัดการผู้ใช้
    'login' => 'login.php',
    'logout' => 'logout.php',
    
    // การจัดการโครงการ
    'projects' => 'projects_list.php',
    'view/projects' => 'projects_list.php',
    'projects/list' => 'projects_list.php',
    'projects/add' => 'add_project.php',
    'projects/edit' => 'edit_project.php',
    'projects/detail' => 'project_detail.php',
    'projects/delete' => 'delete_project.php',
    'projects/view' => 'projects_table_view.php',
    'projects/stats' => 'projects_table_stats.php',
    'projects/data' => 'projects_table_data.php',
    'projects/export' => 'export_projects_table_detailed_xlsx.php',
    
    // โครงการหลัก
    'projects/main-projects' => 'main_projects.php',

    // ตัวชี้วัด
    'projects/indicators' => 'manage_indicators.php',
    'indicators/manage' => 'manage_indicators.php',
    
    // รายงาน
    'reports' => 'custom_report.php',
    'reports/custom' => 'custom_report.php',
    'analytics' => 'analytics.php',
    
    // เครื่องมือ
    'tools/find-replace' => 'admin_find_replace.php',
    'tools/duplicate-fix' => 'fix_duplicate_indicators.php',
    
    // Admin panel
    'admin' => 'admin/index.php',
    'admin/dashboard' => 'admin/dashboard.php',
    'admin/login' => 'admin/login.php',
    'admin/projects' => 'admin/projects_table_view.php',
    'admin/projects/stats' => 'admin/projects_table_stats.php',
    'admin/projects/data' => 'admin/projects_table_data.php',
    'admin/projects/detail' => 'admin/project_detail.php',
    'admin/projects/export' => 'admin/export_projects_table_detailed_xlsx.php',
    'admin/charts' => 'admin/charts/charts.php',
    'admin/charts/builder' => 'admin/charts/chart_builder.php',
    'admin/charts/detail' => 'admin/charts/chart_detail.php',
];

// Protected routes ที่ต้อง login
$protected_routes = [
    'dashboard',
    'projects',
    'projects/list',
    'projects/add', 
    'projects/edit',
    'projects/detail',
    'projects/delete',
    'projects/view',
    'projects/stats',
    'projects/data',
    'projects/export',
    'main-projects',
    'indicators',
    'indicators/manage',
    'reports',
    'reports/custom',
    'analytics',
    'tools/find-replace',
    'tools/duplicate-fix',
    'admin',
    'admin/dashboard',
    'admin/projects',
    'admin/projects/stats',
    'admin/projects/data',
    'admin/projects/detail',
    'admin/projects/export',
    'admin/charts',
    'admin/charts/builder',
    'admin/charts/detail',
];

// Admin only routes
$admin_routes = [
    'admin',
    'admin/dashboard',
    'admin/login',
    'admin/projects',
    'admin/projects/stats',
    'admin/projects/data',
    'admin/projects/detail',
    'admin/projects/export',
    'admin/charts',
    'admin/charts/builder',
    'admin/charts/detail',
    'tools/find-replace',
    'tools/duplicate-fix',
];

// ดึง route จาก URL
$request = $_GET['route'] ?? '';
$request = trim($request, '/');

// ตรวจสอบว่า route มีอยู่หรือไม่
if (!array_key_exists($request, $routes)) {
    // ถ้าไม่มี route ให้แสดงหน้า 404
    http_response_code(404);
    include '404.php';
    exit();
}

// ตรวจสอบการ authentication
if (in_array($request, $protected_routes)) {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        // ถ้ายังไม่ login ให้ redirect ไป login
        $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        if ($script_dir === '/') $script_dir = '';
        header('Location: ' . $base_url . $script_dir . '/login');
        exit();
    }
}

// ตรวจสอบสิทธิ์ admin
if (in_array($request, $admin_routes)) {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        if ($script_dir === '/') $script_dir = '';
        header('Location: ' . $base_url . $script_dir . '/admin/login');
        exit();
    }
    
    // ตรวจสอบระดับผู้ใช้ (สามารถปรับแต่งได้)
    $admin_users = ['admin', 'manager', 'director'];
    if (!in_array($_SESSION['username'] ?? '', $admin_users)) {
        http_response_code(403);
        echo "<!DOCTYPE html>
        <html lang='th'>
        <head>
            <meta charset='UTF-8'>
            <title>ไม่มีสิทธิ์เข้าถึง</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
            <div class='container mt-5'>
                <div class='row justify-content-center'>
                    <div class='col-md-6'>
                        <div class='alert alert-danger text-center'>
                            <h4>ไม่มีสิทธิ์เข้าถึง</h4>
                            <p>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</p>
                            <a href='/home' class='btn btn-primary'>กลับหน้าหลัก</a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
        exit();
    }
}

// เก็บ route สำหรับใช้ในหน้าต่างๆ
$GLOBALS['current_route'] = $request;

// Load ไฟล์ที่ต้องการ
$file_path = $routes[$request];

// ตรวจสอบว่าไฟล์มีอยู่จริง
if (file_exists($file_path)) {
    // ถ้าเป็นไฟล์ HTML ให้ serve ตรงๆ
    if (pathinfo($file_path, PATHINFO_EXTENSION) === 'html') {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($file_path);
    } else {
        // ถ้าเป็นไฟล์ PHP ให้ include
        include $file_path;
    }
} else {
    http_response_code(500);
    echo "<!DOCTYPE html>
    <html lang='th'>
    <head>
        <meta charset='UTF-8'>
        <title>เกิดข้อผิดพลาด</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
        <div class='container mt-5'>
            <div class='row justify-content-center'>
                <div class='col-md-6'>
                    <div class='alert alert-danger text-center'>
                        <h4>เกิดข้อผิดพลาด</h4>
                        <p>ไม่พบไฟล์ที่ต้องการ: $file_path</p>
                        <a href='/home' class='btn btn-primary'>กลับหน้าหลัก</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
}
?>
