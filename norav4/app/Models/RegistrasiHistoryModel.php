<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class RegistrasiHistoryModel
{
    public function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO registrasi_history (
                registrasi_id, status_old_id, status_new_id, action, 
                target_completion_at_old, target_completion_at_new, 
                catatan, keterangan, flag_kendala_active, flag_kendala_tahap, 
                user_id, ip_address
            ) VALUES (
                :registrasi_id, :status_old_id, :status_new_id, :action, 
                :target_old, :target_new, 
                :catatan, :keterangan, :flag_active, :flag_tahap, 
                :user_id, :ip_address
            )",
            [
                'registrasi_id' => $data['registrasi_id'],
                'status_old_id' => $data['status_old_id'] ?? null,
                'status_new_id' => $data['status_new_id'],
                'action'        => $data['action'] ?? 'Update',
                'target_old'    => $data['target_completion_at_old'] ?? null,
                'target_new'    => $data['target_completion_at_new'] ?? null,
                'catatan'       => $data['catatan'] ?? null,
                'keterangan'    => $data['keterangan'] ?? null,
                'flag_active'   => $data['flag_kendala_active'] ?? 0,
                'flag_tahap'    => $data['flag_kendala_tahap'] ?? null,
                'user_id'       => $data['user_id'],
                'ip_address'    => $data['ip_address'] ?? null,
            ]
        );
    }

    public function getByRegistrasi(int $registrasiId): array
    {
        return Database::select(
            "SELECT rh.*, u.name as user_name,
                    ws_old.label as status_old_label, ws_new.label as status_new_label
             FROM registrasi_history rh
             LEFT JOIN users u ON rh.user_id = u.id
             LEFT JOIN workflow_steps ws_old ON rh.status_old_id = ws_old.id
             LEFT JOIN workflow_steps ws_new ON rh.status_new_id = ws_new.id
             WHERE rh.registrasi_id = :id
             ORDER BY rh.created_at DESC",
            ['id' => $registrasiId]
        );
    }
}
