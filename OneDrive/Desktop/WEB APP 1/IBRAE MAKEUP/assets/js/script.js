// Custom JavaScript for User Management System

// Document ready function
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize modals
    var modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Form validation
    initFormValidation();
    
    // Confirm delete actions
    initDeleteConfirmation();
    
    // File upload preview
    initFileUploadPreview();
    
    // Search functionality
    initSearch();
    
    // Session timeout warning
    initSessionTimeout();
});

// Form validation
function initFormValidation() {
    // Custom validation for forms with .needs-validation class
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Password confirmation validation
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (passwordField && confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
    }
    
    // Username validation
    const usernameField = document.getElementById('username');
    if (usernameField) {
        usernameField.addEventListener('input', function() {
            const username = this.value;
            const usernameRegex = /^[a-zA-Z0-9_.-]{3,50}$/;
            
            if (!usernameRegex.test(username)) {
                this.setCustomValidity('Username must be 3-50 characters and contain only letters, numbers, dots, hyphens, and underscores');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Email validation
    const emailFields = document.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        field.addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    });
}

// Delete confirmation
function initDeleteConfirmation() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        
        const url = $(this).attr('href');
        const itemName = $(this).data('name') || 'this item';
        
        if (confirm(`Are you sure you want to delete ${itemName}? This action cannot be undone.`)) {
            window.location.href = url;
        }
    });
}

// File upload preview
function initFileUploadPreview() {
    $('#profile_image').on('change', function() {
        const file = this.files[0];
        const preview = $('#image-preview');
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.attr('src', e.target.result).show();
            };
            
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    });
}

