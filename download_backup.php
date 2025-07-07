<?php
// download_backup.php - Download backup as ZIP file
date_default_timezone_set('Asia/Bangkok');

// Function to create ZIP file
function createZip($source, $destination, $backupName) {
    if (!extension_loaded('zip')) {
        return ['success' => false, 'message' => 'ZIP extension not loaded'];
    }
    
    if (!file_exists($source)) {
        return ['success' => false, 'message' => 'Source directory not found'];
    }
    
    $zip = new ZipArchive();
    $result = $zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    if ($result !== TRUE) {
        return ['success' => false, 'message' => 'Cannot create ZIP file: ' . $result];
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    $filesAdded = 0;
    foreach ($files as $file) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($source) + 1);
        
        if ($file->isDir()) {
            $zip->addEmptyDir($relativePath);
        } else {
            $zip->addFile($filePath, $relativePath);
            $filesAdded++;
        }
    }
    
    $zip->close();
    
    return [
        'success' => true, 
        'files_added' => $filesAdded,
        'zip_size' => filesize($destination)
    ];
}

// Get backup name from request
$backupName = $_GET['backup'] ?? '';

if (empty($backupName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Backup name not provided']);
    exit;
}

// Security check - prevent directory traversal
if (strpos($backupName, '..') !== false || strpos($backupName, '/') !== false || strpos($backupName, '\\') !== false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid backup name']);
    exit;
}

$backupDir = __DIR__ . '/backup/' . $backupName;

// Check if backup exists
if (!is_dir($backupDir)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Backup not found']);
    exit;
}

// Create temporary ZIP file
$tempDir = sys_get_temp_dir();
$zipFileName = $backupName . '.zip';
$zipFilePath = $tempDir . '/' . $zipFileName;

// Create ZIP
$result = createZip($backupDir, $zipFilePath, $backupName);

if (!$result['success']) {
    http_response_code(500);
    echo json_encode($result);
    exit;
}

// Check if file was created successfully
if (!file_exists($zipFilePath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create ZIP file']);
    exit;
}

// Set headers for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
header('Content-Length: ' . filesize($zipFilePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Output file and clean up
readfile($zipFilePath);
unlink($zipFilePath); // Delete temporary file

exit;
?>
