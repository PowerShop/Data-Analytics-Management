<?php
// กำหนดค่าคงที่สำหรับระบบ
define('SYSTEM_ACCESS', true);

// รวมไฟล์หลักของระบบ
require_once '_sys/_api.php';

// ตรวจสอบการ login (ถ้าต้องการ)
// checkAuth();

?>
<!--
    Data Analytics Management System v2.0
    Modern PHP Project Structure with Enhanced UX/UI
    
    Features:
    - Bootstrap 5.3.2 (Latest)
    - Font Awesome 6.5.1 (Latest)
    - jQuery 3.7.1 (Latest)
    - SweetAlert2 11.10.1 (Latest)
    - Chart.js 4.4.1 (Latest)
    - DataTables 1.13.7 (Latest)
    - Modern CSS with CSS Custom Properties
    - Enhanced JavaScript with ES6+ features
    - Responsive Design with Mobile-First Approach
    - Accessibility Features (ARIA, Screen Reader Support)
    - Security Enhancements (CSRF Protection, XSS Prevention)
-->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ระบบจัดการข้อมูลการวิเคราะห์โครงการเพื่อการพัฒนาชุมชนอย่างยั่งยืน">
    <meta name="keywords" content="โครงการ, การวิเคราะห์, ชุมชน, การพัฒนา">
    <meta name="author" content="Data Analytics Management Team">
    
    <title><?= isset($title) ? $title . ' - ' . $_config['app_name'] : $_config['app_name'] ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('_dist/img/favicon.ico') ?>">
    
    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Google Fonts - Noto Sans Thai Looped (Optimized) -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS 5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Font Awesome 6.5.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous">
    
    <!-- DataTables CSS 1.13.7 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <!-- SweetAlert2 CSS 11.10.1 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="_dist/css/custom.css">
    
    <!-- Critical CSS for above-the-fold content -->
    <style>
        /* Critical CSS - Inline for performance */
        body { 
            font-family: 'Noto Sans Thai Looped', sans-serif; 
            background-color: #f8fafc;
            line-height: 1.6;
        }
        .loading-screen { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            display: flex; justify-content: center; align-items: center;
            z-index: 9999; color: white; flex-direction: column;
        }
        .spinner-border {
            margin-top: 1rem;
        }
    </style>
    
    <!-- jQuery 3.7.1 (Loaded early for compatibility) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="text-center">
            <i class="fas fa-chart-line fa-3x mb-3"></i>
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">กำลังโหลด...</span>
            </div>
            <h5>กำลังโหลดระบบ...</h5>
            <p class="mb-0"><?= $_config['app_name'] ?></p>
        </div>
    </div>

    <!-- Skip to main content for accessibility -->
    <a class="visually-hidden-focusable" href="#main-content">ข้ามไปยังเนื้อหาหลัก</a>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));" role="navigation">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="new_index.php">
                <i class="fas fa-chart-line me-2"></i>
                <span class="d-none d-md-inline"><?= $_config['app_name'] ?></span>
                <span class="d-md-none">DAM</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="เปิด/ปิดเมนู">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="new_index.php" aria-label="หน้าแรก">
                            <i class="fas fa-home me-1"></i>หน้าแรก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="new_index.php?page=dashboard" aria-label="แดชบอร์ด">
                            <i class="fas fa-tachometer-alt me-1"></i>แดชบอร์ด
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" 
                           aria-expanded="false" aria-label="เมนูโครงการ">
                            <i class="fas fa-folder me-1"></i>โครงการ
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="new_index.php?page=projects"><i class="fas fa-list me-2"></i>รายการโครงการ</a></li>
                            <li><a class="dropdown-item" href="new_index.php?page=add_project"><i class="fas fa-plus me-2"></i>เพิ่มโครงการใหม่</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="new_index.php?page=manage_indicators"><i class="fas fa-tasks me-2"></i>จัดการตัวชี้วัด</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" 
                           aria-expanded="false" aria-label="เมนูวิเคราะห์ข้อมูล">
                            <i class="fas fa-chart-bar me-1"></i>วิเคราะห์ข้อมูล
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="new_index.php?page=analytics"><i class="fas fa-chart-line me-2"></i>กราฟแผนภูมิ</a></li>
                            <li><a class="dropdown-item" href="new_index.php?page=reports"><i class="fas fa-file-alt me-2"></i>รายงาน</a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text">
                            <i class="fas fa-clock me-1"></i>
                            <span id="current-time"><?= date('H:i') ?></span>
                        </span>
                    </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false" aria-label="เมนูผู้ใช้">
                                <img src="<?= asset('_dist/img/default-avatar.png') ?>" alt="Avatar" 
                                     class="rounded-circle me-2" width="24" height="24">
                                <span class="d-none d-md-inline"><?= $_SESSION['admin_name'] ?? 'ผู้ใช้' ?></span>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" role="main" class="py-4">
        <?php
            // Set timezone from configuration
            date_default_timezone_set(config('app_timezone'));

            // Handle page routing
            if (isset($_GET['page'])) {
                $page = sanitizeInput($_GET['page']);
                includePage($page);
            } else {
                redirect('?page=home');
            }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6><?= config('app_name') ?></h6>
                    <p class="text-muted mb-0">
                        ระบบจัดการข้อมูลการวิเคราะห์โครงการเพื่อการพัฒนาชุมชนอย่างยั่งยืน
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">
                        <strong>เวอร์ชัน:</strong> <?= config('app_version') ?>
                    </p>
                    <p class="text-muted mb-0">
                        © <?= date('Y') ?> สงวนลิขสิทธิ์
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button type="button" class="btn btn-primary position-fixed bottom-0 end-0 m-3 rounded-circle" 
            id="backToTop" style="display: none; width: 50px; height: 50px; z-index: 1000;" 
            data-bs-toggle="tooltip" data-bs-placement="left" title="กลับไปด้านบน">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript Libraries -->
    <!-- Bootstrap JS 5.3.2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Chart.js 4.4.1 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.js"></script>
    
    <!-- DataTables JS 1.13.7 -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 JS 11.10.1 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="_dist/js/app.js"></script>

    <!-- Page-specific JavaScript -->
    <script>
        // Global configuration
        window.APP_CONFIG = {
            name: '<?= $_config['app_name'] ?>',
            version: '<?= $_config['app_version'] ?>',
            debug: <?= $_config['app_debug'] ? 'true' : 'false' ?>,
            baseUrl: '<?= url() ?>'
        };
    </script>

        // Back to top functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });

        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Service Worker registration (for PWA features)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= asset("sw.js") ?>')
                    .then(function(registration) {
                        DataAnalytics.log('Service Worker registered successfully', 'success');
                    })
                    .catch(function(error) {
                        DataAnalytics.log('Service Worker registration failed', 'error', error);
                    });
            });
        }
    </script>

    <!-- Performance monitoring (optional) -->
    <script>
        // Monitor page load performance
        window.addEventListener('load', function() {
            if (window.performance && window.performance.timing) {
                const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
                DataAnalytics.log(`Page loaded in ${loadTime}ms`, 'info');
                
                // Send to analytics if needed
                // analytics.track('page_load_time', { duration: loadTime });
            }
        });
    </script>
</body>
</html>
