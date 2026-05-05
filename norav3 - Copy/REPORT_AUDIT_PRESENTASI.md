# 📊 Rencana Fitur Laporan Audit — Presentasi

> **Untuk:** Klien / Dosen Pembimbing
> **Proyek:** NORA — Sistem Manajemen Kantor Notaris
> **Tanggal:** April 2026

---

## 🎯 Apa Itu Laporan Audit?

**Laporan Audit** adalah fitur yang memberikan **gambaran lengkap** tentang operasional kantor notaris dalam satu periode tertentu (bulanan atau mingguan).

**Tujuannya:**
- 📈 **Monitoring** — Pantau kinerja kantor secara berkala
- 🔍 **Transparansi** — Siapa melakukan apa, kapan, berapa
- 💰 **Kontrol Keuangan** — Tagihan, pembayaran, piutang
- ⏱️ **Evaluasi SLA** — Apakah proses berjalan sesuai target waktu?

---

## 📋 Struktur Laporan

Laporan terdiri dari **3 Bagian Utama:**

```
┌──────────────────────────────────────────────┐
│  BAGIAN 1: LAPORAN REGISTRASI                │
│  • Ringkasan Periode                         │
│  • Persebaran Layanan                        │
│  • Matrix Timeline per Step                  │
│  • Storytelling / Ringkasan Naratif          │
│  • Registrasi Batal / Ditutup                │
├──────────────────────────────────────────────┤
│  BAGIAN 2: LAPORAN KEUANGAN                  │
│  • Ringkasan Keuangan                        │
│  • Detail: Aktif & Belum Lunas               │
│  • Detail: Aktif & Sudah Lunas               │
│  • Riwayat Pembayaran                        │
├──────────────────────────────────────────────┤
│  BAGIAN 3: RANGKING AKTIVITAS USER           │
│  • Pembuat Registrasi Terbanyak              │
│  • Update Status Terbanyak                   │
│  • Tutup Registrasi Terbanyak                │
│  • Input Pembayaran Terbanyak                │
│  • Ringkasan Aktivitas Tim                   │
└──────────────────────────────────────────────┘
```

---

## 🔍 Detail Setiap Bagian

### BAGIAN 1: Laporan Registrasi

#### 1.1 Header Periode
```
Laporan Registrasi
Periode: 1 April 2026 — 30 April 2026
```

#### 1.2 Ringkasan Cepat
| Metric | Nilai |
|---|---|
| Registrasi Baru | 15 |
| Registrasi Ditutup | 8 |
| Masih Aktif | 7 |
| Total Tagihan | Rp 150.000.000 |
| Total Terbayar | Rp 120.000.000 |

#### 1.3 Persebaran Layanan
| Layanan | Jumlah | Persentase |
|---|---|---|
| Jual Beli | 5 | 33.3% |
| Hibah | 4 | 26.7% |
| Waris | 3 | 20.0% |
| Roya | 2 | 13.3% |
| Lainnya | 1 | 6.7% |

#### 1.4 Matrix Timeline per Step (Fitur Unggulan)

**Visualisasi:** Baris = Registrasi, Kolom = Step Workflow, Isi = Hari di Step

```
Legend:
  1 = Draft              6 = Validasi Pajak      11 = Perbaikan
  2 = Pembayaran Admin   7 = Penomoran Akta      12 = Selesai
  3 = Validasi Sertifikat 8 = Pendaftaran        13 = Diserahkan
  4 = Pengecekan Sert.   9 = Pembayaran PNBP     14 = Ditutup
  5 = Pembayaran Pajak   10 = Pemeriksaan BPN    15 = Batal

┌─────┬──────────────┬─────────────┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬──────────┐
│ No  │ Nama Klien   │ Layanan     │ 1 │ 2 │ 3 │ 4 │ 5 │ 6 │ 7 │ 8 │ 9 │10 │11 │ Status   │
├─────┼──────────────┼─────────────┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼───┼──────────┤
│  1  │ Ahmad        │ Jual Beli   │ 1 │ 3 │ 2 │ 5 │ - │ - │ - │ - │ - │ - │ - │ 🔴 Pajak │
│  2  │ Budi         │ Waris       │ 2 │ 1 │ 4 │ 3 │ 1 │ 2 │ 1 │ - │ - │ - │ - │ ✅ Normal│
│  3  │ Citra        │ Hibah       │ 1 │ 2 │ - │ - │ - │ - │ - │ - │ - │ - │ - │ ⏳ Baru  │
└─────┴──────────────┴─────────────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴──────────┘
```

