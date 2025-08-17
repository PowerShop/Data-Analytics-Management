/*
 Navicat Premium Dump SQL

 Source Server         : Localhost
 Source Server Type    : MySQL
 Source Server Version : 90200 (9.2.0)
 Source Host           : localhost:3306
 Source Schema         : data analytics

 Target Server Type    : MySQL
 Target Server Version : 90200 (9.2.0)
 File Encoding         : 65001

 Date: 18/08/2025 01:11:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for budgetitems
-- ----------------------------
DROP TABLE IF EXISTS `budgetitems`;
CREATE TABLE `budgetitems`  (
  `BudgetID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดีงบประมาณ',
  `ProjectID` int NULL DEFAULT NULL COMMENT 'โปรเจคไอดี FK',
  `BudgetType` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ประเภทงบประมาณ',
  `RequestedAmount` decimal(18, 2) NULL DEFAULT NULL COMMENT 'งบประมาณที่เสนอขอ',
  `ApprovedAmount` decimal(18, 2) NULL DEFAULT NULL COMMENT 'งบประมาณที่อนุมัติ',
  `Remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'หมายเหตุ',
  PRIMARY KEY (`BudgetID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `budgetitems_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 603 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลงบประมาณโครงการ' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for indicators
-- ----------------------------
DROP TABLE IF EXISTS `indicators`;
CREATE TABLE `indicators`  (
  `IndicatorID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดีตัวชี้วัด',
  `IndicatorName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อตัวชี้วัด',
  `Unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'หน่วย',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'คำอธิบาย',
  `Year` int NOT NULL COMMENT 'ปีของตัวชี้วัด',
  `StrategyID` int NULL DEFAULT NULL COMMENT 'ไอดียุทธศาสตร์ FK',
  `MainProjectID` int NULL DEFAULT NULL COMMENT 'ไอดีโครงการหลัก FK',
  `IsActive` tinyint(1) NULL DEFAULT 1 COMMENT 'สถานะการใช้งาน (1=ใช้งาน, 0=ไม่ใช้งาน)',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'สร้างเมื่อ',
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'อัพเดทเมื่อ',
  PRIMARY KEY (`IndicatorID`) USING BTREE,
  INDEX `Year`(`Year` ASC) USING BTREE,
  INDEX `IsActive`(`IsActive` ASC) USING BTREE,
  INDEX `StrategyID`(`StrategyID` ASC) USING BTREE,
  INDEX `MainProjectID`(`MainProjectID` ASC) USING BTREE,
  CONSTRAINT `indicators_ibfk_1` FOREIGN KEY (`StrategyID`) REFERENCES `strategies` (`StrategyID`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `indicators_ibfk_2` FOREIGN KEY (`MainProjectID`) REFERENCES `mainprojects` (`MainProjectID`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 165 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลตัวชี้วัดโครงการ' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for mainprojects
-- ----------------------------
DROP TABLE IF EXISTS `mainprojects`;
CREATE TABLE `mainprojects`  (
  `MainProjectID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดีโปรเจคหลัก',
  `MainProjectName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อโปรเจคหลัก',
  `MainProjectCode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'รหัสโปรเจคหลัก',
  `MainProjectDescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'รายละเอียด',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'สร้างเมื่อ',
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'อัพเดทเมื่อ',
  PRIMARY KEY (`MainProjectID`) USING BTREE,
  UNIQUE INDEX `MainProjectCode`(`MainProjectCode` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลโครงการหลัก' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for project_indicator_details
-- ----------------------------
DROP TABLE IF EXISTS `project_indicator_details`;
CREATE TABLE `project_indicator_details`  (
  `DetailID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดีรายละเอียด',
  `ProjectIndicatorID` int NOT NULL COMMENT 'ไอดี project_indicators FK',
  `DetailText` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'รายละเอียดเพิ่มเติม เช่น ตำบล/หมู่บ้าน',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'สร้างเมื่อ',
  PRIMARY KEY (`DetailID`) USING BTREE,
  INDEX `ProjectIndicatorID`(`ProjectIndicatorID` ASC) USING BTREE,
  INDEX `DetailText`(`DetailText`(100) ASC) USING BTREE,
  CONSTRAINT `project_indicator_details_ibfk_1` FOREIGN KEY (`ProjectIndicatorID`) REFERENCES `project_indicators` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 88 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลเพิ่มเติมของตัวชี้วัด' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for project_indicators
-- ----------------------------
DROP TABLE IF EXISTS `project_indicators`;
CREATE TABLE `project_indicators`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโครงการ FK',
  `IndicatorID` int NOT NULL COMMENT 'ไอดีตัวชี้วัด FK',
  `Value` decimal(18, 4) NOT NULL COMMENT 'ค่าตัวชี้วัด',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'สร้างเมื่อ',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  INDEX `IndicatorID`(`IndicatorID` ASC) USING BTREE,
  CONSTRAINT `project_indicators_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `project_indicators_ibfk_2` FOREIGN KEY (`IndicatorID`) REFERENCES `indicators` (`IndicatorID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 519 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลตัวชี้วัด' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectenterprises
-- ----------------------------
DROP TABLE IF EXISTS `projectenterprises`;
CREATE TABLE `projectenterprises`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'โปรเจคไอดี FK',
  `EnterpriseName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อวิสาหกิจ/สถานประกอบการ',
  `EnterpriseType` enum('วิสาหกิจ','ผู้ประกอบการ') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ประเภท',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectenterprises_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 157 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลสถานประกอบการ' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectgvh
-- ----------------------------
DROP TABLE IF EXISTS `projectgvh`;
CREATE TABLE `projectgvh`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `VillageName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ชื่อหมู่บ้าน',
  `CommunityName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ชื่อชุมชน',
  `PerformanceResult` float NULL DEFAULT NULL COMMENT 'ค่า GVH',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectgvh_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูล GVH' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectlocaladmins
-- ----------------------------
DROP TABLE IF EXISTS `projectlocaladmins`;
CREATE TABLE `projectlocaladmins`  (
  `LocalAdminID` int NOT NULL AUTO_INCREMENT,
  `ProjectID` int NOT NULL,
  `AdminName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `AdminType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'อบต.' COMMENT 'อบต., เทศบาล, อปท. อื่นๆ',
  `District` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'อำเภอ',
  `Province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'ราชบุรี',
  `SupportType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'รูปแบบการสนับสนุน',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`LocalAdminID`) USING BTREE,
  INDEX `idx_project_localadmin`(`ProjectID` ASC) USING BTREE,
  INDEX `idx_admin_name`(`AdminName` ASC) USING BTREE,
  CONSTRAINT `projectlocaladmins_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 75 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลองค์กรปกครองส่วนท้องถิ่น' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectnetworks
-- ----------------------------
DROP TABLE IF EXISTS `projectnetworks`;
CREATE TABLE `projectnetworks`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `NetworkName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อเครือข่าย',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectnetworks_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 96 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลเครือข่าย' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectothers
-- ----------------------------
DROP TABLE IF EXISTS `projectothers`;
CREATE TABLE `projectothers`  (
  `OtherID` int NOT NULL AUTO_INCREMENT,
  `ProjectID` int NOT NULL,
  `OrganizationName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `OrganizationType` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ประเภทองค์กร เช่น หน่วยงานรัฐ, เอกชน, NGO',
  `Role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'บทบาทในโครงการ',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'รายละเอียดเพิ่มเติม',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`OtherID`) USING BTREE,
  INDEX `idx_project_others`(`ProjectID` ASC) USING BTREE,
  INDEX `idx_organization_name`(`OrganizationName` ASC) USING BTREE,
  CONSTRAINT `projectothers_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลองค์กรอื่นๆ ที่เข้าร่วมโครงการ' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectproducts
-- ----------------------------
DROP TABLE IF EXISTS `projectproducts`;
CREATE TABLE `projectproducts`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `ProductName` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อผลิตภัณฑ์',
  `ProductType` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ประเภทผลิตภัณฑ์',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'รายละเอียด',
  `StandardNumber` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'เลขมาตรฐานผลิตภัณฑ์',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  INDEX `idx_standard_number`(`StandardNumber` ASC) USING BTREE,
  CONSTRAINT `projectproducts_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 319 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลผลิตภัณฑ์' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projects
-- ----------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects`  (
  `ProjectID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี (Auto)',
  `ProjectCode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'รหัสโครงการ (กรอกเอง)',
  `ProjectName` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อโครงการ',
  `StrategyID` int NULL DEFAULT NULL COMMENT 'ไอดียุทธศาสตร์ FK',
  `AgencyName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'หน่วยงาน',
  `ResponsiblePerson` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'ผู้รับผิดชอบโครงการ',
  `Province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'จังหวัด',
  `TargetArea` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'พื้นที่ดำเนินงาน',
  `ProjectYear` int NULL DEFAULT NULL COMMENT 'ปีของโครงการ',
  `CreateAt` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'สร้างเมื่อ',
  `MainProjectID` int NULL DEFAULT NULL COMMENT 'ไอดีโครงการหลัก FK',
  PRIMARY KEY (`ProjectID`) USING BTREE,
  INDEX `StrategyID`(`StrategyID` ASC) USING BTREE,
  INDEX `MainProjectID`(`MainProjectID` ASC) USING BTREE,
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`StrategyID`) REFERENCES `strategies` (`StrategyID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`MainProjectID`) REFERENCES `mainprojects` (`MainProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`MainProjectID`) REFERENCES `mainprojects` (`MainProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `projects_ibfk_4` FOREIGN KEY (`MainProjectID`) REFERENCES `mainprojects` (`MainProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 282 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลโครงการ' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectschools
-- ----------------------------
DROP TABLE IF EXISTS `projectschools`;
CREATE TABLE `projectschools`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `SchoolName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อโรงเรียน',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectschools_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1549 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลโรงเรียน' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectsoftpower
-- ----------------------------
DROP TABLE IF EXISTS `projectsoftpower`;
CREATE TABLE `projectsoftpower`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `VillageName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ชื่อหมู่บ้าน',
  `Moo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'หมู่',
  `Subdistrict` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ตำบล',
  `District` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'อำเภอ',
  `Province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'จังหวัด',
  `CommunityName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ชื่อชุมชน',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectsoftpower_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลชุมชน Softpower' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectsroi
-- ----------------------------
DROP TABLE IF EXISTS `projectsroi`;
CREATE TABLE `projectsroi`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `SROIResult` float NOT NULL COMMENT 'ค่า SROI',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'รายละเอียด',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectsroi_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 85 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูล SROI' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projecttargetcounts
-- ----------------------------
DROP TABLE IF EXISTS `projecttargetcounts`;
CREATE TABLE `projecttargetcounts`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `GroupID` int NOT NULL COMMENT 'ไอดีกลุ่ม FK',
  `TargetCount` int NULL DEFAULT 0 COMMENT 'จำนวน',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  INDEX `GroupID`(`GroupID` ASC) USING BTREE,
  CONSTRAINT `projecttargetcounts_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `projecttargetcounts_ibfk_2` FOREIGN KEY (`GroupID`) REFERENCES `targetgroups` (`GroupID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 434 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลกลุ่มเป้าหมาย' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectuniversities
-- ----------------------------
DROP TABLE IF EXISTS `projectuniversities`;
CREATE TABLE `projectuniversities`  (
  `UniversityID` int NOT NULL AUTO_INCREMENT,
  `ProjectID` int NOT NULL,
  `UniversityName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `UniversityType` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'เช่น มหาวิทยาลัยรัฐ, เอกชน, ราชภัฏ',
  `Collaboration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'รูปแบบความร่วมมือ',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UniversityID`) USING BTREE,
  INDEX `idx_project_university`(`ProjectID` ASC) USING BTREE,
  INDEX `idx_university_name`(`UniversityName` ASC) USING BTREE,
  CONSTRAINT `projectuniversities_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 94 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลมหาวิทยาลัยที่เข้าร่วมโครงการ' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for projectvillages
-- ----------------------------
DROP TABLE IF EXISTS `projectvillages`;
CREATE TABLE `projectvillages`  (
  `ID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี (Auto)',
  `ProjectID` int NOT NULL COMMENT 'ไอดีโปรเจค FK',
  `VillageName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ชื่อหมู่บ้าน',
  `Moo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'หมู่',
  `Subdistrict` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ตำบล',
  `District` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'อำเภอ',
  `Province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'จังหวัด',
  `Community` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ชุมชน',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ProjectID`(`ProjectID` ASC) USING BTREE,
  CONSTRAINT `projectvillages_ibfk_1` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 591 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูล หมู่บ้าน/ชุมชน' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for saved_queries
-- ----------------------------
DROP TABLE IF EXISTS `saved_queries`;
CREATE TABLE `saved_queries`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sql_query` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for strategies
-- ----------------------------
DROP TABLE IF EXISTS `strategies`;
CREATE TABLE `strategies`  (
  `StrategyID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดียุทธศาสตร์ FK',
  `StrategyName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อยุทธศาสตร์',
  PRIMARY KEY (`StrategyID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ยุทธศาสตร์' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for targetgroups
-- ----------------------------
DROP TABLE IF EXISTS `targetgroups`;
CREATE TABLE `targetgroups`  (
  `GroupID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี FK',
  `GroupName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อกลุ่มเป้าหมาย',
  PRIMARY KEY (`GroupID`) USING BTREE,
  UNIQUE INDEX `GroupName`(`GroupName` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'กลุ่มเป้าหมาย' ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
