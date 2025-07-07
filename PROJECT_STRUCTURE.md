# 📁 โครงสร้างโปรเจค - ระบบจัดการโครงการและวิเคราะห์ข้อมูล

## 🗂️ **ภาพรวมโครงสร้างไฟล์**

```
Data-Analytics/
│
├── 📄 index.php                      # 🏠 หน้าแรกของระบบ
├── 📄 db.php                         # 🔗 การเชื่อมต่อฐานข้อมูล
├── 📄 navbar.php                     # 🧭 แถบเมนูนำทาง
├── 🗄️ database_now.sql              # 💾 ไฟล์ฐานข้อมูล SQL
├── 📋 backup_info.json               # ℹ️ ข้อมูลการสำรอง
├── 📖 README.md                      # 📚 คู่มือการใช้งาน
├── 📄 PROJECT_STRUCTURE.md           # 📋 ไฟล์นี้
│
├── 📁 **หน้าหลักของระบบ** /
│   ├── 📄 add_project.php            # ➕ เพิ่มโครงการใหม่
│   ├── 📄 edit_project.php           # ✏️ แก้ไขโครงการ
│   ├── 📄 delete_project.php         # 🗑️ ลบโครงการ
│   ├── 📄 projects_list.php          # 📋 รายการโครงการ
│   ├── 📄 project_detail.php         # 📊 รายละเอียดโครงการ
│   ├── 📄 main_projects.php          # 🎯 จัดการโครงการหลัก
│   │
│   ├── 📄 dashboard.php              # 📊 แดชบอร์ดหลัก
│   ├── 📄 analytics.php              # 📈 ระบบวิเคราะห์ข้อมูล
│   ├── 📄 manage_indicators.php      # ⚙️ จัดการตัวชี้วัด
│   │
│   ├── 📄 reports.php                # 📄 ระบบรายงาน
│   ├── 📄 custom_report.php          # 🛠️ สร้างรายงานแบบกำหนดเอง
│   ├── 📄 export_report.php          # 📤 ส่งออกรายงาน
│   └── 📄 export_analytics.php       # 📈 ส่งออกข้อมูลวิเคราะห์
│
├── 📁 **ระบบสำรองข้อมูล** /
│   ├── 📄 backup_manager.php         # 💾 จัดการการสำรอง
│   ├── 📄 backup_process.php         # ⚡ กระบวนการสำรอง
│   └── 📄 download_backup.php        # 📥 ดาวน์โหลดไฟล์สำรอง
│
├── 📁 **API Endpoints** /
│   └── api/
│       ├── 🔹 **โครงการ (Projects)**
│       │   ├── 📄 get_main_project.php
│       │   ├── 📄 get_project_indicators.php
│       │   └── 📄 save_project_indicators.php
│       │
│       ├── 🔹 **ตัวชี้วัด (Indicators)**
│       │   ├── 📄 get_indicators.php
│       │   ├── 📄 get_indicator.php
│       │   ├── 📄 get_indicators_by_year.php
│       │   ├── 📄 get_indicators_with_values.php
│       │   ├── 📄 save_indicator.php
│       │   └── 📄 delete_indicator.php
│       │
│       ├── 🔹 **พื้นที่ (Locations)**
│       │   ├── 📄 get_districts.php
│       │   └── 📄 get_subdistricts.php
│       │
│       ├── 🔹 **ฐานข้อมูล (Database)**
│       │   ├── 📄 get_tables.php
│       │   ├── 📄 execute_query.php
│       │   └── 📄 save_query.php
│       │
│       └── 🔹 **อื่นๆ**
│           └── 📄 get_project_indicators_with_details.php
│
├── 📁 **Assets (ทรัพยากรสถิต)** /
│   ├── 📁 css/                       # 🎨 ไฟล์ CSS
│   │   ├── 📄 main.css              # 🎨 สไตล์หลัก
│   │   ├── 📄 backup-manager.css    # 💾 สไตล์หน้า Backup
│   │   ├── 📄 sweetalert2-custom.css # 🍭 สไตล์ SweetAlert2
│   │   ├── 📄 form.css              # 📝 สไตล์ฟอร์ม
│   │   ├── 📄 list.css              # 📋 สไตล์รายการ
│   │   ├── 📄 dashboard.css         # 📊 สไตล์แดชบอร์ด
│   │   ├── 📄 detail.css            # 🔍 สไตล์หน้ารายละเอียด
│   │   ├── 📄 analytics.css         # 📈 สไตล์หน้า Analytics
│   │   └── 📄 index.css             # 🏠 สไตล์หน้าแรก
│   │
│   └── 📁 js/                        # 📜 ไฟล์ JavaScript
│       ├── 📄 main.js               # ⚡ JavaScript หลัก
│       ├── 📄 backup-manager.js     # 💾 JavaScript สำหรับ Backup
│       └── 📄 sweetalert-examples.js # 🍭 ตัวอย่าง SweetAlert2
│
├── 📁 **Includes (ไฟล์ Include)** /
│   ├── 📄 header.php                # 📄 ส่วนหัวของหน้า
│   ├── 📄 footer.php                # 📄 ส่วนท้ายของหน้า
│   ├── 📄 navbar.php                # 🧭 แถบเมนู
│   ├── 📄 db.php                    # 🔗 การเชื่อมต่อฐานข้อมูล
│   └── 📄 page-template.php         # 📄 Template หน้า
│
├── 📁 **Backup (ไฟล์สำรอง)** /
│   ├── 📁 backup_2568-07-03_15-24-00/
│   ├── 📁 backup_2568-07-07_11-58-57/
│   └── 📁 ... (โฟลเดอร์สำรองอื่นๆ)
│
└── 📁 **Testing & Development** /
    └── 📄 test_navbar.php            # 🧪 ทดสอบ Navbar
```

