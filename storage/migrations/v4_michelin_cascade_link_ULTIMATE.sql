-- Nora 2.0: V4 Michelin Cascade Link (ULTIMATE RELOAD)
-- Objective: Absolute Alignment. Edit workflow = Edit template. Delete workflow = Delete template.

-- 1. Ensure step_key in workflow_steps is UNIQUE (Req for parent key)
ALTER TABLE workflow_steps ADD UNIQUE (step_key);

-- 2. Cleanup: Delete ANY possible previous constraint and orphan data
ALTER TABLE note_templates DROP FOREIGN KEY IF EXISTS fk_note_workflow;
ALTER TABLE note_templates DROP FOREIGN KEY IF EXISTS fk_note_workflow_v2;

-- 3. Cleanup: Link existing orphans if any (Strict Data Integrity)
DELETE FROM note_templates WHERE status_key NOT IN (SELECT step_key FROM workflow_steps);

-- 4. THE CHAIN: Add Fresh Foreign Key with CASCADE
-- Handles the auto-edit and auto-delete mission from the USER.
ALTER TABLE note_templates
ADD CONSTRAINT fk_note_workflow_v4 
FOREIGN KEY (status_key) 
REFERENCES workflow_steps(step_key) 
ON UPDATE CASCADE 
ON DELETE CASCADE;

-- 5. Final Scan
SELECT status_key, template_body FROM note_templates ORDER BY id ASC;
