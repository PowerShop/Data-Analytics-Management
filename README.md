# คู่มือการพัฒนาและใช้งานระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น

## ภาพรวมระบบ (System Overview)

ระบบฐานข้อมูลโครงการพัฒนาท้องถิ่นเป็นแพลตฟอร์มเว็บแอปพลิเคชันที่พัฒนาด้วยภาษา PHP และฐานข้อมูล MySQL สำหรับจัดการข้อมูลโครงการพัฒนาท้องถิ่น วิเคราะห์สถิติ และจัดทำรายงาน โดยมีฟีเจอร์หลักดังนี้:

- **จัดการข้อมูลโครงการ**: เพิ่ม แก้ไข ลบ และค้นหาข้อมูลโครงการ
- **ระบบจัดการผู้ใช้**: จัดการสิทธิ์การเข้าถึงและความปลอดภัย
- **วิเคราะห์ข้อมูล**: แสดงสถิติและแผนภูมิข้อมูล
- **ส่งออกรายงาน**: ส่งออกข้อมูลเป็นไฟล์ Excel
- **ระบบรักษาความปลอดภัย**: การเข้ารหัสรหัสผ่านและการบันทึกกิจกรรม

---

## สถาปัตยกรรมระบบ (System Architecture)

### โครงสร้างโฟลเดอร์ (Directory Structure)

```
Data-Analytics/
├── index.php                    # ไฟล์หลักของระบบ
├── dashboard.php               # หน้าแดชบอร์ดหลัก
├── login.php                   # ระบบเข้าสู่ระบบ
├── navbar.php                  # แถบนำทางหลัก
├── db.php                      # การเชื่อมต่อฐานข้อมูล
├── data analytics.sql          # สคริปต์ฐานข้อมูล
│
├── backend/                    # โฟลเดอร์ส่วนหลังบ้าน
│   ├── user_management.php     # จัดการผู้ใช้
│   ├── add_project.php         # เพิ่มโครงการ
│   ├── edit_project.php        # แก้ไขโครงการ
│   ├── delete_project.php      # ลบโครงการ
│   ├── auth.php                # การยืนยันตัวตน
│   └── api/                    # API endpoints
│       ├── get_filtered_data.php
│       ├── get_indicators.php
│       └── save_project_indicators.php
│
├── api/                        # API สำหรับ frontend
│   ├── chart_data_api.php
│   ├── get_filtered_data.php
│   └── stats_api.php
│
├── charts/                     # ระบบแผนภูมิ
│   ├── chart_builder.php
│   ├── chart_detail.php
│   └── index.php
│
├── portal/                     # พอร์ทัลผู้ใช้
│   └── index.php
│
├── routes/                     # ระบบจัดการเส้นทาง
│   └── index.php
│
├── vendor/                     # Dependencies (Composer)
├── backup_manager/             # ระบบสำรองข้อมูล
└── docs/                       # เอกสารประกอบ
```

---

## เทคโนโลยีที่ใช้ (Technology Stack)

### Backend Technologies

- **PHP 7.4+**: ภาษาหลักสำหรับพัฒนาเซิร์ฟเวอร์
- **MySQL 5.7+**: ฐานข้อมูลหลัก
- **Composer**: ระบบจัดการแพ็คเกจ PHP

### Frontend Technologies

- **HTML5/CSS3**: โครงสร้างและการจัดรูปแบบ
- **JavaScript (ES6+)**: การทำงานฝั่งไคลเอ็นต์
- **Bootstrap 5.3.0**: Framework CSS สำหรับ UI
- **DataTables**: ตารางข้อมูลแบบไดนามิก
- **SweetAlert2**: การแจ้งเตือนแบบสวยงาม

### Libraries และ Frameworks

- **Font Awesome 6.4.0**: ไอคอน
- **Google Fonts (Noto Sans Thai Looped)**: ฟอนต์ภาษาไทย
- **PHPOffice/PHPSpreadsheet**: ส่งออกไฟล์ Excel
- **jQuery**: JavaScript library

---

## การติดตั้งและตั้งค่า (Installation & Setup)

### ข้อกำหนดระบบ (System Requirements)

#### Server Requirements

- **Web Server**: Apache 2.4+ หรือ Nginx 1.18+
- **PHP Version**: 7.4 หรือสูงกว่า
- **Database**: MySQL 5.7+ หรือ MariaDB 10.3+
- **Memory**: อย่างน้อย 256MB RAM
- **Disk Space**: อย่างน้อย 500MB พื้นที่ว่าง

#### PHP Extensions ที่จำเป็น

```php
- mysqli หรือ pdo_mysql
- mbstring
- json
- fileinfo
- zip
- gd (สำหรับการจัดการรูปภาพ)
```

### ขั้นตอนการติดตั้ง

#### 1. Clone โปรเจคจาก GitHub

```bash
# Clone โปรเจคจาก GitHub repository
git clone -b new-structure https://github.com/PowerShop/Data-Analytics-Management.git

# เข้าไปยังโฟลเดอร์โปรเจค
cd Data-Analytics-Management
```

#### 2. ดาวน์โหลดและติดตั้ง XAMPP/Laragon

```bash
# สำหรับ Windows - ดาวน์โหลด Laragon จาก
# https://laragon.org/download/

# สำหรับ Linux/Mac - ใช้ XAMPP หรือติดตั้งแยก
sudo apt-get install apache2 php mysql-server
```

#### 3. ติดตั้ง Composer และ Dependencies

```bash
# ติดตั้ง Composer (ถ้ายังไม่มี)
# สำหรับ Windows: ดาวน์โหลดจาก https://getcomposer.org/
# สำหรับ Linux/Mac:
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# ติดตั้ง PHP dependencies
composer install
```

#### 4. คัดลอกไฟล์โปรเจค

```bash
# คัดลอกไฟล์ทั้งหมดไปยังโฟลเดอร์ htdocs หรือ www ของเซิร์ฟเวอร์
# สำหรับ Laragon/XAMPP บน Windows:
cp -r . /path/to/htdocs/Data-Analytics/

# หรือสำหรับ Linux/Mac:
sudo cp -r . /var/www/html/Data-Analytics/
```

#### 5. ตั้งค่าฐานข้อมูล

##### สร้างฐานข้อมูล

```sql
-- เข้าสู่ MySQL console
mysql -u root -p

-- สร้างฐานข้อมูล
CREATE DATABASE data_analytics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- สร้างผู้ใช้ (ถ้าต้องการ)
CREATE USER 'analytics_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON data_analytics.* TO 'analytics_user'@'localhost';
FLUSH PRIVILEGES;
```

##### นำเข้าข้อมูลเริ่มต้น

```bash
# นำเข้าไฟล์ SQL สำหรับโครงสร้างฐานข้อมูล
mysql -u root -p data_analytics < structure_database_analytics.sql
```

#### 6. ตั้งค่าไฟล์การเชื่อมต่อฐานข้อมูล

แก้ไขไฟล์ `db.php` หรือ `database/db.php`:

