# ระบบ URL Routing สำหรับ Data Analytics Management

## การติดตั้งและใช้งาน

### 1. โครงสร้างไฟล์ที่เพิ่มเข้ามา:
- `router.php` - ไฟล์หลักสำหรับจัดการ routing
- `helpers.php` - ฟังก์ชันช่วยเหลือสำหรับ URL management
- `logout.php` - ไฟล์สำหรับออกจากระบบ
- `404.php` - หน้าแสดงเมื่อไม่พบหน้าที่ต้องการ
- `.htaccess` - อัพเดทการตั้งค่า URL rewriting

### 2. URL Routes ที่สามารถใช้งานได้:

#### หน้าหลัก
- `/` หรือ `/home` → `index.php`

#### การจัดการผู้ใช้
- `/login` → `login.php`
- `/logout` → `logout.php`

#### การจัดการโครงการ
- `/projects` → `projects_list.php`
- `/projects/list` → `projects_list.php`
- `/projects/add` → `add_project.php`
- `/projects/edit` → `edit_project.php`
- `/projects/detail` → `project_detail.php`
- `/projects/delete` → `delete_project.php`
- `/projects/view` → `projects_table_view.php`
- `/projects/stats` → `projects_table_stats.php`
- `/projects/data` → `projects_table_data.php`
- `/projects/export` → `export_projects_table_detailed_xlsx.php`

#### อื่นๆ
- `/main-projects` → `main_projects.php`
- `/indicators` → `manage_indicators.php`
- `/dashboard` → `dashboard.php`
- `/analytics` → `analytics.php`
- `/reports/custom` → `custom_report.php`

#### เครื่องมือ
- `/tools/find-replace` → `admin_find_replace.php`
- `/tools/duplicate-fix` → `fix_duplicate_indicators.php`

#### Admin Panel
- `/admin` → `admin/index.php`
- `/admin/dashboard` → `admin/dashboard.php`
- `/admin/login` → `admin/login.php`
- `/admin/projects` → `admin/projects_table_view.php`
- `/admin/charts` → `admin/charts/charts.php`

### 3. คุณสมบัติ:

#### URL Redirects
- URL เก่าที่ใช้ .php จะถูก redirect ไปยัง clean URLs อัตโนมัติ
- เช่น `index.php` → `/home`, `login.php` → `/login`

#### Authentication
- ระบบตรวจสอบสิทธิ์อัตโนมัติ
- Protected routes ต้อง login ก่อน
- Admin routes ต้องมีสิทธิ์ admin

#### Error Handling
- หน้า 404 สวยงามสำหรับ URL ที่ไม่มีอยู่
- การจัดการ session timeout

#### Helper Functions
- `url($route)` - สร้าง URL
- `redirect($route)` - Redirect
- `is_current_route($route)` - ตรวจสอบ route ปัจจุบัน
- `nav_active($route)` - CSS class สำหรับ navigation

### 4. การใช้งานใน Code:

#### สร้าง Links
```php
<a href="<?= url('projects/add') ?>">เพิ่มโครงการ</a>
<a href="<?= url('projects/edit', ['id' => 123]) ?>">แก้ไข</a>
```

#### Redirect
```php
redirect('projects/list');
redirect('login');
```

#### Navigation Active Class
```php
<a class="nav-link <?= nav_active('projects/add') ?>" href="/projects/add">
    เพิ่มโครงการ
</a>
```

#### ตรวจสอบ Route ปัจจุบัน
```php
<?php if (is_current_route('projects/add')): ?>
    <p>กำลังเพิ่มโครงการใหม่</p>
<?php endif; ?>
```

### 5. Security Features:
- CSRF protection
- File access blocking
- Session management
- Permission checking

### 6. การทดสอบ:
ทดสอบ URLs เหล่านี้:
- `http://localhost/Data-Analytics-Management/home`
- `http://localhost/Data-Analytics-Management/projects/add`
- `http://localhost/Data-Analytics-Management/login`
- `http://localhost/Data-Analytics-Management/admin`

### 7. Migration จาก URLs เก่า:
URLs เก่าทั้งหมดจะทำงานต่อไปได้ แต่จะถูก redirect ไปยัง clean URLs อัตโนมัติ:
- `index.php` → `/home`
- `add_project.php` → `/projects/add`
- `projects_list.php` → `/projects`
- และอื่นๆ

ระบบนี้ช่วยให้:
✅ URLs สะอาดและอ่านง่าย
✅ SEO friendly
✅ Backward compatible
✅ Security enhanced
✅ Easy maintenance
