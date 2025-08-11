<?php
/**
 * Database Connection Class
 * ไฟล์เชื่อมต่อฐานข้อมูลส่วนกลาง
 */

class Database {
    private static $pdo = null;
    private static $config = null;
    
    public static function init($config) {
        self::$config = $config;
    }
    
    public static function getConnection() {
        if (self::$pdo === null) {
            if (self::$config === null) {
                throw new Exception("Database config not initialized. Call Database::init() first.");
            }
            
            try {
                $dsn = "mysql:host=" . self::$config['db_host'] . 
                       ";dbname=" . self::$config['db_database'] . 
                       ";charset=" . self::$config['db_charset'];
                
                self::$pdo = new PDO(
                    $dsn,
                    self::$config['db_user'],
                    self::$config['db_password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
            }
        }
        
        return self::$pdo;
    }
    
    public static function close() {
        self::$pdo = null;
    }
    
    // Helper methods สำหรับการทำงานกับฐานข้อมูล
    public static function query($sql, $params = []) {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchAll($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function fetch($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }
    
    public static function execute($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    public static function lastInsertId() {
        $pdo = self::getConnection();
        return $pdo->lastInsertId();
    }
    
    public static function beginTransaction() {
        $pdo = self::getConnection();
        return $pdo->beginTransaction();
    }
    
    public static function commit() {
        $pdo = self::getConnection();
        return $pdo->commit();
    }
    
    public static function rollback() {
        $pdo = self::getConnection();
        return $pdo->rollback();
    }
}

// สร้างตัวแปร global สำหรับใช้งานง่าย
function getPDO() {
    return Database::getConnection();
}

function dbQuery($sql, $params = []) {
    return Database::query($sql, $params);
}

function dbFetchAll($sql, $params = []) {
    return Database::fetchAll($sql, $params);
}

function dbFetch($sql, $params = []) {
    return Database::fetch($sql, $params);
}

function dbExecute($sql, $params = []) {
    return Database::execute($sql, $params);
}
?>
