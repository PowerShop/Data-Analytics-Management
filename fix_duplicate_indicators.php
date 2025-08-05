<?php 
// เพิ่ม error reporting และ timeout
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 นาที
ini_set('memory_limit', '512M');

include 'db.php';
include 'navbar.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$current_step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$max_steps = 10;

// Function สำหรับรันคำสั่ง SQL และแสดงผล
function executeQuery($conn, $sql, $description = '') {
    try {
        // ตรวจสอบการเชื่อมต่อก่อนรัน query
        if (!$conn || mysqli_connect_errno()) {
            return [
                'success' => false,
                'error' => 'Database connection error: ' . mysqli_connect_error(),
                'description' => $description
            ];
        }
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            return [
                'success' => false,
                'error' => 'SQL Error: ' . mysqli_error($conn) . ' | Query: ' . substr($sql, 0, 200) . '...',
                'description' => $description
            ];
        }
        
        $data = [];
        $affected_rows = mysqli_affected_rows($conn);
        
        // ตรวจสอบว่าเป็น SELECT query หรือไม่
        if (stripos(trim($sql), 'SELECT') === 0) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $data[] = $row;
                }
            }
        }
        
        return [
            'success' => true,
            'data' => $data,
            'affected_rows' => $affected_rows,
            'description' => $description
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Exception: ' . $e->getMessage(),
            'description' => $description
        ];
    }
}

// Step functions
function step1($conn) {
    $sql = "SELECT 
        'ข้อมูลก่อนการแก้ไข - ตัวชี้วัดปี 2566' as description,
        COUNT(*) as total_indicators,
        COUNT(DISTINCT IndicatorName) as unique_names
    FROM indicators 
    WHERE Year = 2566";
    
    return executeQuery($conn, $sql, 'ตรวจสอบข้อมูลก่อนการแก้ไข');
}

function step2($conn) {
    $sql = "SELECT 
        IndicatorName,
        COUNT(*) as duplicate_count,
        GROUP_CONCAT(
            CONCAT('ID:', IndicatorID, ' (FK:', 
            COALESCE((SELECT COUNT(*) FROM project_indicators WHERE IndicatorID = i.IndicatorID), 0), ')')
            ORDER BY IndicatorID SEPARATOR ', '
        ) as indicator_details
    FROM indicators i
    WHERE Year = 2566
    GROUP BY IndicatorName
    HAVING COUNT(*) > 1
    ORDER BY IndicatorName";
    
    return executeQuery($conn, $sql, 'แสดงตัวชี้วัดที่ซ้ำกัน');
}

function step3($conn) {
    // สร้างตารางชั่วคราว
    $sql1 = "DROP TEMPORARY TABLE IF EXISTS temp_indicators_to_delete";
    $sql2 = "CREATE TEMPORARY TABLE temp_indicators_to_delete (
        IndicatorID INT PRIMARY KEY,
        IndicatorName VARCHAR(255),
        reason_for_deletion VARCHAR(100)
    )";
    
    mysqli_query($conn, $sql1);
    $result = mysqli_query($conn, $sql2);
    
    if ($result) {
        return [
            'success' => true,
            'data' => [['message' => 'สร้างตารางชั่วคราวสำเร็จ']],
            'description' => 'สร้างตารางชั่วคราว'
        ];
    } else {
        return [
            'success' => false,
            'error' => mysqli_error($conn),
            'description' => 'สร้างตารางชั่วคราว'
        ];
    }
}

