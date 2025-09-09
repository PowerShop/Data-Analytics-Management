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

#### 1. ดาวน์โหลดและติดตั้ง XAMPP/Laragon

```bash
# สำหรับ Windows - ดาวน์โหลด Laragon จาก
# https://laragon.org/download/

# สำหรับ Linux/Mac - ใช้ XAMPP หรือติดตั้งแยก
sudo apt-get install apache2 php mysql-server
```

#### 2. คัดลอกไฟล์โปรเจค

```bash
# คัดลอกไฟล์ทั้งหมดไปยังโฟลเดอร์ htdocs หรือ www
cp -r Data-Analytics /path/to/htdocs/
```

#### 3. ตั้งค่าฐานข้อมูล

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
# นำเข้าไฟล์ SQL
mysql -u root -p data_analytics < data_analytics.sql
```

#### 4. ตั้งค่าไฟล์การเชื่อมต่อฐานข้อมูล

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

#### 5. ติดตั้ง Dependencies ด้วย Composer

```bash
# เข้าไปยังโฟลเดอร์โปรเจค
cd /path/to/Data-Analytics

# ติดตั้ง Composer packages
composer install
```

#### 6. ตั้งค่าการอนุญาตไฟล์

```bash
# ตั้งสิทธิ์การเขียนสำหรับโฟลเดอร์ที่ต้องการ
chmod 755 backup_manager/
chmod 755 passwords/
chmod 755 exports/
```

#### 7. เปิดใช้งานระบบ

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

## แผนการพัฒนาในอนาคต (Future Development)

### ฟีเจอร์ที่วางแผนจะเพิ่ม

#### Phase 1: การปรับปรุง UX/UI

- [ ] Responsive design สำหรับมือถือ
- [ ] Dark mode
- [ ] Multi-language support
- [ ] Real-time notifications

#### Phase 2: การเพิ่มฟีเจอร์

- [ ] API สำหรับ mobile app
- [ ] Advanced reporting system
- [ ] Data visualization dashboard
- [ ] Integration with GIS mapping

#### Phase 3: การปรับปรุงประสิทธิภาพ

- [ ] Database optimization
- [ ] Caching system (Redis/Memcached)
- [ ] CDN integration
- [ ] Load balancing

#### Phase 4: ความปลอดภัยขั้นสูง

- [ ] Two-factor authentication
- [ ] Role-based access control (RBAC)
- [ ] Audit logging
- [ ] Data encryption at rest

---

## ติดต่อและสนับสนุน (Contact & Support)

### ช่องทางการติดต่อ

- **Email**: support@data-analytics.local
- **Documentation**: [Wiki/Confluence Link]
- **Issue Tracker**: [GitHub Issues/Jira]

### การมีส่วนร่วมในการพัฒนา (Contributing)

1. Fork โปรเจค
2. สร้าง feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit การเปลี่ยนแปลง (`git commit -m 'Add some AmazingFeature'`)
4. Push ไปยัง branch (`git push origin feature/AmazingFeature`)
5. เปิด Pull Request

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

*เอกสารนี้เป็นคู่มือการพัฒนาและใช้งานระบบฐานข้อมูลโครงการพัฒนาท้องถิ่น สร้างขึ้นเมื่อวันที่ 9 กันยายน 2025*
