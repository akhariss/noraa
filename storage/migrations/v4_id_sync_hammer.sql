-- Nora 2.0: V4 ID Synchronization Hammer
-- Objective: Ensure ID and Keys are perfectly identical between Template and Workflow.

-- 1. Create a truly synchronized copy in a temporary table
CREATE TEMPORARY TABLE tmp_templates AS 
SELECT id, step_key as status_key, '' as template_body FROM workflow_steps;

-- 2. Update the temp table with existing data from the current note_templates
UPDATE tmp_templates tt
JOIN note_templates nt ON tt.status_key = nt.status_key
SET tt.template_body = nt.template_body;

-- 3. Kill the old table to rebuild it with correct IDs
DELETE FROM note_templates;

-- 4. Re-insert data with the CORRECT ID and Key from the Workflow table
INSERT INTO note_templates (id, status_key, template_body, updated_at)
SELECT id, step_key, template_body, NOW() FROM tmp_templates;

-- 5. Clean up
DROP TEMPORARY TABLE tmp_templates;

-- 6. Verification
SELECT * FROM note_templates ORDER BY id ASC;
