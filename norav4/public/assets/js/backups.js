/**
 * Backup Management JavaScript
 * Notaris & PPAT Tracking System
 */

// Create Backup
function createBackup(type) {
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('type', type);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');
    
    const messageDiv = document.getElementById('backupMessage');
    if (messageDiv) {
        messageDiv.style.display = 'block';
        messageDiv.className = 'form-message';
        messageDiv.textContent = 'Sedang membuat backup...';
    }
    
    fetch(APP_URL + '/index.php?gate=backups', {
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
                }, 2000);
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
}

// Delete Backup
function deleteBackup(filename) {
    if (!confirm('Apakah Anda yakin ingin menghapus backup "' + filename + '"?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('filename', filename);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');
    
    const messageDiv = document.getElementById('backupMessage');
    
    fetch(APP_URL + '/index.php?gate=backups', {
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
}
