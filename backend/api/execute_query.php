<?php
// Disable error display to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['sql']) || empty(trim($input['sql']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'SQL query is required']);
    exit;
}

$sql = trim($input['sql']);

// Basic security check - only allow SELECT statements
if (!preg_match('/^\s*SELECT\s+/i', $sql)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Only SELECT statements are allowed']);
    exit;
}

// Prevent potentially dangerous SQL keywords
$dangerous_keywords = ['DROP', 'DELETE', 'INSERT', 'UPDATE', 'ALTER', 'CREATE', 'TRUNCATE', 'REPLACE'];
foreach ($dangerous_keywords as $keyword) {
    if (preg_match('/\b' . $keyword . '\b/i', $sql)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Dangerous keyword '$keyword' detected"]);
        exit;
    }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");
    
    // Add LIMIT if not present to prevent large result sets
    if (!preg_match('/\bLIMIT\s+\d+/i', $sql)) {
        $sql .= ' LIMIT 1000';
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $columns = [];
    
    if (count($data) > 0) {
        $columns = array_keys($data[0]);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'columns' => $columns,
        'rowCount' => count($data),
        'sql' => $sql
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'sql' => $sql
    ]);
}
?>
