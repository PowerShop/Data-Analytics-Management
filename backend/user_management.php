<?php
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการ login และสิทธิ์
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบสิทธิ์ admin
if ($_SESSION['admin_role'] !== 'admin') {
    header('Location: ../index.php?error=access_denied');
    exit();
}

// Include database connection with backend paths
$db_files = [
    '../database/db.php',
    '../db.php',
    '../../database/db.php',
    '../../db.php'
];

$db_connected = false;
foreach ($db_files as $db_file) {
    if (file_exists($db_file)) {
        try {
            include $db_file;
            if (isset($conn) && $conn->ping()) {
                $db_connected = true;
                break;
            }
        } catch (Exception $e) {
            $db_connected = false;
        }
    }
}

if (!$db_connected) {
    die('ระบบจัดการผู้ใช้ต้องการการเชื่อมต่อฐานข้อมูล กรุณาตรวจสอบการเชื่อมต่อ');
}

// ฟังก์ชันสำหรับบันทึก activity log
function logUserActivity($user_id, $action, $description = null)
{
    global $conn;

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

// ฟังก์ชันสุ่มรหัสผ่าน
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

// ฟังก์ชันบันทึกรหัสผ่านเป็นไฟล์ text
function savePasswordToFile($username, $password) {
    $passwords_dir = __DIR__ . '/../passwords/';
    
    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($passwords_dir)) {
        mkdir($passwords_dir, 0755, true);
    }
    
    $filename = $passwords_dir . 'user_passwords_' . date('Y-m-d') . '.txt';
    $content = date('Y-m-d H:i:s') . " - User: {$username} - Password: {$password}\n";
    
    // เขียนไฟล์ (append mode)
    file_put_contents($filename, $content, FILE_APPEND | LOCK_EX);
    
    return $filename;
}

$message = '';
$error = '';

