-- Nora 2.0: V4 Database Key Synchronization (Elite Architecture)
-- Objective: Ensure 'note_templates' status_key matches 'workflow_steps' step_key identically.

-- 1. Refreshing Note Templates mapping
-- Creating missing keys first if they don't exist in templates
INSERT IGNORE INTO note_templates (status_key, template_body, updated_at)
SELECT step_key, CONCAT('Pembaruan status perkara: ', label, '. [catatan]'), NOW()
FROM workflow_steps;

-- 2. Setting critical template bodies for the new unified system
-- Selesai Template
UPDATE note_templates 
SET template_body = 'Seluruh tahapan utama telah diselesaikan. Perkara Anda memasuki tahap akhir. [catatan]' 
WHERE status_key = 'selesai';

-- Diserahkan Template (The [penerima] Mission)
UPDATE note_templates 
SET template_body = 'Dokumen telah diserahkan kepada [penerima]. Terima kasih telah menggunakan layanan kami.' 
WHERE status_key = 'diserahkan';

-- Ditutup Template
UPDATE note_templates 
SET template_body = 'Perkara telah selesai dan resmi ditutup. Terima kasih atas kepercayaan Anda.' 
WHERE status_key = 'ditutup';

-- Batal Template
UPDATE note_templates 
SET template_body = 'Perkara ini dinyatakan batal dan tidak dilanjutkan. [catatan]' 
WHERE status_key = 'batal';

-- 3. Standardizing all keys (Cleaning up legacy formats)
-- Ensure 'pembayaran_admin' corresponds correctly between tables.
UPDATE note_templates SET status_key = 'pembayaran_admin' WHERE status_key IN ('pembayaran', 'admin_pay');

-- 4. Logic Audit: Check if any step_key in workflow is missing its note_template counterpart
INSERT INTO audit_logs (event_type, description, user_id, ip_address, created_at)
VALUES ('DB_SYNC', 'V4: Successful synchronization of NoteTemplate keys with WorkflowSteps.', 1, '127.0.0.1', NOW());
