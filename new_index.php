<!--
    New Enhanced Main Index File for Data Analytics Management System v2.0
    
    This file serves as the main entry point with improved:
    - Modern PHP 8+ structure
    - Enhanced security
    - Better error handling  
    - Performance optimizations
    - SEO improvements
-->
<?php
    // Initialize the application
    define('SYSTEM_ACCESS', true);
    require_once dirname(__FILE__) . '/_sys/_api.php';

    // Security headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Set page title for SEO
    $pageTitle = '';
    if (isset($_GET['page'])) {
        $pageTitles = [
            'home' => 'หน้าแรก',
            'dashboard' => 'แดชบอร์ด',
            'projects' => 'โครงการ',
            'analytics' => 'วิเคราะห์ข้อมูล',
            'reports' => 'รายงาน',
            'settings' => 'ตั้งค่า'
        ];
        $pageTitle = $pageTitles[$_GET['page']] ?? '';
    }

    // Include the main layout
    include 'main.php';
?>
