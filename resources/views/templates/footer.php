            </div>
        </main>
    </div>

    <!-- Define APP_URL for JavaScript -->
    <script>
        window.APP_URL = '<?= APP_URL ?>';
    </script>

    <script src="<?= APP_URL ?>/public/assets/js/main.js"></script>
    <?php if (!empty($pageScript)): ?>
    <script src="<?= APP_URL ?>/public/assets/js/<?= $pageScript ?>?v=<?= time() ?>"></script>
    <?php endif; ?>

    <script>
    // Toggle sidebar for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('sidebar-open');
            overlay.classList.toggle('overlay-active');
        }
    }

    // Close sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar && overlay) {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('overlay-active');
            }
        }
    });

    // Toggle CMS submenu
    function toggleCmsSubmenu(e) {
        if (e) e.preventDefault();
        const submenu = document.getElementById('cmsSubmenu');
        const chevron = document.getElementById('cmsSubmenuChevron');
        if (!submenu) return;
        
        if (submenu.style.display === 'none' || submenu.style.display === '') {
            submenu.style.display = 'flex';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            submenu.style.display = 'none';
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }

    // Set initial chevron state
    document.addEventListener('DOMContentLoaded', () => {
        const submenu = document.getElementById('cmsSubmenu');
        const chevron = document.getElementById('cmsSubmenuChevron');
        if (submenu && chevron && submenu.style.display === 'flex') {
            chevron.style.transform = 'rotate(180deg)';
        }
    });
    </script>

    <!-- Session Timeout Warning Modal (guidesop.md Pillar 6.3) -->
    <div id="sessionTimeoutModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div style="
            background: #fefefe;
            margin: 15% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        ">
            <div style="text-align: center; margin-bottom: 20px;">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2" style="margin-bottom: 15px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <h3 style="color: var(--primary); margin: 0 0 10px 0;">Session Akan Berakhir</h3>
                <p id="sessionTimeoutMessage" style="color: var(--text); margin: 0; font-size: 14px;">
                    Session Anda akan berakhir dalam <strong id="timeoutCountdown">5</strong> menit.
                </p>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 25px;">
                <button id="btnStayLoggedIn" type="button" style="
                    background: var(--primary);
                    color: var(--gold);
                    border: none;
                    padding: 12px 30px;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                " onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='var(--primary)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    Tetap Login
                </button>
                <button id="btnLogout" type="button" style="
                    background: var(--danger);
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                " onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='var(--danger)'">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Session Timeout JavaScript -->
    <script>
    (function() {
        // Session timeout configuration (must match PHP session.gc_maxlifetime)
        const SESSION_TIMEOUT = 7200 * 1000; // 2 hours in milliseconds
        const WARNING_BEFORE = 300 * 1000;   // 5 minutes before timeout
        
        let sessionTimer;
        let warningTimer;
        let countdownInterval;
        let lastActivity = Date.now();
        let isWarningShown = false;
        
        // Check if timeout warning is in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('timeout') === '1') {
            alert('Session Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        // Only start timer if user is logged in
        function startSessionTimers() {
            if (typeof APP_URL === 'undefined') return;
            
            clearTimeout(sessionTimer);
            clearTimeout(warningTimer);
            clearInterval(countdownInterval);
            
            isWarningShown = false;
            lastActivity = Date.now();
            
            // Show warning 5 minutes before timeout
            warningTimer = setTimeout(() => {
                showSessionWarning();
            }, SESSION_TIMEOUT - WARNING_BEFORE);
            
            // Force logout after timeout
            sessionTimer = setTimeout(() => {
                forceLogout();
            }, SESSION_TIMEOUT);
        }
        
        // Show session warning modal
        function showSessionWarning() {
            if (isWarningShown) return;
            isWarningShown = true;
            
            const modal = document.getElementById('sessionTimeoutModal');
            const countdownEl = document.getElementById('timeoutCountdown');
            
            if (modal && countdownEl) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                
                let secondsLeft = 300; // 5 minutes
                countdownEl.textContent = Math.floor(secondsLeft / 60);
                
                countdownInterval = setInterval(() => {
                    secondsLeft -= 1000;
                    const minutes = Math.floor(secondsLeft / 60000);
                    const seconds = Math.floor((secondsLeft % 60000) / 1000);
                    
                    if (minutes > 0) {
                        countdownEl.textContent = minutes;
                    } else {
                        countdownEl.textContent = seconds + ' detik';
                    }
                    
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        forceLogout();
                    }
                }, 1000);
            }
        }
        
        // Hide session warning modal
        function hideSessionWarning() {
            const modal = document.getElementById('sessionTimeoutModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
            clearInterval(countdownInterval);
        }
        
        // Refresh session (AJAX call)
        function refreshSession() {
            if (typeof APP_URL === 'undefined') return;
            
            fetch(APP_URL + '/index.php?gate=refresh_session')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hideSessionWarning();
                        startSessionTimers();
                    } else {
                        forceLogout();
                    }
                })
                .catch(error => {
                    console.error('Session refresh error:', error);
                    forceLogout();
                });
        }
        
        // Force logout
        function forceLogout() {
            hideSessionWarning();
            window.location.href = APP_URL + '/index.php?gate=logout';
        }
        
        // Update last activity on user interaction
        function updateActivity() {
            lastActivity = Date.now();
            if (isWarningShown) {
                refreshSession();
            }
        }
        
        // Event listeners for user activity
        document.addEventListener('DOMContentLoaded', function() {
            startSessionTimers();
            
            document.addEventListener('click', updateActivity);
            document.addEventListener('keypress', updateActivity);
            document.addEventListener('mousemove', updateActivity);
            document.addEventListener('scroll', updateActivity);
            
            const btnStay = document.getElementById('btnStayLoggedIn');
            if (btnStay) {
                btnStay.addEventListener('click', refreshSession);
            }
            
            const btnLogout = document.getElementById('btnLogout');
            if (btnLogout) {
                btnLogout.addEventListener('click', forceLogout);
            }
        });
    })();
    </script>
</body>
</html>
