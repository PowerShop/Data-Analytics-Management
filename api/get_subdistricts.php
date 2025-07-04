<?php
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['province']) || empty($_GET['province']) || 
    !isset($_GET['district']) || empty($_GET['district'])) {
    echo json_encode([]);
    exit;
}

$province = $conn->real_escape_string($_GET['province']);
$district = $conn->real_escape_string($_GET['district']);

$query = "SELECT DISTINCT Subdistrict 
          FROM ProjectVillages 
          WHERE Province = '$province' 
            AND District = '$district'
            AND Subdistrict IS NOT NULL 
            AND Subdistrict != '' 
          ORDER BY Subdistrict";

$result = $conn->query($query);
$subdistricts = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subdistricts[] = $row;
    }
}

echo json_encode($subdistricts);
?>
