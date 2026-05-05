/**
 * Users Management JavaScript
 * Notaris & PPAT Tracking System
 */

// Create User Form
const createUserForm = document.getElementById('createUserForm');
if (createUserForm) {
    createUserForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('formMessage');
        
        fetch(APP_URL + '/index.php?gate=users', {
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
                        window.location.reload();
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

// Delete User
function deleteUser(userId, username) {
    if (!confirm('Apakah Anda yakin ingin menghapus user "' + username + '"?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('user_id', userId);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');
    
    fetch(APP_URL + '/index.php?gate=users', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('formMessage');
        if (messageDiv) {
            messageDiv.style.display = 'block';
            messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
            messageDiv.textContent = data.message;
            
            if (data.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        }
    })
    .catch(error => {
        const messageDiv = document.getElementById('formMessage');
        if (messageDiv) {
            messageDiv.style.display = 'block';
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    });
}
