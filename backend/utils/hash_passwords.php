<?php
/**
 * ไฟล์สำหรับสร้างรหัสผ่านที่เข้ารหัสแล้ว
 * รันไฟล์นี้เพื่อดูรหัสผ่านที่เข้ารหัสสำหรับใส่ในฐานข้อมูล
 * Backend Version
 */

$passwords = [
    'admin123' => password_hash('admin123', PASSWORD_DEFAULT),
    'manager123' => password_hash('manager123', PASSWORD_DEFAULT),
    'director123' => password_hash('director123', PASSWORD_DEFAULT),
    '084840' => password_hash('084840', PASSWORD_DEFAULT)
];

echo "<!DOCTYPE html>
<html lang='th'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Hash Passwords - ระบบจัดการผู้ใช้</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { font-family: 'Courier New', monospace; background: #f8f9fa; }
        .container { margin-top: 2rem; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 5px; overflow-x: auto; }
        .pass { color: #28a745; font-weight: bold; }
        .fail { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
<div class='container'>
    <div class='row'>
        <div class='col-12'>
            <div class='d-flex justify-content-between align-items-center mb-4'>
                <h1><i class='fas fa-key'></i> Hash Passwords Generator</h1>
                <a href='../user_system/user_management.php' class='btn btn-primary'>
                    <i class='fas fa-users'></i> จัดการผู้ใช้
                </a>
            </div>
        </div>
    </div>";

echo "<div class='row'>";

// Hashed Passwords Card
echo "<div class='col-md-6 mb-4'>
    <div class='card'>
        <div class='card-header'>
            <h5><i class='fas fa-lock'></i> Hashed Passwords for Database</h5>
        </div>
        <div class='card-body'>
            <pre>";
foreach ($passwords as $plain => $hashed) {
    echo "Original: <strong>{$plain}</strong>\n";
    echo "Hashed:   {$hashed}\n";
    echo str_repeat("-", 80) . "\n";
}
echo "</pre>
        </div>
    </div>
</div>";

// SQL Commands Card
echo "<div class='col-md-6 mb-4'>
    <div class='card'>
        <div class='card-header'>
            <h5><i class='fas fa-database'></i> SQL Update Commands</h5>
        </div>
        <div class='card-body'>
            <pre>";
echo "-- คำสั่ง SQL สำหรับอัปเดตรหัสผ่านในฐานข้อมูล\n";
echo "UPDATE users SET Password = '{$passwords['admin123']}' WHERE Username = 'admin';\n";
echo "UPDATE users SET Password = '{$passwords['manager123']}' WHERE Username = 'manager';\n";
echo "UPDATE users SET Password = '{$passwords['director123']}' WHERE Username = 'director';\n";
echo "UPDATE users SET Password = '{$passwords['084840']}' WHERE Username = 'kittisak';\n";
echo "</pre>
        </div>
    </div>
</div>";

echo "</div>";

// Password Verification Test Card
echo "<div class='row'>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                <h5><i class='fas fa-check-circle'></i> Password Verification Test</h5>
            </div>
            <div class='card-body'>
                <pre>";

foreach ($passwords as $plain => $hashed) {
    $verify = password_verify($plain, $hashed);
    $status_class = $verify ? 'pass' : 'fail';
    $status_text = $verify ? "✓ PASS" : "✗ FAIL";
    echo "Verify '<strong>{$plain}</strong>': <span class='{$status_class}'>{$status_text}</span>\n";
}

echo "</pre>
            </div>
        </div>
    </div>
</div>";

// Links Card
echo "<div class='row mt-4'>
    <div class='col-12'>
        <div class='card border-info'>
            <div class='card-header bg-info text-white'>
                <h5><i class='fas fa-link'></i> ลิงก์ที่เกี่ยวข้อง</h5>
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-4'>
                        <a href='update_passwords.php' class='btn btn-warning w-100 mb-2'>
                            <i class='fas fa-sync'></i> อัปเดตรหัสผ่านในฐานข้อมูล
                        </a>
                    </div>
                    <div class='col-md-4'>
                        <a href='../user_system/user_management.php' class='btn btn-success w-100 mb-2'>
                            <i class='fas fa-users'></i> จัดการผู้ใช้งาน
                        </a>
                    </div>
                    <div class='col-md-4'>
                        <a href='../../dashboard.php' class='btn btn-primary w-100 mb-2'>
                            <i class='fas fa-home'></i> กลับไปแดชบอร์ด
                        </a>
                    </div>
                </div>
                
                <hr>
                
                <div class='alert alert-info mb-0'>
                    <h6><i class='fas fa-info-circle'></i> คำแนะนำ:</h6>
                    <ul class='mb-0'>
                        <li>รันไฟล์ <code>update_passwords.php</code> เพื่ออัปเดตรหัสผ่านในฐานข้อมูลให้เป็น hash</li>
                        <li>หลังจากอัปเดตแล้ว สามารถใช้ระบบ login ได้ปกติ</li>
                        <li>รหัสผ่านเริ่มต้น: admin/admin123, manager/manager123, director/director123, kittisak/084840</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>";

echo "
</div>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js'></script>
</body>
</html>";
?>
