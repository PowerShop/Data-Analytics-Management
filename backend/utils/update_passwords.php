<?php
/**
 * ไฟล์สำหรับอัปเดตรหัสผ่านในฐานข้อมูลให้เป็น hash
 * รันไฟล์นี้หนึ่งครั้งเพื่ออัปเดตรหัสผ่านเก่าที่เป็น plain text
 * Backend Version - Updated paths
 */

// Include database connection with backend paths
$db_files = [
    '../../db.php',
    '../../database/db.php',
    '../db.php',
    '../database/db.php'
];

$db_connected = false;
foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        try {
            include $db_file;
            if (isset($conn) && $conn->ping()) {
                $db_connected = true;
                echo "✅ เชื่อมต่อฐานข้อมูลสำเร็จด้วย {$db_file}<br>";
                break;
            }
        } catch (Exception $e) {
            echo "❌ เชื่อมต่อฐานข้อมูลไม่ได้ด้วย {$db_file}: " . $e->getMessage() . "<br>";
        }
    }
}

if (!$db_connected) {
    die('❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้');
}

echo "<h2>🔐 อัปเดตรหัสผ่านในฐานข้อมูล</h2>";

// รหัสผ่านที่ต้องการอัปเดต
$users_to_update = [
    'admin' => 'admin123',
    'manager' => 'manager123', 
    'director' => 'director123',
    'kittisak' => '084840'
];

try {
    // ดึงข้อมูลผู้ใช้ทั้งหมด
    $result = $conn->query("SELECT UserID, Username, Password FROM users");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>Username</th><th>Current Password</th><th>Status</th><th>Action</th></tr>";
        
        while ($user = $result->fetch_assoc()) {
            $username = $user['Username'];
            $current_password = $user['Password'];
            $is_hashed = (strlen($current_password) >= 60 && str_starts_with($current_password, '$2y$'));
            
            echo "<tr>";
            echo "<td><strong>{$username}</strong></td>";
            echo "<td>" . ($is_hashed ? "🔒 Already Hashed" : "⚠️ Plain Text: " . $current_password) . "</td>";
            
            if ($is_hashed) {
                echo "<td style='color: green;'>✅ OK</td>";
                echo "<td>-</td>";
            } else {
                // ตรวจสอบว่ามีรหัสผ่านใหม่สำหรับ user นี้หรือไม่
                if (isset($users_to_update[$username])) {
                    $new_password = $users_to_update[$username];
                    
                    // ตรวจสอบว่ารหัสผ่านปัจจุบันตรงกับที่คาดหวังหรือไม่
                    if ($current_password === $new_password) {
                        // อัปเดตเป็น hash
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_stmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                        $update_stmt->bind_param("si", $hashed_password, $user['UserID']);
                        
                        if ($update_stmt->execute()) {
                            echo "<td style='color: green;'>✅ Updated</td>";
                            echo "<td>🔒 Hashed successfully</td>";
                        } else {
                            echo "<td style='color: red;'>❌ Failed</td>";
                            echo "<td>Error: " . $conn->error . "</td>";
                        }
                        $update_stmt->close();
                    } else {
                        echo "<td style='color: orange;'>⚠️ Password mismatch</td>";
                        echo "<td>Expected: {$new_password}, Got: {$current_password}</td>";
                    }
                } else {
                    echo "<td style='color: orange;'>⚠️ No update rule</td>";
                    echo "<td>Manual update needed</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ ไม่พบผู้ใช้ในฐานข้อมูล";
    }
    
    // ทดสอบการ login หลังอัปเดต
    echo "<h3>🧪 ทดสอบการยืนยันรหัสผ่าน</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Username</th><th>Test Password</th><th>Verification Result</th></tr>";
    
    $test_result = $conn->query("SELECT Username, Password FROM users");
    while ($user = $test_result->fetch_assoc()) {
        $username = $user['Username'];
        $password_hash = $user['Password'];
        
        if (isset($users_to_update[$username])) {
            $test_password = $users_to_update[$username];
            $verify_result = password_verify($test_password, $password_hash);
            
            echo "<tr>";
            echo "<td><strong>{$username}</strong></td>";
            echo "<td>{$test_password}</td>";
            echo "<td style='color: " . ($verify_result ? 'green' : 'red') . ";'>";
            echo $verify_result ? "✅ PASS" : "❌ FAIL";
            echo "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage();
}

echo "<br><hr><br>";
echo "<h3>📋 สรุป</h3>";
echo "<ul>";
echo "<li><strong>admin</strong> → รหัสผ่าน: admin123</li>";
echo "<li><strong>manager</strong> → รหัสผ่าน: manager123</li>";
echo "<li><strong>director</strong> → รหัสผ่าน: director123</li>";
echo "<li><strong>kittisak</strong> → รหัสผ่าน: 084840</li>";
echo "</ul>";

echo "<p><strong>หมายเหตุ:</strong> หลังจากรันสคริปต์นี้แล้ว รหัสผ่านทั้งหมดจะถูกเข้ารหัสด้วย password_hash() และสามารถใช้งาน login ได้ปกติ</p>";

echo "<div style='margin-top: 30px; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196F3;'>";
echo "<h4>🔗 ลิงก์ที่เกี่ยวข้อง</h4>";
echo "<ul>";
echo "<li><a href='../user_system/user_management.php'>จัดการผู้ใช้งาน</a></li>";
echo "<li><a href='../../dashboard.php'>กลับไปแดชบอร์ด</a></li>";
echo "<li><a href='hash_passwords.php'>ดูรหัสผ่านที่เข้ารหัส</a></li>";
echo "</ul>";
echo "</div>";

$conn->close();
?>
