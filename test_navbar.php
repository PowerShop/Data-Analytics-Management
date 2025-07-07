<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Navbar Dropdown</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Test หน้า - ทดสอบ Navbar Dropdown</h1>
        
        <?php include 'navbar.php'; ?>
        
        <div class="alert alert-info mt-4">
            <h4>วิธีทดสอบ:</h4>
            <ol>
                <li>คลิกที่ "เมนูอื่นๆ" ในแถบเมนู</li>
                <li>ดูว่า dropdown เปิดขึ้นมาหรือไม่</li>
                <li>ลองคลิกลิงก์ต่างๆ ใน dropdown</li>
            </ol>
            
            <p>หากยังไม่ทำงาน ให้เปิด Developer Tools (F12) และดู Console สำหรับ errors</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
