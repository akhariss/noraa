-- Nora 2.0: V4 Unified Workflow Migration (Elite Architecture)
-- Objective: Consolidate Note Templates directly into Workflow Steps to remove data redundancy.

-- 1. Create temporary column for the message template in workflow_steps
ALTER TABLE workflow_steps ADD COLUMN note_template TEXT DEFAULT NULL AFTER behavior_role;

-- 2. Populate workflow_steps with existing template data from note_templates (Syncing by Key)
UPDATE workflow_steps ws
JOIN note_templates nt ON ws.step_key = nt.status_key
SET ws.note_template = nt.template_body;

-- 3. Safety Fallback: Ensure no critical step is left without a default note
UPDATE workflow_steps SET note_template = 'Status perkara Anda saat ini: [status_label]. Catatan: [catatan]' WHERE note_template IS NULL;

-- 4. Secure the data: Note template for 'diserahkan' should specifically have the [penerima] placeholder if it doesn't already
UPDATE workflow_steps 
SET note_template = 'Dokumen telah diserahkan kepada [penerima]. Terima kasih telah menggunakan layanan kami.' 
WHERE step_key = 'diserahkan' AND (note_template NOT LIKE '%[penerima]%' OR note_template IS NULL);

-- 5. Finalize the Architecture: Delete the redundant table (Michelin Zero Waste Policy)
DROP TABLE IF EXISTS note_templates;

-- 6. Log completion (SK-10 Audit Trial)
INSERT INTO audit_logs (event_type, description, user_id, ip_address, created_at)
VALUES ('DB_MIGRATION', 'V4: Unified Workflow & Note Template Consolidation Complete.', 1, '127.0.0.1', NOW());
