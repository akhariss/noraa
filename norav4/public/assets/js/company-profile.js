/**
 * Company Profile JavaScript - Nora V4 (Exact Mirror V3)
 * Notaris & PPAT Tracking System
 */

// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (header) {
        header.classList.toggle('scrolled', window.scrollY > 50);
    }
});

// Tracking Form Handler
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
                // Redirect to tracking page
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

// Escape HTML
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

// Smooth scroll
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
    const closeBtn = document.getElementById('close-sidebar-btn');
    const navMenu = document.getElementById('nav-menu');
    const overlay = document.getElementById('sidebar-overlay');
    
    const openMenu = () => {
        if (navMenu) navMenu.classList.add('active');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    };

    const closeMenu = () => {
        if (navMenu) navMenu.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    if (hamburgerBtn) hamburgerBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);

    if (navMenu) {
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    }

    // --- REVEAL ANIMATION ---
    const revealCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    };

    const revealObserver = new IntersectionObserver(revealCallback, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.reveal').forEach(el => {
        revealObserver.observe(el);
    });
});
