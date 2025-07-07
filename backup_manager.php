<?php
include 'db.php';

// Set timezone to Thailand (UTC+7)
date_default_timezone_set('Asia/Bangkok');

// Function to get current Thai Buddhist year
function getThaiYear() {
    return date('Y') + 543;
}

// Function to format date for Thai Buddhist year
function formatThaiDate($timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    $year = date('Y', $timestamp) + 543;
    return date('d/m/', $timestamp) . $year . ' ' . date('H:i:s', $timestamp);
}

// Function to get backup folder list
function getBackupFolders() {
    $backupDir = __DIR__ . '/backup';
    $folders = [];
    
    if (is_dir($backupDir)) {
        $files = scandir($backupDir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_dir($backupDir . '/' . $file)) {
                $backupPath = $backupDir . '/' . $file;
                $infoFile = $backupPath . '/backup_info.json';
                
                if (file_exists($infoFile)) {
                    $info = json_decode(file_get_contents($infoFile), true);
                    $folders[] = [
                        'name' => $file,
                        'path' => $backupPath,
                        'size' => $info['size'] ?? formatBytes(getFolderSize($backupPath)),
                        'date' => $info['created_at'] ?? date('Y-m-d H:i:s', filemtime($backupPath)),
                        'files_count' => $info['copied_files'] ?? 'Unknown',
                        'info' => $info
                    ];
                } else {
                    $folders[] = [
                        'name' => $file,
                        'path' => $backupPath,
                        'size' => formatBytes(getFolderSize($backupPath)),
                        'date' => date('Y-m-d H:i:s', filemtime($backupPath)),
                        'files_count' => 'Unknown',
                        'info' => null
                    ];
                }
            }
        }
    }
    
    // Sort by date (newest first)
    usort($folders, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $folders;
}

// Function to calculate folder size
function getFolderSize($dir) {
    $size = 0;
    if (is_dir($dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $file) {
            $size += $file->getSize();
        }
    }
    return $size;
}

// Function to format bytes
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}
?>

