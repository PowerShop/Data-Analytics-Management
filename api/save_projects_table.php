<?php
include '../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['projects'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลโครงการ']);
    exit;
}

$projects = json_decode($_POST['projects'], true);

if (!$projects || !is_array($projects)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลโครงการไม่ถูกต้อง']);
    exit;
}

try {
    // เริ่ม transaction
    $conn->autocommit(false);
    
    $saved_count = 0;
    $errors = [];

    foreach ($projects as $index => $project) {
        try {
            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($project['projectName'])) {
                $errors[] = "โครงการที่ " . ($index + 1) . ": ต้องมีชื่อโครงการ";
                continue;
            }
            
            if (empty($project['strategyId'])) {
                $errors[] = "โครงการที่ " . ($index + 1) . ": ต้องเลือกยุทธศาสตร์";
                continue;
            }
            
            if (empty($project['mainProjectId'])) {
                $errors[] = "โครงการที่ " . ($index + 1) . ": ต้องเลือกโครงการหลัก";
                continue;
            }

            // บันทึกข้อมูลโครงการหลัก
            $stmt = $conn->prepare("INSERT INTO Projects (ProjectName, ProjectCode, AgencyName, ResponsiblePerson, Province, ProjectYear, StrategyID, MainProjectID, TargetArea) VALUES (?,?,?,?,?,?,?,?,?)");
            
            $province = "ราชบุรี"; // ค่าเริ่มต้น
            $targetArea = $project['villageName'] . (!empty($project['communityName']) ? ", " . $project['communityName'] : "");
            
            $stmt->bind_param(
                "sssssssis",
                $project['projectName'],
                $project['projectCode'],
                $project['agencyName'],
                $project['responsiblePerson'],
                $province,
                $project['projectYear'],
                $project['strategyId'],
                $project['mainProjectId'],
                $targetArea
            );
            
            if (!$stmt->execute()) {
                $errors[] = "โครงการที่ " . ($index + 1) . ": ไม่สามารถบันทึกข้อมูลหลักได้ - " . $stmt->error;
                continue;
            }
            
            $project_id = $conn->insert_id;

            // บันทึกหมู่บ้าน/ชุมชน (ถ้ามี)
            if (!empty($project['villageName'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectVillages (ProjectID, VillageName, Moo, Subdistrict, District, Province, Community) VALUES (?,?,?,?,?,?,?)");
                $moo = $project['villageMoo'] ?? '';
                $subdistrict = $project['villageSubdistrict'] ?? '';
                $district = $project['villageDistrict'] ?? '';
                $community = '';
                
                $stmt->bind_param("issssss",
                    $project_id,
                    $project['villageName'],
                    $moo,
                    $subdistrict,
                    $district,
                    $province,
                    $community
                );
                $stmt->execute();
            }

            // บันทึกโรงเรียน (ถ้ามี)
            if (!empty($project['schoolName'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectSchools (ProjectID, SchoolName) VALUES (?,?)");
                $stmt->bind_param("is", $project_id, $project['schoolName']);
                $stmt->execute();
            }

            // บันทึกวิสาหกิจ (ถ้ามี)
            if (!empty($project['enterpriseName'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectEnterprises (ProjectID, EnterpriseName, EnterpriseType) VALUES (?,?,?)");
                $enterpriseType = !empty($project['enterpriseType']) ? $project['enterpriseType'] : 'วิสาหกิจ';
                $stmt->bind_param("iss", $project_id, $project['enterpriseName'], $enterpriseType);
                $stmt->execute();
            }

            // บันทึกเครือข่าย (ถ้ามี)
            if (!empty($project['networkName'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectNetworks (ProjectID, NetworkName) VALUES (?,?)");
                $stmt->bind_param("is", $project_id, $project['networkName']);
                $stmt->execute();
            }

            // บันทึกผลิตภัณฑ์ (ถ้ามี)
            if (!empty($project['productName'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectProducts (ProjectID, ProductName, ProductType, Description, StandardNumber) VALUES (?,?,?,?,?)");
                $productType = !empty($project['productType']) ? $project['productType'] : '';
                $description = "";
                $standardNumber = !empty($project['productStandard']) ? $project['productStandard'] : '';
                
                $stmt->bind_param("issss",
                    $project_id,
                    $project['productName'],
                    $productType,
                    $description,
                    $standardNumber
                );
                $stmt->execute();
            }

            // บันทึกงบประมาณ (ถ้ามี)
            if (!empty($project['requestedAmount']) || !empty($project['approvedAmount'])) {
                $stmt = $conn->prepare("INSERT INTO BudgetItems (ProjectID, BudgetType, RequestedAmount, ApprovedAmount, Remark) VALUES (?,?,?,?,?)");
                $budgetType = !empty($project['budgetType']) ? $project['budgetType'] : 'งบดำเนินงาน';
                $requestedAmount = !empty($project['requestedAmount']) ? floatval($project['requestedAmount']) : 0;
                $approvedAmount = !empty($project['approvedAmount']) ? floatval($project['approvedAmount']) : 0;
                $remark = "";
                
                $stmt->bind_param("isdds",
                    $project_id,
                    $budgetType,
                    $requestedAmount,
                    $approvedAmount,
                    $remark
                );
                $stmt->execute();
            }

            // บันทึกกลุ่มเป้าหมาย (ถ้ามี)
            if (!empty($project['targetGroups']) && is_array($project['targetGroups'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectTargetCounts (ProjectID, GroupID, TargetCount) VALUES (?,?,?)");
                foreach ($project['targetGroups'] as $groupId => $count) {
                    if ($count > 0) {
                        $stmt->bind_param("iii", $project_id, $groupId, $count);
                        $stmt->execute();
                    }
                }
            }

            // บันทึก SROI (ถ้ามี)
            if (!empty($project['sroiResult'])) {
                $stmt = $conn->prepare("INSERT INTO ProjectSROI (ProjectID, SROIResult, Description) VALUES (?,?,?)");
                $sroiResult = floatval($project['sroiResult']);
                $description = "";
                
                $stmt->bind_param("ids", $project_id, $sroiResult, $description);
                $stmt->execute();
            }

            $saved_count++;
            
        } catch (Exception $e) {
            $errors[] = "โครงการที่ " . ($index + 1) . ": " . $e->getMessage();
        }
    }

    if ($saved_count > 0) {
        // Commit transaction
        $conn->commit();
        
        $response = [
            'success' => true,
            'message' => 'บันทึกข้อมูลสำเร็จ',
            'saved_count' => $saved_count
        ];
        
        if (!empty($errors)) {
            $response['warnings'] = $errors;
        }
        
        echo json_encode($response);
    } else {
        // Rollback transaction
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'ไม่สามารถบันทึกโครงการใดได้',
            'errors' => $errors
        ]);
    }

} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
    ]);
} finally {
    // กลับไปใช้ autocommit
    $conn->autocommit(true);
}
?>
