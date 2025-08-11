/**
 * Data Analytics Management System - Main JavaScript
 * Version 2.0.0
 * Enhanced with modern ES6+ features and better UX
 */

// Application namespace
const DataAnalytics = {
    config: {
        debug: true,
        apiEndpoint: '/api/',
        version: '2.0.0'
    },

    // Initialize application
    init() {
        console.log(`%c🚀 Data Analytics Management System v${this.config.version}`, 
            'color: #667eea; font-weight: bold; font-size: 16px;');
        
        this.setupGlobalEventListeners();
        this.initializeComponents();
        this.setupAjaxDefaults();
        this.log('Application initialized successfully', 'success');
    },

    // Enhanced logging function
    log(message, type = 'info', data = null) {
        if (!this.config.debug) return;

        const styles = {
            info: 'color: #17a2b8; font-weight: bold;',
            success: 'color: #28a745; font-weight: bold;',
            warning: 'color: #ffc107; font-weight: bold;',
            error: 'color: #dc3545; font-weight: bold;'
        };

        const icons = {
            info: 'ℹ️',
            success: '✅',
            warning: '⚠️',
            error: '❌'
        };

        console.log(`%c${icons[type]} ${message}`, styles[type]);
        if (data) {
            console.log(data);
        }
    },

    // Setup global event listeners
    setupGlobalEventListeners() {
        // Enhanced form validation
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('needs-validation')) {
                if (!e.target.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.showAlert('กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง', 'warning');
                }
                e.target.classList.add('was-validated');
            }
        });

        // Enhanced click handlers
        document.addEventListener('click', (e) => {
            // Confirm delete buttons
            if (e.target.classList.contains('btn-delete') || 
                e.target.closest('.btn-delete')) {
                e.preventDefault();
                this.confirmDelete(e.target);
            }

            // Data table actions
            if (e.target.classList.contains('btn-view') || 
                e.target.closest('.btn-view')) {
                e.preventDefault();
                this.handleView(e.target);
            }

            if (e.target.classList.contains('btn-edit') || 
                e.target.closest('.btn-edit')) {
                e.preventDefault();
                this.handleEdit(e.target);
            }
        });

        // Enhanced keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl + S to save (prevent default browser save)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const saveBtn = document.querySelector('.btn-save, [type="submit"]');
                if (saveBtn) {
                    saveBtn.click();
                    this.log('Save triggered via keyboard shortcut', 'info');
                }
            }

            // Escape to close modals
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    const modal = bootstrap.Modal.getInstance(openModal);
                    if (modal) {
                        modal.hide();
                    }
                }
            }
        });
    },

    // Initialize components
    initializeComponents() {
        this.initializeDataTables();
        this.initializeTooltips();
        this.initializeCharts();
        this.initializeFilters();
        this.setupFormEnhancements();
    },

    // Setup AJAX defaults
    setupAjaxDefaults() {
        // jQuery AJAX setup
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                beforeSend: (xhr, settings) => {
                    // Add CSRF token to all AJAX requests
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', token.getAttribute('content'));
                    }
                    
                    // Show loading indicator for longer requests
                    if (settings.showLoading !== false) {
                        this.showLoading();
                    }
                },
                complete: () => {
                    this.hideLoading();
                },
                error: (xhr, status, error) => {
                    this.log('AJAX Error: ' + error, 'error', xhr);
                    this.showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง', 'error');
                }
            });
        }
    },

    // Enhanced alert system using SweetAlert2
    showAlert(message, type = 'info', options = {}) {
        const config = {
            title: this.getAlertTitle(type),
            text: message,
            icon: type === 'error' ? 'error' : type === 'warning' ? 'warning' : 
                  type === 'success' ? 'success' : 'info',
            confirmButtonText: 'ตกลง',
            ...options
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire(config);
        } else {
            // Fallback to browser alert
            alert(`${this.getAlertTitle(type)}: ${message}`);
        }
    },

    getAlertTitle(type) {
        const titles = {
            success: 'สำเร็จ',
            error: 'ข้อผิดพลาด',
            warning: 'คำเตือน',
            info: 'ข้อมูล'
        };
        return titles[type] || 'แจ้งเตือน';
    },

    // Enhanced confirm dialog
    confirmDelete(element) {
        const itemName = element.dataset.name || 'รายการนี้';
        const action = element.dataset.action || element.getAttribute('href');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: `คุณต้องการลบ ${itemName} หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (action) {
                        window.location.href = action;
                    }
                }
            });
        } else {
            if (confirm(`คุณต้องการลบ ${itemName} หรือไม่?`)) {
                if (action) {
                    window.location.href = action;
                }
            }
        }
    },

    // Loading indicators
    showLoading(target = null) {
        const spinner = `
            <div class="d-flex justify-content-center align-items-center p-3" id="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
                <span class="ms-2">กำลังโหลด...</span>
            </div>
        `;

        if (target) {
            target.innerHTML = spinner;
        } else {
            // Show global loading overlay
            if (!document.getElementById('global-loading')) {
                const overlay = document.createElement('div');
                overlay.id = 'global-loading';
                overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
                overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
                overlay.style.zIndex = '9999';
                overlay.innerHTML = `
                    <div class="bg-white p-4 rounded shadow">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border text-primary me-3" role="status">
                                <span class="visually-hidden">กำลังโหลด...</span>
                            </div>
                            <span>กำลังดำเนินการ...</span>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
            }
        }
    },

    hideLoading(target = null) {
        if (target) {
            const spinner = target.querySelector('#loading-spinner');
            if (spinner) {
                spinner.remove();
            }
        } else {
            const globalLoading = document.getElementById('global-loading');
            if (globalLoading) {
                globalLoading.remove();
            }
        }
    },

    // Initialize DataTables with enhanced features
    initializeDataTables() {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('.data-table').each(function() {
                const table = $(this);
                const config = {
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
                    },
                    responsive: true,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    order: [[0, 'desc']],
                    columnDefs: [
                        {
                            targets: 'no-sort',
                            orderable: false
                        }
                    ],
                    drawCallback: function() {
                        // Reinitialize tooltips after table draw
                        DataAnalytics.initializeTooltips();
                    }
                };

                // Merge with custom configuration if exists
                const customConfig = table.data('config');
                if (customConfig) {
                    Object.assign(config, customConfig);
                }

                table.DataTable(config);
            });
        }
    },

    // Initialize tooltips
    initializeTooltips() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    },

    // Initialize charts (Chart.js integration)
    initializeCharts() {
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = 'Noto Sans Thai Looped';
            Chart.defaults.color = '#6c757d';
            
            // Initialize all charts
            document.querySelectorAll('.chart-canvas').forEach(canvas => {
                if (!canvas.chart) { // Prevent re-initialization
                    this.createChart(canvas);
                }
            });
        }
    },

    // Create individual chart
    createChart(canvas) {
        const config = JSON.parse(canvas.dataset.config || '{}');
        const defaultConfig = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        };

        const finalConfig = this.mergeDeep(defaultConfig, config);
        canvas.chart = new Chart(canvas, finalConfig);
    },

    // Initialize dynamic filters
    initializeFilters() {
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', (e) => {
                this.handleFilterChange(e.target);
            });
        });
    },

    // Handle filter changes
    handleFilterChange(select) {
        const filterGroup = select.closest('.filter-group');
        if (filterGroup) {
            const dependentSelects = filterGroup.querySelectorAll('.dependent-filter');
            dependentSelects.forEach(dependent => {
                if (dependent !== select) {
                    this.loadFilterOptions(dependent, select.value);
                }
            });
        }
    },

    // Load filter options via AJAX
    loadFilterOptions(select, parentValue) {
        const endpoint = select.dataset.endpoint;
        if (!endpoint) return;

        this.showLoading(select.parentElement);

        fetch(`${endpoint}?parent=${parentValue}`)
            .then(response => response.json())
            .then(data => {
                this.populateSelect(select, data);
                this.hideLoading(select.parentElement);
            })
            .catch(error => {
                this.log('Error loading filter options', 'error', error);
                this.hideLoading(select.parentElement);
            });
    },

    // Populate select element with options
    populateSelect(select, data) {
        const defaultOption = select.querySelector('option[value=""]');
        select.innerHTML = '';
        
        if (defaultOption) {
            select.appendChild(defaultOption);
        }

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.value;
            option.textContent = item.text;
            select.appendChild(option);
        });
    },

    // Setup form enhancements
    setupFormEnhancements() {
        // Auto-save functionality
        document.querySelectorAll('.auto-save').forEach(form => {
            let timeoutId;
            form.addEventListener('input', () => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    this.autoSaveForm(form);
                }, 2000);
            });
        });

        // Real-time validation
        document.querySelectorAll('.validate-real-time input, .validate-real-time select').forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });
    },

    // Auto-save form data
    autoSaveForm(form) {
        const formData = new FormData(form);
        formData.append('auto_save', '1');

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showSaveIndicator('บันทึกอัตโนมัติแล้ว');
            }
        })
        .catch(error => {
            this.log('Auto-save failed', 'error', error);
        });
    },

    // Show save indicator
    showSaveIndicator(message) {
        const indicator = document.createElement('div');
        indicator.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
        indicator.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(indicator);

        setTimeout(() => {
            if (indicator.parentElement) {
                indicator.remove();
            }
        }, 3000);
    },

    // Validate individual field
    validateField(field) {
        const isValid = field.checkValidity();
        field.classList.toggle('is-valid', isValid);
        field.classList.toggle('is-invalid', !isValid);

        // Show custom error message
        const errorDiv = field.parentElement.querySelector('.invalid-feedback');
        if (errorDiv && !isValid) {
            errorDiv.textContent = field.validationMessage;
        }
    },

    // Handle view action
    handleView(element) {
        const url = element.dataset.url || element.getAttribute('href');
        if (url) {
            window.open(url, '_blank');
        }
    },

    // Handle edit action
    handleEdit(element) {
        const url = element.dataset.url || element.getAttribute('href');
        if (url) {
            window.location.href = url;
        }
    },

    // Utility function to deep merge objects
    mergeDeep(target, source) {
        const output = Object.assign({}, target);
        if (this.isObject(target) && this.isObject(source)) {
            Object.keys(source).forEach(key => {
                if (this.isObject(source[key])) {
                    if (!(key in target))
                        Object.assign(output, { [key]: source[key] });
                    else
                        output[key] = this.mergeDeep(target[key], source[key]);
                } else {
                    Object.assign(output, { [key]: source[key] });
                }
            });
        }
        return output;
    },

    // Check if value is object
    isObject(item) {
        return item && typeof item === 'object' && !Array.isArray(item);
    },

    // Format number with Thai locale
    formatNumber(number, decimals = 0) {
        return new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    },

    // Format currency
    formatCurrency(amount, showSymbol = true) {
        const formatted = this.formatNumber(amount, 2);
        return showSymbol ? `${formatted} บาท` : formatted;
    },

    // Copy text to clipboard
    copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                this.showAlert('คัดลอกไปยังคลิปบอร์ดแล้ว', 'success');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showAlert('คัดลอกไปยังคลิปบอร์ดแล้ว', 'success');
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    DataAnalytics.init();
});

// Global functions for backward compatibility
function showAlert(message, type = 'info') {
    DataAnalytics.showAlert(message, type);
}

function confirmDelete(element) {
    DataAnalytics.confirmDelete(element);
}

function formatNumber(number, decimals = 0) {
    return DataAnalytics.formatNumber(number, decimals);
}

function formatCurrency(amount, showSymbol = true) {
    return DataAnalytics.formatCurrency(amount, showSymbol);
}