function step4($conn) {
    try {
        // ตรวจสอบว่าตาราง temp มีอยู่หรือไม่
        $check_temp = mysqli_query($conn, "SELECT 1 FROM temp_indicators_to_delete LIMIT 1");
        if (!$check_temp) {
            return [
                'success' => false,
                'error' => 'ตารางชั่วคราวไม่พร้อมใช้งาน กรุณารัน step 3 ก่อน',
                'description' => 'หา IndicatorID ที่จะลบ'
            ];
        }

        $total_deleted = 0;
        
        // หาตัวชี้วัดที่ซ้ำกันทั้งหมด (เฉพาะปี 2566)
        $sql_duplicates = "
        SELECT IndicatorName, GROUP_CONCAT(IndicatorID ORDER BY IndicatorID) as all_ids
        FROM indicators 
        WHERE Year = 2566
        GROUP BY IndicatorName
        HAVING COUNT(*) > 1";
        
        $duplicate_result = mysqli_query($conn, $sql_duplicates);
        
        if ($duplicate_result && mysqli_num_rows($duplicate_result) > 0) {
            while ($dup_row = mysqli_fetch_assoc($duplicate_result)) {
                $indicator_name = $dup_row['IndicatorName'];
                $all_ids = explode(',', $dup_row['all_ids']);
                
                // ตรวจสอบว่า ID ไหนถูกใช้งานใน project_indicators
                $used_ids = [];
                $unused_ids = [];
                
                foreach ($all_ids as $id) {
                    $check_usage = mysqli_query($conn, "SELECT COUNT(*) as count FROM project_indicators WHERE IndicatorID = $id");
                    $usage_row = mysqli_fetch_assoc($check_usage);
                    
                    if ($usage_row['count'] > 0) {
                        $used_ids[] = $id;
                    } else {
                        $unused_ids[] = $id;
                    }
                }
                
                // ตรรกะการลบ:
                if (count($used_ids) > 0 && count($unused_ids) > 0) {
                    // กรณีที่ 1: มี ID ที่ใช้งานและไม่ใช้งาน -> ลบทุกตัวที่ไม่ใช้งาน
                    foreach ($unused_ids as $id_to_delete) {
                        $sql_insert = "INSERT INTO temp_indicators_to_delete (IndicatorID, IndicatorName, reason_for_deletion)
                        VALUES ($id_to_delete, '$indicator_name', 'ซ้ำกัน-ไม่ใช้งาน (เก็บตัวที่ใช้งาน)')";
                        
                        if (mysqli_query($conn, $sql_insert)) {
                            $total_deleted++;
                        }
                    }
                } else if (count($used_ids) > 1) {
                    // กรณีที่ 2: ทุกตัวถูกใช้งาน -> เก็บตัว ID ต่ำสุด, ลบที่เหลือ
                    $keep_id = min($used_ids);
                    foreach ($used_ids as $id) {
                        if ($id != $keep_id) {
                            $sql_insert = "INSERT INTO temp_indicators_to_delete (IndicatorID, IndicatorName, reason_for_deletion)
                            VALUES ($id, '$indicator_name', 'ซ้ำกัน-ใช้งานแล้ว (เก็บ ID ต่ำสุด)')";
                            
                            if (mysqli_query($conn, $sql_insert)) {
                                $total_deleted++;
                            }
                        }
                    }
                } else if (count($unused_ids) > 1) {
                    // กรณีที่ 3: ทุกตัวไม่ถูกใช้งาน -> เก็บตัว ID ต่ำสุด, ลบที่เหลือ
                    $keep_id = min($unused_ids);
                    foreach ($unused_ids as $id) {
                        if ($id != $keep_id) {
                            $sql_insert = "INSERT INTO temp_indicators_to_delete (IndicatorID, IndicatorName, reason_for_deletion)
                            VALUES ($id, '$indicator_name', 'ซ้ำกัน-ไม่ใช้งาน (เก็บ ID ต่ำสุด)')";
                            
                            if (mysqli_query($conn, $sql_insert)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }
        
        // สรุปผลการวิเคราะห์
        $summary_sql = "
        SELECT 
            reason_for_deletion,
            COUNT(*) as count
        FROM temp_indicators_to_delete 
        GROUP BY reason_for_deletion";
        
        $summary_result = mysqli_query($conn, $summary_sql);
        $summary_data = [];
        
        if ($summary_result && mysqli_num_rows($summary_result) > 0) {
            while ($summary_row = mysqli_fetch_assoc($summary_result)) {
                $summary_data[] = $summary_row;
            }
        }
        
        // เพิ่มข้อมูลสรุป
        $summary_data[] = ['reason_for_deletion' => 'รวมทั้งสิ้น', 'count' => $total_deleted];
        $summary_data[] = ['reason_for_deletion' => 'หมายเหตุ', 'count' => 'ลบเฉพาะตัวชี้วัดที่ซ้ำกัน ไม่ลบทุกตัว'];
        
        return [
            'success' => true,
            'data' => $summary_data,
            'description' => 'วิเคราะห์ตัวชี้วัดซ้ำและเลือกลบ'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Exception: ' . $e->getMessage(),
            'description' => 'หา IndicatorID ที่จะลบ'
        ];
    }
}

function step5($conn) {
    $sql = "SELECT 
        'รายการตัวชี้วัดที่จะลบ' as description,
        td.IndicatorID,
        td.IndicatorName,
        td.reason_for_deletion,
        COALESCE((SELECT COUNT(*) FROM project_indicators WHERE IndicatorID = td.IndicatorID), 0) as fk_count
    FROM temp_indicators_to_delete td
    ORDER BY td.IndicatorName, td.IndicatorID";
    
    return executeQuery($conn, $sql, 'รายการที่จะลบ');
}

function step6($conn) {
    $sql = "UPDATE project_indicators pi
    JOIN temp_indicators_to_delete td ON pi.IndicatorID = td.IndicatorID
    JOIN indicators i_keep ON (
        i_keep.Year = 2566 
        AND i_keep.IndicatorName = td.IndicatorName
        AND i_keep.IndicatorID = (
            SELECT MIN(i_min.IndicatorID) 
            FROM indicators i_min 
            WHERE i_min.Year = 2566 
              AND i_min.IndicatorName = td.IndicatorName
              AND i_min.IndicatorID NOT IN (SELECT IndicatorID FROM temp_indicators_to_delete)
        )
    )
    SET pi.IndicatorID = i_keep.IndicatorID
    WHERE td.reason_for_deletion = 'ซ้ำกันและใช้งานแล้ว-เก็บตัว ID ต่ำสุด'";
    
    $result = mysqli_query($conn, $sql);
    $affected = mysqli_affected_rows($conn);
    
    if ($result) {
        return [
            'success' => true,
            'data' => [['updated_records' => $affected, 'message' => 'อัพเดท FK สำเร็จ']],
            'affected_rows' => $affected,
            'description' => 'อัพเดท Foreign Key'
        ];
    } else {
        return [
            'success' => false,
            'error' => mysqli_error($conn),
            'description' => 'อัพเดท Foreign Key'
        ];
    }
}

function step7($conn) {
    $sql = "DELETE i FROM indicators i
    JOIN temp_indicators_to_delete td ON i.IndicatorID = td.IndicatorID";
    
    $result = mysqli_query($conn, $sql);
    $affected = mysqli_affected_rows($conn);
    
    if ($result) {
        return [
            'success' => true,
            'data' => [['deleted_records' => $affected, 'message' => 'ลบตัวชี้วัดซ้ำสำเร็จ']],
            'affected_rows' => $affected,
            'description' => 'ลบตัวชี้วัดซ้ำ'
        ];
    } else {
        return [
            'success' => false,
            'error' => mysqli_error($conn),
            'description' => 'ลบตัวชี้วัดซ้ำ'
        ];
    }
}

function step8($conn) {
    $sql = "SELECT 
        'ข้อมูลหลังการแก้ไข - ตัวชี้วัดปี 2566' as description,
        COUNT(*) as total_indicators,
        COUNT(DISTINCT IndicatorName) as unique_names
    FROM indicators 
    WHERE Year = 2566";
    
    return executeQuery($conn, $sql, 'ตรวจสอบผลลัพธ์หลังแก้ไข');
}

function step9($conn) {
    $sql = "SELECT 
        'ตรวจสอบตัวชี้วัดซ้ำที่เหลือ' as description,
        IndicatorName,
        COUNT(*) as duplicate_count
    FROM indicators 
    WHERE Year = 2566
    GROUP BY IndicatorName
    HAVING COUNT(*) > 1";
    
    return executeQuery($conn, $sql, 'ตรวจสอบตัวชี้วัดซ้ำที่เหลือ');
}

function step10($conn) {
    // ตรวจสอบ FK integrity
    $sql1 = "SELECT 
        'ตรวจสอบ FK integrity' as description,
        COUNT(*) as orphaned_project_indicators
    FROM project_indicators pi
    LEFT JOIN indicators i ON pi.IndicatorID = i.IndicatorID
    WHERE i.IndicatorID IS NULL";
    
    $result1 = executeQuery($conn, $sql1, 'ตรวจสอบ FK integrity');
    
    // สรุปการดำเนินการ
    $sql2 = "SELECT 
        'สรุปการดำเนินการ' as description,
        (SELECT COUNT(*) FROM temp_indicators_to_delete) as total_deleted,
        (SELECT COUNT(*) FROM temp_indicators_to_delete WHERE reason_for_deletion = 'ซ้ำกันและไม่ได้ใช้งาน') as deleted_unused,
        (SELECT COUNT(*) FROM temp_indicators_to_delete WHERE reason_for_deletion = 'ซ้ำกันและใช้งานแล้ว-เก็บตัว ID ต่ำสุด') as deleted_used_duplicates";
    
    $result2 = executeQuery($conn, $sql2, 'สรุปการดำเนินการ');
    
    // ทำความสะอาดตารางชั่วคราว
    mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_indicators_to_delete");
    
    return [
        'success' => true,
        'data' => array_merge($result1['data'], $result2['data']),
        'description' => 'ตรวจสอบผลลัพธ์สุดท้าย'
    ];
}

// รันขั้นตอนตามที่เลือก
$result = null;
$debug_info = "Current Step: $current_step, Memory Usage: " . memory_get_usage(true) . " bytes";

try {
    switch ($current_step) {
        case 1: $result = step1($conn); break;
        case 2: $result = step2($conn); break;
        case 3: $result = step3($conn); break;
        case 4: 
            $debug_info .= " | Starting Step 4...";
            $result = step4($conn); 
            $debug_info .= " | Step 4 completed";
            break;
        case 5: $result = step5($conn); break;
        case 6: $result = step6($conn); break;
        case 7: $result = step7($conn); break;
        case 8: $result = step8($conn); break;
        case 9: $result = step9($conn); break;
        case 10: $result = step10($conn); break;
        default:
            $result = [
                'success' => false,
                'error' => 'Invalid step number: ' . $current_step,
                'description' => 'Invalid Step'
            ];
    }
} catch (Exception $e) {
    $result = [
        'success' => false,
        'error' => 'Exception in main switch: ' . $e->getMessage(),
        'description' => 'System Error'
    ];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขตัวชี้วัดซ้ำ - ปี 2566</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .step-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .step-progress {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .progress-step {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            text-align: center;
            line-height: 40px;
            margin: 0 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .progress-step.active {
            background: #667eea;
            color: white;
        }
        
        .progress-step.completed {
            background: #28a745;
            color: white;
        }
        
        .result-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .btn-step {
            border-radius: 20px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-step:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .badge-large {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">กำลังโหลด...</span>
        </div>
        <p class="mt-2">กำลังประมวลผล...</p>
    </div>
</div>

<div class="container-fluid">
    <div class="main-container">
        <!-- Header -->
        <div class="step-header">
            <h1><i class="fas fa-tools"></i> แก้ไขตัวชี้วัดซ้ำกัน - ปี 2566</h1>
            <p>ระบบแก้ไขข้อมูลตัวชี้วัดที่ซ้ำกันแบบ Step-by-Step</p>
        </div>

        <!-- Progress Bar -->
        <div class="step-progress text-center">
            <h5 class="mb-3">ความคืบหน้า</h5>
            <?php for ($i = 1; $i <= $max_steps; $i++): ?>
                <span class="progress-step <?= $i == $current_step ? 'active' : ($i < $current_step ? 'completed' : '') ?>" 
                      onclick="window.location.href='?step=<?= $i ?>'"><?= $i ?></span>
            <?php endfor; ?>
            <div class="mt-2">
                <small class="text-muted">ขั้นตอนที่ <?= $current_step ?> จาก <?= $max_steps ?></small>
            </div>
        </div>

        <!-- Step Description -->
        <div class="result-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-cog text-primary"></i> 
                    <?php
                    $step_names = [
                        1 => 'ตรวจสอบข้อมูลก่อนการแก้ไข',
                        2 => 'แสดงตัวชี้วัดที่ซ้ำกัน',
                        3 => 'สร้างตารางชั่วคราว',
                        4 => 'หา IndicatorID ที่จะลบ',
                        5 => 'แสดงรายการที่จะลบ',
                        6 => 'อัพเดท Foreign Key',
                        7 => 'ลบตัวชี้วัดซ้ำ',
                        8 => 'ตรวจสอบผลลัพธ์หลังแก้ไข',
                        9 => 'ตรวจสอบตัวชี้วัดซ้ำที่เหลือ',
                        10 => 'ตรวจสอบผลลัพธ์สุดท้าย'
                    ];
                    echo $step_names[$current_step];
                    ?>
                </h4>
                <span class="badge badge-large bg-primary">Step <?= $current_step ?></span>
            </div>

            <!-- Debug Info (แสดงเฉพาะเมื่อมี error) -->
            <?php if (!$result || !$result['success']): ?>
                <div class="alert alert-info">
                    <small><strong>Debug Info:</strong> <?= htmlspecialchars($debug_info) ?></small>
                </div>
            <?php endif; ?>

            <!-- Results -->
            <?php if ($result): ?>
                <?php if ($result['success']): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $result['description'] ?> - สำเร็จ
                        <?php if (isset($result['affected_rows']) && $result['affected_rows'] > 0): ?>
                            <small class="d-block mt-1">จำนวนแถวที่ได้รับผลกระทบ: <?= $result['affected_rows'] ?></small>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($result['data'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <?php foreach (array_keys($result['data'][0]) as $column): ?>
                                            <th><?= htmlspecialchars($column) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['data'] as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $key => $value): ?>
                                                <td>
                                                    <?php if ($key == 'duplicate_count' && $value > 1): ?>
                                                        <span class="badge bg-warning"><?= htmlspecialchars($value) ?></span>
                                                    <?php elseif ($key == 'fk_count' && $value > 0): ?>
                                                        <span class="badge bg-info"><?= htmlspecialchars($value) ?></span>
                                                    <?php elseif ($key == 'reason_for_deletion'): ?>
                                                        <small class="text-muted"><?= htmlspecialchars($value) ?></small>
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($value) ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> ไม่มีข้อมูลที่ต้องแสดง
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> เกิดข้อผิดพลาด: <?= $result['error'] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Warning Messages -->
            <?php if ($current_step == 5): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>คำเตือน:</strong> กรุณาตรวจสอบรายการที่จะลบให้ละเอียดก่อนดำเนินการขั้นตอนต่อไป
                </div>
            <?php elseif ($current_step >= 6 && $current_step <= 7): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> 
                    <strong>ระวัง:</strong> ขั้นตอนนี้จะทำการแก้ไขข้อมูลในฐานข้อมูล กรุณาสำรองข้อมูลก่อนดำเนินการ
                </div>
            <?php endif; ?>
        </div>

        <!-- Navigation -->
        <div class="text-center">
            <?php if ($current_step > 1): ?>
                <a href="?step=<?= $current_step - 1 ?>" class="btn btn-secondary btn-step">
                    <i class="fas fa-arrow-left"></i> ขั้นตอนก่อนหน้า
                </a>
            <?php endif; ?>
            
            <?php if ($current_step < $max_steps): ?>
                <a href="?step=<?= $current_step + 1 ?>" class="btn btn-primary btn-step">
                    ขั้นตอนถัดไป <i class="fas fa-arrow-right"></i>
                </a>
            <?php else: ?>
                <a href="projects_list.php" class="btn btn-success btn-step">
                    <i class="fas fa-check"></i> เสร็จสิ้น - กลับหน้าหลัก
                </a>
            <?php endif; ?>
            
            <a href="projects_list.php" class="btn btn-outline-secondary btn-step">
                <i class="fas fa-times"></i> ยกเลิก
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// แสดง loading เมื่อคลิกลิงก์
document.querySelectorAll('a[href*="step="]').forEach(function(link) {
    link.addEventListener('click', function() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    });
});

// ซ่อน loading เมื่อหน้าโหลดเสร็จ
window.addEventListener('load', function() {
    document.getElementById('loadingOverlay').style.display = 'none';
});

// เตือนก่อนดำเนินการขั้นตอนที่อันตราย
<?php if ($current_step >= 6 && $current_step <= 7): ?>
document.querySelector('a[href="?step=<?= $current_step + 1 ?>"]')?.addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'คำเตือน!',
        text: 'ขั้นตอนนี้จะทำการแก้ไขข้อมูลในฐานข้อมูล คุณแน่ใจหรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ดำเนินการ!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?step=<?= $current_step + 1 ?>';
        }
    });
});
<?php endif; ?>

// แสดงความสำเร็จเมื่อเสร็จสิ้นทุกขั้นตอน
<?php if ($current_step == 10 && $result && $result['success']): ?>
Swal.fire({
    title: 'สำเร็จ!',
    text: 'แก้ไขตัวชี้วัดซ้ำกันเสร็จสิ้น',
    icon: 'success',
    confirmButtonText: 'ตกลง'
});
<?php endif; ?>
</script>
</body>
</html>
