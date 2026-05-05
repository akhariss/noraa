<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Adapters\Database;

/**
 * Entity: TransaksiHistory
 * Audit trail cicilan pembayaran. Bisa nominal positif (bayar) atau minus (koreksi).
 */
class TransaksiHistory
{
    /**
     * Get semua riwayat untuk 1 transaksi.
     */
    public function getByTransaksiId(int $transaksiId): array
    {
        return Database::select(
            'SELECT th.id, th.nominal_bayar, th.tanggal_bayar, th.catatan,
                    th.created_by, th.created_at, u.username, u.name
             FROM transaksi_history th
             LEFT JOIN users u ON u.id = th.created_by
             WHERE th.transaksi_id = :transaksi_id
             ORDER BY th.created_at DESC',
            ['transaksi_id' => $transaksiId]
        );
    }

    /**
     * Get semua riwayat untuk 1 registrasi (via transaksi_id lookup).
     */
    public function getByRegistrasiId(int $registrasiId): array
    {
        return Database::select(
            'SELECT th.id, th.nominal_bayar, th.tanggal_bayar, th.catatan,
                    th.created_by, th.created_at, u.username, u.name
             FROM transaksi_history th
             LEFT JOIN users u ON u.id = th.created_by
             JOIN transaksi t ON t.id = th.transaksi_id
             WHERE t.registrasi_id = :registrasi_id
             ORDER BY th.created_at DESC',
            ['registrasi_id' => $registrasiId]
        );
    }

    /**
     * Insert riwayat pembayaran (bisa positif atau minus untuk koreksi).
     */
    public function create(int $transaksiId, float $nominalBayar, string $tanggalBayar, string $catatan, int $createdBy): int
    {
        return Database::insert(
            'INSERT INTO transaksi_history (transaksi_id, nominal_bayar, tanggal_bayar, catatan, created_by)
             VALUES (:transaksi_id, :nominal_bayar, :tanggal_bayar, :catatan, :created_by)',
            [
                'transaksi_id'  => $transaksiId,
                'nominal_bayar' => $nominalBayar,
                'tanggal_bayar' => $tanggalBayar,
                'catatan'       => $catatan,
                'created_by'    => $createdBy,
            ]
        );
    }
}
