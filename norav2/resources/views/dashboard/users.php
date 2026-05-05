<?php
/**
 * Users Management View - Super Admin First
 */

$currentUser = getCurrentUser();
$pageTitle = 'User Management';
$activePage = 'users';

require VIEWS_PATH . '/templates/header.php';
?>

<style>
/* CSS Grid Auto-Fit for Actions */
.users-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-input {
    width: 100%;
    padding: 0 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    height: 44px;
    transition: all 0.3s ease;
    background: var(--cream);
}
.search-input:focus {
    background: var(--white);
    border-color: var(--gold);
    outline: none;
    box-shadow: 0 0 0 3px rgba(156, 124, 56, 0.1);
}

.role-select {
    width: 100%;
    padding: 0 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    height: 44px;
    background: var(--cream);
    cursor: pointer;
    transition: all 0.3s ease;
}
.role-select:focus {
    background: var(--white);
    border-color: var(--gold);
    outline: none;
}

.action-buttons {
    display: flex;
    gap: 12px;
    height: 44px;
}

.btn-refresh {
    width: 44px;
    height: 44px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--white);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    flex-shrink: 0;
    color: var(--text-muted);
}
.btn-refresh:hover { 
    background: var(--cream); 
    color: var(--primary);
    border-color: var(--primary);
}

.btn-add-user {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 20px;
    height: 44px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: var(--gold);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    flex: 1;
    box-shadow: 0 4px 15px rgba(27, 58, 75, 0.2);
}
.btn-add-user:hover { 
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(27, 58, 75, 0.3);
}

/* Premium Card wrapper for Filters */
.users-action-card {
    background: var(--white);
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    margin-bottom: 10px;
    border: 1px solid rgba(156, 124, 56, 0.1);
}

/* Hero Section Adaptation */
.users-hero {
    background: linear-gradient(145deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 10px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.users-hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    width: 100%;
}

.users-badge {
    display: inline-block;
    background: rgba(156, 124, 56, 0.2);
    color: var(--gold-light);
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 8px;
    border: 1px solid rgba(156, 124, 56, 0.4);
}

.users-hero-subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .users-hero-title {
        font-size: 22px;
    }
    .users-action-card {
        padding: 14px;
    }
    
    /* Paksa 2 kolom di HP, ringkas vertikal */
    .users-filter-grid { 
        grid-template-columns: 1fr 1fr;
        gap: 10px; 
    }
    
    /* Pencarian membentang penuh di atas (Baris 1) */
    .filter-group:nth-child(1) { 
        grid-column: span 2; 
    }
    
    .mobile-hide-label { display: none !important; }
    
    /* Rampingkan ukuran input & tombol untuk layar sentuh optimal */
    .search-input, .role-select, .action-buttons, .btn-refresh, .btn-add-user {
        height: 38px;
        font-size: 13px;
    }
    .filter-label {
        font-size: 10px;
        margin-bottom: -2px;
    }
}
</style>

<!-- Premium Hero Header -->
<div class="users-hero">
    <div class="users-hero-content">
        <span class="users-badge">User Management</span>
        <p class="users-hero-subtitle">Kelola akses pengguna, tingkat privilese, dan administrator sistem</p>
    </div>
</div>

