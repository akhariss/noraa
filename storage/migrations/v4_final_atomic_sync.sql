-- Nora 2.0: V4 Final Atomic Synchronization 
-- Objective: 1:1 ID and Key Matching between Template and Workflow.

-- 1. Wipe note_templates to start fresh (Zero Waste)
TRUNCATE TABLE note_templates;

-- 2. Atomic Sync: Re-inserting every template tied to its correct Workflow ID
INSERT INTO note_templates (id, status_key, template_body, updated_at)
SELECT id, step_key, 
    CASE 
        WHEN step_key = 'draft' THEN 'Perkara Anda telah terdaftar dan saat ini sedang dalam tahap awal. [catatan]'
        WHEN step_key = 'pembayaran_admin' THEN 'Proses pembayaran jasa notaris sedang dilakukan sebagai bagian dari tahapan awal penanganan perkara. [catatan]'
        WHEN step_key = 'validasi_sertifikat' THEN 'Sertifikat sedang diperiksa untuk memastikan data yang tercatat sesuai dengan catatan resmi. [catatan]'
        WHEN step_key = 'pencecekan_sertifikat' THEN 'Dilakukan pengecekan lanjutan untuk memastikan sertifikat bebas dari kendala hukum atau administrasi. [catatan]'
        WHEN step_key = 'pembayaran_pajak' THEN 'Proses pembayaran pajak yang berkaitan dengan perkara Anda sedang dilaksanakan. [catatan]'
        WHEN step_key = 'validasi_pajak' THEN 'Pembayaran pajak sedang dalam tahap pemeriksaan dan validasi oleh instansi terkait. [catatan]'
        WHEN step_key = 'penomoran_akta' THEN 'Akta sedang dalam proses penomoran sebagai bagian dari legalitas dokumen Anda. [catatan]'
        WHEN step_key = 'pendaftaran' THEN 'Perkara sedang dalam proses pendaftaran resmi ke instansi yang berwenang. [catatan]'
        WHEN step_key = 'pembayaran_pnbp' THEN 'Pembayaran PNBP sedang diproses sebagai bagian dari biaya resmi pendaftaran perkara. [catatan]'
        WHEN step_key = 'pemeriksaan_bpn' THEN 'Berkas perkara sedang dalam tahap pemeriksaan oleh pihak BPN. [catatan]'
        WHEN step_key = 'perbaikan' THEN 'Terdapat penyesuaian atau perbaikan administrasi yang sedang kami proses untuk kelancaran perkara. [catatan]'
        WHEN step_key = 'selesai' THEN 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]'
        WHEN step_key = 'diserahkan' THEN 'Dokumen telah diserahkan kepada [penerima]. Terima kasih telah menggunakan layanan kami.'
        WHEN step_key = 'ditutup' THEN 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda.'
        WHEN step_key = 'batal' THEN 'Perkara ini dinyatakan batal dan tidak dilanjutkan. [catatan]'
        ELSE CONCAT('Status perkara Anda: ', label, '. [catatan]')
    END, 
    NOW()
FROM workflow_steps;

-- 3. Verification Scan
SELECT * FROM note_templates ORDER BY id ASC;