**Aturan Isi Cell:**
- **Angka** = jumlah hari yang dihabiskan di step tersebut
- **"-"** = belum pernah masuk step itu
- **Cell kuning** = sedang diproses saat ini
- **Cell merah** = overdue (melebihi SLA)

#### 1.5 Storytelling / Ringkasan Naratif

> *"Periode April 2026, kantor menerima **15 registrasi baru** dengan layanan terbanyak adalah **Jual Beli (33%)**. Saat ini **7 registrasi masih aktif**, di mana **5 berjalan normal** sesuai SLA dan **2 mengalami keterlambatan**.*
>
> *Bottleneck utama ada di tahap **Pembayaran Pajak** (rata-rata 3 hari melebihi SLA) dan **Perbaikan** (2 registrasi harus diulang).*
>
> *Secara keseluruhan, **80% registrasi berjalan lancar** tanpa kendala."*

#### 1.6 Registrasi Batal / Ditutup
| No | No. Registrasi | Klien | Layanan | Status | Alasan | Tanggal |
|---|---|---|---|---|---|---|
| 1 | NP-20260402-002 | Dani | Roya | Batal | Klien membatalkan | 5 Apr 2026 |
| 2 | NP-20260410-009 | Eka | Waris | Ditutup | Selesai | 20 Apr 2026 |

---

### BAGIAN 2: Laporan Keuangan

#### 2.1 Ringkasan Keuangan
| Metric | Nilai |
|---|---|
| Total Masuk (Pembayaran) | Rp 120.000.000 |
| Jumlah Transaksi | 23 |
| Rata-rata per Transaksi | Rp 5.217.391 |

#### 2.2 Detail: Aktif & BELUM Lunas
| No | No. Registrasi | Klien | Layanan | Tagihan | Bayar | Sisa | Ket |
|---|---|---|---|---|---|---|---|
| 1 | NP-20260401-001 | Ahmad | Jual Beli | 15 jt | 10 jt | 5 jt | ⏳ Cicil |
| 2 | NP-20260405-003 | Budi | Waris | 8 jt | 0 | 8 jt | ❌ Belum |

#### 2.3 Detail: Aktif & SUDAH Lunas
| No | No. Registrasi | Klien | Layanan | Tagihan | Bayar | Tgl Lunas | Status |
|---|---|---|---|---|---|---|---|
| 1 | NP-20260408-007 | Citra | Hibah | 12 jt | 12 jt | 15 Apr | ✅ Lunas |
| 2 | NP-20260410-011 | Dian | Roya | 5 jt | 5 jt | 18 Apr | ✅ Lunas |

#### 2.4 Riwayat Pembayaran (Timeline)
| Tanggal | No. Registrasi | Klien | Nominal | Catatan | Oleh |
|---|---|---|---|---|---|
| 1 Apr | NP-20260401-001 | Ahmad | Rp 5.000.000 | DP | notaris |
| 5 Apr | NP-20260401-001 | Ahmad | Rp 5.000.000 | Pelunasan | notaris |
| 10 Apr | NP-20260405-003 | Budi | Rp 3.000.000 | Cicilan 1 | staff |

---

### BAGIAN 3: Ranking Aktivitas User

#### 3.1 Pembuat Registrasi Terbanyak
| Rank | Nama | Role | Jumlah | Persentase |
|---|---|---|---|---|
| 🥇 1 | notaris | Admin | 8 | 53.3% |
| 🥈 2 | staff1 | Staff | 5 | 33.3% |
| 🥉 3 | staff2 | Staff | 2 | 13.3% |

