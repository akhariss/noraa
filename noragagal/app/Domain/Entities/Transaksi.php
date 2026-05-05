<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;

/**
 * Entity: Transaksi
 * 1 per registrasi — simpan total_tagihan + jumlah_bayar (denormalized).
 * jumlah_bayar di-update otomatis setiap ada entry di transaksi_history.
 */
class Transaksi
{
    /**
     * Find transaksi by registrasi ID.
     */
    public function findByRegistrasiId(int $registrasiId): ?array
    {
        return Database::selectOne(
            'SELECT id, registrasi_id, total_tagihan, jumlah_bayar, created_at, updated_at
             FROM transaksi WHERE registrasi_id = :registrasi_id LIMIT 1',
            ['registrasi_id' => $registrasiId]
        );
    }

    /**
     * Create transaksi record (saat registrasi baru dibuat).
     */
    public function create(int $registrasiId, float $totalTagihan): int
    {
        return Database::insert(
            'INSERT INTO transaksi (registrasi_id, total_tagihan, jumlah_bayar)
             VALUES (:registrasi_id, :total_tagihan, 0)',
            [
                'registrasi_id'  => $registrasiId,
                'total_tagihan'  => $totalTagihan,
            ]
        );
    }

    /**
     * Update total_tagihan.
     */
    public function updateTotalTagihan(int $registrasiId, float $totalTagihan): bool
    {
        Database::execute(
            'UPDATE transaksi SET total_tagihan = :total_tagihan WHERE registrasi_id = :registrasi_id',
            ['total_tagihan' => $totalTagihan, 'registrasi_id' => $registrasiId]
        );
        return true;
    }

    /**
     * Update jumlah_bayar (dipanggil setelah INSERT transaksi_history).
     */
    public function updateJumlahBayar(int $transaksiId): bool
    {
        // Re-calculate: jumlah_bayar = SUM(nominal_bayar) from history
        $result = Database::selectOne(
            'SELECT COALESCE(SUM(nominal_bayar), 0) as total FROM transaksi_history WHERE transaksi_id = :transaksi_id',
            ['transaksi_id' => $transaksiId]
        );

        Database::execute(
            'UPDATE transaksi SET jumlah_bayar = :jumlah_bayar WHERE id = :id',
            ['jumlah_bayar' => (float)$result['total'], 'id' => $transaksiId]
        );
        return true;
    }

    /**
     * Get sisa pembayaran.
     */
    public function getSisa(int $registrasiId): float
    {
        $transaksi = $this->findByRegistrasiId($registrasiId);
        if (!$transaksi) {
            return 0;
        }
        return (float)$transaksi['total_tagihan'] - (float)$transaksi['jumlah_bayar'];
    }

    /**
     * Cek apakah sudah lunas.
     */
    public function isLunas(int $registrasiId): bool
    {
        $transaksi = $this->findByRegistrasiId($registrasiId);
        if (!$transaksi) {
            return true; // tidak ada tagihan = dianggap lunas
        }
        return (float)$transaksi['jumlah_bayar'] >= (float)$transaksi['total_tagihan'];
    }
}
