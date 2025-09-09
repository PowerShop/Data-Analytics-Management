<?php
// เริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการ login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: portal/');
    exit();
}

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    // บันทึก logout log ถ้าเชื่อมต่อฐานข้อมูลได้
    if (isset($_SESSION['use_database']) && $_SESSION['use_database'] && isset($_SESSION['admin_user_id'])) {
        include_once 'backend/includes/user_functions.php';
        $db_files = ['database/db.php', './db.php'];
        foreach ($db_files as $db_file) {
            if (file_exists($db_file)) {
                try {
                    include $db_file;
                    if (isset($conn) && $conn->ping()) {
                        logActivity($conn, $_SESSION['admin_user_id'], 'LOGOUT', 'User logged out');
                        break;
                    }
                } catch (Exception $e) {
                    // ไม่ต้อง error ถ้า log ไม่ได้
                }
            }
        }
    }
    
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<link rel="icon" type="image/x-icon" href="favicon.ico">
<nav class="navbar navbar-expand-lg navbar-dark sticky-top"
    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fas fa-user-shield me-2"></i>
            ระบบจัดการโครงการ
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            แดชบอร์ด
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'projects_table_view.php') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=admin-projects">
                            <i class="fas fa-table me-1"></i>
                            รายงานโครงการ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>"
                            href="charts/index.php">
                            <i class="fas fa-chart-bar me-1"></i>
                            กราฟและแผนภูมิ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'user_guide.php') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=user-guide">
                            <i class="fas fa-chart-bar me-1"></i>
                            คู่มือการใช้งาน
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'chart_builder.php') ? 'active' : ''; ?>" href="chart_builder.php">
                        <i class="fas fa-magic me-1"></i>
                        ตัวสร้างกราฟขั้นสูง
                    </a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'backend') ? 'active' : ''; ?>"
                            href="/kittisak/routes/?redirect=backend">
                            <i class="fas fa-cog me-1"></i>
                            หลังบ้าน
                        </a>
                    </li>
                    <!-- <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'user_management.php') ? 'active' : ''; ?>"
                            href="user_management.php">
                            <i class="fas fa-users me-1"></i>
                            จัดการผู้ใช้
                        </a>
                    </li>
                    <?php endif; ?> -->
                    <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="otherDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-h me-1"></i>
                        อื่น ๆ
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'user_guide.php') ? 'active' : ''; ?>" href="user_guide.php">
                                <i class="fas fa-book me-2"></i>
                                คู่มือการใช้งาน
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-muted disabled" href="#">
                                <i class="fas fa-info-circle me-2"></i>
                                เพิ่มเติมในอนาคต
                            </a>
                        </li>
                    </ul>
                </li> -->
                </ul>

                <ul class="navbar-nav">
                    <!-- System Status Button -->
                    <li class="nav-item">
                        <button class="btn btn-outline-light me-2" id="systemStatusBtn" onclick="showSystemStatus()" 
                                title="ตรวจสอบสถานะระบบ" data-bs-toggle="tooltip">
                            <i class="fas fa-server me-1"></i>
                            <span class="status-indicator" id="statusIndicator">
                                <i class="fas fa-circle text-success"></i>
                            </span>
                        </button>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?php echo $_SESSION['admin_username']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="dashboard.php">
                                    <i class="fas fa-home me-2"></i>
                                    หน้าหลัก
                                </a>
                            </li>
                            <!-- <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin'): ?>
                            <li>
                                <a class="dropdown-item" href="user_management.php">
                                    <i class="fas fa-users me-2"></i>
                                    จัดการผู้ใช้
                                </a>
                            </li>
                            <?php endif; ?> -->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="?logout=1">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    .navbar-nav .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
    }

    .navbar-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .dropdown-item.active {
        background: #667eea;
        color: white;
    }

    /* System Status Styles */
    #systemStatusBtn {
        border-radius: 20px;
        padding: 6px 12px;
        transition: all 0.3s ease;
        position: relative;
    }

    #systemStatusBtn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .status-indicator {
        font-size: 0.8em;
        animation: pulse 2s infinite;
    }

    .status-indicator .fa-circle {
        font-size: 0.7em;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    /* Status Modal Styles */
    .status-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid #28a745;
    }

    .status-card.warning {
        border-left-color: #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    }

    .status-card.error {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    }

    .status-card.info {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    }

    .status-value {
        font-weight: 600;
        font-size: 1.1em;
    }

    .status-label {
        font-size: 0.9em;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .progress-custom {
        height: 8px;
        border-radius: 4px;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }
</style>

<!-- SweetAlert2 for System Status -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Start monitoring system status
        startSystemMonitoring();
    });

    // System status monitoring
    let systemStatus = {
        database: { status: 'unknown', latency: 0 },
        network: { status: 'unknown', latency: 0 },
        memory: { used: 0, total: 0 },
        session: { status: 'unknown', expires: 0 }
    };

    function startSystemMonitoring() {
        // Check status every 30 seconds
        setInterval(checkSystemStatus, 30000);
        // Initial check
        checkSystemStatus();
    }

    async function checkSystemStatus() {
        try {
            // ใช้ absolute path แทน relative path
            const baseUrl = window.location.origin + '/Data-Analytics';
            const response = await fetch(`api/system_status_api.php`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                if (response.status === 404) {
                    console.warn('System status API not found (404)');
                    updateStatusIndicator('error');
                    return;
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success) {
                systemStatus = {
                    database: {
                        status: data.database.status === 'healthy' ? 'connected' : 'error',
                        latency: data.response_time_ms
                    },
                    network: {
                        status: 'connected', // Assume connected if API responds
                        latency: data.response_time_ms
                    },
                    session: {
                        status: data.session.status === 'active' ? 'active' : 'expired',
                        expires: data.session.status === 'active' ? 3600 : 0 // Default 1 hour
                    },
                    overall: data.status
                };

                // Update status indicator based on overall status
                updateStatusIndicator(data.status === 'healthy' ? 'success' : 
                                    data.status === 'warning' ? 'warning' : 'error');
            } else {
                console.error('API returned error:', data.message);
                updateStatusIndicator('error');
            }

        } catch (error) {
            console.error('System status check failed:', error);
            updateStatusIndicator('error');
        }
    }

    function updateStatusIndicator(overrideStatus = null) {
        const indicator = document.getElementById('statusIndicator');
        const icon = indicator.querySelector('.fa-circle');

        let status = overrideStatus;
        if (!status) {
            // Determine overall status
            if (systemStatus.database.status === 'error' || systemStatus.network.status === 'error') {
                status = 'error';
            } else if (systemStatus.database.status === 'unknown' || systemStatus.network.status === 'unknown') {
                status = 'warning';
            } else {
                status = 'success';
            }
        }

        // Remove all status classes
        icon.classList.remove('text-success', 'text-warning', 'text-danger');

        // Add appropriate status class
        switch (status) {
            case 'success':
                icon.classList.add('text-success');
                break;
            case 'warning':
                icon.classList.add('text-warning');
                break;
            case 'error':
                icon.classList.add('text-danger');
                break;
        }
    }

    // Show system status modal
    async function showSystemStatus() {
        // Show loading modal first
        Swal.fire({
            title: 'กำลังตรวจสอบสถานะระบบ...',
            html: '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: 2000
        });

        try {
            // Fetch comprehensive system status
            const baseUrl = window.location.origin + '/Data-Analytics';
            const response = await fetch(`api/system_status_api.php`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('ไม่สามารถตรวจสอบสถานะได้ในขณะนี้ โปรดลองใหม่ในภายหลัง');
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch system status');
            }

            // Get memory usage (approximate)
            const memoryUsage = performance.memory ?
                {
                    used: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024),
                    total: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024),
                    limit: Math.round(performance.memory.jsHeapSizeLimit / 1024 / 1024)
                } : null;

            // Get page load time
            const loadTime = performance.timing ?
                performance.timing.loadEventEnd - performance.timing.navigationStart : 0;

            // Create status HTML - Simplified for general users
            let statusHtml = `
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-center mb-3">
                                <i class="fas fa-server text-primary me-2"></i>
                                สถานะระบบ
                            </h5>
                            <div class="text-center mb-3">
                                <small class="text-muted">
                                    อัปเดตล่าสุด: ${data.timestamp} | 
                                    เวลาตอบสนอง: ${data.response_time_ms}ms
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Database Status -->
                        <div class="col-md-6">
                            <div class="status-card ${data.database.status === 'healthy' ? '' : data.database.status === 'warning' ? 'warning' : 'error'}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-database fa-2x me-3 ${data.database.status === 'healthy' ? 'text-success' : data.database.status === 'warning' ? 'text-warning' : 'text-danger'}"></i>
                                    <div class="flex-grow-1">
                                        <div class="status-label">ฐานข้อมูล</div>
                                        <div class="status-value">
                                            ${data.database.status === 'healthy' ? 'เชื่อมต่อปกติ' : 
                                              data.database.status === 'warning' ? 'มีคำเตือน' : 'มีปัญหา'}
                                        </div>
                                        <small class="text-muted">
                                            ${data.database.tables_count} ตาราง, ${data.database.views_count} view
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session Status -->
                        <div class="col-md-6">
                            <div class="status-card ${data.session.status === 'active' ? '' : 'warning'}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-clock fa-2x me-3 ${data.session.status === 'active' ? 'text-success' : 'text-warning'}"></i>
                                    <div class="flex-grow-1">
                                        <div class="status-label">Session ผู้ใช้</div>
                                        <div class="status-value">
                                            ${data.session.status === 'active' ? 'ใช้งานได้' : 'ไม่ได้ใช้งาน'}
                                        </div>
                                        <small class="text-muted">
                                            ${data.session.info && data.session.info.admin_logged_in ? 'เข้าสู่ระบบแล้ว' : 'ไม่ได้เข้าสู่ระบบ'}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Performance -->
                        <div class="col-md-6">
                            <div class="status-card info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tachometer-alt fa-2x me-3 text-info"></i>
                                    <div class="flex-grow-1">
                                        <div class="status-label">ประสิทธิภาพ</div>
                                        <div class="status-value">
                                            ${loadTime > 0 ? loadTime + 'ms' : 'N/A'}
                                        </div>
                                        <small class="text-muted">
                                            เวลาโหลดหน้าเว็บ
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PHP Version -->
                        <div class="col-md-6">
                            <div class="status-card">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-code fa-2x me-3 text-primary"></i>
                                    <div class="flex-grow-1">
                                        <div class="status-label">PHP Version</div>
                                        <div class="status-value">
                                            ${data.system.php_version}
                                        </div>
                                        <small class="text-muted">
                                            Memory: ${data.system.memory_limit}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Status Summary -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="status-card ${data.status === 'healthy' ? 'success' : data.status === 'warning' ? 'warning' : 'error'}">
                                <div class="text-center">
                                    <div class="status-label mb-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        สรุปสถานะระบบ
                                    </div>
                                    <div class="status-value">
                                        ${data.status === 'healthy' ? 
                                            '<span class="text-success"><i class="fas fa-check-circle me-2"></i>ระบบทำงานปกติ</span>' : 
                                            data.status === 'warning' ? 
                                            '<span class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>มีคำเตือน</span>' : 
                                            '<span class="text-danger"><i class="fas fa-times-circle me-2"></i>มีปัญหา</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Show status modal
            Swal.fire({
                html: statusHtml,
                showConfirmButton: true,
                confirmButtonText: 'ปิด',
                width: '600px',
                customClass: {
                    popup: 'status-modal',
                    content: 'status-modal-content'
                },
                didOpen: () => {
                    // Add custom styles for the modal
                    const style = document.createElement('style');
                    style.textContent = `
                        .status-modal .swal2-html-container {
                            padding: 1rem;
                        }
                        .status-modal .row {
                            margin: 0;
                        }
                        .status-modal .status-card {
                            margin-bottom: 0.5rem;
                        }
                        @media (max-width: 768px) {
                            .status-modal {
                                width: 90% !important;
                            }
                        }
                    `;
                    document.head.appendChild(style);
                }
            });

        } catch (error) {
            console.error('Failed to load system status:', error);
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถโหลดข้อมูลสถานะระบบ',
                text: error.message || 'เกิดข้อผิดพลาดในการเชื่อมต่อ โปรดลองใหม่ในภายหลัง',
                confirmButtonText: 'ปิด'
            });
        }
    }
</script>