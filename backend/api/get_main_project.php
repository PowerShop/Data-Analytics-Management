<?php
header('Content-Type: application/json');
include '../db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ไม่พบ ID โครงการหลัก']);
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM MainProjects WHERE MainProjectID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'error' => 'ไม่พบโครงการหลัก']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
