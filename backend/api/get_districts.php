<?php
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['province']) || empty($_GET['province'])) {
    echo json_encode([]);
    exit;
}

$province = $conn->real_escape_string($_GET['province']);

$query = "SELECT DISTINCT District 
          FROM ProjectVillages 
          WHERE Province = '$province' 
            AND District IS NOT NULL 
            AND District != '' 
          ORDER BY District";

$result = $conn->query($query);
$districts = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $districts[] = $row;
    }
}

echo json_encode($districts);
?>
