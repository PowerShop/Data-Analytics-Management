<?php
// backup_process.php - Backend for backup operations
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Set timezone to Thailand (UTC+7)
date_default_timezone_set('Asia/Bangkok');

// Function to check if a file/folder should be skipped
function shouldSkip($path, $name) {
    $skipFolders = ['backup', '.git', 'node_modules', 'vendor', '.vscode', '.idea'];
    $skipFiles = ['.DS_Store', 'Thumbs.db', '.gitignore', '.env'];
    $skipExtensions = ['.log', '.tmp', '.cache'];
    
    // Skip hidden files and folders (starting with .)
    if (strpos($name, '.') === 0 && !in_array($name, ['.htaccess'])) {
        return true;
    }
    
    // Skip specific folders
    if (in_array($name, $skipFolders)) {
        return true;
    }
    
    // Skip specific files
    if (in_array($name, $skipFiles)) {
        return true;
    }
    
    // Skip files with specific extensions
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (in_array('.' . $extension, $skipExtensions)) {
        return true;
    }
    
    return false;
}

// Function to recursively copy directory
function copyDirectory($source, $destination) {
    $files_copied = 0;
    $errors = [];
    $skipped = 0;
    
    if (!is_dir($source)) {
        return ['success' => false, 'message' => 'Source directory not found'];
    }
    
    if (!mkdir($destination, 0755, true) && !is_dir($destination)) {
        return ['success' => false, 'message' => 'Cannot create destination directory'];
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $sourcePath = $item->getPathname();
        $fileName = $item->getFilename();
        $relativePath = substr($sourcePath, strlen($source) + 1);
        $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;
        
        // Check if this file/folder should be skipped
        if (shouldSkip($sourcePath, $fileName)) {
            $skipped++;
            continue;
        }
        
        if ($item->isDir()) {
            if (!mkdir($targetPath, 0755, true) && !is_dir($targetPath)) {
                $errors[] = "Cannot create directory: " . $targetPath;
            }
        } else {
            if (copy($item->getPathname(), $targetPath)) {
                $files_copied++;
            } else {
                $errors[] = "Cannot copy file: " . $item->getPathname();
            }
        }
    }
    
    return [
        'success' => true,
        'files_copied' => $files_copied,
        'skipped' => $skipped,
        'errors' => $errors
    ];
}

// Function to recursively delete directory
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            rmdir($fileinfo->getRealPath());
        } else {
            unlink($fileinfo->getRealPath());
        }
    }
    
    return rmdir($dir);
}

// Function to get Thai Buddhist year
function getThaiYear() {
    return date('Y') + 543;
}

// Function to format date for backup name
function formatBackupDate() {
    $year = getThaiYear();
    return $year . '-' . date('m-d_H-i-s');
}

// Function to get folder size
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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$action = $input['action'];

