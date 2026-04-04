-- Nora 2.0: V4 Database Final hammer Sync
-- Objective: Force synchronization by cleaning and ensuring exact matches.

-- 1. Clean up potential whitespace in keys
UPDATE note_templates SET status_key = TRIM(status_key);
UPDATE workflow_steps SET step_key = TRIM(step_key);

-- 2. Force Sync Templates (Using TRIM to be extra sure)
-- Selesai
UPDATE note_templates 
SET template_body = 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]' 
WHERE TRIM(status_key) = 'selesai';

-- Diserahkan
UPDATE note_templates 
SET template_body = 'Dokumen telah diserahkan kepada [penerima]. Terima kasih telah menggunakan layanan kami.' 
WHERE TRIM(status_key) = 'diserahkan';

-- Ditutup
UPDATE note_templates 
SET template_body = 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda.' 
WHERE TRIM(status_key) = 'ditutup';

-- Batal
UPDATE note_templates 
SET template_body = 'Perkara ini dinyatakan batal dan tidak dilanjutkan. [catatan]' 
WHERE TRIM(status_key) = 'batal';

-- 3. Validation: Verify if 'diserahkan' exists
SELECT status_key, template_body FROM note_templates WHERE status_key = 'diserahkan';
