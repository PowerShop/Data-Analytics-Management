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
    $conn->query("DELETE FROM BudgetItems WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectTargetCounts WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectVillages WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectSchools WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectNetworks WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectEnterprises WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectProducts WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectGVH WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectSROI WHERE ProjectID = $id");
    $conn->query("DELETE FROM ProjectSoftPower WHERE ProjectID = $id");
    
    // ลบโครงการ
    $conn->query("DELETE FROM Projects WHERE ProjectID = $id");
    
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