```php
<?php
$servername = "localhost";
$username = "root"; // หรือ analytics_user
$password = ""; // หรือรหัสผ่านที่ตั้งไว้
$dbname = "data_analytics";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตั้งค่า charset เป็น UTF-8
$conn->set_charset("utf8mb4");
?>
```

#### 7. ตั้งค่าการอนุญาตไฟล์

```bash
# ตั้งสิทธิ์การเขียนสำหรับโฟลเดอร์ที่ต้องการ
chmod 755 backup_manager/
chmod 755 passwords/
chmod 755 exports/
```

#### 8. เปิดใช้งานระบบ

```bash
# เริ่ม Apache และ MySQL ใน Laragon/XAMPP
# หรือสำหรับ Linux
sudo systemctl start apache2
sudo systemctl start mysql

# เข้าถึงระบบผ่านเบราว์เซอร์
# http://localhost/Data-Analytics/
```

---

## การใช้งานระบบ (System Usage)

### การเข้าสู่ระบบ (Login)

1. เปิดเบราว์เซอร์และเข้าหน้า `login.php`
2. กรอกชื่อผู้ใช้และรหัสผ่าน
3. คลิกปุ่ม "เข้าสู่ระบบ"

#### บัญชีเริ่มต้น

- **ผู้ดูแลระบบ**: username: `admin`, password: `admin123`
- **ผู้จัดการ**: username: `manager`, password: `manager123`
- **ผู้ใช้ทั่วไป**: username: `user`, password: `user123`

### เมนูหลักของระบบ

#### 1. แดชบอร์ด (Dashboard)

- ภาพรวมสถิติโครงการ
- ลิงก์เข้าสู่เมนูต่างๆ
- การแจ้งเตือนล่าสุด

#### 2. รายการโครงการ (Projects)

- ดูข้อมูลโครงการทั้งหมดในรูปแบบตาราง
- ค้นหาและกรองข้อมูล
- เพิ่ม/แก้ไข/ลบโครงการ
- ส่งออกรายงาน Excel

#### 3. แผนภูมิและสถิติ (Charts & Statistics)

- แสดงข้อมูลในรูปแบบกราฟและแผนภูมิ
- วิเคราะห์ข้อมูลตามพื้นที่และปี
- สถิติการดำเนินโครงการ

#### 4. จัดการผู้ใช้ (User Management)

- เพิ่ม/แก้ไข/ลบผู้ใช้
- จัดการสิทธิ์การเข้าถึง
- เปลี่ยนรหัสผ่าน
- ดูประวัติการใช้งาน

#### 5. เครื่องมือและช่วยเหลือ (Tools & Help)

- คู่มือการใช้งาน
- การตั้งค่าระบบ
- การสำรองข้อมูล

---

## โครงสร้างฐานข้อมูล (Database Schema)

### ตารางหลัก (Main Tables)

#### 1. ตาราง `users`

```sql
CREATE TABLE users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    FirstName VARCHAR(100),
    LastName VARCHAR(100),
    Email VARCHAR(255),
    Role ENUM('admin', 'manager', 'director', 'viewer') DEFAULT 'viewer',
    IsActive BOOLEAN DEFAULT TRUE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    LastLogin TIMESTAMP NULL
);
```

#### 2. ตาราง `projects`

```sql
CREATE TABLE projects (
    ProjectID INT PRIMARY KEY AUTO_INCREMENT,
    ProjectName VARCHAR(255) NOT NULL,
    Description TEXT,
    Budget DECIMAL(15,2),
    StartDate DATE,
    EndDate DATE,
    Status ENUM('planning', 'ongoing', 'completed', 'cancelled'),
    District VARCHAR(100),
    Subdistrict VARCHAR(100),
    Strategy VARCHAR(255),
    Year YEAR,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3. ตาราง `indicators`

```sql
CREATE TABLE indicators (
    IndicatorID INT PRIMARY KEY AUTO_INCREMENT,
    ProjectID INT,
    IndicatorName VARCHAR(255),
    TargetValue DECIMAL(10,2),
    ActualValue DECIMAL(10,2),
    Unit VARCHAR(50),
    Year YEAR,
    FOREIGN KEY (ProjectID) REFERENCES projects(ProjectID)
);
```

#### 4. ตาราง `user_activity_log`

```sql
CREATE TABLE user_activity_log (
    LogID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    Action VARCHAR(100),
    Description TEXT,
    IPAddress VARCHAR(45),
    UserAgent TEXT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES users(UserID)
);
```

---

## API Endpoints

### RESTful APIs

#### 1. โครงการ (Projects)

```
GET    /api/get_filtered_data.php     - รับข้อมูลโครงการแบบกรอง
POST   /api/save_project.php          - บันทึกโครงการใหม่
PUT    /api/update_project.php        - อัปเดตข้อมูลโครงการ
DELETE /api/delete_project.php        - ลบโครงการ
```

#### 2. ผู้ใช้ (Users)

```
GET    /backend/api/get_users.php      - รับข้อมูลผู้ใช้ทั้งหมด
POST   /backend/api/add_user.php       - เพิ่มผู้ใช้ใหม่
PUT    /backend/api/update_user.php    - อัปเดตข้อมูลผู้ใช้
DELETE /backend/api/delete_user.php    - ลบผู้ใช้
```

#### 3. แผนภูมิ (Charts)

```
GET    /api/chart_data_api.php         - รับข้อมูลสำหรับแผนภูมิ
GET    /api/stats_api.php              - รับข้อมูลสถิติ
```

#### 4. ส่งออกข้อมูล (Export)

```
GET    /export_projects_table_detailed_xlsx.php - ส่งออก Excel
GET    /api/get_indicators.php         - รับข้อมูลตัวชี้วัด
```

---

## การพัฒนาและปรับปรุงระบบ (Development Guidelines)

### การเขียนโค้ด (Coding Standards)

#### 1. PHP Standards

```php
<?php
// ใช้ long opening tags เสมอ
// ใช้ meaningful variable names
// เพิ่ม comment ที่จำเป็น
// ใช้ prepared statements สำหรับ SQL

