-- ═══════════════════════════════════════════════════════════════
-- MISSION: THE ULTIMATE MICHELIN CLEAN-UP (v5.22 - Bulletproof)
-- ═══════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 0;

-- 1. FIX WORKFLOW BEHAVIOR ROLES (Standard 0-6)
UPDATE workflow_steps SET behavior_role = 4 WHERE step_key = 'batal';
UPDATE workflow_steps SET behavior_role = 5 WHERE step_key = 'diserahkan';
UPDATE workflow_steps SET behavior_role = 6 WHERE step_key = 'ditutup';

-- 2. CLEAN AUDIT LOG
-- Step A: Drop the foreign key constraint FIRST
ALTER TABLE audit_log DROP FOREIGN KEY audit_log_ibfk_1;
-- Step B: Now safe to drop the columns
ALTER TABLE audit_log
DROP COLUMN registrasi_id,
DROP COLUMN old_value;

-- 3. UPGRADE REGISTRASI_HISTORY (Integer-ID Base)
-- Step A: Add new INT(11) columns (SIGNED to match workflow_steps.id)
ALTER TABLE registrasi_history
ADD COLUMN status_old_id INT(11) NULL AFTER registrasi_id,
ADD COLUMN status_new_id INT(11) NULL AFTER status_old_id;

-- Step B: Map existing string data to integer IDs
UPDATE registrasi_history rh
JOIN workflow_steps ws_old ON rh.status_old = ws_old.step_key
SET rh.status_old_id = ws_old.id;

UPDATE registrasi_history rh
JOIN workflow_steps ws_new ON rh.status_new = ws_new.step_key
SET rh.status_new_id = ws_new.id;

-- Step C: Purge legacy string columns and redundant user info
ALTER TABLE registrasi_history
DROP COLUMN status_old,
DROP COLUMN status_new,
DROP COLUMN user_name,
DROP COLUMN user_role;

-- 4. LINK FOREIGN KEYS (Integrity Bridge)
ALTER TABLE registrasi_history
ADD CONSTRAINT fk_hist_status_old FOREIGN KEY (status_old_id) REFERENCES workflow_steps(id) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT fk_hist_status_new FOREIGN KEY (status_new_id) REFERENCES workflow_steps(id) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
