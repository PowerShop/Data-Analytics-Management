# 📊 ระบบจัดการโครงการและวิเคราะห์ข้อมูล

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chart.js&logoColor=white)

**ระบบจัดการโครงการและวิเคราะห์ข้อมูลแบบครบวงจร**

[📱 Demo](#) • [📖 เอกสาร](#features) • [🚀 เริ่มใช้งาน](#installation) • [💡 คุณสมบัติ](#features)

</div>

---

## 🌟 **ภาพรวมระบบ**

ระบบจัดการโครงการและวิเคราะห์ข้อมูลที่พัฒนาด้วย PHP และ MySQL เพื่อใช้ในการบริหารจัดการโครงการขององค์กร สามารถติดตามโครงการ จัดการตัวชี้วัด และสร้างรายงานเชิงวิเคราะห์ได้อย่างครบถ้วน

### ✨ **จุดเด่นของระบบ**
- 🎯 **จัดการโครงการแบบครบวงจร** - ตั้งแต่เพิ่ม แก้ไข ลบ และติดตามโครงการ
- 📈 **ระบบ Analytics ขั้นสูง** - วิเคราะห์ข้อมูลด้วยกราฟและชาร์ตแบบ Real-time
- 📊 **Dashboard แบบ Interactive** - แสดงสถิติและข้อมูลสำคัญในหน้าเดียว
- 🎨 **UI/UX ที่สวยงาม** - ออกแบบด้วย Bootstrap 5 และ Font Awesome
- 📱 **Responsive Design** - ใช้งานได้ทุกอุปกรณ์
- 🔒 **ระบบสำรองข้อมูล** - Backup และ Restore อัตโนมัติ

---

## 🚀 **การติดตั้งและใช้งาน**

### **ความต้องการของระบบ**
- PHP 7.4 หรือสูงกว่า
- MySQL 5.7 หรือสูงกว่า
- Web Server (Apache/Nginx)
- Web Browser ที่รองรับ HTML5

### **ขั้นตอนการติดตั้ง**

1. **Clone หรือ Download โปรเจค**
   ```bash
   git clone [repository-url]
   cd Data-Analytics
   ```

2. **ตั้งค่าฐานข้อมูล**
   ```sql
   -- สร้างฐานข้อมูล
   CREATE DATABASE data_analytics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   
   -- Import ไฟล์ database_now.sql
   mysql -u username -p data_analytics < database_now.sql
   ```

3. **แก้ไขการเชื่อมต่อฐานข้อมูล**
   ```php
   // แก้ไขไฟล์ db.php
   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "data_analytics";
   ```

4. **เริ่มใช้งาน**
   - เปิด Web Browser แล้วไปที่ `http://localhost/Data-Analytics/`
   - ระบบพร้อมใช้งาน! 🎉

---

## 💡 **คุณสมบัติหลัก**

### 🏠 **หน้าแรก (Dashboard)**
- แสดงสถิติโครงการทั้งหมด
- กราฟแสดงข้อมูลเชิงวิเคราะห์
- ลิงก์ด่วนไปยังฟังก์ชันต่างๆ

### 📝 **จัดการโครงการ**
- ➕ เพิ่มโครงการใหม่พร้อมรายละเอียดครบถ้วน
- ✏️ แก้ไขข้อมูลโครงการ
- 🗑️ ลบโครงการ (พร้อม Confirmation)
- 📋 รายการโครงการแบบตาราง
- 🔍 ค้นหาและกรองข้อมูล

### 📊 **ระบบ Analytics**
- 📈 กราฟแสดงแนวโน้มโครงการ
- 🥧 Pie Chart แสดงสัดส่วนโครงการ
- 📊 Bar Chart เปรียบเทียบข้อมูล
- 🎯 วิเคราะห์ตัวชี้วัดโครงการ
- 📍 แผนที่แสดงพื้นที่โครงการ

### 📋 **ระบบรายงาน**
- 📄 สร้างรายงานมาตรฐาน
- 🛠️ Custom Report Builder
- 📥 Export ข้อมูลเป็น PDF, Excel, CSV
- 🎨 Template รายงานที่สวยงาม

### ⚙️ **ระบบจัดการ**
- 🎯 จัดการตัวชี้วัดโครงการ
- 💾 ระบบ Backup/Restore
- 📊 จัดการโครงการหลักและยุทธศาสตร์
- 🏢 จัดการข้อมูลหน่วยงาน

---

## 🗂️ **โครงสร้างไฟล์**

```
Data-Analytics/
├── 📁 api/                    # API Endpoints
│   ├── delete_indicator.php
│   ├── get_*.php             # GET APIs
│   └── save_*.php            # POST APIs
├── 📁 assets/                # Static Assets
│   ├── 🎨 css/              # Stylesheets
│   └── 📜 js/               # JavaScript Files
├── 📁 backup/               # ไฟล์สำรองข้อมูล
├── 📁 includes/             # ไฟล์ Include
│   ├── header.php
│   ├── footer.php
│   └── navbar.php
├── 📄 *.php                 # หน้าหลักของระบบ
├── 🗄️ database_now.sql     # ไฟล์ฐานข้อมูล
└── 📖 README.md            # ไฟล์นี้
```

**[📋 ดูโครงสร้างไฟล์แบบละเอียด →](PROJECT_STRUCTURE.md)**

---

## 🎨 **เทคโนโลยีที่ใช้**

### **Backend**
- **PHP** - ภาษาหลักในการพัฒนา
- **MySQL** - ฐานข้อมูลหลัก
- **MySQLi** - การเชื่อมต่อฐานข้อมูล

### **Frontend** 
- **HTML5** - โครงสร้างหน้าเว็บ
- **CSS3** - การจัดรูปแบบ
- **Bootstrap 5** - CSS Framework
- **JavaScript (ES6+)** - ฟังก์ชันการทำงาน
- **jQuery** - จัดการ DOM และ AJAX
- **Chart.js** - สร้างกราฟและชาร์ต
- **Font Awesome** - ไอคอน

### **เครื่องมือเพิ่มเติม**
- **DataTables** - ตารางข้อมูลแบบ Interactive
- **SweetAlert2** - การแจ้งเตือนที่สวยงาม
- **Moment.js** - จัดการวันที่และเวลา

---

## 📱 **หน้าจอหลักของระบบ**

| หน้า | คำอธิบาย | ไฟล์ |
|------|----------|------|
| 🏠 **หน้าแรก** | Dashboard และสถิติโครงการ | `index.php` |
| ➕ **เพิ่มโครงการ** | ฟอร์มเพิ่มโครงการใหม่ | `add_project.php` |
| 📋 **รายการโครงการ** | แสดงโครงการทั้งหมดแบบตาราง | `projects_list.php` |
| 🎯 **โครงการหลัก** | จัดการโครงการหลัก | `main_projects.php` |
| 📊 **Dashboard** | แดชบอร์ดแสดงกราฟ | `dashboard.php` |
| 📈 **Analytics** | วิเคราะห์ข้อมูลขั้นสูง | `analytics.php` |
| 📄 **รายงาน** | ระบบรายงานมาตรฐาน | `reports.php` |
| 🛠️ **Custom Report** | สร้างรายงานแบบกำหนดเอง | `custom_report.php` |
| ⚙️ **จัดการตัวชี้วัด** | ตั้งค่าตัวชี้วัดโครงการ | `manage_indicators.php` |
| 💾 **จัดการสำรอง** | Backup และ Restore | `backup_manager.php` |

---

## 🔧 **API Endpoints**

### **โครงการ (Projects)**
```
GET  /api/get_main_project.php      # ดึงข้อมูลโครงการหลัก
GET  /api/get_project_indicators.php # ดึงตัวชี้วัดโครงการ  
POST /api/save_project_indicators.php # บันทึกตัวชี้วัดโครงการ
```

### **ตัวชี้วัด (Indicators)**
```
GET  /api/get_indicators.php        # ดึงรายการตัวชี้วัด
GET  /api/get_indicator.php         # ดึงตัวชี้วัดแต่ละตัว
POST /api/save_indicator.php        # บันทึกตัวชี้วัด
DELETE /api/delete_indicator.php    # ลบตัวชี้วัด
```

### **พื้นที่ (Locations)**
```
GET /api/get_districts.php          # ดึงรายการอำเภอ
GET /api/get_subdistricts.php       # ดึงรายการตำบล
```

### **ฐานข้อมูล (Database)**
```
GET /api/get_tables.php             # ดึงรายการตาราง
POST /api/execute_query.php         # Execute SQL Query
```

---

## 🛠️ **การปรับแต่งและขยายระบบ**

### **เพิ่ม CSS ใหม่**
```php
// เพิ่มในไฟล์ assets/css/
<link rel="stylesheet" href="assets/css/your-style.css">
```

### **เพิ่ม JavaScript ใหม่**
```php
// เพิ่มในไฟล์ assets/js/
<script src="assets/js/your-script.js"></script>
```

### **เพิ่มหน้าใหม่**
1. สร้างไฟล์ PHP ใหม่
2. Include `navbar.php` และ `db.php`
3. เพิ่มลิงก์ในเมนู navbar

---

## 🚨 **การแก้ไขปัญหาที่พบบ่อย**

### **Navbar Dropdown ไม่ทำงาน**
- ตรวจสอบให้แน่ใจว่า Bootstrap JS โหลดเพียงครั้งเดียว
- ตรวจสอบ Console ใน Developer Tools

### **ฐานข้อมูลเชื่อมต่อไม่ได้**
- ตรวจสอบการตั้งค่าใน `db.php`
- ตรวจสอบว่า MySQL Server ทำงาน

### **ไฟล์ไม่พบ (404 Error)**
- ตรวจสอบ path ของไฟล์
- ตรวจสอบ permission ของไฟล์

---

## 🤝 **การมีส่วนร่วม**

เรายินดีรับการมีส่วนร่วมจากนักพัฒนาทุกท่าน!

1. **Fork** โปรเจค
2. สร้าง **Feature Branch** (`git checkout -b feature/amazing-feature`)
3. **Commit** การเปลี่ยนแปลง (`git commit -m 'Add amazing feature'`)
4. **Push** ไปยัง Branch (`git push origin feature/amazing-feature`)
5. เปิด **Pull Request**

---

## 📞 **ติดต่อและสนับสนุน**

- 🐛 **รายงานปัญหา**: [Issues](../../issues)
- 💡 **ขอ Feature ใหม่**: [Feature Requests](../../issues)
- 📧 **อีเมล**: your-email@example.com
- 📱 **เว็บไซต์**: https://your-website.com

---

## 📄 **License**

โปรเจคนี้อยู่ภายใต้ MIT License - ดูรายละเอียดได้ที่ [LICENSE](LICENSE) file.

---

<div align="center">

**สร้างด้วย ❤️ โดยทีมพัฒนา**

⭐ **ถ้าชอบโปรเจคนี้ กรุณาให้ Star ด้วยนะครับ!** ⭐

[![Stars](https://img.shields.io/github/stars/username/repo?style=social)](../../stargazers)
[![Forks](https://img.shields.io/github/forks/username/repo?style=social)](../../network/members)

</div>
