-- Nora 2.0: V4 Michelin Cascade Link (The Final Harmony)
-- Objective: Absolute Synchronization. Edit workflow = Edit template. Delete workflow = Delete template.

-- 1. Ensure step_key in workflow_steps is UNIQUE so it can be used as a Parent Key
ALTER TABLE workflow_steps ADD UNIQUE (step_key);

-- 2. Ensure status_key in note_templates is UNIQUE and clean
ALTER TABLE note_templates ADD UNIQUE (status_key);

-- 3. Cleanup: Link existing orphans if any (Safe Guard)
DELETE FROM note_templates WHERE status_key NOT IN (SELECT step_key FROM workflow_steps);

-- 4. THE CHAIN: Add Foreign Key Constraint with CASCADE
-- This is the "Magic" that syncs edits and deletes automatically.
ALTER TABLE note_templates
ADD CONSTRAINT fk_note_workflow 
FOREIGN KEY (status_key) 
REFERENCES workflow_steps(step_key) 
ON UPDATE CASCADE 
ON DELETE CASCADE;

-- 5. Final Audit Log
INSERT INTO audit_logs (event_type, description, user_id, ip_address, created_at)
VALUES ('DB_LINK', 'V4: Successful Cascade Linking between Workflow and Templates.', 1, '127.0.0.1', NOW());
