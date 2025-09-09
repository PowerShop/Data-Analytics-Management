<?php
// เริ่ม session เฉพาะเมื่อยังไม่มี session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตั้งค่า timezone เป็น UTC+7 (Asia/Bangkok)
date_default_timezone_set('Asia/Bangkok');

// ตั้งค่า header JSON ก่อนทุกอย่าง
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// ฟังก์ชันสำหรับส่ง JSON response
function sendJsonResponse($data) {
    echo json_encode($data);
    exit;
}

// ฟังก์ชันสำหรับส่ง error response
function sendErrorResponse($message, $status = 'error') {
    sendJsonResponse([
        'success' => false,
        'message' => $message,
        'status' => $status,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// Include database connection with error handling
$db_connected = false;
$conn = null;

try {
    // ใช้ไฟล์ database connection ที่สร้างขึ้นสำหรับ API
    if (file_exists(__DIR__ . '/db_connection.php')) {
        require_once __DIR__ . '/db_connection.php';
        if (isset($conn) && $conn instanceof mysqli) {
            $db_connected = true;
        }
    }

    // ถ้าไฟล์ API ไม่พบ ให้ fallback ไปหาไฟล์อื่น
    if (!$db_connected) {
        $db_files = [
            __DIR__ . '/../db.php',           // จาก api/ ไป db.php ใน root
            __DIR__ . '/../database/db.php', // จาก api/ ไป database/db.php
        ];

        foreach ($db_files as $db_file) {
            if (file_exists($db_file)) {
                // ใช้ output buffering เพื่อป้องกัน HTML output
                ob_start();
                $include_success = include $db_file;
                $output = ob_get_clean();

                // ตรวจสอบว่ามี HTML output หรือไม่ และมี $conn หรือไม่
                if (!empty($output)) {
                    continue; // ข้ามไฟล์นี้ถ้ามี HTML output
                }

                if (!isset($conn) || !$include_success) {
                    continue; // ข้ามถ้าไม่มีการตั้งค่า $conn
                }

                $db_connected = true;
                break;
            }
        }
    }

    if (!$db_connected || !isset($conn)) {
        throw new Exception('Database connection could not be established');
    }

} catch (Exception $e) {
    sendErrorResponse('Database connection error: ' . $e->getMessage());
}

try {
    $start_time = microtime(true);

    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    $db_status = 'healthy';
    $db_error = null;
    $db_warnings = [];

    if (!$conn) {
        $db_status = 'error';
        $db_error = 'Database connection object not found';
    } elseif ($conn->connect_error) {
        $db_status = 'error';
        $db_error = $conn->connect_error;
    } else {
        // ทดสอบการเชื่อมต่อด้วยการ query ง่ายๆ
        $test_query = $conn->query("SELECT 1");
        if (!$test_query) {
            $db_status = 'error';
            $db_error = $conn->error;
        } else {
            // ทดสอบการ query ตารางหลัก
            $main_table_test = $conn->query("SELECT COUNT(*) FROM projects LIMIT 1");
            if (!$main_table_test) {
                $db_warnings[] = 'Cannot query main tables: ' . $conn->error;
            }
        }
    }

    // ตรวจสอบจำนวนตารางและ view
    $tables_count = 0;
    $views_count = 0;
    $skipped_views_count = 0;
    $error_views_count = 0;
    $tables_info = [];
    $views_info = [];

    // Blacklist สำหรับ view ที่ไม่ต้องการเรียกใช้
    $view_blacklist = ['active_users_view', 'login_stats_view'];

    if ($db_status === 'healthy') {
        // ตรวจสอบตาราง
        $tables_result = $conn->query("SHOW TABLES");
        if ($tables_result) {
            $tables_count = $tables_result->num_rows;
            while ($row = $tables_result->fetch_array()) {
                $table_name = $row[0];
                // นับจำนวนแถวในแต่ละตาราง (มี error handling)
                $row_count = 0;
                $is_view = false;

                try {
                    // ตรวจสอบว่าเป็น view หรือ table
                    $type_result = $conn->query("SELECT TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table_name'");
                    if ($type_result) {
                        $type_data = $type_result->fetch_assoc();
                        $is_view = ($type_data && $type_data['TABLE_TYPE'] === 'VIEW');
                    }

                    if ($is_view) {
                        // ตรวจสอบว่า view นี้อยู่ใน blacklist หรือไม่
                        if (in_array($table_name, $view_blacklist)) {
                            $views_info[] = [
                                'name' => $table_name,
                                'rows' => -2, // -2 = skipped (blacklisted)
                                'type' => 'view',
                                'status' => 'skipped'
                            ];
                            $skipped_views_count++;
                            continue;
                        }

                        // สำหรับ view ปกติ ให้ query แบบมี error handling
                        $count_result = $conn->query("SELECT COUNT(*) as count FROM `$table_name`");
                        if ($count_result) {
                            $count_data = $count_result->fetch_assoc();
                            $row_count = $count_data ? intval($count_data['count']) : 0;
                            $views_info[] = [
                                'name' => $table_name,
                                'rows' => $row_count,
                                'type' => 'view'
                            ];
                            $views_count++;
                        } else {
                            $views_info[] = [
                                'name' => $table_name,
                                'rows' => -1, // Error querying view
                                'type' => 'view',
                                'error' => $conn->error
                            ];
                            $error_views_count++;
                        }
                    } else {
                        // สำหรับ table ปกติ
                        $count_result = $conn->query("SELECT COUNT(*) as count FROM `$table_name`");
                        if ($count_result) {
                            $count_data = $count_result->fetch_assoc();
                            $row_count = $count_data ? intval($count_data['count']) : 0;
                        } else {
                            $row_count = -1; // Error querying table
                        }
                        $tables_info[] = [
                            'name' => $table_name,
                            'rows' => $row_count,
                            'type' => 'table'
                        ];
                        $tables_count++;
                    }
                } catch (Exception $e) {
                    // ถ้ามีปัญหากับตาราง/view นี้
                    if ($is_view) {
                        $views_info[] = [
                            'name' => $table_name,
                            'rows' => -1,
                            'type' => 'view',
                            'error' => $e->getMessage()
                        ];
                        $error_views_count++;
                    } else {
                        $tables_info[] = [
                            'name' => $table_name,
                            'rows' => -1,
                            'type' => 'table',
                            'error' => $e->getMessage()
                        ];
                        $tables_count++;
                    }
                }
            }
        }
    }

    // ตรวจสอบตารางหลัก
    $main_tables_status = [];
    $main_tables = ['projects', 'project_indicators', 'budgetitems', 'projectvillages'];

    foreach ($main_tables as $table) {
        $status = 'unknown';
        $row_count = 0;

        if ($db_status === 'healthy') {
            try {
                $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
                if ($result) {
                    $count_data = $result->fetch_assoc();
                    $row_count = $count_data ? intval($count_data['count']) : 0;
                    $status = $row_count > 0 ? 'healthy' : 'empty';
                } else {
                    $status = 'error';
                }
            } catch (Exception $e) {
                $status = 'error';
            }
        }

        $main_tables_status[$table] = [
            'status' => $status,
            'rows' => $row_count
        ];
    }

    // ตรวจสอบ session
    $session_status = 'unknown';
    $session_info = [];

    if (isset($_SESSION)) {
        $session_status = 'active';
        $session_info = [
            'session_id' => session_id(),
            'admin_logged_in' => isset($_SESSION['admin_logged_in']) ? $_SESSION['admin_logged_in'] : false,
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            'last_activity' => isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : null
        ];
    } else {
        $session_status = 'inactive';
    }

    // วัดเวลาตอบสนอง
    $response_time = round((microtime(true) - $start_time) * 1000, 2); // มิลลิวินาที

    // ข้อมูลระบบ
    $system_info = [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'memory_usage' => memory_get_peak_usage(true),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size')
    ];

    // สรุปสถานะโดยรวม
    $overall_status = 'healthy';
    if ($db_status === 'error') {
        $overall_status = 'error';
    } elseif ($session_status === 'inactive') {
        $overall_status = 'warning';
    } elseif (!empty($db_warnings)) {
        $overall_status = 'warning'; // มี warnings แต่ยังทำงานได้
    }

    sendJsonResponse([
        'success' => true,
        'status' => $overall_status,
        'timestamp' => date('Y-m-d H:i:s'),
        'response_time_ms' => $response_time,
        'database' => [
            'status' => $db_status,
            'error' => $db_error,
            'warnings' => $db_warnings,
            'tables_count' => $tables_count,
            'views_count' => $views_count,
            'skipped_views_count' => $skipped_views_count,
            'error_views_count' => $error_views_count,
            'total_objects' => $tables_count + $views_count + $skipped_views_count + $error_views_count,
            'tables_info' => $tables_info,
            'views_info' => $views_info,
            'main_tables' => $main_tables_status
        ],
        'session' => [
            'status' => $session_status,
            'info' => $session_info
        ],
        'system' => $system_info
    ]);

} catch (Exception $e) {
    sendErrorResponse('System error: ' . $e->getMessage());
}
?>
