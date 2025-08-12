<?php
include '../db.php';

if (!isset($_GET['id'])) {
    die("ไม่พบรหัสโครงการ");
}

$id = $_GET['id'];

try {
    // เริ่ม transaction
    $conn->autocommit(false);
    
    // ลบข้อมูลที่เกี่ยวข้องทั้งหมด
    $conn->query("DELETE FROM budgetitems WHERE projectid = $id");
    $conn->query("DELETE FROM projecttargetcounts WHERE projectid = $id");
    $conn->query("DELETE FROM projectvillages WHERE projectid = $id");
    $conn->query("DELETE FROM projectschools WHERE projectid = $id");
    $conn->query("DELETE FROM projectnetworks WHERE projectid = $id");
    $conn->query("DELETE FROM projectenterprises WHERE projectid = $id");
    $conn->query("DELETE FROM projectproducts WHERE projectid = $id");
    $conn->query("DELETE FROM projectgvh WHERE projectid = $id");
    $conn->query("DELETE FROM projectsroi WHERE projectid = $id");
    $conn->query("DELETE FROM projectsoftpower WHERE projectid = $id");
    
    // ลบโครงการ
    $conn->query("DELETE FROM projects WHERE projectid = $id");
    
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
