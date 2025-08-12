<?php
// Disable error display to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include '../db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");
    
    // Get all tables in the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tableName = $row[0];
        
        // Get column information for each table
        $columnStmt = $pdo->query("DESCRIBE `$tableName`");
        $columns = [];
        
        while ($col = $columnStmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = [
                'field' => $col['Field'],
                'type' => $col['Type'],
                'null' => $col['Null'],
                'key' => $col['Key'],
                'default' => $col['Default'],
                'extra' => $col['Extra']
            ];
        }
        
        $tables[] = [
            'name' => $tableName,
            'columns' => $columns
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $tables
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
