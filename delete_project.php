<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("ไม่พบรหัสโครงการ");
}

$id = $_GET['id'];

try {
    // เริ่ม transaction
    $conn->autocommit(false);
    
    // ลบข้อมูลที่เกี่ยวข้องทั้งหมด
    $conn->query("DELETE FROM budgetItems WHERE projectID = $id");
    $conn->query("DELETE FROM projectTargetCounts WHERE projectID = $id");
    $conn->query("DELETE FROM projectVillages WHERE projectID = $id");
    $conn->query("DELETE FROM projectSchools WHERE projectID = $id");
    $conn->query("DELETE FROM projectNetworks WHERE projectID = $id");
    $conn->query("DELETE FROM projectEnterprises WHERE projectID = $id");
    $conn->query("DELETE FROM projectProducts WHERE projectID = $id");
    $conn->query("DELETE FROM projectGVH WHERE projectID = $id");
    $conn->query("DELETE FROM projectSROI WHERE projectID = $id");
    $conn->query("DELETE FROM projectSoftPower WHERE projectID = $id");
    
    // ลบโครงการ
    $conn->query("DELETE FROM projects WHERE projectID = $id");
    
    // commit transaction
    $conn->commit();
    
    // กลับไปยังหน้าเดิม
    header("Location: projects_list.php?deleted=success");
    exit;
    
} catch (Exception $e) {
    // rollback transaction
    $conn->rollback();
    header("Location: projects_list.php?error=delete_failed");
    exit;
}
?>
