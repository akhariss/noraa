<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;
use App\Adapters\Logger;

/**
 * SK-06: RegistrasiHistory Model
 */
class RegistrasiHistory
{
    public function create(array $data): void
    {
        try {
            Database::insert(
                "INSERT INTO registrasi_history (registrasi_id, status_old_id, status_new_id, action, target_completion_at_old, target_completion_at_new, catatan, keterangan, 
                                               flag_kendala_active, flag_kendala_tahap, user_id, ip_address)
                 VALUES (:registrasi_id, :status_old_id, :status_new_id, :action, :target_old, :target_new, :catatan, :keterangan, 
                        :flag_active, :flag_tahap, :user_id, :ip_address)",
                [
                    'registrasi_id'      => $data['registrasi_id'],
                    'status_old_id'      => $data['status_old_id'] ?? null,
                    'status_new_id'      => $data['status_new_id'],
                    'action'             => $data['action'] ?? 'Update',
                    'target_old'         => $data['target_completion_at_old'] ?? null,
                    'target_new'         => $data['target_completion_at_new'] ?? null,
                    'catatan'            => $data['catatan'] ?? null,
                    'keterangan'         => $data['keterangan'] ?? null,
                    'flag_active'        => $data['flag_kendala_active'] ?? 0,
                    'flag_tahap'         => $data['flag_kendala_tahap'] ?? null,
                    'user_id'            => $data['user_id'],
                    'ip_address'         => $data['ip_address'] ?? null,
                ]
            );
        } catch (\PDOException $e) {
            Logger::error('RegistrasiHistory create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getByRegistrasi(int $registrasiId): array
    {
        return Database::select(
            "SELECT rh.id, rh.registrasi_id, rh.status_old_id, rh.status_new_id, rh.catatan, rh.keterangan,
                     rh.flag_kendala_active, rh.flag_kendala_tahap, rh.user_id,
                     rh.ip_address, rh.created_at,
                     COALESCE(u.name, u.username) as user_name, u.role as user_role,
                     ws_old.label as status_old_label, ws_old.behavior_role as status_old_behavior_role,
                     ws_new.label as status_new_label, ws_new.behavior_role as status_new_behavior_role
              FROM registrasi_history rh
              LEFT JOIN users u ON rh.user_id = u.id
              LEFT JOIN workflow_steps ws_old ON rh.status_old_id = ws_old.id
              LEFT JOIN workflow_steps ws_new ON rh.status_new_id = ws_new.id
             WHERE rh.registrasi_id = :registrasi_id
             ORDER BY rh.created_at DESC",
            ['registrasi_id' => $registrasiId]
        );
    }

    public function getLatest(int $registrasiId): ?array
    {
        return Database::selectOne(
            "SELECT rh.id, rh.registrasi_id, rh.status_old_id, rh.status_new_id, rh.catatan,
                    rh.flag_kendala_active, rh.flag_kendala_tahap, u.username as user_name, rh.created_at
             FROM registrasi_history rh
             LEFT JOIN users u ON rh.user_id = u.id
             WHERE rh.registrasi_id = :registrasi_id
             ORDER BY rh.created_at DESC LIMIT 1",
            ['registrasi_id' => $registrasiId]
        );
    }
}
