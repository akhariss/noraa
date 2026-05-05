<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Entities\AuditLog;
use App\Adapters\Logger;

/**
 * SK-14: BackupService
 */
class BackupService
{
    private AuditLog $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLog();
    }

    public function createBackup(int $userId, string $role): array
    {
        $timestamp = date('Y-m-d_His');
        $filename = "backup_{$timestamp}.sql";
        $filepath = BACKUPS_PATH . '/' . $filename;

        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            escapeshellarg(DB_PASS),
            escapeshellarg(DB_NAME),
            escapeshellarg($filepath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($filepath)) {
            $this->auditLogModel->create($userId, $role, 'backup_create', null, null,
                json_encode(['filename' => $filename]));
            return ['success' => true, 'message' => 'Backup berhasil dibuat', 'filename' => $filename, 'filepath' => $filepath];
        }

        Logger::error('Backup creation failed', ['output' => implode("\n", $output)]);
        return ['success' => false, 'message' => 'Gagal membuat backup: ' . implode("\n", $output)];
    }

    public function createSiteBackup(int $userId, string $role): array
    {
        $timestamp = date('Y-m-d_His');
        $filename = "site_backup_{$timestamp}.zip";
        $filepath = BACKUPS_PATH . '/' . $filename;

        $zip = new \ZipArchive();
        if ($zip->open($filepath, \ZipArchive::CREATE) !== true) {
            return ['success' => false, 'message' => 'Gagal membuat archive zip'];
        }

        $directories = ['config', 'controllers', 'app', 'models', 'views', 'public', 'utils'];
        foreach ($directories as $dir) {
            $fullPath = BASE_PATH . '/' . $dir;
            if (is_dir($fullPath)) {
                $this->addDirToZip($zip, $fullPath, $dir);
            }
        }

        $indexPath = BASE_PATH . '/public/index.php';
        if (file_exists($indexPath)) {
            $zip->addFile($indexPath, 'public/index.php');
        }

        $zip->close();

        if (file_exists($filepath)) {
            $this->auditLogModel->create($userId, $role, 'backup_site', null, null,
                json_encode(['filename' => $filename]));
            return ['success' => true, 'message' => 'Backup site berhasil dibuat', 'filename' => $filename, 'filepath' => $filepath];
        }

        return ['success' => false, 'message' => 'Gagal membuat backup site'];
    }

    private function addDirToZip(\ZipArchive $zip, string $path, string $zipPath): void
    {
        $dir = opendir($path);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filePath = $path . '/' . $file;
            $zipFilePath = $zipPath . '/' . $file;

            if (is_dir($filePath)) {
                $this->addDirToZip($zip, $filePath, $zipFilePath);
            } else {
                $zip->addFile($filePath, $zipFilePath);
            }
        }
        closedir($dir);
    }

    public function restoreBackup(string $filename, int $userId, string $role): array
    {
        $filepath = BACKUPS_PATH . '/' . $filename;
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => 'File backup tidak ditemukan'];
        }

        $command = sprintf(
            'mysql --host=%s --user=%s --password=%s %s < %s 2>&1',
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            escapeshellarg(DB_PASS),
            escapeshellarg(DB_NAME),
            escapeshellarg($filepath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->auditLogModel->create($userId, $role, AUDIT_RESTORE, null, null,
                json_encode(['filename' => $filename]));
            return ['success' => true, 'message' => 'Database berhasil dipulihkan'];
        }

        Logger::error('Backup restore failed', ['output' => implode("\n", $output)]);
        return ['success' => false, 'message' => 'Gagal memulihkan database: ' . implode("\n", $output)];
    }

    public function listBackups(): array
    {
        $backups = [];
        if (!is_dir(BACKUPS_PATH)) {
            return $backups;
        }

        $files = scandir(BACKUPS_PATH);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filepath = BACKUPS_PATH . '/' . $file;
            $backups[] = [
                'filename' => $file,
                'filepath' => $filepath,
                'size'     => filesize($filepath),
                'created'  => filectime($filepath),
                'type'     => pathinfo($file, PATHINFO_EXTENSION),
            ];
        }

        usort($backups, fn($a, $b) => $b['created'] - $a['created']);
        return $backups;
    }

    public function deleteBackup(string $filename, int $userId, string $role): array
    {
        $filepath = BACKUPS_PATH . '/' . $filename;
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }

        if (unlink($filepath)) {
            $this->auditLogModel->create($userId, $role, 'backup_delete', null, null,
                json_encode(['filename' => $filename]));
            return ['success' => true, 'message' => 'Backup dihapus'];
        }

        return ['success' => false, 'message' => 'Gagal menghapus backup'];
    }

    public function downloadBackup(string $filename): void
    {
        $filepath = BACKUPS_PATH . '/' . $filename;
        if (!file_exists($filepath)) {
            http_response_code(404);
            echo 'File tidak ditemukan';
            exit;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