<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลสำรอง - Backup Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .backup-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .backup-card:hover {
            transform: translateY(-5px);
        }
        
        .backup-btn {
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .backup-history {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .status-success { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        
        .progress-container {
            display: none;
        }
        
        .file-item {
            padding: 8px 12px;
            margin: 2px 0;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .btn-group .btn {
            margin-right: 5px;
        }
        
        .btn-group .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-primary">💾 จัดการข้อมูลสำรอง</h2>
                    <p class="text-muted">สำรองข้อมูลโปรเจคทั้งหมดเพื่อความปลอดภัย</p>
                </div>

                <!-- Backup Action Card -->
                <div class="backup-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-3">🔄 สร้างข้อมูลสำรองใหม่</h4>
                            <p class="text-muted mb-3">
                                คัดลอกไฟล์ทั้งหมดในโปรเจคไปยังโฟลเดอร์ backup พร้อมเพิ่มวันที่และเวลาในชื่อโฟลเดอร์
                            </p>
                            <div class="progress-container">
                                <div class="progress mb-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="backup-status" class="small text-muted"></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <button id="backup-btn" class="btn btn-primary backup-btn w-100" onclick="startBackup()">
                                <i class="fas fa-download me-2"></i>เริ่มสำรองข้อมูล
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Card -->
                <div id="results-card" class="backup-card mb-4" style="display: none;">
                    <h5 class="fw-bold mb-3">📋 ผลการสำรองข้อมูล</h5>
                    <div id="backup-results"></div>
                </div>

                <!-- Backup History -->
                <div class="backup-card">
                    <h5 class="fw-bold mb-3">📚 ประวัติการสำรองข้อมูล</h5>
                    <div class="backup-history">
                        <?php
                        $backup_dir = __DIR__ . '/backup';
                        $backups = [];
                        
                        if (is_dir($backup_dir)) {
                            $items = scandir($backup_dir);
                            foreach ($items as $item) {
                                if ($item != '.' && $item != '..' && is_dir($backup_dir . '/' . $item)) {
                                    $path = $backup_dir . '/' . $item;
                                    $backups[] = [
                                        'name' => $item,
                                        'path' => $path,
                                        'time' => filemtime($path),
                                        'size' => getDirSize($path)
                                    ];
                                }
                            }
                            
                            // เรียงตามวันที่ใหม่สุด
                            usort($backups, function($a, $b) {
                                return $b['time'] - $a['time'];
                            });
                        }
                        
                        function getDirSize($dir) {
                            $size = 0;
                            if (is_dir($dir)) {
                                $files = new RecursiveIteratorIterator(
                                    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
                                );
                                foreach ($files as $file) {
                                    $size += $file->getSize();
                                }
                            }
                            return $size;
                        }
                        
                        // function formatBytes($bytes, $precision = 2) {
                        //     $units = array('B', 'KB', 'MB', 'GB', 'TB');
                            
                        //     for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                        //         $bytes /= 1024;
                        //     }
                            
                        //     return round($bytes, $precision) . ' ' . $units[$i];
                        // }
                        
                        if (empty($backups)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>ยังไม่มีประวัติการสำรองข้อมูล</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ชื่อโฟลเดอร์</th>
                                            <th>วันที่สร้าง</th>
                                            <th>ขนาด</th>
                                            <th>การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($backups as $backup): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-folder text-warning me-2"></i>
                                                <?= htmlspecialchars($backup['name']) ?>
                                            </td>
                                            <td><?= formatThaiDate($backup['time']) ?></td>
                                            <td><?= formatBytes($backup['size']) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-success" 
                                                            onclick="downloadBackup('<?= htmlspecialchars($backup['name']) ?>')"
                                                            title="ดาวน์โหลดเป็น ZIP">
                                                        <i class="fas fa-download"></i> ดาวน์โหลด
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteBackup('<?= htmlspecialchars($backup['name']) ?>')"
                                                            title="ลบ backup">
                                                        <i class="fas fa-trash"></i> ลบ
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let isBackingUp = false;

        function startBackup() {
            if (isBackingUp) return;
            
            isBackingUp = true;
            const btn = document.getElementById('backup-btn');
            const progressContainer = document.querySelector('.progress-container');
            const progressBar = document.querySelector('.progress-bar');
            const statusElement = document.getElementById('backup-status');
            const resultsCard = document.getElementById('results-card');
            
            // แสดง UI การทำงาน
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังสำรองข้อมูล...';
            progressContainer.style.display = 'block';
            progressBar.style.width = '10%';
            statusElement.textContent = 'เริ่มต้นการสำรองข้อมูล...';
            
            // ส่งข้อมูลไปทำ backup
            fetch('backup_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'create_backup' })
            })
            .then(response => response.json())
            .then(data => {
                progressBar.style.width = '100%';
                
                if (data.success) {
                    statusElement.innerHTML = '<span class="status-success"><i class="fas fa-check-circle"></i> สำรองข้อมูลสำเร็จ!</span>';
                    
                    // แสดงผลลัพธ์
                    document.getElementById('backup-results').innerHTML = `
                        <div class="alert alert-success">
                            <h6 class="fw-bold mb-2">✅ สำรองข้อมูลสำเร็จ!</h6>
                            <p class="mb-2"><strong>โฟลเดอร์:</strong> ${data.backup_name}</p>
                            <p class="mb-2"><strong>ไฟล์ที่คัดลอก:</strong> ${data.copied_files} ไฟล์</p>
                            <p class="mb-2"><strong>ไฟล์ที่ข้าม:</strong> ${data.skipped_files || 0} ไฟล์</p>
                            <p class="mb-2"><strong>ขนาด:</strong> ${data.size}</p>
                            <p class="mb-0"><strong>เวลาที่ใช้:</strong> ${data.execution_time} วินาที</p>
                            ${data.errors_count > 0 ? `<p class="mb-0 text-warning"><strong>ข้อผิดพลาด:</strong> ${data.errors_count} รายการ</p>` : ''}
                        </div>
                    `;
                    resultsCard.style.display = 'block';
                    
                    // รีเฟรชหน้าหลัง 2 วินาที
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    statusElement.innerHTML = '<span class="status-error"><i class="fas fa-exclamation-circle"></i> เกิดข้อผิดพลาด: ' + data.message + '</span>';
                    
                    document.getElementById('backup-results').innerHTML = `
                        <div class="alert alert-danger">
                            <h6 class="fw-bold">❌ การสำรองข้อมูลล้มเหลว</h6>
                            <p class="mb-0">${data.message}</p>
                        </div>
                    `;
                    resultsCard.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusElement.innerHTML = '<span class="status-error"><i class="fas fa-exclamation-circle"></i> เกิดข้อผิดพลาดในการเชื่อมต่อ</span>';
                
                document.getElementById('backup-results').innerHTML = `
                    <div class="alert alert-danger">
                        <h6 class="fw-bold">❌ เกิดข้อผิดพลาด</h6>
                        <p class="mb-0">ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้</p>
                    </div>
                `;
                resultsCard.style.display = 'block';
            })
            .finally(() => {
                // รีเซ็ต UI
                setTimeout(() => {
                    isBackingUp = false;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-download me-2"></i>เริ่มสำรองข้อมูล';
                    progressContainer.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 1000);
            });
        }

        function deleteBackup(folderName) {
            if (!confirm('คุณต้องการลบข้อมูลสำรอง "' + folderName + '" ใช่หรือไม่?')) {
                return;
            }
            
            fetch('backup_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    action: 'delete_backup',
                    backup_name: folderName 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('ลบข้อมูลสำรองสำเร็จ');
                    window.location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            });
        }

        function downloadBackup(folderName) {
            // Show loading state
            const downloadBtn = event.target.closest('button');
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังเตรียม...';
            downloadBtn.disabled = true;
            
            // Create download link
            const downloadUrl = 'download_backup.php?backup=' + encodeURIComponent(folderName);
            
            // Create temporary anchor element for download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = folderName + '.zip';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Reset button state after a short delay
            setTimeout(() => {
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
            }, 2000);
        }
    </script>
</body>
</html>