// Search functionality
function initSearch() {
    $('#search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        const searchTarget = $(this).data('target') || '.searchable-row';
        
        $(searchTarget).each(function() {
            const text = $(this).text().toLowerCase();
            
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Update results counter if exists
        const visibleRows = $(searchTarget + ':visible').length;
        $('#search-results-count').text(visibleRows + ' results found');
    });
}

// Session timeout warning
function initSessionTimeout() {
    // Show warning 5 minutes before session expires
    const warningTime = 55 * 60 * 1000; // 55 minutes in milliseconds
    
    setTimeout(function() {
        showSessionWarning();
    }, warningTime);
}

function showSessionWarning() {
    const modal = `
        <div class="modal fade" id="sessionWarningModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Session Expiring Soon
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>Your session will expire in 5 minutes. Do you want to extend your session?</p>
                        <div class="text-center">
                            <div id="countdown" class="h4 text-warning">05:00</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Logout</button>
                        <button type="button" class="btn btn-primary" onclick="extendSession()">Extend Session</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#sessionWarningModal').modal('show');
    
    // Start countdown
    startCountdown();
    
    // Auto logout when modal is dismissed without extending
    $('#sessionWarningModal').on('hidden.bs.modal', function() {
        window.location.href = 'logout.php';
    });
}

function startCountdown() {
    let timeLeft = 300; // 5 minutes in seconds
    
    const countdown = setInterval(function() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        $('#countdown').text(
            (minutes < 10 ? '0' : '') + minutes + ':' + 
            (seconds < 10 ? '0' : '') + seconds
        );
        
        timeLeft--;
        
        if (timeLeft < 0) {
            clearInterval(countdown);
            window.location.href = 'logout.php';
        }
    }, 1000);
}

function extendSession() {
    // Make AJAX call to extend session
    $.ajax({
        url: 'extend_session.php',
        method: 'POST',
        success: function(response) {
            $('#sessionWarningModal').modal('hide');
            showAlert('Session extended successfully', 'success');
        },
        error: function() {
            showAlert('Failed to extend session', 'error');
        }
    });
}

// Utility functions
function showAlert(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'success' ? 'alert-success' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container').first().prepend(alert);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').first().fadeOut('slow');
    }, 5000);
}

function showLoading(button) {
    const originalText = button.html();
    button.data('original-text', originalText);
    button.html('<span class="spinner"></span> Loading...').prop('disabled', true);
}

function hideLoading(button) {
    const originalText = button.data('original-text');
    button.html(originalText).prop('disabled', false);
}

// AJAX form submission
function submitFormAjax(formId, successCallback) {
    $(formId).on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        showLoading(submitBtn);
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method') || 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading(submitBtn);
                
                if (response.success) {
                    showAlert(response.message, 'success');
                    if (successCallback) successCallback(response);
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function() {
                hideLoading(submitBtn);
                showAlert('An error occurred. Please try again.', 'error');
            }
        });
    });
}

// Data table functionality
function initDataTable(tableId, options = {}) {
    const defaultOptions = {
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[0, 'asc']],
        responsive: true,
        searching: true,
        paging: true,
        info: true
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    if ($.fn.DataTable) {
        $(tableId).DataTable(finalOptions);
    }
}

// Export functionality
function exportTable(format, filename = 'export') {
    const table = document.querySelector('.table');
    
    if (format === 'csv') {
        exportToCSV(table, filename);
    } else if (format === 'pdf') {
        exportToPDF(table, filename);
    }
}

function exportToCSV(table, filename) {
    const csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    
    downloadLink.download = filename + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Enhanced UI Features
$(document).ready(function() {
    // Add loading states to buttons
    initLoadingStates();
    
    // Add smooth scrolling to anchors
    initSmoothScrolling();
    
    // Add form auto-save functionality
    initAutoSave();
    
    // Add keyboard shortcuts
    initKeyboardShortcuts();
    
    // Add theme toggle functionality
    initThemeToggle();
    
    // Add enhanced notifications
    initNotifications();
    
    // Add table enhancements
    initTableEnhancements();
});

// Loading states for buttons
function initLoadingStates() {
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.html('<span class="spinner me-2"></span>Processing...')
                .prop('disabled', true);
        
        // Reset after 30 seconds in case of issues
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
        }, 30000);
    });
}

// Smooth scrolling
function initSmoothScrolling() {
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 800);
        }
    });
}

// Auto-save functionality
function initAutoSave() {
    let autoSaveTimer;
    
    $('form textarea, form input[type="text"]').on('input', function() {
        clearTimeout(autoSaveTimer);
        
        autoSaveTimer = setTimeout(() => {
            const formData = $(this).closest('form').serialize();
            const formId = $(this).closest('form').attr('id');
            
            if (formId) {
                localStorage.setItem('autosave_' + formId, formData);
                showNotification('Draft saved automatically', 'info', 2000);
            }
        }, 3000);
    });
    
    // Restore auto-saved data
    $('form[id]').each(function() {
        const formId = $(this).attr('id');
        const savedData = localStorage.getItem('autosave_' + formId);
        
        if (savedData) {
            const urlParams = new URLSearchParams(savedData);
            
            urlParams.forEach((value, key) => {
                const field = $(this).find(`[name="${key}"]`);
                if (field.length && !field.val()) {
                    field.val(value);
                }
            });
        }
    });
}

// Keyboard shortcuts
function initKeyboardShortcuts() {
    $(document).on('keydown', function(e) {
        // Ctrl+S to save form
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            $('form:visible').first().submit();
        }
        
        // Ctrl+N for new item
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            $('.btn-primary[href*="add"], .btn-primary[href*="new"]').first().click();
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
    });
}

// Theme toggle
function initThemeToggle() {
    const theme = localStorage.getItem('theme') || 'light';
    $('body').attr('data-theme', theme);
    
    $('.theme-toggle').on('click', function() {
        const currentTheme = $('body').attr('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        $('body').attr('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        showNotification(`Switched to ${newTheme} theme`, 'success', 2000);
    });
}

// Enhanced notifications
function initNotifications() {
    // Create notification container if it doesn't exist
    if (!$('#notification-container').length) {
        $('body').append('<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }
}

function showNotification(message, type = 'info', duration = 5000) {
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show notification-item" 
             style="margin-bottom: 10px; min-width: 300px; animation: slideInRight 0.3s ease;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('#notification-container').append(notification);
    
    if (duration > 0) {
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, duration);
    }
}

// Table enhancements
function initTableEnhancements() {
    // Add row selection
    $('.table tbody tr').on('click', function(e) {
        if (!$(e.target).is('button, a, input')) {
            $(this).toggleClass('table-active');
        }
    });
    
    // Add column sorting
    $('.table th[data-sort]').css('cursor', 'pointer').on('click', function() {
        const table = $(this).closest('table');
        const column = $(this).data('sort');
        const order = $(this).hasClass('sort-asc') ? 'desc' : 'asc';
        
        // Remove previous sort indicators
        table.find('th').removeClass('sort-asc sort-desc');
        $(this).addClass('sort-' + order);
        
        // Sort table rows
        const rows = table.find('tbody tr').get();
        
        rows.sort((a, b) => {
            const aVal = $(a).find(`td[data-sort="${column}"]`).text().trim();
            const bVal = $(b).find(`td[data-sort="${column}"]`).text().trim();
            
            if (order === 'asc') {
                return aVal.localeCompare(bVal, undefined, { numeric: true });
            } else {
                return bVal.localeCompare(aVal, undefined, { numeric: true });
            }
        });
        
        table.find('tbody').empty().append(rows);
    });
    
    // Add row hover effects with data preview
    $('.table tbody tr').hover(
        function() {
            $(this).find('.btn-group').addClass('show');
        },
        function() {
            $(this).find('.btn-group').removeClass('show');
        }
    );
}

// Advanced search functionality
function initAdvancedSearch() {
    $('.advanced-search-toggle').on('click', function() {
        $('.advanced-search-panel').slideToggle();
    });
    
    $('.search-filter').on('change', function() {
        applyFilters();
    });
}

function applyFilters() {
    const filters = {};
    
    $('.search-filter').each(function() {
        const filterName = $(this).attr('name');
        const filterValue = $(this).val();
        
        if (filterValue) {
            filters[filterName] = filterValue.toLowerCase();
        }
    });
    
    $('.searchable-row').each(function() {
        let showRow = true;
        
        Object.keys(filters).forEach(filterName => {
            const cellValue = $(this).find(`[data-filter="${filterName}"]`).text().toLowerCase();
            
            if (!cellValue.includes(filters[filterName])) {
                showRow = false;
            }
        });
        
        $(this).toggle(showRow);
    });
    
    // Update results counter
    const visibleRows = $('.searchable-row:visible').length;
    $('#filter-results-count').text(`${visibleRows} results found`);
}

// Form field animations
function initFieldAnimations() {
    $('.form-control, .form-select').on('focus', function() {
        $(this).closest('.form-group').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-group').removeClass('focused');
    });
}

// Add CSS for animations
const animationCSS = `
    <style>
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .table tbody tr.table-active {
            background-color: rgba(99, 102, 241, 0.1) !important;
        }
        
        .table th.sort-asc::after {
            content: ' ↑';
            color: var(--primary-color);
        }
        
        .table th.sort-desc::after {
            content: ' ↓';
            color: var(--primary-color);
        }
        
        .form-group.focused .form-label {
            color: var(--primary-color);
            transform: scale(0.95);
        }
        
        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-primary: #e5e7eb;
            --card-bg: #2d3748;
            --border-color: #4a5568;
        }
        
        [data-theme="dark"] body {
            background: var(--bg-color);
            color: var(--text-primary);
        }
        
        [data-theme="dark"] .card {
            background: var(--card-bg);
            border-color: var(--border-color);
        }
    </style>
`;

$('head').append(animationCSS);
