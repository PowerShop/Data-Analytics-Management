-- สร้างตารางผู้ใช้สำหรับระบบ Data Analytics Management
-- รันไฟล์นี้เพื่อสร้างตารางและข้อมูลเริ่มต้น

-- สร้างตาราง users
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL UNIQUE,
  `Password` varchar(255) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Role` enum('admin','manager','director','viewer') NOT NULL DEFAULT 'viewer',
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `LastLogin` timestamp NULL DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  KEY `fk_users_created_by` (`CreatedBy`),
  CONSTRAINT `fk_users_created_by` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- เพิ่มข้อมูลผู้ใช้เริ่มต้น (รหัสผ่านเป็น hash ของรหัสผ่านเดิม)
INSERT INTO `users` (`Username`, `Password`, `FirstName`, `LastName`, `Email`, `Role`, `IsActive`, `CreatedBy`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'หลัก', 'admin@system.com', 'admin', 1, NULL),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้จัดการ', 'ระบบ', 'manager@system.com', 'manager', 1, 1),
('director', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้อำนวยการ', 'ระบบ', 'director@system.com', 'director', 1, 1),
('kittisak', '$2y$10$7wfzWGdYrKlAK5G8JlGTXe5lZQZj6/KK6PJ5HKMPl7XZFbqGV5zE6', 'กิตติศักดิ์', 'ระบบ', 'kittisak@system.com', 'admin', 1, 1);

-- หมายเหตุ: รหัสผ่านที่เข้ารหัสแล้ว
-- admin, manager, director = password123
-- kittisak = 084840

-- สร้างตาราง user_sessions สำหรับจัดการ session (ถ้าต้องการ)
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `SessionID` varchar(255) NOT NULL,
  `UserID` int(11) NOT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `UserAgent` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ExpiresAt` timestamp NOT NULL,
  PRIMARY KEY (`SessionID`),
  KEY `fk_sessions_user` (`UserID`),
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- สร้างตาราง user_activity_log สำหรับบันทึกกิจกรรมของผู้ใช้
CREATE TABLE IF NOT EXISTS `user_activity_log` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `Action` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `UserAgent` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`LogID`),
  KEY `fk_activity_user` (`UserID`),
  KEY `idx_activity_created` (`CreatedAt`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- สร้าง View สำหรับข้อมูลผู้ใช้ (ไม่รวมรหัสผ่าน)
CREATE OR REPLACE VIEW `user_info_view` AS
SELECT 
    u.UserID,
    u.Username,
    u.FirstName,
    u.LastName,
    u.Email,
    u.Role,
    u.IsActive,
    u.CreatedAt,
    u.UpdatedAt,
    u.LastLogin,
    creator.Username as CreatedByUsername,
    CONCAT(creator.FirstName, ' ', creator.LastName) as CreatedByName
FROM users u
LEFT JOIN users creator ON u.CreatedBy = creator.UserID;