// จัดการ Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_user':
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // ตรวจสอบว่า username ซ้ำหรือไม่
            $check_stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ?");
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "ชื่อผู้ใช้ '{$username}' มีอยู่ในระบบแล้ว";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $created_by = $_SESSION['admin_user_id'];

                $stmt = $conn->prepare("INSERT INTO users (Username, Password, FirstName, LastName, Email, Role, IsActive, CreatedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssii", $username, $hashed_password, $first_name, $last_name, $email, $role, $is_active, $created_by);

                if ($stmt->execute()) {
                    $new_user_id = $conn->insert_id;
                    
                    // บันทึกรหัสผ่านเป็นไฟล์ text
                    $password_file = savePasswordToFile($username, $password);
                    
                    logUserActivity($_SESSION['admin_user_id'], 'USER_CREATED', "Created user: {$username} (ID: {$new_user_id})");
                    $message = "เพิ่มผู้ใช้ '{$username}' เรียบร้อยแล้ว (รหัสผ่านถูกบันทึกไว้ในไฟล์)";
                } else {
                    $error = "เกิดข้อผิดพลาดในการเพิ่มผู้ใช้: " . $conn->error;
                }
                $stmt->close();
            }
            $check_stmt->close();
            break;

        case 'edit_user':
            $user_id = intval($_POST['user_id']);
            $username = trim($_POST['username']);
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // ตรวจสอบว่า username ซ้ำหรือไม่ (ยกเว้นตัวเอง)
            $check_stmt = $conn->prepare("SELECT UserID FROM users WHERE Username = ? AND UserID != ?");
            $check_stmt->bind_param("si", $username, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "ชื่อผู้ใช้ '{$username}' มีอยู่ในระบบแล้ว";
            } else {
                $stmt = $conn->prepare("UPDATE users SET Username = ?, FirstName = ?, LastName = ?, Email = ?, Role = ?, IsActive = ? WHERE UserID = ?");
                $stmt->bind_param("sssssii", $username, $first_name, $last_name, $email, $role, $is_active, $user_id);

                if ($stmt->execute()) {
                    logUserActivity($_SESSION['admin_user_id'], 'USER_UPDATED', "Updated user: {$username} (ID: {$user_id})");
                    $message = "อัปเดตข้อมูลผู้ใช้ '{$username}' เรียบร้อยแล้ว";
                } else {
                    $error = "เกิดข้อผิดพลาดในการอัปเดตผู้ใช้: " . $conn->error;
                }
                $stmt->close();
            }
            $check_stmt->close();
            break;

        case 'change_password':
            $user_id = intval($_POST['user_id']);
            $new_password = $_POST['new_password'];

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {
                // ดึงชื่อผู้ใช้สำหรับ log
                $user_stmt = $conn->prepare("SELECT Username FROM users WHERE UserID = ?");
                $user_stmt->bind_param("i", $user_id);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $username = $user_result->fetch_assoc()['Username'] ?? 'Unknown';
                $user_stmt->close();

                // บันทึกรหัสผ่านที่เปลี่ยนเป็นไฟล์ text
                $password_file = savePasswordToFile($username, $new_password);

                logUserActivity($_SESSION['admin_user_id'], 'PASSWORD_CHANGED', "Changed password for user: {$username} (ID: {$user_id})");
                $message = "เปลี่ยนรหัสผ่านสำเร็จ (รหัสผ่านถูกบันทึกไว้ในไฟล์)";
            } else {
                $error = "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน: " . $conn->error;
            }
            $stmt->close();
            break;

        case 'delete_user':
            $user_id = intval($_POST['user_id']);

            // ป้องกันการลบตัวเอง
            if ($user_id == $_SESSION['admin_user_id']) {
                $error = "ไม่สามารถลบบัญชีของตัวเองได้";
                break;
            }

            // ดึงชื่อผู้ใช้ก่อนลบ
            $user_stmt = $conn->prepare("SELECT Username FROM users WHERE UserID = ?");
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $username = $user_result->fetch_assoc()['Username'] ?? 'Unknown';
            $user_stmt->close();

            $stmt = $conn->prepare("DELETE FROM users WHERE UserID = ?");
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                logUserActivity($_SESSION['admin_user_id'], 'USER_DELETED', "Deleted user: {$username} (ID: {$user_id})");
                $message = "ลบผู้ใช้ '{$username}' เรียบร้อยแล้ว";
            } else {
                $error = "เกิดข้อผิดพลาดในการลบผู้ใช้: " . $conn->error;
            }
            $stmt->close();
            break;

        case 'reset_password':
            $user_id = intval($_POST['user_id']);

            $default_password = 'password123';
            $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {
                // ดึงชื่อผู้ใช้สำหรับ log
                $user_stmt = $conn->prepare("SELECT Username FROM users WHERE UserID = ?");
                $user_stmt->bind_param("i", $user_id);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $username = $user_result->fetch_assoc()['Username'] ?? 'Unknown';
                $user_stmt->close();

                // บันทึกรหัสผ่านที่รีเซ็ตเป็นไฟล์ text
                $password_file = savePasswordToFile($username, $default_password);

                logUserActivity($_SESSION['admin_user_id'], 'PASSWORD_RESET', "Reset password for user: {$username} (ID: {$user_id}) to default");
                $message = "รีเซ็ตรหัสผ่านของผู้ใช้ '{$username}' เรียบร้อยแล้ว (รหัสผ่านใหม่: password123 - ถูกบันทึกไว้ในไฟล์)";
            } else {
                $error = "เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน: " . $conn->error;
            }
            $stmt->close();
            break;
    }
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$users_result = $conn->query("SELECT * FROM user_info_view ORDER BY CreatedAt DESC");
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// ตรวจสอบไฟล์รหัสผ่านล่าสุด
$passwords_dir = __DIR__ . '/../passwords/';
$latest_password_file = null;
$password_files_count = 0;