---

## 📋 **รายละเอียดไฟล์แต่ละประเภท**

### 🏠 **หน้าหลักของระบบ**

| ไฟล์ | คำอธิบาย | ฟังก์ชันหลัก |
|------|----------|---------------|
| `index.php` | หน้าแรกของระบบ | แสดงภาพรวม, สถิติ, และลิงก์ด่วน |
| `dashboard.php` | แดชบอร์ดแสดงกราฟ | กราฟ Chart.js, สถิติเชิงลึก |
| `analytics.php` | วิเคราะห์ข้อมูลขั้นสูง | กรองข้อมูล, กราฟแบบ Interactive |

### 📝 **จัดการโครงการ**

| ไฟล์ | คำอธิบาย | ฟังก์ชันหลัก |
|------|----------|---------------|
| `add_project.php` | เพิ่มโครงการใหม่ | ฟอร์มเพิ่มโครงการ, Validation |
| `edit_project.php` | แก้ไขโครงการ | แก้ไขข้อมูลโครงการที่มีอยู่ |
| `delete_project.php` | ลบโครงการ | ลบโครงการพร้อม Confirmation |
| `projects_list.php` | รายการโครงการ | ตารางแสดงโครงการ, การค้นหา |
| `project_detail.php` | รายละเอียดโครงการ | แสดงข้อมูลโครงการแบบละเอียด |
| `main_projects.php` | จัดการโครงการหลัก | CRUD โครงการหลักและยุทธศาสตร์ |

### 📊 **รายงานและวิเคราะห์**

| ไฟล์ | คำอธิบาย | ฟังก์ชันหลัก |
|------|----------|---------------|
| `reports.php` | ระบบรายงาน | สร้างรายงานมาตรฐาน |
| `custom_report.php` | รายงานแบบกำหนดเอง | Query Builder, Custom Reports |
| `export_report.php` | ส่งออกรายงาน | Export เป็น PDF, Excel |
| `export_analytics.php` | ส่งออกข้อมูลวิเคราะห์ | Export ข้อมูล Analytics |

### ⚙️ **การจัดการระบบ**

| ไฟล์ | คำอธิบาย | ฟังก์ชันหลัก |
|------|----------|---------------|
| `manage_indicators.php` | จัดการตัวชี้วัด | CRUD ตัวชี้วัดโครงการ |
| `backup_manager.php` | จัดการการสำรอง | สำรอง/คืนค่าข้อมูล |
| `backup_process.php` | กระบวนการสำรอง | ประมวลผลการสำรองข้อมูล |
| `download_backup.php` | ดาวน์โหลดไฟล์สำรอง | ดาวน์โหลดไฟล์ ZIP |

---

## 🔌 **API Endpoints โดยละเอียด**

### 📊 **Projects API**

```php
// ดึงข้อมูลโครงการหลัก
GET /api/get_main_project.php
Parameters: ปีโครงการ, ยุทธศาสตร์

// ดึงตัวชี้วัดของโครงการ
GET /api/get_project_indicators.php
Parameters: ProjectID

// บันทึกตัวชี้วัดโครงการ
POST /api/save_project_indicators.php
Body: ข้อมูลตัวชี้วัดแบบ JSON
```

### 🎯 **Indicators API**

```php
// ดึงรายการตัวชี้วัดทั้งหมด
GET /api/get_indicators.php

// ดึงตัวชี้วัดแต่ละตัว
GET /api/get_indicator.php?id={IndicatorID}

// ดึงตัวชี้วัดตามปี
GET /api/get_indicators_by_year.php?year={Year}

// ดึงตัวชี้วัดพร้อมค่า
GET /api/get_indicators_with_values.php

// บันทึกตัวชี้วัด
POST /api/save_indicator.php
Body: ข้อมูลตัวชี้วัดแบบ JSON

// ลบตัวชี้วัด
DELETE /api/delete_indicator.php?id={IndicatorID}
```

### 🗺️ **Location API**

```php
// ดึงรายการอำเภอ
GET /api/get_districts.php?province={ProvinceName}

// ดึงรายการตำบล
GET /api/get_subdistricts.php?district={DistrictName}
```

### 🗄️ **Database API**

```php
// ดึงรายการตาราง
GET /api/get_tables.php

// Execute SQL Query
POST /api/execute_query.php
Body: { "query": "SELECT * FROM..." }

// บันทึก Query
POST /api/save_query.php
Body: { "name": "Query Name", "query": "SELECT..." }
```

