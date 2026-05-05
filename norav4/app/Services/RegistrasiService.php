<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\RegistrasiModel;
use App\Models\KlienModel;
use App\Models\RegistrasiHistoryModel;
use App\Models\AuditLogModel;
use App\Core\Database;
use Exception;

/**
 * RegistrasiService - Handles business logic for registration
 */
class RegistrasiService
{
    private RegistrasiModel $registrasi;
    private KlienModel $klien;
    private RegistrasiHistoryModel $history;
    private AuditLogModel $audit;

    public function __construct()
    {
        $this->registrasi = new RegistrasiModel();
        $this->klien = new KlienModel();
        $this->history = new RegistrasiHistoryModel();
        $this->audit = new AuditLogModel();
    }

    /**
     * Create new registration with all associated records
     */
    public function createRegistrasi(array $data, array $user): array
    {
        try {
            Database::beginTransaction();

            // 1. Get or create klien
            $klienId = $this->klien->getOrCreate([
                'nama' => $data['klien_nama'],
                'hp' => $data['klien_hp'],
                'email' => $data['klien_email'] ?? null
            ]);

            // 2. Generate Nomor Registrasi
            $nomorRegistrasi = 'NP-' . date('Ymd') . '-' . str_pad((string)rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // 3. Prepare Registration Data
            $regData = [
                'klien_id'             => $klienId,
                'layanan_id'           => (int)$data['layanan_id'],
                'nomor_registrasi'     => $nomorRegistrasi,
                'current_step_id'      => (int)($data['current_step_id'] ?? 1),
                'target_completion_at' => $data['target_date'] ? $data['target_date'] . ' 23:59:59' : null,
                'keterangan'           => $data['keterangan'] ?? null,
                'catatan_internal'     => $data['catatan'] ?? null
            ];

            $registrasiId = $this->registrasi->create($regData);

            // 4. Create History
            $this->history->create([
                'registrasi_id' => $registrasiId,
                'status_old_id' => null,
                'status_new_id' => $regData['current_step_id'],
                'action'        => 'Create',
                'catatan'       => $regData['catatan_internal'] ?? 'Registrasi baru dibuat.',
                'user_id'       => $user['id'],
                'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            // 5. Log Audit
            $this->audit->create(
                (int)$user['id'],
                $user['role'],
                'CREATE_REGISTRASI',
                json_encode(['id' => $registrasiId, 'data' => $regData])
            );

            // 6. Handle Transaction (Optional - depends if tagihan is provided)
            $totalTagihan = (float)($data['total_tagihan'] ?? 0);
            $pembayaran = (float)($data['pembayaran'] ?? 0);

            if ($totalTagihan > 0) {
                Database::insert(
                    "INSERT INTO transaksi (registrasi_id, total_tagihan, jumlah_bayar, created_at, updated_at) 
                     VALUES (:rid, :total, :bayar, NOW(), NOW())",
                    [
                        'rid' => $registrasiId,
                        'total' => $totalTagihan,
                        'bayar' => $pembayaran
                    ]
                );

                if ($pembayaran > 0) {
                    Database::insert(
                        "INSERT INTO transaksi_history (registrasi_id, jumlah, tanggal, catatan, user_id, created_at) 
                         VALUES (:rid, :jml, NOW(), :cat, :uid, NOW())",
                        [
                            'rid' => $registrasiId,
                            'jml' => $pembayaran,
                            'cat' => 'Pembayaran awal saat registrasi',
                            'uid' => $user['id']
                        ]
                    );
                }
            }

            // 7. Generate Tracking Token
            $verificationCode = substr(preg_replace('/[^0-9]/', '', $data['klien_hp']), -4);
            $trackingToken = $this->generateTrackingToken($registrasiId, $verificationCode);
            $this->registrasi->update($registrasiId, [
                'tracking_token' => $trackingToken,
                'verification_code' => $verificationCode
            ]);

            Database::commit();

            return [
                'success' => true,
                'message' => 'Registrasi berhasil dibuat',
                'id' => $registrasiId,
                'nomor_registrasi' => $nomorRegistrasi,
                'tracking_token' => $trackingToken,
                'klien_nama' => $data['klien_nama'],
                'klien_hp' => $data['klien_hp']
            ];

        } catch (Exception $e) {
            Database::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function generateTrackingToken(int $id, string $code): string
    {
        $payload = json_encode(['id' => $id, 'code' => $code, 'ts' => time()]);
        return base64_encode($payload);
    }
}