try {
    switch ($action) {
        case 'create_backup':
            // Create backup folder name with timestamp
            $backupName = 'backup_' . formatBackupDate();
            $sourceDir = __DIR__;
            $backupDir = __DIR__ . '/backup/' . $backupName;
            
            // Start backup process
            $startTime = microtime(true);
            
            // Create backup directory
            if (!mkdir($backupDir, 0755, true)) {
                throw new Exception('Cannot create backup directory');
            }
            
            // Get list of files/folders to backup (exclude backup folder itself)
            $filesToBackup = [];
            $files = scandir($sourceDir);
            
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !shouldSkip($sourceDir . '/' . $file, $file)) {
                    $filesToBackup[] = $file;
                }
            }
            
            $totalFiles = 0;
            $copiedFiles = 0;
            $skippedFiles = 0;
            $errors = [];
            
            // Copy each file/folder
            foreach ($filesToBackup as $file) {
                $sourcePath = $sourceDir . '/' . $file;
                $targetPath = $backupDir . '/' . $file;
                
                if (is_dir($sourcePath)) {
                    $result = copyDirectory($sourcePath, $targetPath);
                    if ($result['success']) {
                        $copiedFiles += $result['files_copied'];
                        $skippedFiles += isset($result['skipped']) ? $result['skipped'] : 0;
                        $errors = array_merge($errors, $result['errors']);
                    } else {
                        $errors[] = $result['message'];
                    }
                } else {
                    if (copy($sourcePath, $targetPath)) {
                        $copiedFiles++;
                    } else {
                        $errors[] = "Cannot copy file: " . $file;
                    }
                }
                $totalFiles++;
            }
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);
            
            // Create backup info file
            $backupInfo = [
                'backup_name' => $backupName,
                'created_at' => date('Y-m-d H:i:s'),
                'thai_date' => (date('Y') + 543) . '-' . date('m-d H:i:s'),
                'total_files' => $totalFiles,
                'copied_files' => $copiedFiles,
                'skipped_files' => $skippedFiles,
                'size' => formatBytes(getFolderSize($backupDir)),
                'execution_time' => $executionTime . ' seconds',
                'errors' => $errors
            ];
            
            file_put_contents($backupDir . '/backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            echo json_encode([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_name' => $backupName,
                'total_files' => $totalFiles,
                'copied_files' => $copiedFiles,
                'skipped_files' => $skippedFiles,
                'size' => formatBytes(getFolderSize($backupDir)),
                'execution_time' => $executionTime,
                'errors_count' => count($errors),
                'errors' => $errors
            ]);
            break;
            
        case 'delete_backup':
            if (!isset($input['backup_name'])) {
                throw new Exception('Backup name not provided');
            }
            
            $backupName = $input['backup_name'];
            $backupPath = __DIR__ . '/backup/' . $backupName;
            
            // Security check - make sure it's within backup folder
            if (strpos(realpath($backupPath), realpath(__DIR__ . '/backup/')) !== 0) {
                throw new Exception('Invalid backup path');
            }
            
            if (!is_dir($backupPath)) {
                throw new Exception('Backup folder not found');
            }
            
            if (deleteDirectory($backupPath)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Backup deleted successfully',
                    'backup_name' => $backupName
                ]);
            } else {
                throw new Exception('Failed to delete backup folder');
            }
            break;
            
        case 'get_backup_info':
            if (!isset($input['backup_name'])) {
                throw new Exception('Backup name not provided');
            }
            
            $backupName = $input['backup_name'];
            $infoFile = __DIR__ . '/backup/' . $backupName . '/backup_info.json';
            
            if (file_exists($infoFile)) {
                $info = json_decode(file_get_contents($infoFile), true);
                echo json_encode([
                    'success' => true,
                    'info' => $info
                ]);
            } else {
                // Generate basic info if no info file exists
                $backupPath = __DIR__ . '/backup/' . $backupName;
                if (is_dir($backupPath)) {
                    $info = [
                        'backup_name' => $backupName,
                        'created_at' => date('Y-m-d H:i:s', filemtime($backupPath)),
                        'size' => formatBytes(getFolderSize($backupPath)),
                        'total_files' => 'Unknown',
                        'copied_files' => 'Unknown'
                    ];
                    echo json_encode([
                        'success' => true,
                        'info' => $info
                    ]);
                } else {
                    throw new Exception('Backup folder not found');
                }
            }
            break;
            
        case 'list_backups':
            $backupDir = __DIR__ . '/backup';
            $backups = [];
            
            if (is_dir($backupDir)) {
                $files = scandir($backupDir);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && is_dir($backupDir . '/' . $file)) {
                        $backupPath = $backupDir . '/' . $file;
                        $infoFile = $backupPath . '/backup_info.json';
                        
                        if (file_exists($infoFile)) {
                            $info = json_decode(file_get_contents($infoFile), true);
                        } else {
                            $info = [
                                'backup_name' => $file,
                                'created_at' => date('Y-m-d H:i:s', filemtime($backupPath)),
                                'size' => formatBytes(getFolderSize($backupPath)),
                                'total_files' => 'Unknown'
                            ];
                        }
                        
                        $backups[] = $info;
                    }
                }
            }
            
            // Sort by creation date (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            echo json_encode([
                'success' => true,
                'backups' => $backups
            ]);
            break;
            
        default:
            throw new Exception('Unknown action');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
