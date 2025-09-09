/*
 Navicat Premium Dump SQL

 Source Server         : Local MySQL
 Source Server Type    : MySQL
 Source Server Version : 90200 (9.2.0)
 Source Host           : localhost:3306
 Source Schema         : data analytics

 Target Server Type    : MySQL
 Target Server Version : 90200 (9.2.0)
 File Encoding         : 65001

 Date: 09/09/2025 12:30:11
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
) ENGINE = InnoDB AUTO_INCREMENT = 337 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลงบประมาณโครงการ' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 135 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลตัวชี้วัดโครงการ' ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลโครงการหลัก' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 43 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลเพิ่มเติมของตัวชี้วัด' ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB AUTO_INCREMENT = 166 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลตัวชี้วัด' ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB AUTO_INCREMENT = 73 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลสถานประกอบการ' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูล GVH' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลองค์กรปกครองส่วนท้องถิ่น' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลเครือข่าย' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลองค์กรอื่นๆ ที่เข้าร่วมโครงการ' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 164 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลผลิตภัณฑ์' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 274 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลโครงการ' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 843 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลโรงเรียน' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลชุมชน Softpower' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูล SROI' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 217 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลกลุ่มเป้าหมาย' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 56 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูลมหาวิทยาลัยที่เข้าร่วมโครงการ' ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 293 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ตารางเก็บข้อมูล หมู่บ้าน/ชุมชน' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for saved_charts
-- ----------------------------
DROP TABLE IF EXISTS `saved_charts`;
CREATE TABLE `saved_charts`  (
  `ChartID` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ChartType` enum('bar','line','pie','doughnut','radar','polarArea','scatter') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bar',
  `XAxis` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `YAxis` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DataSource` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'projects',
  `CustomSQL` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `Filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `StyleSettings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `CreatedBy` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ChartID`) USING BTREE,
  INDEX `idx_created_by`(`CreatedBy` ASC) USING BTREE,
  INDEX `idx_is_active`(`IsActive` ASC) USING BTREE,
  INDEX `idx_created_at`(`CreatedAt` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for strategies
-- ----------------------------
DROP TABLE IF EXISTS `strategies`;
CREATE TABLE `strategies`  (
  `StrategyID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดียุทธศาสตร์ FK',
  `StrategyName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อยุทธศาสตร์',
  PRIMARY KEY (`StrategyID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'ยุทธศาสตร์' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for targetgroups
-- ----------------------------
DROP TABLE IF EXISTS `targetgroups`;
CREATE TABLE `targetgroups`  (
  `GroupID` int NOT NULL AUTO_INCREMENT COMMENT 'ไอดี FK',
  `GroupName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'ชื่อกลุ่มเป้าหมาย',
  PRIMARY KEY (`GroupID`) USING BTREE,
  UNIQUE INDEX `GroupName`(`GroupName` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'กลุ่มเป้าหมาย' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for temp_indicators_to_delete
-- ----------------------------
DROP TABLE IF EXISTS `temp_indicators_to_delete`;
CREATE TABLE `temp_indicators_to_delete`  (
  `IndicatorID` int NOT NULL,
  `IndicatorName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `reason_for_deletion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`IndicatorID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- View structure for active_users_view
-- ----------------------------
DROP VIEW IF EXISTS `active_users_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `active_users_view` AS select `u`.`UserID` AS `UserID`,`u`.`Username` AS `Username`,`u`.`FirstName` AS `FirstName`,`u`.`LastName` AS `LastName`,`u`.`Role` AS `Role`,`u`.`LastActivity` AS `LastActivity`,`s`.`SessionID` AS `SessionID`,`s`.`IPAddress` AS `IPAddress`,`s`.`LoginTime` AS `LoginTime`,`s`.`ExpiresAt` AS `ExpiresAt`,(case when ((`s`.`ExpiresAt` > now()) and (`s`.`IsActive` = 1)) then 'Online' else 'Offline' end) AS `OnlineStatus` from (`users` `u` left join `user_sessions` `s` on(((`u`.`UserID` = `s`.`UserID`) and (`s`.`IsActive` = 1) and (`s`.`ExpiresAt` > now())))) where (`u`.`Status` = 'active');

-- ----------------------------
-- View structure for login_stats_view
-- ----------------------------
DROP VIEW IF EXISTS `login_stats_view`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `login_stats_view` AS select cast(`login_history`.`LoginTime` as date) AS `LoginDate`,count(0) AS `TotalLogins`,count(distinct `login_history`.`UserID`) AS `UniqueUsers`,sum((case when (`login_history`.`LoginStatus` = 'success') then 1 else 0 end)) AS `SuccessfulLogins`,sum((case when (`login_history`.`LoginStatus` = 'failed') then 1 else 0 end)) AS `FailedLogins` from `login_history` where (`login_history`.`LoginTime` >= (now() - interval 30 day)) group by cast(`login_history`.`LoginTime` as date) order by `LoginDate` desc;

-- ----------------------------
-- Procedure structure for CleanupExpiredSessions
-- ----------------------------
DROP PROCEDURE IF EXISTS `CleanupExpiredSessions`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `CleanupExpiredSessions`()
BEGIN
  UPDATE user_sessions 
  SET IsActive = 0 
  WHERE ExpiresAt < NOW() AND IsActive = 1;
  
  DELETE FROM password_reset_tokens 
  WHERE ExpiresAt < NOW() AND IsUsed = 0;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for IsUserOnline
-- ----------------------------
DROP FUNCTION IF EXISTS `IsUserOnline`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `IsUserOnline`(user_id INT) RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE session_count INT DEFAULT 0;
  
  SELECT COUNT(*) INTO session_count
  FROM user_sessions 
  WHERE UserID = user_id 
    AND IsActive = 1 
    AND ExpiresAt > NOW();
    
  RETURN session_count > 0;
END
;;
delimiter ;

-- ----------------------------
-- Event structure for AutoCleanupSessions
-- ----------------------------
DROP EVENT IF EXISTS `AutoCleanupSessions`;
delimiter ;;
CREATE EVENT `AutoCleanupSessions`
ON SCHEDULE
EVERY '1' HOUR STARTS '2025-07-07 16:03:40'
DO CALL CleanupExpiredSessions()
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
