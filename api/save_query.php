<?php
// Disable error display to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save a new query
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || !isset($input['sql'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and SQL are required']);
        exit;
    }
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8");
        
        // Create saved_queries table if it doesn't exist
        $createTableSQL = "CREATE TABLE IF NOT EXISTS saved_queries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            sql_query TEXT NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($createTableSQL);
        
        // Insert the new query
        $stmt = $pdo->prepare("INSERT INTO saved_queries (name, sql_query, description) VALUES (?, ?, ?)");
        $stmt->execute([
            $input['name'],
            $input['sql'],
            $input['description'] ?? ''
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Query saved successfully',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get all saved queries
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8");
        
        $stmt = $pdo->query("SELECT * FROM saved_queries ORDER BY created_at DESC");
        $queries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $queries
        ]);
        
    } catch (Exception $e) {
        // If table doesn't exist, return empty array
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
    }
    
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete a saved query
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Query ID is required']);
        exit;
    }
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8");
        
        $stmt = $pdo->prepare("DELETE FROM saved_queries WHERE id = ?");
        $result = $stmt->execute([$input['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Query deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Query not found'
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
