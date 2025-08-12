<?php
include '../db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $projectId = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    $indicatorData = isset($_POST['indicators']) ? $_POST['indicators'] : [];
    
    if ($projectId <= 0) {
        throw new Exception('Invalid project ID');
    }
    
    if (empty($indicatorData)) {
        throw new Exception('No indicator data provided');
    }
    
    // เริ่ม transaction
    $conn->autocommit(false);
    
    // ลบข้อมูลตัวชี้วัดเดิมทั้งหมดของโครงการนี้
    $conn->query("DELETE FROM project_indicators WHERE ProjectID = $projectId");
    
    // เตรียม statements
    $stmt_indicator = $conn->prepare("INSERT INTO project_indicators (ProjectID, IndicatorID, Value) VALUES (?, ?, ?)");
    $stmt_detail = $conn->prepare("INSERT INTO project_indicator_details (ProjectIndicatorID, DetailText) VALUES (?, ?)");
    
    foreach ($indicatorData as $indicator) {
        $indicatorId = intval($indicator['indicator_id']);
        $values = $indicator['values'];
        
        if (!is_array($values)) {
            continue;
        }
        
        foreach ($values as $valueData) {
            $value = floatval($valueData['value']);
            $details = isset($valueData['details']) ? $valueData['details'] : [];
            
            if ($value <= 0) {
                continue;
            }
            
            // บันทึกค่าตัวชี้วัด
            $stmt_indicator->bind_param("iid", $projectId, $indicatorId, $value);
            if (!$stmt_indicator->execute()) {
                throw new Exception('Failed to save indicator value');
            }
            
            $projectIndicatorId = $conn->insert_id;
            
            // บันทึกรายละเอียดเพิ่มเติม
            if (!empty($details) && is_array($details)) {
                foreach ($details as $detail) {
                    $detailText = trim($detail);
                    if (!empty($detailText)) {
                        $stmt_detail->bind_param("is", $projectIndicatorId, $detailText);
                        if (!$stmt_detail->execute()) {
                            throw new Exception('Failed to save indicator detail');
                        }
                    }
                }
            }
        }
    }
    
    // commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'บันทึกข้อมูลตัวชี้วัดสำเร็จ'
    ]);
    
} catch (Exception $e) {
    // rollback transaction
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