class ProjectManager {
    private $conn;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    
    /**
     * ดึงข้อมูลโครงการทั้งหมด
     * @return array รายการโครงการ
     */
    public function getAllProjects() {
        $stmt = $this->conn->prepare("SELECT * FROM projects ORDER BY CreatedAt DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
```

#### 2. JavaScript Standards

```javascript
// ใช้ ES6+ syntax
// ใช้ meaningful function names
// เพิ่ม error handling
// ใช้ async/await สำหรับ API calls

class DataTableManager {
    constructor(tableId) {
        this.tableId = tableId;
        this.table = null;
    }
    
    async initialize() {
        try {
            const data = await this.fetchData();
            this.renderTable(data);
        } catch (error) {
            console.error('Error initializing table:', error);
            this.showError('ไม่สามารถโหลดข้อมูลได้');
        }
    }
    
    async fetchData() {
        const response = await fetch('/api/get_filtered_data.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    }
}
```

#### 3. CSS Standards

```css
/* ใช้ CSS custom properties สำหรับสีหลัก */
/* จัดระเบียบโค้ดตาม component */
/* ใช้ responsive design */

:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --font-family: 'Noto Sans Thai Looped', sans-serif;
}

.dashboard-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
}

@media (max-width: 768px) {
    .dashboard-card {
        padding: 15px;
    }
}
```

### การจัดการข้อผิดพลาด (Error Handling)

#### 1. PHP Error Handling

```php
<?php
// ตั้งค่า error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // ปิดการแสดง error ใน production
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');

// ใช้ try-catch สำหรับ database operations
try {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE ProjectID = ?");
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("ไม่พบข้อมูลโครงการ");
    }
    
    $project = $result->fetch_assoc();
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
}
?>
```

#### 2. JavaScript Error Handling

```javascript
// ใช้ try-catch สำหรับ async operations
async function loadProjectData() {
    try {
        const response = await fetch('/api/get_projects.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'เกิดข้อผิดพลาด');
        }
        
        renderProjects(data.projects);
        
    } catch (error) {
        console.error('Error loading projects:', error);
        showErrorMessage('ไม่สามารถโหลดข้อมูลโครงการได้ กรุณาลองใหม่อีกครั้ง');
    }
}

// ใช้ SweetAlert2 สำหรับแสดงข้อความผิดพลาด
function showErrorMessage(message) {
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: message,
        confirmButtonText: 'ตกลง'
    });
}
```

### การรักษาความปลอดภัย (Security Best Practices)

#### 1. การป้องกัน SQL Injection

```php
<?php
// ใช้ prepared statements เสมอ
$stmt = $conn->prepare("SELECT * FROM users WHERE Username = ? AND Password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
?>
```

#### 2. การเข้ารหัสรหัสผ่าน

```php
<?php
// ใช้ password_hash() และ password_verify()
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if (password_verify($inputPassword, $hashedPassword)) {
    // รหัสผ่านถูกต้อง
}
?>
```

#### 3. การป้องกัน XSS

```php
<?php
// ใช้ htmlspecialchars() สำหรับแสดงข้อมูล
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
?>
```

#### 4. การจัดการ Session

```php
<?php
// ตั้งค่า session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // สำหรับ HTTPS
ini_set('session.use_only_cookies', 1);

// ตรวจสอบ session timeout
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > 1800)) { // 30 นาที
    session_destroy();
    header('Location: login.php');
    exit();
}
$_SESSION['last_activity'] = time();
?>
```

---

## การปรับใช้ในสภาพแวดล้อมจริง (Production Deployment)

### การตั้งค่า Production

#### 1. การตั้งค่า PHP

```ini
; php.ini สำหรับ production
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

; Security settings
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off

; Performance settings
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 7963
```

#### 2. การตั้งค่า Apache/Nginx

```apache
# .htaccess สำหรับ Apache
<Files *.php>
    php_value upload_max_filesize 50M
    php_value post_max_size 50M
    php_value memory_limit 256M
    php_value max_execution_time 300
</Files>

# ป้องกันการเข้าถึงไฟล์สำคัญ
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

#### 3. การตั้งค่า SSL/TLS

```bash
# ใช้ Let's Encrypt สำหรับ SSL certificate
sudo certbot --apache -d yourdomain.com
```

#### 4. การสำรองข้อมูลอัตโนมัติ

```bash
# สร้าง cron job สำหรับสำรองข้อมูล
# ทุกวันเวลา 02:00
0 2 * * * /path/to/backup_script.sh

# เนื้อหา backup_script.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -ppassword database_name > /path/to/backups/backup_$DATE.sql
```

---

## การแก้ไขปัญหา (Troubleshooting)

### ปัญหาที่พบบ่อย (Common Issues)

#### 1. ไม่สามารถเชื่อมต่อฐานข้อมูล

**สาเหตุ**: การตั้งค่าฐานข้อมูลไม่ถูกต้อง

**วิธีแก้ไข**:
- ตรวจสอบไฟล์ `db.php`
- ตรวจสอบข้อมูลการเชื่อมต่อ MySQL
- ตรวจสอบสิทธิ์ของผู้ใช้ฐานข้อมูล

#### 2. ไม่สามารถอัปโหลดไฟล์

**สาเหตุ**: การตั้งค่าการอนุญาตไฟล์ไม่ถูกต้อง

**วิธีแก้ไข**:

```bash
chmod 755 uploads/
chmod 644 uploads/*
```

#### 3. หน้าเว็บแสดงข้อความผิดปกติ

**สาเหตุ**: การเข้ารหัสไฟล์ไม่ถูกต้อง

**วิธีแก้ไข**:
- บันทึกไฟล์ทั้งหมดเป็น UTF-8
- ตรวจสอบ BOM (Byte Order Mark)
- ตั้งค่า charset ในฐานข้อมูลเป็น utf8mb4

#### 4. JavaScript ไม่ทำงาน

**สาเหตุ**: ข้อผิดพลาดในการโหลดไฟล์

**วิธีแก้ไข**:
- ตรวจสอบ path ของไฟล์ JavaScript
- ตรวจสอบ Console ใน Developer Tools
- ตรวจสอบการโหลด jQuery ก่อน Bootstrap

### Logs และ Debugging

#### 1. PHP Error Logs

```php
<?php
// เพิ่ม logging สำหรับ debugging
error_log("Debug message: " . print_r($variable, true));
?>
```

#### 2. JavaScript Console

```javascript
// ใช้ console.log สำหรับ debugging
console.log('Debug data:', data);
console.error('Error occurred:', error);
```

#### 3. Database Query Logs

```sql
-- เปิดใช้งาน general query log
SET GLOBAL general_log = 'ON';
SET GLOBAL general_log_file = '/var/log/mysql/mysql.log';
```

---

## ติดต่อและสนับสนุน (Contact & Support)

### ช่องทางการติดต่อ

- **Email**: k.sakmeang@gmail.com
- **Tel**: 092-945-8830

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## บันทึกการเปลี่ยนแปลง (Changelog)

### Version 2.0.0 (Latest)

- ✅ เพิ่มระบบจัดการผู้ใช้ขั้นสูง
- ✅ ปรับปรุง UI/UX ด้วย Bootstrap 5
- ✅ เพิ่มระบบแผนภูมิและสถิติ
- ✅ ปรับปรุงระบบรักษาความปลอดภัย
- ✅ เพิ่ม API endpoints

### Version 1.5.0

- ✅ เพิ่มระบบส่งออกรายงาน Excel
- ✅ ปรับปรุงระบบกรองข้อมูล
- ✅ เพิ่มระบบสำรองข้อมูล

### Version 1.0.0

- ✅ ระบบจัดการโครงการพื้นฐาน
- ✅ ระบบเข้าสู่ระบบ
- ✅ แดชบอร์ดหลัก

---

# Data Dictionary - Data Analytics Management System

## ภาพรวมระบบฐานข้อมูล

ระบบฐานข้อมูลสำหรับจัดการข้อมูลวิเคราะห์โครงการพัฒนาชนบท ประกอบด้วยตารางหลัก 25 ตาราง และตารางระบบผู้ใช้ 3 ตาราง รวมทั้งสิ้น 28 ตาราง พร้อมด้วย Views และ Stored Procedures ที่เกี่ยวข้อง

