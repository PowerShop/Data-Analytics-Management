<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไม่พบหน้าที่ต้องการ - 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .error-icon {
            font-size: 5rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .error-title {
            font-size: 3rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 1rem;
        }
        
        .error-message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .btn-home {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            background: linear-gradient(135deg, #5a6bd8, #6a42a0);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .quick-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .quick-links h6 {
            color: #495057;
            margin-bottom: 1rem;
        }
        
        .quick-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .quick-links a:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #5a6bd8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 class="error-title">404</h1>
            
            <p class="error-message">
                ขอโทษค่ะ ไม่พบหน้าที่คุณต้องการ<br>
                หน้าที่คุณพยายามเข้าถึงอาจถูกย้าย ลบ หรือไม่มีอยู่จริง
            </p>
            
            <a href="/home" class="btn-home">
                <i class="fas fa-home me-2"></i>กลับหน้าหลัก
            </a>
            
            <div class="quick-links">
                <h6>ลิงก์ที่อาจจะมีประโยชน์:</h6>
                <a href="/projects"><i class="fas fa-folder me-1"></i>โครงการ</a>
                <a href="/dashboard"><i class="fas fa-chart-bar me-1"></i>แดชบอร์ด</a>
                <a href="/reports"><i class="fas fa-file-alt me-1"></i>รายงาน</a>
                <a href="/login"><i class="fas fa-sign-in-alt me-1"></i>เข้าสู่ระบบ</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