#### 3.2 Update Status Terbanyak
| Rank | Nama | Role | Jumlah | Persentase |
|---|---|---|---|---|
| 🥇 1 | staff1 | Staff | 23 | 46.0% |
| 🥈 2 | notaris | Admin | 18 | 36.0% |
| 🥉 3 | staff2 | Staff | 9 | 18.0% |

#### 3.3 Tutup Registrasi Terbanyak
| Rank | Nama | Role | Total | Selesai | Batal |
|---|---|---|---|---|---|
| 🥇 1 | notaris | Admin | 6 | 5 | 1 |
| 🥈 2 | staff1 | Staff | 2 | 2 | 0 |

#### 3.4 Input Pembayaran Terbanyak
| Rank | Nama | Role | Transaksi | Total | Rata-rata |
|---|---|---|---|---|---|
| 🥇 1 | notaris | Admin | 15 | Rp 85 jt | Rp 5,6 jt |
| 🥈 2 | staff1 | Staff | 8 | Rp 35 jt | Rp 4,3 jt |

#### 3.5 Ringkasan Aktivitas Tim

> *"Periode April 2026, **Admin (notaris)** paling aktif membuat registrasi baru (53.3%), sementara **Staff (staff1)** paling sering update status (46%). Admin juga menangani pembayaran terbanyak (65.2% dari total transaksi). Kesimpulannya: Pembagian kerja cukup merata, tapi Admin lebih fokus di front-office (registrasi + pembayaran), Staff lebih di back-office (update status/proses)."*

---

## 🎨 Format Output

Laporan ini didesain **print-friendly** untuk dicetak dalam format **PDF A4 Landscape**.

**Fitur Visual:**
- 🟢 **Hijau** = Normal / Lunas / Selesai
- 🟡 **Kuning** = Sedang diproses / Warning
- 🔴 **Merah** = Overdue / Batal / Belum bayar
- ⚪ **Abu-abu** = Belum applicable

**Layout:**
- Header: Periode + Judul Laporan
- Body: 3 Bagian utama (Registrasi → Keuangan → User)
- Footer: Tanggal cetak + Nama pengguna yang mencetak

---

## ✅ Manfaat untuk Kantor Notaris

| Manfaat | Penjelasan |
|---|---|
| **Monitoring Kinerja** | Tahu berapa registrasi masuk & selesai per bulan |
| **Evaluasi SLA** | Identifikasi bottleneck proses |
| **Kontrol Keuangan** | Pantau tagihan & piutang yang belum lunas |
| **Audit Internal** | Siapa melakukan apa — akuntabilitas |
| **Laporan ke Pemilik** | Data konkret untuk presentasi bulanan |

---

## 🔧 Teknologi & Kelayakan

**Apakah Bisa Dibuat?**
> ✅ **BISA 100%** — Semua data yang dibutuhkan **sudah ada** di database.
> Tidak perlu perubahan struktur database.
> Hanya perlu query yang kreatif dan layout yang rapi.

**Database yang Digunakan:**
- `registrasi` + `klien` + `layanan` → Data registrasi
- `registrasi_history` → Timeline & matrix
- `transaksi` + `transaksi_history` → Keuangan
- `users` → Ranking aktivitas
- `workflow_steps` → Definisi SLA per step
- `kendala` → Data hambatan

**Estimasi Waktu Pengembangan:**
- Backend (Service + Controller): 2-3 hari
- Frontend (Views + Print CSS): 2-3 hari
- Testing & Refinement: 1-2 hari
- **Total: ~5-8 hari kerja**

---

## 📌 Langkah Selanjutnya

1. **Selesaikan** modul Registrasi & Finalisasi yang masih ada bug
2. **Stabilkan** Dashboard utama
3. **Mulai implement** Laporan Audit dari `ReportService.php` → Controller → Views
4. **Testing** dengan data dummy
5. **Presentasi** ke klien/dosen

---

> **Dokumen ini adalah gambaran konsep — detail teknis ada di `PLAN_REPORT_AUDIT.md`**
