<?php
// PDF Viewer Page
include 'navbar.php';

// Get PDF filename from URL parameter
$pdf_file = isset($_GET['file']) ? $_GET['file'] : 'user_manual.pdf';

// Validate filename to prevent directory traversal
$allowed_files = ['user_manual.pdf'];
if (!in_array($pdf_file, $allowed_files)) {
    $pdf_file = 'user_manual.pdf';
}

// Check if file exists
$pdf_path = 'docs/' . $pdf_file;
$file_exists = file_exists($pdf_path);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer - <?php echo htmlspecialchars($pdf_file); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .pdf-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }

        .pdf-viewer {
            width: 100%;
            height: 80vh;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .pdf-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }

        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            flex-direction: column;
        }

        .error-message {
            text-align: center;
            padding: 50px;
            color: #dc3545;
        }

        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="pdf-container">
            <div class="pdf-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">
                            <i class="fas fa-file-pdf me-2"></i>PDF Viewer
                        </h2>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-file me-1"></i><?php echo htmlspecialchars($pdf_file); ?>
                        </p>
                    </div>
                    <div>
                        <a href="<?php echo htmlspecialchars($pdf_path); ?>" target="_blank" class="btn-custom me-2">
                            <i class="fas fa-external-link-alt me-1"></i>เปิดในหน้าต่างใหม่
                        </a>
                        <a href="<?php echo htmlspecialchars($pdf_path); ?>" download class="btn-custom">
                            <i class="fas fa-download me-1"></i>ดาวน์โหลด
                        </a>
                    </div>
                </div>
            </div>

            <div class="file-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong><i class="fas fa-folder me-1"></i>ที่อยู่ไฟล์:</strong>
                        <code><?php echo htmlspecialchars($pdf_path); ?></code>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="fas fa-info-circle me-1"></i>สถานะ:</strong>
                        <?php if ($file_exists): ?>
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>ไฟล์พร้อมใช้งาน (หากไฟล์ไม่แสดงอาจเป็นเพราะ Internet Download Manager ของท่านมีการเรียกดาวน์โหลดไฟล์ก่อน โปรดปิด ส่วนเสริมIDM และรีเฟรชหน้า)
                            </span>
                        <?php else: ?>
                            <span class="text-danger">
                                <i class="fas fa-times-circle me-1"></i>ไม่พบไฟล์
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($file_exists): ?>
                <div id="pdf-loading" class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">กำลังโหลด...</span>
                    </div>
                    <p class="mt-3 text-muted">กำลังโหลดเอกสาร PDF...</p>
                </div>

                <iframe
                    src="<?php echo htmlspecialchars($pdf_path); ?>"
                    class="pdf-viewer"
                    id="pdf-iframe"
                    style="display: none;"
                    onload="hideLoading()">
                    <p>เบราว์เซอร์ของคุณไม่รองรับการแสดง PDF
                       <a href="<?php echo htmlspecialchars($pdf_path); ?>" target="_blank">คลิกที่นี่เพื่อเปิด</a>
                    </p>
                </iframe>
            <?php else: ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>ไม่พบไฟล์ PDF</h4>
                    <p>ไฟล์ <code><?php echo htmlspecialchars($pdf_file); ?></code> ไม่พบในระบบ</p>
                    <a href="user_guide.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>กลับไปหน้าคู่มือ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function hideLoading() {
            document.getElementById('pdf-loading').style.display = 'none';
            document.getElementById('pdf-iframe').style.display = 'block';
        }

        // Fallback for browsers that don't support iframe onload
        setTimeout(function() {
            const loading = document.getElementById('pdf-loading');
            const iframe = document.getElementById('pdf-iframe');
            if (loading && iframe && loading.style.display !== 'none') {
                hideLoading();
            }
        }, 3000);
    </script>
</body>
</html>
