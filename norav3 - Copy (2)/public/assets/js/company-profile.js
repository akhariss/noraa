/**
 * Company Profile JavaScript
 * Notaris & PPAT Tracking System
 */

// Define APP_URL if not already defined
if (typeof APP_URL === 'undefined') {
    window.APP_URL = window.location.origin + '/newnota';
}

// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (header) {
        header.classList.toggle('scrolled', window.scrollY > 50);
    }
});

// Tracking Form Handler - SECURE: Search by nomor registrasi + verification
function initTrackingForm() {
    const trackingForm = document.getElementById('trackingForm');
    if (!trackingForm) return;
    
    trackingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById('trackingResult');
        
        if (!resultDiv) return;
        
        resultDiv.innerHTML = '<div class="loading">Memeriksa nomor registrasi...</div>';
        
        // Search by nomor registrasi
        fetch(APP_URL + '/index.php?gate=lacak', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                // Redirect to tracking page for verification
                window.location.href = APP_URL + '/index.php?gate=lacak';
            } else {
                const message = data.message || 'Nomor registrasi tidak ditemukan';
                resultDiv.innerHTML = `<div class="result-empty">${escapeHtml(message)}</div>`;
            }
        })
        .catch(error => {
            console.error('Tracking error:', error);
            resultDiv.innerHTML = '<div class="result-error">Terjadi kesalahan. Silakan coba lagi.</div>';
        });
    });
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.length > 1) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initTrackingForm();
    
    // Mobile menu toggle
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburgerBtn && navMenu) {
        hamburgerBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
        
        // Close menu when clicking a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
            });
        });
    }
});
