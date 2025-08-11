<?php
    // Core Functions for Data Analytics Management System

    // Function Redirect with optional delay
    function redirect($url, $delay = 0)
    {
        if ($delay > 0) {
            echo "<script type='text/javascript'>
                setTimeout(function(){
                    window.location.href = '$url';
                }, $delay);
            </script>";
        } else {
            header("Location: $url");
            exit();
        }
    }

    // Legacy function for compatibility
    function rdr($url, $time)
    {
        redirect($url, $time);
    }

    // Function for secure password hashing
    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Function for password verification
    function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    // Legacy function for compatibility (deprecated)
    function encode($password)
    {
        return sha1($password);
    }

    // Enhanced PDO Query function with better error handling
    function query($sql, $params = array())
    {
        global $api, $_config;
        try {
            $stmt = $api->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            if (isset($_config['app_debug']) && $_config['app_debug']) {
                throw new Exception("Database Error: " . $e->getMessage());
            }
            return false;
        }
    }

    // Function to get single record
    function queryOne($sql, $params = array())
    {
        $stmt = query($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Function to get all records
    function queryAll($sql, $params = array())
    {
        $stmt = query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
    }

    // Function to count records
    function queryCount($sql, $params = array())
    {
        $stmt = query($sql, $params);
        return $stmt ? $stmt->rowCount() : 0;
    }

    // Enhanced Thai Date function with more options
    function getThaiDate($format = 'full', $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        $year = date("Y", $timestamp) + 543;
        $month = date("n", $timestamp);
        $day = date("j", $timestamp);
        $hour = date("H", $timestamp);
        $minute = date("i", $timestamp);

        $monthsFull = [
            "", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
            "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
        ];
        
        $monthsShort = [
            "", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
            "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
        ];

        switch ($format) {
            case 'full':
                return "$day $monthsFull[$month] $year, $hour:$minute น.";
            case 'short':
                return "$day $monthsShort[$month] $year";
            case 'date_only':
                return "$day $monthsFull[$month] $year";
            case 'time_only':
                return "$hour:$minute น.";
            default:
                return "$day $monthsFull[$month] $year, $hour:$minute น.";
        }
    }

    // Legacy function for compatibility
    function DateThai()
    {
        return getThaiDate('full');
    }

    // Function to sanitize input
    function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Function to generate CSRF token
    function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Function to verify CSRF token
    function verifyCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Function to format numbers in Thai locale
    function formatNumber($number, $decimals = 0)
    {
        return number_format($number, $decimals, '.', ',');
    }

    // Function to format currency in Thai Baht
    function formatCurrency($amount, $showSymbol = true)
    {
        $formatted = formatNumber($amount, 2);
        return $showSymbol ? $formatted . ' บาท' : $formatted;
    }

    // Function to check if user is logged in
    function isLoggedIn()
    {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    // Function to require login
    function requireLogin($redirectUrl = 'login.php')
    {
        if (!isLoggedIn()) {
            redirect($redirectUrl);
        }
    }

    

    // Function to generate breadcrumb
    function generateBreadcrumb($items)
    {
        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        $count = count($items);
        
        foreach ($items as $index => $item) {
            if ($index === $count - 1) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . $item['title'] . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
            }
        }
        
        $html .= '</ol></nav>';
        return $html;
    }

    // Function to include page with error handling
    function includePage($page)
    {
        $pagePath = '_page/' . $page . '.php';
        if (file_exists($pagePath)) {
            include $pagePath;
        } else {
            include '_page/404.php';
        }
    }

    // Function to get file size in human readable format
    function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    // Function to validate email
    function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Function to generate random string
    function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    
/**
 * ฟังก์ชันช่วยเหลือ
 */

// ฟังก์ชันสำหรับแสดงวันที่เป็นภาษาไทย
function formatDateThai($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

// ฟังก์ชันสำหรับแสดงตัวเลขเป็นไทย
function formatNumberThai($number, $decimals = 0) {
    return number_format($number, $decimals);
}

// ฟังก์ชันสำหรับป้องกัน XSS
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// ฟังก์ชันสำหรับสร้าง URL
function url($path = '') {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . '/' . ltrim($path, '/');
}

// ฟังก์ชันสำหรับแสดง flash message
function setFlash($type, $message) {
    if (!isset($_SESSION)) session_start();
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type = null) {
    if (!isset($_SESSION)) session_start();
    if ($type) {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

// ฟังก์ชันสำหรับตรวจสอบสิทธิ์ (placeholder)
function checkAuth() {
    // TODO: implement authentication check
    return true;
}

// ฟังก์ชันสำหรับล็อก
function logActivity($message, $level = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message" . PHP_EOL;
    @error_log($log_message, 3, __DIR__ . '/../logs/system.log');
}
?>

