-- Nora 2.0: V4 Michelin Cascade Link (The Clean Version)
-- Objective: Absolute Alignment. Edit workflow = Edit template. Delete workflow = Delete template.

-- 1. Ensure step_key in workflow_steps is UNIQUE (Req for parent key)
ALTER TABLE workflow_steps ADD UNIQUE (step_key);

-- 2. Build the Bridge: Add Foreign Key with CASCADE on UPDATE and DELETE
-- This handles the auto-edit and auto-delete mission from the USER.
ALTER TABLE note_templates
ADD CONSTRAINT fk_note_workflow 
FOREIGN KEY (status_key) 
REFERENCES workflow_steps(step_key) 
ON UPDATE CASCADE 
ON DELETE CASCADE;

-- 3. Verification Scan
SELECT * FROM note_templates WHERE status_key = 'diserahkan';
