/**
 * Registrasi JavaScript
 * Notaris & PPAT Tracking System
 */

// Create Registrasi Form
const createRegistrasiForm = document.getElementById('createRegistrasiForm');
if (createRegistrasiForm) {
    createRegistrasiForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const messageDiv = document.getElementById('formMessage');

        fetch(APP_URL + '/index.php?gate=registrasi_store', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (messageDiv) {
                messageDiv.style.display = 'block';
                messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
                messageDiv.textContent = data.message;

                if (data.success) {
                    setTimeout(() => {
                        window.location.href = APP_URL + '/index.php?gate=registrasi';
                    }, 1500);
                }
            }
        })
        .catch(error => {
            if (messageDiv) {
                messageDiv.style.display = 'block';
                messageDiv.className = 'form-message error';
                messageDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        });
    });
}

// Mobile: Toggle details on row click
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('#registrasiTable tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't toggle if clicking on link or button
            if (e.target.tagName === 'A' || e.target.closest('a') || e.target.tagName === 'BUTTON') {
                return;
            }
            
            // Toggle active class
            this.classList.toggle('active');
            
            // Toggle mobile details visibility
            const mobileDetails = this.querySelector('.mobile-details');
            if (mobileDetails) {
                if (this.classList.contains('active')) {
                    mobileDetails.style.display = 'block';
                } else {
                    mobileDetails.style.display = 'none';
                }
            }
        });
    });
});