<!-- Fluid Grid Filter Card -->
<div class="users-action-card">
    <div class="users-filter-grid">
        <!-- Grid Item 1: Pencarian -->
        <div class="filter-group">
            <label for="searchUser" class="filter-label">Pencarian</label>
            <input type="text" id="searchUser" class="search-input" placeholder="Cari nama atau username..." onkeyup="searchUser()">
        </div>
        
        <!-- Grid Item 2: Filter Role -->
        <div class="filter-group">
            <label for="filterRole" class="filter-label">Filter Hak Akses</label>
            <select id="filterRole" class="role-select" onchange="filterUser()">
                <option value="">Semua Role</option>
                <option value="administrator">👑 Administrator</option>
                <option value="staff">👤 Staff</option>
            </select>
        </div>
        
        <!-- Grid Item 3: Aksi -->
        <div class="filter-group">
            <span class="filter-label mobile-hide-label" style="opacity: 0; display: block;">Aksi</span> <!-- Invisible spacer to push buttons down on desktop -->
            <div class="action-buttons">
                <button type="button" onclick="location.reload()" class="btn-refresh" title="Refresh Tabel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                </button>
                <button type="button" onclick="openAddUserModal()" class="btn-add-user">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah User</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
            <table class="data-table" id="userTable" style="
                width: 100%;
                min-width: 800px;
                border-collapse: collapse;
                font-size: 13px;
            ">
                <thead>
                    <tr style="background: var(--cream);">
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600;">Username</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600;">Role</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600;" class="hide-mobile">Dibuat</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600;" class="hide-mobile">Terakhir Update</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        $isCurrentUser = ($u['id'] == ($currentUser['user_id'] ?? 0));
                        $isOwner = ($u['role'] === ROLE_OWNER);
                        $roleLabel = ROLE_LABELS[$u['role']] ?? $u['role'];
                    ?>
                    <tr data-role="<?= $u['role'] ?>" data-username="<?= strtolower($u['username']) ?>" style="border-bottom: 1px solid var(--border); <?= $isOwner ? 'background: rgba(156,124,56,0.05);' : '' ?>">
                        <td style="padding: 12px 14px; font-weight: 600; color: var(--primary);" data-label="Username">
                            <span style="display: block;"><?= htmlspecialchars($u['name'] ?? $u['username']) ?></span>
                            <span style="display: block; font-size: 11px; color: var(--text-muted); font-weight: 400;">@<?= htmlspecialchars($u['username']) ?></span>
                        </td>
                        <td style="padding: 12px 14px;" data-label="Role">
                            <span class="badge" style="
                                display: inline-block;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 11px;
                                font-weight: 600;
                                background: <?= $isOwner ? 'var(--gold)' : '#17a2b8' ?>;
                                color: <?= $isOwner ? '#000' : '#fff' ?>;
                            ">
                                <?= $isOwner ? '👑 ' : '👤 ' ?><?= $roleLabel ?>
                            </span>
                        </td>
                        <td style="padding: 12px 14px; color: var(--text);" data-label="Dibuat" class="hide-mobile"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td style="padding: 12px 14px; color: var(--text);" data-label="Terakhir Update" class="hide-mobile"><?= date('d M Y', strtotime($u['updated_at'])) ?></td>
                        <td style="padding: 12px 14px;" data-label="Aksi">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <?php if (!$isCurrentUser): ?>
                                    <button type="button" onclick="openEditUserModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>', '<?= $u['role'] ?>')" class="btn-sm" style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 4px;
                                        padding: 6px 12px;
                                        background: var(--primary);
                                        color: var(--gold);
                                        border-radius: 6px;
                                        font-size: 13px;
                                        font-weight: 600;
                                        border: none;
                                        cursor: pointer;
                                        transition: background-color 0.2s;
                                    " onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='var(--primary)'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button type="button" onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" class="btn-sm" style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 4px;
                                        padding: 6px 12px;
                                        background: #dc3545;
                                        color: white;
                                        border-radius: 6px;
                                        font-size: 13px;
                                        font-weight: 600;
                                        border: none;
                                        cursor: pointer;
                                        transition: background-color 0.2s;
                                    " onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 6px 12px; border-radius: 6px; background: #e2e8f0; color: #64748b; font-size: 13px; font-weight: 600;">User saat ini</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal [REMOVED DUPLICATE] -->

<!-- Custom Confirm Modal -->
<div id="confirmModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
<!-- Confirm Modal (Delete) -->
<div id="confirmModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--white); border-radius: 12px; padding: 32px; max-width: 450px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 48px; height: 48px; background: #fee; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">Konfirmasi Hapus</h3>
        </div>
        <p id="confirmMessage" style="margin: 0 0 24px 0; color: var(--text); font-size: 14px; line-height: 1.6;"></p>
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" onclick="closeConfirmModal()" style="background: var(--cream); color: var(--text); padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">Batal</button>
            <button type="button" id="confirmYesBtn" style="background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9998; align-items: center; justify-content: center;">
    <div style="background: var(--white); border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">➕ Tambah User Baru</h3>
            <button type="button" onclick="closeAddUserModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <form id="addUserForm">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <div style="margin-bottom: 20px;">
                <label for="new_name" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Nama Lengkap</label>
                <input type="text" id="new_name" name="name" required placeholder="Ahmad Notaris, S.H." style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 20px;">
                <label for="new_username" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Username</label>
                <input type="text" id="new_username" name="username" required placeholder="Contoh: ahmad_n" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 20px;">
                <label for="new_password" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Password</label>
                <input type="password" id="new_password" name="password" required minlength="6" autocomplete="new-password" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 24px;">
                <label for="new_role" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Role</label>
                <select id="new_role" name="role" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
                    <option value="staff">👤 Staff</option>
                    <option value="administrator">👑 Administrator</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeAddUserModal()" style="background: var(--cream); color: var(--text); padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="background: var(--primary); color: var(--gold); padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9998; align-items: center; justify-content: center;">
    <div style="background: var(--white); border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; color: var(--primary); font-size: 18px;">✏️ Edit User</h3>
            <button type="button" onclick="closeEditUserModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <form id="editUserForm">
            <input type="hidden" id="edit_user_id" name="user_id">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <div style="margin-bottom: 20px;">
                <label for="edit_name" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Nama Lengkap</label>
                <input type="text" id="edit_name" name="name" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 20px;">
                <label for="edit_username" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Username</label>
                <input type="text" id="edit_username" name="username" required autocomplete="username" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 20px;">
                <label for="edit_password" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Password Baru (Opsional)</label>
                <input type="password" id="edit_password" name="password" minlength="6" autocomplete="new-password" placeholder="Kosongkan jika tidak berubah" style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 24px;">
                <label for="edit_role" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text); font-size: 14px;">Role</label>
                <select id="edit_role" name="role" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit;">
                    <option value="staff">👤 Staff</option>
                    <option value="administrator">👑 Administrator</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeEditUserModal()" style="background: var(--cream); color: var(--text); padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="background: var(--primary); color: var(--gold); padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="formMessage" class="form-message" style="display: none;"></div>

