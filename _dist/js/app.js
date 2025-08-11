/**
 * Data Analytics Management System v2.0
 * Custom JavaScript for Enhanced User Experience
 */

// การตั้งค่าพื้นฐาน
const App = {
    config: {
        loadingDelay: 800,
        animationDuration: 300,
        toastTimeout: 3000
    },
    
    // เริ่มต้นระบบ
    init: function() {
        this.hideLoadingScreen();
        this.initAnimations();
        this.initTooltips();
        this.initClock();
        this.initSmoothScroll();
        this.initFormValidation();
        this.initDataTables();
        this.initCharts();
        this.logSystemInit();
    },
    
    // ซ่อนหน้าจอโหลด
    hideLoadingScreen: function() {
        setTimeout(() => {
            const loadingScreen = document.getElementById('loadingScreen');
            if (loadingScreen) {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, this.config.animationDuration);
            }
        }, this.config.loadingDelay);
    },
    
    // เริ่มต้น animations
    initAnimations: function() {
        // Intersection Observer สำหรับ animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // สังเกต elements ที่ต้องการ animate
        document.querySelectorAll('.stats-card, .content-section').forEach(el => {
            observer.observe(el);
        });
    },
    
    // เริ่มต้น tooltips
    initTooltips: function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },
    
    // นาฬิกาแบบ real-time
    initClock: function() {
        const clockElement = document.getElementById('current-time');
        if (clockElement) {
            setInterval(() => {
                const now = new Date();
                const timeString = now.toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                clockElement.textContent = timeString;
            }, 1000);
        }
    },
    
    // การเลื่อนอย่างนุ่มนวล
    initSmoothScroll: function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    },
    
    // การตรวจสอบฟอร์ม
    initFormValidation: function() {
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    this.showToast('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
                }
                form.classList.add('was-validated');
            });
        });
    },
    
    // เริ่มต้น DataTables
    initDataTables: function() {
        if (typeof DataTable !== 'undefined') {
            $('.data-table').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
                },
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 'no-sort' }
                ]
            });
        }
    },
    
    // เริ่มต้น Charts
    initCharts: function() {
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = 'Noto Sans Thai Looped';
            Chart.defaults.color = '#64748b';
            Chart.defaults.borderColor = '#e2e8f0';
        }
    },
    
    // แสดง Toast notification
    showToast: function(message, type = 'info', duration = null) {
        const toastContainer = this.getToastContainer();
        const toastId = 'toast-' + Date.now();
        const iconMap = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-triangle',
            warning: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="${iconMap[type]} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            delay: duration || this.config.toastTimeout
        });
        
        toast.show();
        
        // ลบ toast หลังจากแสดงเสร็จ
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    },
    
    // สร้าง Toast container
    getToastContainer: function() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    },
    
    // ยืนยันการลบ
    confirmDelete: function(message = 'คุณต้องการลบข้อมูลนี้หรือไม่?', callback = null) {
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    },
    
    // แสดงการโหลด
    showLoading: function(element = null) {
        if (element) {
            element.classList.add('loading');
            element.disabled = true;
        }
    },
    
    // ซ่อนการโหลด
    hideLoading: function(element = null) {
        if (element) {
            element.classList.remove('loading');
            element.disabled = false;
        }
    },
    
    // Log การเริ่มต้นระบบ
    logSystemInit: function() {
        console.log('%c🚀 Data Analytics Management System v2.0', 
            'color: #2563eb; font-size: 16px; font-weight: bold;');
        console.log('%cระบบเริ่มต้นเรียบร้อยแล้ว', 
            'color: #10b981; font-size: 14px;');
        console.log('%c⏰ เวลา:', 'color: #f59e0b; font-weight: bold;', new Date().toLocaleString('th-TH'));
    }
};

// Utility functions
const Utils = {
    // Format ตัวเลข
    formatNumber: function(num, decimals = 0) {
        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(num);
    },
    
    // Format วันที่
    formatDate: function(date, options = {}) {
        const defaultOptions = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        return new Date(date).toLocaleDateString('th-TH', { ...defaultOptions, ...options });
    },
    
    // ตรวจสอบ email
    isValidEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    // สร้าง UUID
    generateUUID: function() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },
    
    // ดาวน์โหลดไฟล์
    downloadFile: function(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

// เริ่มต้นระบบเมื่อ DOM พร้อม
document.addEventListener('DOMContentLoaded', function() {
    App.init();
});

// Global functions สำหรับใช้งานใน templates
window.showToast = App.showToast.bind(App);
window.confirmDelete = App.confirmDelete.bind(App);
window.showLoading = App.showLoading.bind(App);
window.hideLoading = App.hideLoading.bind(App);
window.Utils = Utils;
