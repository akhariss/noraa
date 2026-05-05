<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\Entities\Transaksi;
use App\Domain\Entities\TransaksiHistory;
use App\Adapters\Logger;

/**
 * Service: TransaksiService
 * Logic bisnis pembayaran — set tagihan, tambah bayar, koreksi, cek lunas.
 */
class TransaksiService
{
    private Transaksi $transaksiModel;
    private TransaksiHistory $historyModel;

    public function __construct()
    {
        $this->transaksiModel = new Transaksi();
        $this->historyModel   = new TransaksiHistory();
    }

    /**
     * Inisialisasi transaksi saat registrasi baru dibuat.
     */
    public function initTransaksi(int $registrasiId, float $totalTagihan): bool
    {
        // Validation: Prevent absurd values
        $maxRealistic = 999999999999.99; // ~1 triliun
        if ($totalTagihan <= 0) {
            return true; // tidak ada tagihan
        }
        if ($totalTagihan > $maxRealistic) {
            error_log("initTransaksi blocked absurd value: {$totalTagihan} for registrasi {$registrasiId}");
            return false; // Silently block absurd values
        }

        $existing = $this->transaksiModel->findByRegistrasiId($registrasiId);
        if ($existing) {
            return false; // sudah ada
        }

        $this->transaksiModel->create($registrasiId, $totalTagihan);
        return true;
    }

    /**
     * Set / ubah total tagihan.
     */
    public function setTotalTagihan(int $registrasiId, float $totalTagihan, int $userId): array
    {
        // Validation: Prevent absurd values
        $maxRealistic = 999999999999.99; // ~1 triliun (maksimum realistis)
        if ($totalTagihan < 0) {
            return ['success' => false, 'message' => 'Total tagihan tidak boleh minus'];
        }
        if ($totalTagihan > $maxRealistic) {
            return ['success' => false, 'message' => 'Total tagihan terlalu besar. Maksimum: Rp ' . number_format($maxRealistic, 0, ',', '.')];
        }

        try {
            // Check if transaksi record exists
            $existing = $this->transaksiModel->findByRegistrasiId($registrasiId);
            
            if (!$existing) {
                // Create new transaksi record if doesn't exist
                $this->transaksiModel->create($registrasiId, $totalTagihan);
            } else {
                // Update existing
                $this->transaksiModel->updateTotalTagihan($registrasiId, $totalTagihan);
            }
            
            Logger::info('TRANSAKSI_SET_TAGIHAN', [
                'registrasi_id' => $registrasiId,
                'total_tagihan' => $totalTagihan,
                'user_id'       => $userId,
            ]);
            return ['success' => true, 'message' => 'Total tagihan berhasil ditetapkan'];
        } catch (\Throwable $e) {
            Logger::error('SET_TAGIHAN_FAILED', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal menetapkan tagihan: ' . $e->getMessage()];
        }
    }

    /**
     * Tambah pembayaran (bisa positif = bayar, atau minus = koreksi).
     */
    public function tambahBayar(int $registrasiId, float $nominalBayar, string $tanggalBayar, string $catatan, int $userId): array
    {
        // Validation: Prevent absurd values
        $maxRealistic = 999999999999.99; // ~1 triliun
        if ($nominalBayar == 0) {
            return ['success' => false, 'message' => 'Nominal tidak boleh nol'];
        }
        if (abs($nominalBayar) > $maxRealistic) {
            return ['success' => false, 'message' => 'Nominal terlalu besar. Maksimum: Rp ' . number_format($maxRealistic, 0, ',', '.')];
        }

        if (empty($tanggalBayar)) {
            return ['success' => false, 'message' => 'Tanggal wajib diisi'];
        }

        try {
            // Pastikan transaksi ada
            $transaksi = $this->transaksiModel->findByRegistrasiId($registrasiId);
            if (!$transaksi) {
                return ['success' => false, 'message' => 'Transaksi belum diinisialisasi. Set total tagihan terlebih dahulu.'];
            }

            // Validation: Prevent overpayment
            $currentPaid = (float)$transaksi['jumlah_bayar'];
            $totalTagihan = (float)$transaksi['total_tagihan'];
            $newTotalPaid = $currentPaid + $nominalBayar;
            
            if ($nominalBayar > 0 && $newTotalPaid > $totalTagihan) {
                $maxAllowed = $totalTagihan - $currentPaid;
                return ['success' => false, 'message' => 'Nominal melebihi sisa tagihan. Maksimal: Rp ' . number_format($maxAllowed, 0, ',', '.')];
            }

            // Insert history
            $historyId = $this->historyModel->create(
                (int)$transaksi['id'],
                $nominalBayar,
                $tanggalBayar,
                $catatan,
                $userId
            );

            // Re-calculate jumlah_bayar
            $this->transaksiModel->updateJumlahBayar((int)$transaksi['id']);

            // Refresh data
            $updated = $this->transaksiModel->findByRegistrasiId($registrasiId);
            $sisa = (float)$updated['total_tagihan'] - (float)$updated['jumlah_bayar'];
            $lunas = $sisa <= 0;

            Logger::info('TRANSAKSI_PEMBAYARAN', [
                'registrasi_id' => $registrasiId,
                'nominal'       => $nominalBayar,
                'jumlah_bayar'  => $updated['jumlah_bayar'],
                'sisa'          => $sisa,
                'lunas'         => $lunas,
                'user_id'       => $userId,
            ]);

            return [
                'success'      => true,
                'message'      => $lunas
                    ? 'Pembayaran berhasil dicatat. ✅ LUNAS'
                    : 'Pembayaran berhasil dicatat. Sisa: Rp ' . number_format($sisa, 0, ',', '.'),
                'sisa'         => $sisa,
                'lunas'        => $lunas,
                'jumlah_bayar' => (float)$updated['jumlah_bayar'],
                'total_tagihan'=> (float)$updated['total_tagihan'],
            ];
        } catch (\Throwable $e) {
            Logger::error('TAMBAH_BAYAR_FAILED', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal memproses pembayaran'];
        }
    }

    /**
     * Cek apakah registrasi sudah lunas.
     */
    public function cekLunas(int $registrasiId): bool
    {
        return $this->transaksiModel->isLunas($registrasiId);
    }

    /**
     * Get summary pembayaran untuk 1 registrasi.
     */
    public function getSummary(int $registrasiId): array
    {
        $transaksi = $this->transaksiModel->findByRegistrasiId($registrasiId);
        if (!$transaksi) {
            return [
                'has_transaksi' => false,
                'total_tagihan' => 0,
                'jumlah_bayar'  => 0,
                'sisa'          => 0,
                'lunas'         => true,
                'riwayat'       => [],
            ];
        }

        $totalTagihan = (float)$transaksi['total_tagihan'];
        $jumlahBayar  = (float)$transaksi['jumlah_bayar'];
        $sisa         = $totalTagihan - $jumlahBayar;

        return [
            'has_transaksi' => true,
            'total_tagihan' => $totalTagihan,
            'jumlah_bayar'  => $jumlahBayar,
            'sisa'          => $sisa,
            'lunas'         => $sisa <= 0,
            'riwayat'       => $this->historyModel->getByTransaksiId((int)$transaksi['id']),
        ];
    }
}
