<?php
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection with error handling
$db_files = [
    'database/db.php',
    './db.php',
    '../database/db.php',
    '../db.php'
];

$db_connected = false;
$use_database = false;
foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        try {
            include $db_file;
            // ทดสอบการเชื่อมต่อฐานข้อมูล
            if (isset($conn) && $conn->ping()) {
                $db_connected = true;
                $use_database = true;
                // echo "Database connected using {$db_file}";
                break;
            }
        } catch (Exception $e) {
            // ถ้าเชื่อมต่อไม่ได้ ให้ใช้ระบบ hardcode
            echo "Database connection failed using {$db_file}: " . $e->getMessage();
            $db_connected = false;
        }
    }
}

// ถ้าได้รับ GET error=access_denied ให้แสดงข้อความ error
if (isset($_GET['error']) && $_GET['error'] === 'access_denied') {
    $error = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ กรุณาเข้าสู่ระบบด้วยบัญชีที่มีสิทธิ์';
}

// ฟังก์ชันสำหรับบันทึก activity log
function logUserActivity($user_id, $action, $description = null) {
    global $conn, $use_database;
    
    if (!$use_database || !$conn) return;
    
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $conn->prepare("INSERT INTO user_activity_log (UserID, Action, Description, IPAddress, UserAgent) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $action, $description, $ip_address, $user_agent);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // ไม่ต้อง error ถ้า log ไม่ได้
    }
}

// ตรวจสอบการ login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $login_success = false;
    $user_data = null;
    
    if ($use_database && $conn) {
        // ใช้ฐานข้อมูล
        try {
            $stmt = $conn->prepare("SELECT UserID, Username, Password, FirstName, LastName, Email, Role, IsActive FROM users WHERE Username = ? AND IsActive = 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                // Debug: ตรวจสอบรหัสผ่านในฐานข้อมูล
                // echo "DB Password: " . $user['Password'] . "<br>";
                // echo "Input Password: " . $password . "<br>";
                // echo "Password Verify: " . (password_verify($password, $user['Password']) ? 'true' : 'false') . "<br>";
                
                // ตรวจสอบว่ารหัสผ่านในฐานข้อมูลเป็น hash หรือ plain text
                $password_hash = $user['Password'];
                
                // ถ้ารหัสผ่านยังไม่ได้เข้ารหัส (plain text) ให้เข้ารหัสและอัปเดต
                if (strlen($password_hash) < 60 || !str_starts_with($password_hash, '$2y$')) {
                    // รหัสผ่านเป็น plain text ให้ตรวจสอบแบบ plain text ก่อน
                    if ($password === $password_hash) {
                        // ถ้าตรงกัน ให้เข้ารหัสและอัปเดตในฐานข้อมูล
                        $new_hash = password_hash($password, PASSWORD_DEFAULT);
                        $update_hash_stmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                        $update_hash_stmt->bind_param("si", $new_hash, $user['UserID']);
                        $update_hash_stmt->execute();
                        $update_hash_stmt->close();
                        
                        $login_success = true;
                        $user_data = $user;
                    }
                } else {
                    // รหัสผ่านเป็น hash แล้ว ใช้ password_verify
                    if (password_verify($password, $password_hash)) {
                        $login_success = true;
                        $user_data = $user;
                    }
                }
                
                if ($login_success) {
                    
                    // อัปเดต LastLogin
                    $update_stmt = $conn->prepare("UPDATE users SET LastLogin = NOW() WHERE UserID = ?");
                    $update_stmt->bind_param("i", $user['UserID']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    // บันทึก activity log
                    logUserActivity($user['UserID'], 'LOGIN', 'User logged in successfully');
                } else {
                    logUserActivity(null, 'LOGIN_FAILED', "Failed login attempt for username: {$username}");
                }
            } else {
                logUserActivity(null, 'LOGIN_FAILED', "Login attempt with non-existent username: {$username}");
            }
            $stmt->close();
        } catch (Exception $e) {
            // ถ้าฐานข้อมูลมีปัญหา ให้ fallback ไปใช้ hardcode
            $use_database = false;
        }
    }
    
    // ถ้าฐานข้อมูลไม่ทำงาน หรือไม่พบผู้ใช้ในฐานข้อมูล ให้ใช้ข้อมูล hardcode
    if (!$login_success && !$use_database) {
        $admin_users = [
            'admin' => ['password' => 'admin123', 'role' => 'admin', 'name' => 'ผู้ดูแลระบบ'],
            'manager' => ['password' => 'manager123', 'role' => 'manager', 'name' => 'ผู้จัดการ'],
            'director' => ['password' => 'director123', 'role' => 'director', 'name' => 'ผู้อำนวยการ'],
            'kittisak' => ['password' => '084840', 'role' => 'admin', 'name' => 'กิตติศักดิ์']
        ];
        
        if (isset($admin_users[$username]) && $admin_users[$username]['password'] === $password) {
            $login_success = true;
            $user_data = [
                'UserID' => 0,
                'Username' => $username,
                'FirstName' => $admin_users[$username]['name'],
                'LastName' => 'ระบบ',
                'Email' => $username . '@system.local',
                'Role' => $admin_users[$username]['role']
            ];
        }
    }
    
    if ($login_success && $user_data) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user_data['Username'];
        $_SESSION['admin_user_id'] = $user_data['UserID'];
        $_SESSION['admin_role'] = $user_data['Role'];
        $_SESSION['admin_name'] = $user_data['FirstName'] . ' ' . $user_data['LastName'];
        $_SESSION['admin_email'] = $user_data['Email'];
        $_SESSION['use_database'] = $use_database;
        
        header('Location: index.php');
        exit();
    } else {
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง หรือบัญชีถูกปิดใช้งาน';
    }
}

// ถ้า login แล้วให้ redirect ไป dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans Thai Looped', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .alert-danger {
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }
        
        .admin-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .admin-info h6 {
            color: #495057;
            margin-bottom: 15px;
        }
        
        .admin-info .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .admin-info .user-item:last-child {
            border-bottom: none;
        }
        
        .user-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .min-vh-100 {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="min-vh-100">
        <div class="login-wrapper">
            <div class="login-container">
                <div class="login-header">
                    <h3 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>
                        เข้าสู่ระบบ
                    </h3>
                    <p class="mb-0 mt-2 opacity-75">กรุณาเข้าสู่ระบบเพื่อดูรายงาน</p>
                </div>
                
                <div class="login-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            เข้าสู่ระบบ
                        </button>
                    </form>
                    
                    <div class="admin-info" hidden>
                        <h6><i class="fas fa-info-circle me-2"></i>ข้อมูลสำหรับทดสอบ:</h6>
                        <div class="user-item">
                            <span><strong>admin</strong></span>
                            <span class="user-badge">admin123</span>
                        </div>
                        <div class="user-item">
                            <span><strong>manager</strong></span>
                            <span class="user-badge">manager123</span>
                        </div>
                        <div class="user-item">
                            <span><strong>director</strong></span>
                            <span class="user-badge">director123</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
