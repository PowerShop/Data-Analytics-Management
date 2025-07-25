<?php
// ไฟล์สำหรับ debug เมื่อมีปัญหา
// เรียกใช้ไฟล์นี้เพื่อตรวจสอบปัญหา

echo "<h3>🔍 การตรวจสอบระบบ</h3>";

// ตรวจสอบ PHP version
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// ตรวจสอบ Extensions ที่จำเป็น
$required_extensions = ['mysqli', 'zip', 'xml', 'gd'];
echo "<p><strong>Extensions:</strong></p><ul>";
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ เปิดใช้งาน" : "❌ ไม่ได้เปิดใช้งาน";
    echo "<li>$ext: $status</li>";
}
echo "</ul>";

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
echo "<p><strong>การเชื่อมต่อฐานข้อมูล:</strong></p>";
try {
    include 'db.php';
    echo "<p>✅ เชื่อมต่อฐานข้อมูลสำเร็จ</p>";
    
    // ทดสอบ query
    $result = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '$dbname'");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>📊 จำนวนตารางในฐานข้อมูล: " . ($row['count'] ?? 0) . " ตาราง</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ เชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage() . "</p>";
}

// ตรวจสอบการเขียนไฟล์
echo "<p><strong>การเขียนไฟล์:</strong></p>";
$test_file = 'test_write.txt';
if (file_put_contents($test_file, 'test')) {
    echo "<p>✅ สามารถเขียนไฟล์ได้</p>";
    unlink($test_file); // ลบไฟล์ทดสอบ
} else {
    echo "<p>❌ ไม่สามารถเขียนไฟล์ได้</p>";
}

// ตรวจสอบ Memory Limit
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " วินาที</p>";

// ตรวจสอบโฟลเดอร์สำคัญ
echo "<p><strong>โฟลเดอร์:</strong></p><ul>";
$folders = ['vendor', 'admin', 'backup'];
foreach ($folders as $folder) {
    $status = is_dir($folder) ? "✅ พบ" : "❌ ไม่พบ";
    echo "<li>$folder/: $status</li>";
}
echo "</ul>";

echo "<hr>";
echo "<p><em>หากพบปัญหา กรุณาส่งข้อมูลนี้ให้ผู้ดูแลระบบ</em></p>";
?>
