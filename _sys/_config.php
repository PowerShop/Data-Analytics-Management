<?php
/**
 * Configuration File
 * ไฟล์การตั้งค่าหลักของระบบ
 */

// ป้องกันการเข้าถึงโดยตรง
if (!defined('SYSTEM_ACCESS')) {
    die('Direct access not allowed');
}

// Configuration file for Data Analytics Management System
$_config = array();

// Database Configuration
$_config['db_host'] = 'localhost';
$_config['db_database'] = 'data analytics';
$_config['db_user'] = 'root';
$_config['db_password'] = 'Kittisak644245001';
$_config['db_charset'] = 'utf8mb4';

// Application Configuration
$_config['app_name'] = 'ระบบจัดการข้อมูลการวิเคราะห์โครงการ';
$_config['app_version'] = '2.0.0';
$_config['app_timezone'] = 'Asia/Bangkok';
$_config['app_debug'] = true; // Set to false in production

// Session Configuration
$_config['session_name'] = 'DATA_ANALYTICS_SESSION';
$_config['session_lifetime'] = 86400; // 24 hours

// File Upload Configuration
$_config['upload_max_size'] = '10M';
$_config['upload_allowed_types'] = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];

// Pagination Configuration
$_config['items_per_page'] = 20;

// Asset Version (for cache busting)
$_config['asset_version'] = '2.0.0';

// ตั้งค่า timezone
date_default_timezone_set($_config['app_timezone']);

// Load database connection class
require_once __DIR__ . '/_database.php';

// Initialize database
Database::init($_config);

// สร้างตัวแปร PDO global สำหรับใช้งาน (backward compatibility)
try {
    $pdo = Database::getConnection();
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
    // ในการใช้งานจริง อาจจะ redirect ไปหน้า error
}

?>
