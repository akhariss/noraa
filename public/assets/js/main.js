/**
 * Main JavaScript
 * Notaris & PPAT Tracking System
 */

// Debug: Check if APP_URL is defined
console.log('[Session] Initialized with APP_URL:', typeof APP_URL !== 'undefined' ? 'OK' : 'UNDEFINED');

// Modal functions
function showCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'flex';
}

function closeCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'none';
    document.getElementById('createUserForm').reset();
}

function closeCMSModal() {
    document.getElementById('editCMSModal').style.display = 'none';
}

// Close modal on outside click
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});

// Form message helper
function showFormMessage(elementId, message, isSuccess) {
    const el = document.getElementById(elementId);
    if (el) {
        el.style.display = 'block';
        el.className = 'form-message ' + (isSuccess ? 'success' : 'error');
        el.textContent = message;
        
        setTimeout(() => {
            el.style.display = 'none';
        }, 5000);
    }
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Confirm action
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// AJAX helper with session expiration detection
async function ajaxRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    if (data) {
        if (method === 'POST' || method === 'PUT' || method === 'DELETE') {
            options.body = JSON.stringify(data);
        }
    }
    
    try {
        const response = await fetch(url, options);
        
        // Check for session expiration (401 Unauthorized) or access denied (403 Forbidden)
        if (response.status === 401 || response.status === 403) {
            // Session expired or user lost access - redirect to login
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }
        
        // Check for other HTTP errors
        if (!response.ok) {
            console.error('HTTP Error:', response.status, response.statusText);
            return { success: false, message: 'Terjadi kesalahan: ' + response.statusText };
        }
        
        const jsonResponse = await response.json();
        
        // Also check JSON response for session expiration message
        if (jsonResponse && jsonResponse.message && 
            (jsonResponse.message.includes('Session expired') || 
             jsonResponse.message.includes('Unauthorized'))) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }
        
        return jsonResponse;
    } catch (error) {
        console.error('AJAX Request Error:', error);
        return { success: false, message: 'Gagal mengirim permintaan' };
    }
}

// Form submission helper with session expiration detection
async function submitForm(formId, url) {
    const form = document.getElementById(formId);
    if (!form) return null;
    
    const formData = new FormData(form);
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        // Check for session expiration
        if (response.status === 401 || response.status === 403) {
            window.location.href = APP_URL + '/index.php?gate=login&expired=1';
            return null;
        }
        
        if (!response.ok) {
            console.error('HTTP Error:', response.status, response.statusText);
            return { success: false, message: 'Terjadi kesalahan: ' + response.statusText };
        }
        
        return response.json();
    } catch (error) {
        console.error('Form Submit Error:', error);
        return { success: false, message: 'Gagal mengirim formulir' };
    }
}

// Initialize tooltips (Universal theme-matched support)
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    // Global dismiss
    document.addEventListener('click', (e) => {
        if (!e.target.closest('[data-tooltip]')) {
            document.querySelectorAll('.nora-tooltip').forEach(t => t.remove());
            tooltipElements.forEach(el => el._tooltip = null);
        }
    });

    window.addEventListener('scroll', () => {
        document.querySelectorAll('.nora-tooltip').forEach(t => t.remove());
        tooltipElements.forEach(el => el._tooltip = null);
    }, {passive: true});

    tooltipElements.forEach(el => {
        let hideTimeout;
        let lastShowTime = 0;
        
        const showTooltip = () => {
            clearTimeout(hideTimeout);
            if (el._tooltip) return;
            
            lastShowTime = Date.now();
            
            // Remove others
            document.querySelectorAll('.nora-tooltip').forEach(t => t.remove());
            tooltipElements.forEach(otherEl => otherEl._tooltip = null);
            
            const tooltip = document.createElement('div');
            tooltip.className = 'nora-tooltip';
            tooltip.textContent = el.getAttribute('data-tooltip');
            
            // Professional Nora 2.0 Theme Styling
            tooltip.style.cssText = `
                position: absolute;
                background: var(--primary);
                color: var(--white);
                padding: 10px 14px;
                border: 1px solid var(--gold);
                border-radius: 6px;
                font-family: inherit;
                font-size: 13px;
                line-height: 1.5;
                font-weight: 500;
                max-width: 250px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 99999;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s ease;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = el.getBoundingClientRect();
            let topPos = rect.top + window.scrollY - tooltip.offsetHeight - 12;
            let leftPos = rect.left + window.scrollX + (rect.width - tooltip.offsetWidth) / 2;
            
            if (topPos < window.scrollY + 10) topPos = rect.bottom + window.scrollY + 12;
            if (leftPos < 10) leftPos = 10;
            if (leftPos + tooltip.offsetWidth > window.innerWidth - 10) leftPos = window.innerWidth - tooltip.offsetWidth - 10;
            
            tooltip.style.top = topPos + 'px';
            tooltip.style.left = leftPos + 'px';
            
            requestAnimationFrame(() => {
                tooltip.style.opacity = '1';
            });
            
            el._tooltip = tooltip;
        };

        const hideTooltip = () => {
            if (el._tooltip) {
                const t = el._tooltip;
                t.style.opacity = '0';
                hideTimeout = setTimeout(() => { if (t.parentNode) t.remove(); }, 200);
                el._tooltip = null;
            }
        };

        // Desktop Hover
        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);
        
        // Tap Handling (Solves mobile simulated-event bugs)
        el.addEventListener('click', (e) => {
            e.stopPropagation(); 
            // Anti-flash logic: if shown via simulated mouseenter in the last 300ms, ignore the fast-following click event
            if (Date.now() - lastShowTime < 300) return;
            
            if (el._tooltip) {
                hideTooltip();
            } else {
                showTooltip();
            }
        });
    });
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initTooltips();

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.form-message');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    });
    
    // Session keep-alive: refresh session every 10 minutes if user is logged in
    // This prevents session expiration during long periods of inactivity
    if (typeof APP_URL !== 'undefined') {
        // Check if we're on a protected page (not login page)
        const urlParams = new URLSearchParams(window.location.search);
        const gate = urlParams.get('gate');
        
        if (gate && gate !== 'login' && gate !== 'logout' && gate !== 'home' && gate !== 'lacak') {
            // Session keep-alive: refresh session every 10 minutes
            setInterval(function() {
                fetch(APP_URL + '/index.php?gate=refresh_session', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        // Session expired, redirect to login
                        window.location.href = APP_URL + '/index.php?gate=login&expired=1';
                    }
                })
                .catch(error => console.warn('[Session Keep-Alive] Error:', error));
            }, 600000); // 10 minutes
        }
    }
});

// Toggle Sidebar for Mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (sidebar && overlay) {
        sidebar.classList.toggle('sidebar-open');
        overlay.classList.toggle('overlay-active');
        
        // Prevent body scroll when sidebar is open
        if (sidebar.classList.contains('sidebar-open')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

// Close sidebar when clicking on nav item (mobile)
document.addEventListener('DOMContentLoaded', function() {
    const sidebarNavItems = document.querySelectorAll('.sidebar-nav .nav-item');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebarNavItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('overlay-active');
                document.body.style.overflow = '';
            }
        });
    });
});

// Toggle Submenu (for CMS Editor)
function toggleSubmenu(element) {
    // Find the submenu following this nav-item
    const navSubmenu = element.nextElementSibling;
    
    if (navSubmenu && navSubmenu.classList.contains('nav-submenu')) {
        navSubmenu.classList.toggle('active');
        element.classList.toggle('active');
    }
}
