<div class="container text-center py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="content-section">
                <!-- 404 Error Icon -->
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
                </div>
                
                <!-- Error Message -->
                <h1 class="display-4 text-primary-custom mb-3">404</h1>
                <h2 class="h4 mb-3">ไม่พบหน้าที่ค้นหา</h2>
                <p class="text-muted mb-4">
                    ขออภัย หน้าที่คุณกำลังมองหาไม่มีอยู่ในระบบ 
                    อาจเป็นเพราะลิงก์ที่คุณคลิกมีปัญหา หรือหน้านั้นถูกย้ายไปที่อื่นแล้ว
                </p>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="?page=home" class="btn btn-primary me-md-2">
                        <i class="fas fa-home me-2"></i>กลับหน้าแรก
                    </a>
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                        <i class="fas fa-arrow-left me-2"></i>ย้อนกลับ
                    </button>
                </div>
                
                <!-- Help Links -->
                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-muted">หรือลองเข้าไปที่:</h6>
                    <div class="row g-2 mt-2">
                        <div class="col-md-4">
                            <a href="?page=dashboard" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-tachometer-alt me-1"></i>แดชบอร์ด
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?page=projects" class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-folder me-1"></i>โครงการ
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?page=analytics" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-chart-bar me-1"></i>วิเคราะห์ข้อมูล
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Log 404 error for analytics
DataAnalytics.log('404 Error: Page not found', 'warning', {
    url: window.location.href,
    referrer: document.referrer,
    timestamp: new Date().toISOString()
});
</script>