if (file_exists($passwords_dir)) {
    $password_files = glob($passwords_dir . 'user_passwords_*.txt');
    if (!empty($password_files)) {
        $latest_password_file = basename(end($password_files));
        $password_files_count = count($password_files);
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้งาน - ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.9);
        }

        .table th {
            background: rgba(102, 126, 234, 0.1);
            border-top: none;
            font-weight: 600;
            padding: 1rem;
            color: #4a5568;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .badge {
            font-size: 0.75em;
            padding: 0.5rem 1rem;
            border-radius: 25px;
        }

        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 2rem;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .action-btn {
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 2px;
            font-size: 12px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-edit {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #218838 0%, #1ea080 100%);
            color: white;
        }

        .btn-password {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }

        .btn-password:hover {
            background: linear-gradient(135deg, #e0a800 0%, #dc6502 100%);
            color: white;
        }

        .btn-reset {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #5f359a 0%, #d91a72 100%);
            color: white;
        }

        .btn-toggle {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
        }

        .btn-toggle:hover {
            background: linear-gradient(135deg, #5f359a 0%, #d91a72 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #fd5c70 100%);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #fc4f65 100%);
            color: white;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: none;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .navbar-custom .navbar-brand {
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-custom .navbar-brand:hover {
            color: rgba(255, 255, 255, 0.8);
            transform: translateX(-5px);
        }

        .navbar-custom .btn-outline-light {
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .navbar-custom .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #155724;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #721c24;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
        }

        .page-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            opacity: 0.8;
        }

        .btn-secondary {
            background: rgba(108, 117, 125, 0.8);
            border: none;
            border-radius: 12px;
        }

        .btn-secondary:hover {
            background: rgba(108, 117, 125, 1);
            transform: translateY(-1px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
            color: white;
            border-radius: 12px;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e0a800 0%, #dc6502 100%);
            color: white;
            transform: translateY(-1px);
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .container-fluid {
            padding: 0 2rem;
        }

        /* Custom Role Badges */
        .badge-admin {
            background: linear-gradient(135deg, #dc3545 0%, #fd5c70 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3);
        }

        .badge-manager {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(255, 193, 7, 0.3);
        }

        .badge-director {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(23, 162, 184, 0.3);
        }

        .badge-viewer {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
        }

        /* Status Badges */
        .badge.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd5c70 100%) !important;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3);
        }

        /* Hover effects for table rows */
        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.08) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Mini Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="fas fa-users me-2"></i>จัดการผู้ใช้งาน</h2>
                            <p class="mb-0">เพิ่ม แก้ไข ลบ และจัดการผู้ใช้งานในระบบ</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Password Files Info -->
                <?php if ($latest_password_file): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-file-alt me-2"></i>
                        <strong>ไฟล์รหัสผ่าน:</strong> มีไฟล์รหัสผ่านทั้งหมด <?php echo $password_files_count; ?> ไฟล์ 
                        (ล่าสุด: <?php echo $latest_password_file; ?>)
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>รายชื่อผู้ใช้งาน
                            <span class="badge bg-light text-dark ms-2"><?php echo count($users); ?> คน</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ผู้ใช้</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>อีเมล</th>
                                        <th>บทบาท</th>
                                        <th>สถานะ</th>
                                        <th>เข้าสู่ระบบล่าสุด</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <?php echo strtoupper(substr($user['FirstName'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            <?php echo htmlspecialchars($user['Username']); ?></div>
                                                        <small class="text-muted">ID: <?php echo $user['UserID']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-medium">
                                                    <?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($user['Email']): ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($user['Email']); ?>"
                                                        class="text-decoration-none">
                                                        <?php echo htmlspecialchars($user['Email']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $role_colors = [
                                                    'admin' => 'badge-admin',
                                                    'manager' => 'badge-manager',
                                                    'director' => 'badge-director',
                                                    'viewer' => 'badge-viewer'
                                                ];
                                                $role_names = [
                                                    'admin' => 'ผู้ดูแลระบบ',
                                                    'manager' => 'ผู้จัดการ',
                                                    'director' => 'ผู้อำนวยการ',
                                                    'viewer' => 'ผู้ดู'
                                                ];
                                                ?>
                                                <span
                                                    class="badge <?php echo $role_colors[$user['Role']] ?? 'badge-viewer'; ?>">
                                                    <i
                                                        class="fas fa-<?php echo $user['Role'] == 'admin' ? 'crown' : ($user['Role'] == 'manager' ? 'user-tie' : ($user['Role'] == 'director' ? 'user-shield' : 'eye')); ?> me-1"></i>
                                                    <?php echo $role_names[$user['Role']] ?? $user['Role']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($user['IsActive']): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>ใช้งาน
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-ban me-1"></i>ปิดใช้งาน
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user['LastLogin']): ?>
                                                    <small><?php echo date('d/m/Y H:i', strtotime($user['LastLogin'])); ?></small>
                                                <?php else: ?>
                                                    <small class="text-warning">ยังไม่เคยเข้าสู่ระบบ</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="action-btn btn-edit"
                                                        onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="action-btn btn-password"
                                                        onclick="changePassword(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars($user['Username']); ?>')">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                    <button type="button" class="action-btn btn-reset"
                                                        onclick="resetPassword(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars($user['Username']); ?>')"
                                                        title="รีเซ็ตรหัสผ่านเป็น password123">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <?php if ($user['UserID'] != $_SESSION['admin_user_id']): ?>
                                                        <button type="button" class="action-btn btn-toggle"
                                                            onclick="toggleStatus(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars($user['Username']); ?>', <?php echo $user['IsActive']; ?>)">
                                                            <i
                                                                class="fas fa-<?php echo $user['IsActive'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                        <button type="button" class="action-btn btn-delete"
                                                            onclick="deleteUser(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars($user['Username']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form onsubmit="event.preventDefault(); addUser();">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_user">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">ชื่อผู้ใช้ *</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">รหัสผ่าน *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="generated_password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()" title="ซ่อน/แสดงรหัสผ่าน">
                                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateRandomPassword()" title="สุ่มรหัสผ่าน">
                                        <i class="fas fa-dice"></i>
                                    </button>
                                </div>
                                <small class="text-muted">คลิกไอคอนลูกเต๋าเพื่อสุ่มรหัสผ่าน หรือพิมพ์รหัสผ่านเอง</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">ชื่อ *</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">นามสกุล *</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <input type="email" class="form-control" name="email">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">บทบาท *</label>
                                <select class="form-select" name="role" required>
                                    <option value="viewer">ผู้ดู</option>
                                    <option value="director">ผู้อำนวยการ</option>
                                    <option value="manager">ผู้จัดการ</option>
                                    <option value="admin">ผู้ดูแลระบบ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        checked>
                                    <label class="form-check-label" for="is_active">
                                        เปิดใช้งานบัญชี
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">เพิ่มผู้ใช้</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลผู้ใช้
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" id="edit_user_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_username" class="form-label">ชื่อผู้ใช้ *</label>
                                <input type="text" class="form-control" name="username" id="edit_username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">รหัสผ่าน</label>
                                <button type="button" class="btn btn-warning btn-sm w-100"
                                    onclick="changePasswordFromEdit()">
                                    <i class="fas fa-key me-1"></i>เปลี่ยนรหัสผ่าน
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_first_name" class="form-label">ชื่อ *</label>
                                <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_last_name" class="form-label">นามสกุล *</label>
                                <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_email" class="form-label">อีเมล</label>
                            <input type="email" class="form-control" name="email" id="edit_email">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_role" class="form-label">บทบาท *</label>
                                <select class="form-select" name="role" id="edit_role" required>
                                    <option value="viewer">ผู้ดู</option>
                                    <option value="director">ผู้อำนวยการ</option>
                                    <option value="manager">ผู้จัดการ</option>
                                    <option value="admin">ผู้ดูแลระบบ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active"
                                        id="edit_is_active">
                                    <label class="form-check-label" for="edit_is_active">
                                        เปิดใช้งานบัญชี
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่าน
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="changePasswordForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="user_id" id="password_user_id">

                        <div class="mb-3">
                            <label class="form-label">ผู้ใช้</label>
                            <div class="form-control-plaintext fw-bold" id="password_username"></div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">รหัสผ่านใหม่ *</label>
                            <input type="password" class="form-control" name="new_password" id="new_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่ *</label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-warning">เปลี่ยนรหัสผ่าน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function editUser(user) {
            document.getElementById('edit_user_id').value = user.UserID;
            document.getElementById('edit_username').value = user.Username;
            document.getElementById('edit_first_name').value = user.FirstName;
            document.getElementById('edit_last_name').value = user.LastName;
            document.getElementById('edit_email').value = user.Email || '';
            document.getElementById('edit_role').value = user.Role;
            document.getElementById('edit_is_active').checked = user.IsActive == 1;

            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        function changePassword(userId, username) {
            document.getElementById('password_user_id').value = userId;
            document.getElementById('password_username').textContent = username;
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';

            new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
        }

        function changePasswordFromEdit() {
            const userId = document.getElementById('edit_user_id').value;
            const username = document.getElementById('edit_username').value;

            // ปิด edit modal ก่อน
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();

            // เปิด change password modal
            setTimeout(() => {
                changePassword(userId, username);
            }, 500);
        }

        function toggleStatus(userId, username, currentStatus) {
            const action = currentStatus ? 'ปิดใช้งาน' : 'เปิดใช้งาน';

            Swal.fire({
                title: `${action}บัญชีผู้ใช้?`,
                text: `คุณต้องการ${action}บัญชี "${username}" หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: currentStatus ? '#dc3545' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `${action}`,
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="user_id" value="${userId}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteUser(userId, username) {
            Swal.fire({
                title: 'ลบผู้ใช้?',
                text: `คุณต้องการลบผู้ใช้ "${username}" หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" value="${userId}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Validate password confirmation
        document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                Swal.fire('ข้อผิดพลาด', 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน', 'error');
            }
        });

        // ฟังก์ชันสุ่มรหัสผ่าน
        function generateRandomPassword() {
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const numbers = '0123456789';
            const symbols = '!@#$%^&*';
            
            let password = '';
            
            // รับรองว่ามีตัวอักษรทุกประเภทอย่างน้อย 1 ตัว
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            password += symbols.charAt(Math.floor(Math.random() * symbols.length));
            
            // เพิ่มตัวอักษรแบบสุ่มจนครบ 12 ตัว
            const allChars = uppercase + lowercase + numbers + symbols;
            for (let i = password.length; i < 12; i++) {
                password += allChars.charAt(Math.floor(Math.random() * allChars.length));
            }
            
            // สุ่มตำแหน่งตัวอักษร
            password = password.split('').sort(() => Math.random() - 0.5).join('');
            
            document.getElementById('generated_password').value = password;
            
            // แสดงรหัสผ่านที่สุ่มได้พร้อมปุ่มคัดลอก
            Swal.fire({
                title: 'รหัสผ่านที่สุ่มได้',
                html: `
                    <div class="text-center">
                        <div class="alert alert-info mb-3">
                            <strong>รหัสผ่าน:</strong><br>
                            <code class="fs-5">${password}</code>
                        </div>
                        <small class="text-muted">รหัสผ่านนี้จะถูกบันทึกในไฟล์เมื่อเพิ่มผู้ใช้</small>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'คัดลอก',
                cancelButtonText: 'ปิด',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    navigator.clipboard.writeText(password).then(() => {
                        Swal.fire('สำเร็จ', 'คัดลอกรหัสผ่านแล้ว', 'success');
                    }).catch(() => {
                        // Fallback สำหรับเบราว์เซอร์ที่ไม่รองรับ clipboard API
                        const textArea = document.createElement('textarea');
                        textArea.value = password;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        Swal.fire('สำเร็จ', 'คัดลอกรหัสผ่านแล้ว', 'success');
                    });
                }
            });
        }

        // ฟังก์ชันรีเซ็ตรหัสผ่าน
        function resetPassword(userId, username) {
            Swal.fire({
                title: 'รีเซ็ตรหัสผ่าน?',
                text: `คุณต้องการรีเซ็ตรหัสผ่านของ "${username}" เป็น "password123" หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6f42c1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'รีเซ็ต',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="user_id" value="${userId}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // ฟังก์ชันซ่อน/แสดงรหัสผ่าน
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('generated_password');
            const toggleIcon = document.getElementById('password-toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // ฟังก์ชันแสดงความแข็งแกร่งของรหัสผ่าน
        function showPasswordStrength(password) {
            if (!password) {
                // ล้าง indicator ถ้ารหัสผ่านว่าง
                const indicator = document.getElementById('password-strength-indicator');
                if (indicator) {
                    indicator.remove();
                }
                return;
            }
            
            const { strength, feedback } = checkPasswordStrength(password);
            let color, text, icon, bgColor;
            
            if (strength <= 2) {
                color = '#dc3545';
                text = 'อ่อน';
                icon = 'fa-exclamation-triangle';
                bgColor = 'rgba(220, 53, 69, 0.1)';
            } else if (strength <= 3) {
                color = '#ffc107';
                text = 'ปานกลาง';
                icon = 'fa-exclamation-circle';
                bgColor = 'rgba(255, 193, 7, 0.1)';
            } else if (strength <= 4) {
                color = '#28a745';
                text = 'แข็งแรง';
                icon = 'fa-check-circle';
                bgColor = 'rgba(40, 167, 69, 0.1)';
            } else {
                color = '#20c997';
                text = 'แข็งแรงมาก';
                icon = 'fa-shield-alt';
                bgColor = 'rgba(32, 201, 151, 0.1)';
            }
            
            // สร้างหรืออัปเดต indicator
            let indicator = document.getElementById('password-strength-indicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'password-strength-indicator';
                indicator.className = 'mt-2 p-2 rounded';
                indicator.style.border = `1px solid ${color}`;
                indicator.style.backgroundColor = bgColor;
                document.querySelector('#addUserModal .input-group').appendChild(indicator);
            } else {
                indicator.style.border = `1px solid ${color}`;
                indicator.style.backgroundColor = bgColor;
            }
            
            indicator.innerHTML = `
                <small style="color: ${color}; font-weight: bold;">
                    <i class="fas ${icon} me-1"></i>ความแข็งแรง: ${text} (${password.length} ตัวอักษร)
                    ${feedback.length > 0 ? '<br><i class="fas fa-lightbulb me-1"></i>ควรมี: ' + feedback.join(', ') : '<br><i class="fas fa-check me-1"></i>รหัสผ่านนี้ปลอดภัยแล้ว'}
                </small>
            `;
        }

        // เพิ่ม event listener สำหรับตรวจสอบรหัสผ่านแบบ real-time
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('generated_password');
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    showPasswordStrength(this.value);
                });
            }

            // ล้างฟอร์มเมื่อปิด modal
            const addUserModal = document.getElementById('addUserModal');
            if (addUserModal) {
                addUserModal.addEventListener('hidden.bs.modal', function() {
                    // ล้างฟอร์ม
                    const form = addUserModal.querySelector('form');
                    if (form) {
                        form.reset();
                    }
                    
                    // ล้าง indicators
                    const indicators = ['password-strength-indicator', 'username-availability-indicator'];
                    indicators.forEach(id => {
                        const indicator = document.getElementById(id);
                        if (indicator) {
                            indicator.remove();
                        }
                    });
                    
                    // แสดงข้อความแจ้งเตือน
                    Swal.fire({
                        title: 'ยกเลิกการเพิ่มผู้ใช้',
                        text: 'ฟอร์มได้ถูกล้างแล้ว',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }

            // ตรวจสอบชื่อผู้ใช้ซ้ำแบบ real-time
            const usernameInput = document.querySelector('#addUserModal input[name="username"]');
            if (usernameInput) {
                let usernameCheckTimeout;
                usernameInput.addEventListener('input', function() {
                    clearTimeout(usernameCheckTimeout);
                    const username = this.value.trim();
                    
                    if (username.length < 3) {
                        showUsernameIndicator('ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร', '#6c757d', 'fa-info-circle');
                        return;
                    }
                    
                    usernameCheckTimeout = setTimeout(() => {
                        checkUsernameAvailability(username);
                    }, 500);
                });
            }
        });

        // ฟังก์ชันตรวจสอบความพร้อมใช้งานของชื่อผู้ใช้
        function checkUsernameAvailability(username) {
            // ตรวจสอบชื่อผู้ใช้ในฝั่งไคลเอ็นต์ก่อน (ถ้ามีข้อมูลผู้ใช้)
            const existingUsernames = <?php echo json_encode(array_column($users, 'Username')); ?>;
            
            if (existingUsernames.includes(username)) {
                showUsernameIndicator('ชื่อผู้ใช้ "' + username + '" ถูกใช้งานแล้ว', '#dc3545', 'fa-times-circle');
                return;
            }
            
            // ตรวจสอบรูปแบบชื่อผู้ใช้
            if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                showUsernameIndicator('ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษร ตัวเลข และขีดล่างเท่านั้น', '#ffc107', 'fa-exclamation-triangle');
                return;
            }
            
            showUsernameIndicator('ชื่อผู้ใช้ "' + username + '" พร้อมใช้งาน', '#28a745', 'fa-check-circle');
        }

        // ฟังก์ชันแสดง indicator สำหรับชื่อผู้ใช้
        function showUsernameIndicator(message, color, icon) {
            let indicator = document.getElementById('username-availability-indicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'username-availability-indicator';
                indicator.className = 'mt-1';
                
                const usernameInput = document.querySelector('#addUserModal input[name="username"]');
                if (usernameInput) {
                    usernameInput.parentNode.appendChild(indicator);
                }
            }
            
            indicator.innerHTML = `
                <small style="color: ${color};">
                    <i class="fas ${icon} me-1"></i>${message}
                </small>
            `;
        }

        // ฟังก์ชันเพิ่มผู้ใช้
        function addUser() {
            const username = document.querySelector('#addUserModal input[name="username"]').value.trim();
            const password = document.querySelector('#addUserModal input[name="password"]').value.trim();
            const firstName = document.querySelector('#addUserModal input[name="first_name"]').value.trim();
            const lastName = document.querySelector('#addUserModal input[name="last_name"]').value.trim();
            const email = document.querySelector('#addUserModal input[name="email"]').value.trim();
            const role = document.querySelector('#addUserModal select[name="role"]').value;
            const isActive = document.querySelector('#addUserModal input[name="is_active"]').checked;
            const generatedPassword = document.getElementById('generated_password').value.trim();
            
            // ตรวจสอบข้อมูล
            if (!username) {
                Swal.fire('ข้อผิดพลาด', 'กรุณากรอกชื่อผู้ใช้', 'error');
                return;
            }
            
            if (!firstName || !lastName) {
                Swal.fire('ข้อผิดพลาด', 'กรุณากรอกชื่อและนามสกุล', 'error');
                return;
            }
            
            if (!password && !generatedPassword) {
                Swal.fire('ข้อผิดพลาด', 'กรุณากรอกรหัสผ่านหรือสุ่มรหัสผ่าน', 'error');
                return;
            }
            
            if (!role) {
                Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกบทบาท', 'error');
                return;
            }
            
            // ใช้รหัสผ่านที่สุ่มได้ถ้ามี
            const finalPassword = generatedPassword || password;
            
            // ตรวจสอบความแข็งแกร่งของรหัสผ่าน
            const { strength, feedback } = checkPasswordStrength(finalPassword);
            if (strength < 3) {
                Swal.fire({
                    title: 'รหัสผ่านอ่อนเกินไป',
                    html: `
                        <div class="text-center">
                            <div class="alert alert-warning mb-3">
                                <strong>คำแนะนำ:</strong><br>
                                ${feedback.join('<br>')}
                            </div>
                            <p>คุณต้องการใช้รหัสผ่านนี้หรือสุ่มรหัสผ่านใหม่?</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'ใช้รหัสผ่านนี้',
                    denyButtonText: 'สุ่มใหม่',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#ffc107',
                    denyButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ใช้รหัสผ่านนี้ต่อไป
                        submitAddUser(username, firstName, lastName, email, role, isActive, finalPassword);
                    } else if (result.isDenied) {
                        // สุ่มรหัสผ่านใหม่
                        generateRandomPassword();
                    }
                });
                return;
            }
            
            // ถ้ารหัสผ่านแข็งแรงพอ ให้ส่งข้อมูลไปยังเซิร์ฟเวอร์
            submitAddUser(username, firstName, lastName, email, role, isActive, finalPassword);
        }

        // ฟังก์ชันส่งข้อมูลเพิ่มผู้ใช้ไปยังเซิร์ฟเวอร์
        function submitAddUser(username, firstName, lastName, email, role, isActive, finalPassword) {
            // แสดงการยืนยัน
            Swal.fire({
                title: 'ยืนยันการเพิ่มผู้ใช้',
                html: `
                    <div class="text-left">
                        <p><strong>ชื่อผู้ใช้:</strong> ${username}</p>
                        <p><strong>ชื่อ-นามสกุล:</strong> ${firstName} ${lastName}</p>
                        <p><strong>อีเมล:</strong> ${email || '-'}</p>
                        <p><strong>บทบาท:</strong> ${role}</p>
                        <p><strong>รหัสผ่าน:</strong> ${finalPassword}</p>
                        <p><strong>สถานะ:</strong> ${isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}</p>
                        <small class="text-muted">รหัสผ่านนี้จะถูกบันทึกในไฟล์สำหรับการอ้างอิง</small>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'เพิ่มผู้ใช้',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#007bff'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งข้อมูลไปยังเซิร์ฟเวอร์
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="add_user">
                        <input type="hidden" name="username" value="${username}">
                        <input type="hidden" name="password" value="${finalPassword}">
                        <input type="hidden" name="first_name" value="${firstName}">
                        <input type="hidden" name="last_name" value="${lastName}">
                        <input type="hidden" name="email" value="${email}">
                        <input type="hidden" name="role" value="${role}">
                        <input type="hidden" name="is_active" value="${isActive ? 1 : 0}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</html>