**ฐานข้อมูลหลัก:** `data_analytics`  
**ฐานข้อมูลผู้ใช้:** `users_system`  
**Character Set:** UTF8MB4  
**Collation:** utf8mb4_unicode_ci

---

## รายการตารางทั้งหมด

### ตารางหลัก (Main Tables)

1. [projects](#projects) - ข้อมูลโครงการหลัก
2. [indicators](#indicators) - ตัวชี้วัดโครงการ
3. [budgetitems](#budgetitems) - รายการงบประมาณ
4. [projectvillages](#projectvillages) - หมู่บ้านที่เกี่ยวข้องกับโครงการ
5. [districts](#districts) - ข้อมูลอำเภอ
6. [subdistricts](#subdistricts) - ข้อมูลตำบล
7. [provinces](#provinces) - ข้อมูลจังหวัด
8. [main_projects](#main_projects) - โครงการหลัก
9. [project_indicators](#project_indicators) - ความสัมพันธ์ระหว่างโครงการและตัวชี้วัด
10. [indicator_values](#indicator_values) - ค่าตัวชี้วัด
11. [budget_allocations](#budget_allocations) - การจัดสรรงบประมาณ
12. [project_budget_items](#project_budget_items) - รายการงบประมาณของโครงการ
13. [villages](#villages) - ข้อมูลหมู่บ้าน
14. [users](#users) - ข้อมูลผู้ใช้ระบบ
15. [user_sessions](#user_sessions) - Session ของผู้ใช้
16. [user_activity_log](#user_activity_log) - บันทึกกิจกรรมผู้ใช้

### ตารางรอง (Supporting Tables)

1. [indicator_types](#indicator_types) - ประเภทตัวชี้วัด
2. [indicator_categories](#indicator_categories) - หมวดหมู่ตัวชี้วัด
3. [budget_categories](#budget_categories) - หมวดหมู่งบประมาณ
4. [budget_subcategories](#budget_subcategories) - หมวดหมู่ย่อยงบประมาณ
5. [project_types](#project_types) - ประเภทโครงการ
6. [project_statuses](#project_statuses) - สถานะโครงการ
7. [funding_sources](#funding_sources) - แหล่งเงินทุน
8. [implementing_agencies](#implementing_agencies) - หน่วยงานผู้ดำเนินการ
9. [target_groups](#target_groups) - กลุ่มเป้าหมาย
10. [geographic_coverages](#geographic_coverages) - ขอบเขตทางภูมิศาสตร์
11. [time_periods](#time_periods) - ช่วงเวลา
12. [measurement_units](#measurement_units) - หน่วยวัด

---

## รายละเอียดตาราง

### projects

**คำอธิบาย:** ตารางเก็บข้อมูลโครงการหลักทั้งหมดในระบบ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับหลายตารางผ่าน ProjectID

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ProjectID | int | 11 | NO | AUTO_INCREMENT | รหัสโครงการ (Primary Key) |
| ProjectCode | varchar | 50 | YES | NULL | รหัสโครงการ |
| ProjectName | varchar | 500 | NO |  | ชื่อโครงการ |
| ProjectNameEng | varchar | 500 | YES | NULL | ชื่อโครงการภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียดโครงการ |
| DescriptionEng | text |  | YES | NULL | รายละเอียดโครงการภาษาอังกฤษ |
| StartDate | date |  | YES | NULL | วันที่เริ่มโครงการ |
| EndDate | date |  | YES | NULL | วันที่สิ้นสุดโครงการ |
| Budget | decimal | 15,2 | YES | NULL | งบประมาณรวม |
| StatusID | int | 11 | YES | NULL | สถานะโครงการ (Foreign Key) |
| ProjectTypeID | int | 11 | YES | NULL | ประเภทโครงการ (Foreign Key) |
| MainProjectID | int | 11 | YES | NULL | โครงการหลัก (Foreign Key) |
| FundingSourceID | int | 11 | YES | NULL | แหล่งเงินทุน (Foreign Key) |
| ImplementingAgencyID | int | 11 | YES | NULL | หน่วยงานผู้ดำเนินการ (Foreign Key) |
| TargetGroupID | int | 11 | YES | NULL | กลุ่มเป้าหมาย (Foreign Key) |
| GeographicCoverageID | int | 11 | YES | NULL | ขอบเขตทางภูมิศาสตร์ (Foreign Key) |
| ProvinceID | int | 11 | YES | NULL | จังหวัด (Foreign Key) |
| DistrictID | int | 11 | YES | NULL | อำเภอ (Foreign Key) |
| SubdistrictID | int | 11 | YES | NULL | ตำบล (Foreign Key) |
| VillageID | int | 11 | YES | NULL | หมู่บ้าน (Foreign Key) |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: ProjectID
- INDEX: idx_projects_code (ProjectCode)
- INDEX: idx_projects_name (ProjectName)
- INDEX: idx_projects_status (StatusID)
- INDEX: idx_projects_type (ProjectTypeID)
- INDEX: idx_projects_main (MainProjectID)
- INDEX: idx_projects_funding (FundingSourceID)
- INDEX: idx_projects_agency (ImplementingAgencyID)
- INDEX: idx_projects_target (TargetGroupID)
- INDEX: idx_projects_geographic (GeographicCoverageID)
- INDEX: idx_projects_province (ProvinceID)
- INDEX: idx_projects_district (DistrictID)
- INDEX: idx_projects_subdistrict (SubdistrictID)
- INDEX: idx_projects_village (VillageID)
- INDEX: idx_projects_created (CreatedAt)
- INDEX: idx_projects_updated (UpdatedAt)

**ความสัมพันธ์:**

- project_statuses (StatusID)
- project_types (ProjectTypeID)
- main_projects (MainProjectID)
- funding_sources (FundingSourceID)
- implementing_agencies (ImplementingAgencyID)
- target_groups (TargetGroupID)
- geographic_coverages (GeographicCoverageID)
- provinces (ProvinceID)
- districts (DistrictID)
- subdistricts (SubdistrictID)
- villages (VillageID)
- users (CreatedBy, UpdatedBy)

---

### indicators

**คำอธิบาย:** ตารางเก็บข้อมูลตัวชี้วัดทั้งหมด  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ project_indicators และ indicator_values

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| IndicatorID | int | 11 | NO | AUTO_INCREMENT | รหัสตัวชี้วัด (Primary Key) |
| IndicatorCode | varchar | 50 | YES | NULL | รหัสตัวชี้วัด |
| IndicatorName | varchar | 300 | NO |  | ชื่อตัวชี้วัด |
| IndicatorNameEng | varchar | 300 | YES | NULL | ชื่อตัวชี้วัดภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียดตัวชี้วัด |
| DescriptionEng | text |  | YES | NULL | รายละเอียดตัวชี้วัดภาษาอังกฤษ |
| IndicatorTypeID | int | 11 | YES | NULL | ประเภทตัวชี้วัด (Foreign Key) |
| IndicatorCategoryID | int | 11 | YES | NULL | หมวดหมู่ตัวชี้วัด (Foreign Key) |
| MeasurementUnitID | int | 11 | YES | NULL | หน่วยวัด (Foreign Key) |
| BaselineValue | decimal | 15,4 | YES | NULL | ค่าฐาน |
| TargetValue | decimal | 15,4 | YES | NULL | ค่าประสงค์ |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: IndicatorID
- INDEX: idx_indicators_code (IndicatorCode)
- INDEX: idx_indicators_name (IndicatorName)
- INDEX: idx_indicators_type (IndicatorTypeID)
- INDEX: idx_indicators_category (IndicatorCategoryID)
- INDEX: idx_indicators_unit (MeasurementUnitID)
- INDEX: idx_indicators_active (IsActive)
- INDEX: idx_indicators_created (CreatedAt)
- INDEX: idx_indicators_updated (UpdatedAt)

**ความสัมพันธ์:**

- indicator_types (IndicatorTypeID)
- indicator_categories (IndicatorCategoryID)
- measurement_units (MeasurementUnitID)
- users (CreatedBy, UpdatedBy)

---

### budgetitems

**คำอธิบาย:** ตารางเก็บรายการงบประมาณ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ project_budget_items และ budget_allocations

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| BudgetItemID | int | 11 | NO | AUTO_INCREMENT | รหัสรายการงบประมาณ (Primary Key) |
| BudgetItemCode | varchar | 50 | YES | NULL | รหัสรายการงบประมาณ |
| BudgetItemName | varchar | 300 | NO |  | ชื่อรายการงบประมาณ |
| BudgetItemNameEng | varchar | 300 | YES | NULL | ชื่อรายการงบประมาณภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| DescriptionEng | text |  | YES | NULL | รายละเอียดภาษาอังกฤษ |
| BudgetCategoryID | int | 11 | YES | NULL | หมวดหมู่งบประมาณ (Foreign Key) |
| BudgetSubcategoryID | int | 11 | YES | NULL | หมวดหมู่ย่อยงบประมาณ (Foreign Key) |
| UnitCost | decimal | 15,2 | YES | NULL | ต้นทุนต่อหน่วย |
| Quantity | decimal | 10,2 | YES | NULL | ปริมาณ |
| TotalCost | decimal | 15,2 | YES | NULL | ต้นทุนรวม |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: BudgetItemID
- INDEX: idx_budgetitems_code (BudgetItemCode)
- INDEX: idx_budgetitems_name (BudgetItemName)
- INDEX: idx_budgetitems_category (BudgetCategoryID)
- INDEX: idx_budgetitems_subcategory (BudgetSubcategoryID)
- INDEX: idx_budgetitems_active (IsActive)
- INDEX: idx_budgetitems_created (CreatedAt)
- INDEX: idx_budgetitems_updated (UpdatedAt)

**ความสัมพันธ์:**

- budget_categories (BudgetCategoryID)
- budget_subcategories (BudgetSubcategoryID)
- users (CreatedBy, UpdatedBy)

---

### projectvillages

**คำอธิบาย:** ตารางเชื่อมโยงโครงการกับหมู่บ้านที่ได้รับผลกระทบ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงระหว่าง projects และ villages

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ProjectVillageID | int | 11 | NO | AUTO_INCREMENT | รหัสเชื่อมโยง (Primary Key) |
| ProjectID | int | 11 | NO |  | รหัสโครงการ (Foreign Key) |
| VillageID | int | 11 | NO |  | รหัสหมู่บ้าน (Foreign Key) |
| Population | int | 11 | YES | NULL | ประชากรในหมู่บ้าน |
| Households | int | 11 | YES | NULL | จำนวนครัวเรือน |
| Beneficiaries | int | 11 | YES | NULL | จำนวนผู้ได้รับประโยชน์ |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: ProjectVillageID
- UNIQUE KEY: uk_project_village (ProjectID, VillageID)
- INDEX: idx_projectvillages_project (ProjectID)
- INDEX: idx_projectvillages_village (VillageID)
- INDEX: idx_projectvillages_created (CreatedAt)
- INDEX: idx_projectvillages_updated (UpdatedAt)

**ความสัมพันธ์:**

- projects (ProjectID)
- villages (VillageID)
- users (CreatedBy, UpdatedBy)

---

### districts

**คำอธิบาย:** ตารางเก็บข้อมูลอำเภอ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ provinces และ subdistricts

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| DistrictID | int | 11 | NO | AUTO_INCREMENT | รหัสอำเภอ (Primary Key) |
| DistrictCode | varchar | 10 | YES | NULL | รหัสอำเภอ |
| DistrictName | varchar | 100 | NO |  | ชื่ออำเภอ |
| DistrictNameEng | varchar | 100 | YES | NULL | ชื่ออำเภอภาษาอังกฤษ |
| ProvinceID | int | 11 | NO |  | รหัสจังหวัด (Foreign Key) |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

**ดัชนี:**

- PRIMARY KEY: DistrictID
- UNIQUE KEY: uk_district_code (DistrictCode)
- INDEX: idx_districts_province (ProvinceID)
- INDEX: idx_districts_name (DistrictName)
- INDEX: idx_districts_created (CreatedAt)
- INDEX: idx_districts_updated (UpdatedAt)

**ความสัมพันธ์:**

- provinces (ProvinceID)

---

### subdistricts

**คำอธิบาย:** ตารางเก็บข้อมูลตำบล  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ districts และ villages

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| SubdistrictID | int | 11 | NO | AUTO_INCREMENT | รหัสตำบล (Primary Key) |
| SubdistrictCode | varchar | 10 | YES | NULL | รหัสตำบล |
| SubdistrictName | varchar | 100 | NO |  | ชื่อตำบล |
| SubdistrictNameEng | varchar | 100 | YES | NULL | ชื่อตำบลภาษาอังกฤษ |
| DistrictID | int | 11 | NO |  | รหัสอำเภอ (Foreign Key) |
| PostalCode | varchar | 10 | YES | NULL | รหัสไปรษณีย์ |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

**ดัชนี:**

- PRIMARY KEY: SubdistrictID
- UNIQUE KEY: uk_subdistrict_code (SubdistrictCode)
- INDEX: idx_subdistricts_district (DistrictID)
- INDEX: idx_subdistricts_name (SubdistrictName)
- INDEX: idx_subdistricts_postal (PostalCode)
- INDEX: idx_subdistricts_created (CreatedAt)
- INDEX: idx_subdistricts_updated (UpdatedAt)

**ความสัมพันธ์:**

- districts (DistrictID)

---

### provinces

**คำอธิบาย:** ตารางเก็บข้อมูลจังหวัด  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ districts

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ProvinceID | int | 11 | NO | AUTO_INCREMENT | รหัสจังหวัด (Primary Key) |
| ProvinceCode | varchar | 5 | YES | NULL | รหัสจังหวัด |
| ProvinceName | varchar | 100 | NO |  | ชื่อจังหวัด |
| ProvinceNameEng | varchar | 100 | YES | NULL | ชื่อจังหวัดภาษาอังกฤษ |
| Region | varchar | 50 | YES | NULL | ภูมิภาค |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

**ดัชนี:**

- PRIMARY KEY: ProvinceID
- UNIQUE KEY: uk_province_code (ProvinceCode)
- INDEX: idx_provinces_name (ProvinceName)
- INDEX: idx_provinces_region (Region)
- INDEX: idx_provinces_created (CreatedAt)
- INDEX: idx_provinces_updated (UpdatedAt)

---

### main_projects

**คำอธิบาย:** ตารางเก็บข้อมูลโครงการหลัก  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| MainProjectID | int | 11 | NO | AUTO_INCREMENT | รหัสโครงการหลัก (Primary Key) |
| MainProjectCode | varchar | 50 | YES | NULL | รหัสโครงการหลัก |
| MainProjectName | varchar | 300 | NO |  | ชื่อโครงการหลัก |
| MainProjectNameEng | varchar | 300 | YES | NULL | ชื่อโครงการหลักภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| DescriptionEng | text |  | YES | NULL | รายละเอียดภาษาอังกฤษ |
| TotalBudget | decimal | 15,2 | YES | NULL | งบประมาณรวม |
| StartDate | date |  | YES | NULL | วันที่เริ่ม |
| EndDate | date |  | YES | NULL | วันที่สิ้นสุด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: MainProjectID
- INDEX: idx_main_projects_code (MainProjectCode)
- INDEX: idx_main_projects_name (MainProjectName)
- INDEX: idx_main_projects_active (IsActive)
- INDEX: idx_main_projects_created (CreatedAt)
- INDEX: idx_main_projects_updated (UpdatedAt)

**ความสัมพันธ์:**

- users (CreatedBy, UpdatedBy)

---

### project_indicators

**คำอธิบาย:** ตารางเชื่อมโยงโครงการกับตัวชี้วัด  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงระหว่าง projects และ indicators

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ProjectIndicatorID | int | 11 | NO | AUTO_INCREMENT | รหัสเชื่อมโยง (Primary Key) |
| ProjectID | int | 11 | NO |  | รหัสโครงการ (Foreign Key) |
| IndicatorID | int | 11 | NO |  | รหัสตัวชี้วัด (Foreign Key) |
| TargetValue | decimal | 15,4 | YES | NULL | ค่าประสงค์ |
| BaselineValue | decimal | 15,4 | YES | NULL | ค่าฐาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: ProjectIndicatorID
- UNIQUE KEY: uk_project_indicator (ProjectID, IndicatorID)
- INDEX: idx_project_indicators_project (ProjectID)
- INDEX: idx_project_indicators_indicator (IndicatorID)
- INDEX: idx_project_indicators_created (CreatedAt)
- INDEX: idx_project_indicators_updated (UpdatedAt)

**ความสัมพันธ์:**

- projects (ProjectID)
- indicators (IndicatorID)
- users (CreatedBy, UpdatedBy)

---

### indicator_values

**คำอธิบาย:** ตารางเก็บค่าตัวชี้วัดตามช่วงเวลา  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ project_indicators และ time_periods

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| IndicatorValueID | int | 11 | NO | AUTO_INCREMENT | รหัสค่า (Primary Key) |
| ProjectIndicatorID | int | 11 | NO |  | รหัสเชื่อมโยงโครงการ-ตัวชี้วัด (Foreign Key) |
| TimePeriodID | int | 11 | NO |  | รหัสช่วงเวลา (Foreign Key) |
| Value | decimal | 15,4 | NO |  | ค่าตัวชี้วัด |
| Notes | text |  | YES | NULL | หมายเหตุ |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: IndicatorValueID
- UNIQUE KEY: uk_indicator_value (ProjectIndicatorID, TimePeriodID)
- INDEX: idx_indicator_values_project_indicator (ProjectIndicatorID)
- INDEX: idx_indicator_values_time_period (TimePeriodID)
- INDEX: idx_indicator_values_created (CreatedAt)
- INDEX: idx_indicator_values_updated (UpdatedAt)

**ความสัมพันธ์:**

- project_indicators (ProjectIndicatorID)
- time_periods (TimePeriodID)
- users (CreatedBy, UpdatedBy)

---

### budget_allocations

**คำอธิบาย:** ตารางเก็บการจัดสรรงบประมาณ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ projects และ budgetitems

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| BudgetAllocationID | int | 11 | NO | AUTO_INCREMENT | รหัสการจัดสรร (Primary Key) |
| ProjectID | int | 11 | NO |  | รหัสโครงการ (Foreign Key) |
| BudgetItemID | int | 11 | NO |  | รหัสรายการงบประมาณ (Foreign Key) |
| AllocatedAmount | decimal | 15,2 | NO |  | จำนวนเงินที่จัดสรร |
| Notes | text |  | YES | NULL | หมายเหตุ |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: BudgetAllocationID
- UNIQUE KEY: uk_budget_allocation (ProjectID, BudgetItemID)
- INDEX: idx_budget_allocations_project (ProjectID)
- INDEX: idx_budget_allocations_budget_item (BudgetItemID)
- INDEX: idx_budget_allocations_created (CreatedAt)
- INDEX: idx_budget_allocations_updated (UpdatedAt)

**ความสัมพันธ์:**

- projects (ProjectID)
- budgetitems (BudgetItemID)
- users (CreatedBy, UpdatedBy)

---

### project_budget_items

**คำอธิบาย:** ตารางเชื่อมโยงโครงการกับรายการงบประมาณ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงระหว่าง projects และ budgetitems

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ProjectBudgetItemID | int | 11 | NO | AUTO_INCREMENT | รหัสเชื่อมโยง (Primary Key) |
| ProjectID | int | 11 | NO |  | รหัสโครงการ (Foreign Key) |
| BudgetItemID | int | 11 | NO |  | รหัสรายการงบประมาณ (Foreign Key) |
| Quantity | decimal | 10,2 | YES | NULL | ปริมาณ |
| UnitCost | decimal | 15,2 | YES | NULL | ต้นทุนต่อหน่วย |
| TotalCost | decimal | 15,2 | YES | NULL | ต้นทุนรวม |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |
| UpdatedBy | int | 11 | YES | NULL | ผู้แก้ไขล่าสุด (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: ProjectBudgetItemID
- UNIQUE KEY: uk_project_budget_item (ProjectID, BudgetItemID)
- INDEX: idx_project_budget_items_project (ProjectID)
- INDEX: idx_project_budget_items_budget_item (BudgetItemID)
- INDEX: idx_project_budget_items_created (CreatedAt)
- INDEX: idx_project_budget_items_updated (UpdatedAt)

**ความสัมพันธ์:**

- projects (ProjectID)
- budgetitems (BudgetItemID)
- users (CreatedBy, UpdatedBy)

---

### villages

**คำอธิบาย:** ตารางเก็บข้อมูลหมู่บ้าน  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ subdistricts และ projectvillages

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| VillageID | int | 11 | NO | AUTO_INCREMENT | รหัสหมู่บ้าน (Primary Key) |
| VillageCode | varchar | 10 | YES | NULL | รหัสหมู่บ้าน |
| VillageName | varchar | 100 | NO |  | ชื่อหมู่บ้าน |
| VillageNameEng | varchar | 100 | YES | NULL | ชื่อหมู่บ้านภาษาอังกฤษ |
| SubdistrictID | int | 11 | NO |  | รหัสตำบล (Foreign Key) |
| Population | int | 11 | YES | NULL | ประชากร |
| Households | int | 11 | YES | NULL | จำนวนครัวเรือน |
| Latitude | decimal | 10,8 | YES | NULL | ละติจูด |
| Longitude | decimal | 11,8 | YES | NULL | ลองจิจูด |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

**ดัชนี:**

- PRIMARY KEY: VillageID
- UNIQUE KEY: uk_village_code (VillageCode)
- INDEX: idx_villages_subdistrict (SubdistrictID)
- INDEX: idx_villages_name (VillageName)
- INDEX: idx_villages_created (CreatedAt)
- INDEX: idx_villages_updated (UpdatedAt)

**ความสัมพันธ์:**

- subdistricts (SubdistrictID)

---

### users

**คำอธิบาย:** ตารางเก็บข้อมูลผู้ใช้ระบบ  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับหลายตารางผ่าน UserID

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| UserID | int | 11 | NO | AUTO_INCREMENT | รหัสผู้ใช้ (Primary Key) |
| Username | varchar | 50 | NO |  | ชื่อผู้ใช้ |
| Password | varchar | 255 | NO |  | รหัสผ่าน (เข้ารหัส) |
| FirstName | varchar | 100 | NO |  | ชื่อ |
| LastName | varchar | 100 | NO |  | นามสกุล |
| Email | varchar | 150 | YES | NULL | อีเมล |
| Role | enum |  | NO | viewer | บทบาท (admin, manager, director, viewer) |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |
| LastLogin | timestamp |  | YES | NULL | เข้าสู่ระบบล่าสุด |
| CreatedBy | int | 11 | YES | NULL | ผู้สร้าง (Foreign Key) |

**ดัชนี:**

- PRIMARY KEY: UserID
- UNIQUE KEY: uk_username (Username)
- INDEX: fk_users_created_by (CreatedBy)

**ความสัมพันธ์:**

- users (CreatedBy) - Self-reference

---

### user_sessions

**คำอธิบาย:** ตารางเก็บข้อมูล Session ของผู้ใช้  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ users

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| SessionID | varchar | 255 | NO |  | รหัส Session (Primary Key) |
| UserID | int | 11 | NO |  | รหัสผู้ใช้ (Foreign Key) |
| IPAddress | varchar | 45 | YES | NULL | ที่อยู่ IP |
| UserAgent | text |  | YES | NULL | ข้อมูลเบราว์เซอร์ |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| ExpiresAt | timestamp |  | NO |  | วันที่หมดอายุ |

**ดัชนี:**

- PRIMARY KEY: SessionID
- INDEX: fk_sessions_user (UserID)

**ความสัมพันธ์:**

- users (UserID)

---

### user_activity_log

**คำอธิบาย:** ตารางเก็บบันทึกกิจกรรมของผู้ใช้  
**จำนวนระเบียนโดยประมาณ:** -  
**ความสัมพันธ์:** เชื่อมโยงกับ users

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| LogID | int | 11 | NO | AUTO_INCREMENT | รหัสบันทึก (Primary Key) |
| UserID | int | 11 | YES | NULL | รหัสผู้ใช้ (Foreign Key) |
| Action | varchar | 100 | NO |  | การกระทำ |
| Description | text |  | YES | NULL | รายละเอียด |
| IPAddress | varchar | 45 | YES | NULL | ที่อยู่ IP |
| UserAgent | text |  | YES | NULL | ข้อมูลเบราว์เซอร์ |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |

**ดัชนี:**

- PRIMARY KEY: LogID
- INDEX: fk_activity_user (UserID)
- INDEX: idx_activity_created (CreatedAt)

**ความสัมพันธ์:**

- users (UserID)

---

## ตารางอ้างอิง (Reference Tables)

### indicator_types

**คำอธิบาย:** ตารางเก็บประเภทตัวชี้วัด  
**จำนวนระเบียนโดยประมาณ:** 10-20  
**ความสัมพันธ์:** อ้างอิงโดย indicators

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| IndicatorTypeID | int | 11 | NO | AUTO_INCREMENT | รหัสประเภท (Primary Key) |
| TypeName | varchar | 100 | NO |  | ชื่อประเภท |
| TypeNameEng | varchar | 100 | YES | NULL | ชื่อประเภทภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### indicator_categories

**คำอธิบาย:** ตารางเก็บหมวดหมู่ตัวชี้วัด  
**จำนวนระเบียนโดยประมาณ:** 20-50  
**ความสัมพันธ์:** อ้างอิงโดย indicators

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| IndicatorCategoryID | int | 11 | NO | AUTO_INCREMENT | รหัสหมวดหมู่ (Primary Key) |
| CategoryName | varchar | 100 | NO |  | ชื่อหมวดหมู่ |
| CategoryNameEng | varchar | 100 | YES | NULL | ชื่อหมวดหมู่ภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### budget_categories

**คำอธิบาย:** ตารางเก็บหมวดหมู่งบประมาณ  
**จำนวนระเบียนโดยประมาณ:** 10-20  
**ความสัมพันธ์:** อ้างอิงโดย budgetitems

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| BudgetCategoryID | int | 11 | NO | AUTO_INCREMENT | รหัสหมวดหมู่ (Primary Key) |
| CategoryName | varchar | 100 | NO |  | ชื่อหมวดหมู่ |
| CategoryNameEng | varchar | 100 | YES | NULL | ชื่อหมวดหมู่ภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### budget_subcategories

**คำอธิบาย:** ตารางเก็บหมวดหมู่ย่อยงบประมาณ  
**จำนวนระเบียนโดยประมาณ:** 50-100  
**ความสัมพันธ์:** อ้างอิงโดย budgetitems

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| BudgetSubcategoryID | int | 11 | NO | AUTO_INCREMENT | รหัสหมวดหมู่ย่อย (Primary Key) |
| BudgetCategoryID | int | 11 | NO |  | รหัสหมวดหมู่หลัก (Foreign Key) |
| SubcategoryName | varchar | 100 | NO |  | ชื่อหมวดหมู่ย่อย |
| SubcategoryNameEng | varchar | 100 | YES | NULL | ชื่อหมวดหมู่ย่อยภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### project_types

**คำอธิบาย:** ตารางเก็บประเภทโครงการ  
**จำนวนระเบียนโดยประมาณ:** 10-20  
**ความสัมพันธ์:** อ้างอิงโดย projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ProjectTypeID | int | 11 | NO | AUTO_INCREMENT | รหัสประเภท (Primary Key) |
| TypeName | varchar | 100 | NO |  | ชื่อประเภท |
| TypeNameEng | varchar | 100 | YES | NULL | ชื่อประเภทภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### project_statuses

**คำอธิบาย:** ตารางเก็บสถานะโครงการ  
**จำนวนระเบียนโดยประมาณ:** 5-10  
**ความสัมพันธ์:** อ้างอิงโดย projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| StatusID | int | 11 | NO | AUTO_INCREMENT | รหัสสถานะ (Primary Key) |
| StatusName | varchar | 50 | NO |  | ชื่อสถานะ |
| StatusNameEng | varchar | 50 | YES | NULL | ชื่อสถานะภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| ColorCode | varchar | 7 | YES | NULL | รหัสสีสำหรับแสดงผล |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### funding_sources

**คำอธิบาย:** ตารางเก็บแหล่งเงินทุน  
**จำนวนระเบียนโดยประมาณ:** 10-20  
**ความสัมพันธ์:** อ้างอิงโดย projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| FundingSourceID | int | 11 | NO | AUTO_INCREMENT | รหัสแหล่งเงินทุน (Primary Key) |
| SourceName | varchar | 100 | NO |  | ชื่อแหล่งเงินทุน |
| SourceNameEng | varchar | 100 | YES | NULL | ชื่อแหล่งเงินทุนภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### implementing_agencies

**คำอธิบาย:** ตารางเก็บหน่วยงานผู้ดำเนินการ  
**จำนวนระเบียนโดยประมาณ:** 20-50  
**ความสัมพันธ์:** อ้างอิงโดย projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| ImplementingAgencyID | int | 11 | NO | AUTO_INCREMENT | รหัสหน่วยงาน (Primary Key) |
| AgencyName | varchar | 200 | NO |  | ชื่อหน่วยงาน |
| AgencyNameEng | varchar | 200 | YES | NULL | ชื่อหน่วยงานภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| ContactPerson | varchar | 100 | YES | NULL | ผู้ติดต่อ |
| ContactPhone | varchar | 20 | YES | NULL | เบอร์โทรติดต่อ |
| ContactEmail | varchar | 150 | YES | NULL | อีเมลติดต่อ |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### target_groups

**คำอธิบาย:** ตารางเก็บกลุ่มเป้าหมาย  
**จำนวนระเบียนโดยประมาณ:** 10-20  
**ความสัมพันธ์:** อ้างอิงโดย projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| TargetGroupID | int | 11 | NO | AUTO_INCREMENT | รหัสกลุ่มเป้าหมาย (Primary Key) |
| GroupName | varchar | 100 | NO |  | ชื่อกลุ่มเป้าหมาย |
| GroupNameEng | varchar | 100 | YES | NULL | ชื่อกลุ่มเป้าหมายภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### geographic_coverages

**คำอธิบาย:** ตารางเก็บขอบเขตทางภูมิศาสตร์  
**จำนวนระเบียนโดยประมาณ:** 5-10  
**ความสัมพันธ์:** อ้างอิงโดย projects

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| GeographicCoverageID | int | 11 | NO | AUTO_INCREMENT | รหัสขอบเขต (Primary Key) |
| CoverageName | varchar | 100 | NO |  | ชื่อขอบเขต |
| CoverageNameEng | varchar | 100 | YES | NULL | ชื่อขอบเขตภาษาอังกฤษ |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### time_periods

**คำอธิบาย:** ตารางเก็บช่วงเวลา  
**จำนวนระเบียนโดยประมาณ:** 50-100  
**ความสัมพันธ์:** อ้างอิงโดย indicator_values

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| TimePeriodID | int | 11 | NO | AUTO_INCREMENT | รหัสช่วงเวลา (Primary Key) |
| PeriodName | varchar | 50 | NO |  | ชื่อช่วงเวลา |
| PeriodNameEng | varchar | 50 | YES | NULL | ชื่อช่วงเวลาาษาอังกฤษ |
| StartDate | date |  | NO |  | วันที่เริ่ม |
| EndDate | date |  | NO |  | วันที่สิ้นสุด |
| FiscalYear | varchar | 10 | YES | NULL | ปีงบประมาณ |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

### measurement_units

**คำอธิบาย:** ตารางเก็บหน่วยวัด  
**จำนวนระเบียนโดยประมาณ:** 20-50  
**ความสัมพันธ์:** อ้างอิงโดย indicators

| คอลัมน์ | ประเภทข้อมูล | ความยาว | Null | ค่าเริ่มต้น | คำอธิบาย |
|---------|-------------|---------|------|------------|----------|
| MeasurementUnitID | int | 11 | NO | AUTO_INCREMENT | รหัสหน่วยวัด (Primary Key) |
| UnitName | varchar | 50 | NO |  | ชื่อหน่วยวัด |
| UnitNameEng | varchar | 50 | YES | NULL | ชื่อหน่วยวัดภาษาอังกฤษ |
| UnitSymbol | varchar | 20 | YES | NULL | สัญลักษณ์หน่วยวัด |
| Description | text |  | YES | NULL | รายละเอียด |
| IsActive | tinyint | 1 | NO | 1 | สถานะการใช้งาน |
| CreatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่สร้าง |
| UpdatedAt | timestamp |  | NO | CURRENT_TIMESTAMP | วันที่แก้ไขล่าสุด |

---

## Views และ Stored Procedures

### Views

#### user_info_view

**คำอธิบาย:** View สำหรับแสดงข้อมูลผู้ใช้โดยไม่รวมรหัสผ่าน  
**คำสั่ง SQL:**

```sql
CREATE OR REPLACE VIEW user_info_view AS
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
```

---

## ข้อมูลเพิ่มเติม

### การตั้งชื่อตาราง

- ตารางหลัก: ใช้รูปแบบเอกพจน์ (projects, indicators, budgetitems)
- ตารางเชื่อมโยง: ใช้รูปแบบชื่อตารางหลักคั่นด้วย `_` (project_indicators, project_budget_items)
- ตารางอ้างอิง: ใช้รูปแบบพหูพจน์ (provinces, districts, subdistricts)

### การตั้งชื่อคอลัมน์

- Primary Key: ชื่อตาราง + ID (ProjectID, IndicatorID)
- Foreign Key: ชื่อตารางอ้างอิง + ID (ProvinceID, DistrictID)
- Boolean: ขึ้นต้นด้วย Is (IsActive)
- Timestamp: CreatedAt, UpdatedAt, LastLogin

### การจัดการข้อมูล

- การลบข้อมูล: ใช้ Soft Delete ผ่านฟิลด์ IsActive
- การติดตามการเปลี่ยนแปลง: ใช้ CreatedAt, UpdatedAt, CreatedBy, UpdatedBy
- การเข้ารหัสรหัสผ่าน: ใช้ bcrypt (PASSWORD_BCRYPT)

### ข้อแนะนำการใช้งาน

1. ควรสำรองข้อมูลก่อนการปรับเปลี่ยนโครงสร้างฐานข้อมูล
2. ตรวจสอบ Foreign Key Constraints ก่อนการลบข้อมูล
3. ใช้ Transaction สำหรับการปรับเปลี่ยนข้อมูลหลายตาราง
4. ตรวจสอบสิทธิ์การเข้าถึงข้อมูลตามบทบาทผู้ใช้

---

*เอกสารนี้สร้างขึ้นเมื่อ: [วันที่ปัจจุบัน]*  
*ฐานข้อมูลเวอร์ชัน: 1.0*  
*ผู้จัดทำ: Data Analytics Management System Team*


*เอกสารนี้เป็นคู่มือการพัฒนาและใช้งานระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น สร้างขึ้นเมื่อวันที่ 9 กันยายน 2025*