---

## 🎨 **Assets โดยละเอียด**

### 📁 **CSS Files**

| ไฟล์ | ขนาด | คำอธิบาย |
|------|------|----------|
| `main.css` | ~15KB | สไตล์หลักของระบบ |
| `backup-manager.css` | ~8KB | สไตล์เฉพาะหน้า Backup |
| `sweetalert2-custom.css` | ~3KB | ปรับแต่ง SweetAlert2 |
| `form.css` | ~5KB | สไตล์ฟอร์มต่างๆ |
| `list.css` | ~4KB | สไตล์ตารางและรายการ |
| `dashboard.css` | ~6KB | สไตล์แดชบอร์ดและกราฟ |
| `detail.css` | ~4KB | สไตล์หน้ารายละเอียด |
| `analytics.css` | ~7KB | สไตล์หน้า Analytics |
| `index.css` | ~5KB | สไตล์หน้าแรก |

### 📁 **JavaScript Files**

| ไฟล์ | ขนาด | คำอธิบาย |
|------|------|----------|
| `main.js` | ~12KB | JavaScript หลักของระบบ |
| `backup-manager.js` | ~6KB | ฟังก์ชันจัดการ Backup |
| `sweetalert-examples.js` | ~4KB | ตัวอย่างการใช้ SweetAlert2 |

---

## 🔗 **Dependencies และ Libraries**

### **CDN Libraries ที่ใช้**

```html
<!-- CSS Frameworks -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### **เวอร์ชันที่ใช้**

- **Bootstrap**: 5.3.0
- **jQuery**: 3.7.0  
- **Chart.js**: Latest
- **DataTables**: 1.13.6
- **Font Awesome**: 6.4.0
- **SweetAlert2**: 11.x

---

## 🔧 **การปรับแต่งและขยายระบบ**

### **เพิ่มหน้าใหม่**

1. **สร้างไฟล์ PHP ใหม่**
   ```php
   <?php include 'db.php'; ?>
   <?php include 'navbar.php'; ?>
   <!DOCTYPE html>
   <html lang="th">
   <head>
       <meta charset="UTF-8">
       <title>หน้าใหม่</title>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   </head>
   <body>
       <!-- เนื้อหาของหน้า -->
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   </body>
   </html>
   ```

2. **เพิ่มลิงก์ใน navbar.php**
   ```php
   <li class="nav-item">
       <a class="nav-link" href="new_page.php">หน้าใหม่</a>
   </li>
   ```

### **เพิ่ม API ใหม่**

1. **สร้างไฟล์ในโฟลเดอร์ api/**
   ```php
   <?php
   include '../db.php';
   header('Content-Type: application/json');
   
   // ประมวลผล API
   echo json_encode($result);
   ?>
   ```

### **เพิ่ม CSS/JS ใหม่**

1. **สร้างไฟล์ใน assets/css/ หรือ assets/js/**
2. **Include ในหน้าที่ต้องการใช้**
   ```html
   <link rel="stylesheet" href="assets/css/new-style.css">
   <script src="assets/js/new-script.js"></script>
   ```

---

## 🗄️ **โครงสร้างฐานข้อมูล**

### **ตารางหลัก**

- **Projects** - ข้อมูลโครงการ
- **MainProjects** - โครงการหลัก
- **Strategies** - ยุทธศาสตร์
- **Indicators** - ตัวชี้วัด
- **ProjectIndicators** - ตัวชี้วัดของแต่ละโครงการ
- **ProjectVillages** - พื้นที่ดำเนินโครงการ

### **ความสำคัญของไฟล์**

🔴 **สำคัญมาก** - ไฟล์หลักที่ห้ามลบ
- `db.php`, `navbar.php`, `index.php`
- `database_now.sql`

🟡 **สำคัญปานกลาง** - ไฟล์ฟีเจอร์หลัก
- หน้าจัดการโครงการ, API files
- CSS/JS files

🟢 **สำคัญน้อย** - ไฟล์เสริม
- ไฟล์ทดสอบ, backup files

---

## 📈 **สถิติโปรเจค**

- **จำนวนไฟล์ PHP**: ~25+ ไฟล์
- **จำนวน API Endpoints**: ~15+ endpoints  
- **จำนวนหน้าหลัก**: ~12 หน้า
- **ขนาดโปรเจค**: ~650KB (ไม่รวม backup)
- **จำนวนตาราง**: ~10+ ตาราง

---

## 🏗️ **Architecture Overview**

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│                 │    │                 │    │                 │
│ • Bootstrap 5   │◄──►│ • PHP 7.4+      │◄──►│ • MySQL 5.7+    │
│ • jQuery        │    │ • MySQLi        │    │ • UTF8MB4       │
│ • Chart.js      │    │ • JSON APIs     │    │ • InnoDB        │
│ • SweetAlert2   │    │ • File Backup   │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

---

<div align="center">

**📋 โครงสร้างนี้ออกแบบมาเพื่อความง่ายในการพัฒนาและบำรุงรักษา**

*อัปเดตล่าสุด: 7 กรกฎาคม 2568*

</div>