<script>
let userTableRows = null;
function getUserTableRows() {
    if (!userTableRows) userTableRows = document.querySelectorAll('#userTable tbody tr');
    return userTableRows;
}

function searchUser() {
    const search = document.getElementById('searchUser').value.toLowerCase();
    const rows = getUserTableRows();
    const filterRole = document.getElementById('filterRole').value;
    rows.forEach(row => {
        const username = row.dataset.username;
        const name = row.querySelector('td[data-label="Username"] span').textContent.toLowerCase();
        const role = row.dataset.role;
        const matchSearch = username.includes(search) || name.includes(search);
        const matchRole = !filterRole || role === filterRole;
        row.style.display = (matchSearch && matchRole) ? '' : 'none';
    });
}
function filterUser() { searchUser(); }

function openAddUserModal() { document.getElementById('addUserModal').style.display = 'flex'; }
function closeAddUserModal() { document.getElementById('addUserModal').style.display = 'none'; document.getElementById('addUserForm').reset(); }
function openEditUserModal(userId, username, role, name) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_password').value = '';
    document.getElementById('editUserModal').style.display = 'flex';
}
function closeEditUserModal() { document.getElementById('editUserModal').style.display = 'none'; document.getElementById('editUserForm').reset(); }

[document.getElementById('addUserModal'), document.getElementById('editUserModal'), document.getElementById('confirmModal')].forEach(modal => {
    modal.addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
});

// FORM HANDLERS
async function handleFormSubmit(e, action) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', action);
    const messageDiv = document.getElementById('formMessage');
    
    messageDiv.style.display = 'block';
    messageDiv.className = 'form-message';
    messageDiv.textContent = 'Memproses...';
    
    try {
        const response = await fetch('<?= APP_URL ?>/index.php?gate=users', { method: 'POST', body: formData });
        const data = await response.json();
        messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
        messageDiv.textContent = data.message;
        if (data.success) setTimeout(() => window.location.reload(), 1500);
        else setTimeout(() => { messageDiv.style.display = 'none'; }, 5000);
    } catch (err) {
        messageDiv.className = 'form-message error';
        messageDiv.textContent = 'Terjadi kesalahan sistem.';
    }
}

document.getElementById('addUserForm').addEventListener('submit', (e) => handleFormSubmit(e, 'create'));
document.getElementById('editUserForm').addEventListener('submit', (e) => handleFormSubmit(e, 'update'));

function deleteUser(userId, username) {
    const confirmMessage = document.getElementById('confirmMessage');
    confirmMessage.innerHTML = `Apakah Anda yakin ingin menghapus user <strong>"${username}"</strong>?`;
    document.getElementById('confirmModal').style.display = 'flex';
    document.getElementById('confirmYesBtn').onclick = async () => {
        document.getElementById('confirmModal').style.display = 'none';
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('user_id', userId);
        formData.append('csrf_token', '<?= generateCSRFToken() ?>');
        const messageDiv = document.getElementById('formMessage');
        const response = await fetch('<?= APP_URL ?>/index.php?gate=users', { method: 'POST', body: formData });
        const data = await response.json();
        messageDiv.style.display = 'block';
        messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
        messageDiv.textContent = data.message;
        if (data.success) setTimeout(() => window.location.reload(), 1500);
    };
}
function closeConfirmModal() { document.getElementById('confirmModal').style.display = 'none'; }
</script>

<?php require VIEWS_PATH . '/templates/footer.php'; ?>
