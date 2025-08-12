<?php
session_start();

// ตรวจสอบการ login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // ข้อมูล login แบบง่าย (สามารถปรับแต่งได้)
    $admin_users = [
        'admin' => 'admin123',
        'manager' => 'manager123',
        'director' => 'director123',
        'user' => 'user123',
        'kittisak' => '084840'
    ];
    
    if (isset($admin_users[$username]) && $admin_users[$username] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: index.php');
        exit();
    } else {
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}

// ถ้า login แล้วให้ redirect ไป index
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบจัดการโครงการ</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
                        <i class="fas fa-chart-bar me-2"></i>
                        ระบบจัดการโครงการ
                    </h3>
                    <p class="mb-0 mt-2 opacity-75">กรุณาเข้าสู่ระบบเพื่อใช้งาน</p>
                </div>
                
                <div class="login-body">
                    <form method="POST" id="loginForm">
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
                    
                    <!-- <div class="admin-info">
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
                            <span><strong>user</strong></span>
                            <span class="user-badge">user123</span>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (isset($error)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'เข้าสู่ระบบไม่สำเร็จ!',
                text: '<?php echo $error; ?>',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
    <?php endif; ?>
    
    <?php if (isset($_GET['timeout'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'หมดเวลาการใช้งาน!',
                text: 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง',
                icon: 'warning',
                confirmButtonText: 'ตกลง'
            });
        });
    </script>
    <?php endif; ?>
    
</body>
</html>
