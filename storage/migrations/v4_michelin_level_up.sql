-- Nora 2.0: V4 Michelin Level Up (Final Refactor)
-- Objective: Scale up performance by moving from String keys to Integer IDs (Michelin v4.50).

-- 1. ADD NEW ELITE COLUMN
ALTER TABLE note_templates ADD COLUMN workflow_step_id INT(11) AFTER id;

-- 2. MAPPING MAGIC (Filling Data based on existing Keys)
UPDATE note_templates nt
JOIN workflow_steps ws ON nt.status_key = ws.step_key
SET nt.workflow_step_id = ws.id;

-- 3. CLEANING UP NFT INDEXES (Removing duplicate indexes from workflow_steps)
ALTER TABLE workflow_steps DROP INDEX step_key_2;
ALTER TABLE workflow_steps DROP INDEX step_key_3;
ALTER TABLE workflow_steps DROP INDEX step_key_4;

-- 4. CLEANING UP NFT INDEXES (Removing duplicate indexes from note_templates)
ALTER TABLE note_templates DROP FOREIGN KEY fk_note_workflow_v4;
ALTER TABLE note_templates DROP INDEX status_key_2;
ALTER TABLE note_templates DROP INDEX idx_status_key;

-- 5. ESTABLISHING THE NEW ELITE CHAIN (Integer FK)
ALTER TABLE note_templates
ADD CONSTRAINT fk_workflow_step_id
FOREIGN KEY (workflow_step_id) 
REFERENCES workflow_steps(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- 6. FINAL CLEANING (Removing the legacy String key)
ALTER TABLE note_templates DROP COLUMN status_key;

-- 7. OPTIMIZATION: Ensure the new FK is indexed
CREATE INDEX idx_workflow_step_id ON note_templates(workflow_step_id);

-- 8. VERIFICATION
SELECT * FROM note_templates;
