<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Entities\User;
use App\Domain\Entities\AuditLog;
use App\Adapters\Logger;

/**
 * SK-14: UserService
 */
class UserService
{
    private User $userModel;
    private AuditLog $auditLogModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->auditLogModel = new AuditLog();
    }

    public function getAllUsers(): array
    {
        return $this->userModel->getAll();
    }

    public function getUser(int $id): ?array
    {
        return $this->userModel->findById($id);
    }

    public function createUser(array $data, int $createdBy, string $role): array
    {
        if (!in_array($data['role'], [ROLE_OWNER, ROLE_STAFF])) {
            return ['success' => false, 'message' => 'Role tidak valid'];
        }

        $existing = $this->userModel->findByUsername($data['username']);
        if ($existing) {
            return ['success' => false, 'message' => 'Username sudah digunakan'];
        }

        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        $success = $this->userModel->create($data);

        if ($success) {
            $newUser = $this->userModel->findByUsername($data['username']);
            $this->auditLogModel->create($createdBy, $role, AUDIT_CREATE, null, null,
                json_encode(['user_created' => $newUser['username'], 'role' => $data['role']]));
            return ['success' => true, 'message' => 'User berhasil dibuat', 'user' => $newUser];
        }

        return ['success' => false, 'message' => 'Gagal membuat user'];
    }

    public function updateUser(int $userId, array $data, int $updatedBy, string $role): array
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        if (isset($data['username'])) {
            $existing = $this->userModel->findByUsername($data['username']);
            if ($existing && $existing['id'] !== $userId) {
                return ['success' => false, 'message' => 'Username sudah digunakan'];
            }
        }

        $oldData = ['username' => $user['username'], 'role' => $user['role']];
        $success = $this->userModel->update($userId, $data);

        if ($success) {
            $this->auditLogModel->create($updatedBy, $role, AUDIT_UPDATE, null,
                json_encode($oldData),
                json_encode(['username' => $data['username'] ?? $user['username'], 'role' => $data['role'] ?? $user['role']]));
            return ['success' => true, 'message' => 'User berhasil diperbarui'];
        }

        return ['success' => false, 'message' => 'Gagal memperbarui user'];
    }

    public function deleteUser(int $userId, int $deletedBy, string $role): array
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        if ($user['role'] === ROLE_OWNER) {
            $allUsers = $this->getAllUsers();
            $adminCount = count(array_filter($allUsers, fn($u) => $u['role'] === ROLE_OWNER));
            if ($adminCount <= 1) {
                return ['success' => false, 'message' => 'Tidak dapat menghapus administrator terakhir'];
            }
        }

        $success = $this->userModel->delete($userId);

        if ($success) {
            $this->auditLogModel->create($deletedBy, $role, AUDIT_DELETE, null,
                json_encode(['user_deleted' => $user['username'], 'role' => $user['role']]), null);
            return ['success' => true, 'message' => 'User dihapus'];
        }

        return ['success' => false, 'message' => 'Gagal menghapus user'];
    }

    public function resetPassword(int $userId, string $newPassword, int $resetBy, string $role): array
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        $success = $this->userModel->resetPassword($userId, $newPassword);

        if ($success) {
            $this->auditLogModel->create($resetBy, $role, 'password_reset', null, null,
                json_encode(['password_reset_for' => $user['username']]));
            return ['success' => true, 'message' => 'Password berhasil direset'];
        }

        return ['success' => false, 'message' => 'Gagal mereset password'];
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): array
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        $fullUser = $this->userModel->findByUsername($user['username']);
        if (!$this->userModel->verifyPassword($oldPassword, $fullUser['password_hash'])) {
            return ['success' => false, 'message' => 'Password lama salah'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        $success = $this->userModel->resetPassword($userId, $newPassword);

        if ($success) {
            $this->auditLogModel->create($userId, $user['role'], 'password_change', null, null,
                json_encode(['password_changed' => true]));
            return ['success' => true, 'message' => 'Password berhasil diubah'];
        }

        return ['success' => false, 'message' => 'Gagal mengubah password'];
    }
}
