<?php
/**
 * Main API Core File
 * ไฟล์หลักของระบบ API
 */

// ป้องกันการเข้าถึงโดยตรง
if (!defined('SYSTEM_ACCESS')) {
    define('SYSTEM_ACCESS', true);
}

// Include configuration
require_once dirname(__FILE__) . '/_config.php';

// Include functions
require_once dirname(__FILE__) . '/_func.php';

// Set error reporting based on debug mode
if ($_config['app_debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Configure session
ini_set('session.name', $_config['session_name']);
ini_set('session.gc_maxlifetime', $_config['session_lifetime']);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create API object with database connection
try {
    $api = (object) [
        'db' => Database::getConnection(),
        'config' => $_config
    ];

    // Legacy compatibility
    $api->sql = $api->db;

    } catch (PDOException $e) {
        if ($_config['app_debug']) {
            die("Database Connection Error: " . $e->getMessage());
        } else {
            die("Database connection failed. Please try again later.");
        }
    }

    // Handle API requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
        (isset($_POST['action']) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false))) {
        
        header('Content-Type: application/json');
        
        try {
            // Get POST data
            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
                $action = $input['action'] ?? '';
            } else {
                $action = $_POST['action'] ?? '';
                $input = $_POST;
            }
            
            switch ($action) {
                case 'save_project_indicators':
                    $project_id = $input['project_id'] ?? 0;
                    $indicators = $input['indicators'] ?? [];
                    
                    $success = 0;
                    foreach ($indicators as $indicator_id => $value) {
                        if (!empty($value)) {
                            $stmt = $api->db->prepare("
                                INSERT INTO project_indicators (project_id, indicator_id, value) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE value = VALUES(value)
                            ");
                            if ($stmt->execute([$project_id, $indicator_id, $value])) {
                                $success++;
                            }
                        }
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'message' => "บันทึกตัวชี้วัด $success รายการสำเร็จ"
                    ]);
                    break;
                    
                case 'save_single_indicator':
                    $project_id = $input['project_id'] ?? 0;
                    $indicator_id = $input['indicator_id'] ?? 0;
                    $value = $input['value'] ?? '';
                    
                    $stmt = $api->db->prepare("
                        INSERT INTO project_indicators (project_id, indicator_id, value) 
                        VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE value = VALUES(value)
                    ");
                    
                    if ($stmt->execute([$project_id, $indicator_id, $value])) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'บันทึกสำเร็จ'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'ไม่สามารถบันทึกได้'
                        ]);
                    }
                    break;
                    
                case 'delete_project':
                    $project_id = $input['project_id'] ?? 0;
                    
                    // ลบตัวชี้วัดของโครงการก่อน
                    $stmt = $api->db->prepare("DELETE FROM project_indicators WHERE project_id = ?");
                    $stmt->execute([$project_id]);
                    
                    // ลบโครงการ
                    $stmt = $api->db->prepare("DELETE FROM projects WHERE id = ?");
                    if ($stmt->execute([$project_id])) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'ลบโครงการสำเร็จ'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'ไม่สามารถลบโครงการได้'
                        ]);
                    }
                    break;
                    
                case 'get_report_data':
                    $stmt = $api->db->query("
                        SELECT p.*, 
                               COUNT(pi.id) as indicator_count,
                               (SELECT COUNT(*) FROM project_indicators pi2 WHERE pi2.project_id = p.id AND pi2.value IS NOT NULL) as filled_indicators
                        FROM projects p 
                        LEFT JOIN project_indicators pi ON p.id = pi.project_id 
                        GROUP BY p.id 
                        ORDER BY p.created_date DESC
                    ");
                    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $data = [];
                    foreach ($projects as $project) {
                        $progress = $project['indicator_count'] > 0 ? 
                                   round(($project['filled_indicators'] / $project['indicator_count']) * 100) : 0;
                        
                        $data[] = [
                            'id' => $project['id'],
                            'project_name' => $project['project_name'],
                            'status' => $project['status'],
                            'created_date' => date('d/m/Y', strtotime($project['created_date'])),
                            'indicator_count' => $project['indicator_count'],
                            'progress' => $progress
                        ];
                    }
                    
                    echo json_encode(['data' => $data]);
                    break;
                    
                default:
                    echo json_encode([
                        'success' => false,
                        'message' => 'Unknown action'
                    ]);
                    break;
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    // Function to get asset URL with version for cache busting
    function asset($path, $version = null)
    {
        global $_config;
        $version = $version ?: $_config['asset_version'];
        return $path . '?v=' . $version;
    }

    // Function to get config value
    function config($key, $default = null)
    {
        global $_config;
        return isset($_config[$key]) ? $_config[$key] : $default;
    }
?>
