-- ============================================================================
-- MIGRATION: v2.1 - Add Lock Mechanism Columns
-- Database: norasblmupdate2
-- Date: 2026-04-05
-- Author: Development Team
-- ============================================================================

-- ⚠️ IMPORTANT: Run this migration BEFORE using the new lock features
-- This script adds two new columns for lock mechanism and batal flag

-- Check if columns exist before adding them
-- This prevents errors if migration is run multiple times

USE norasblmupdate3;

-- Check and add 'locked' column if it doesn't exist
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'registrasi' 
AND COLUMN_NAME = 'locked' 
AND TABLE_SCHEMA = DATABASE();

IF @col_exists = 0 THEN
    ALTER TABLE registrasi 
    ADD COLUMN locked tinyint(1) DEFAULT 0 COMMENT 'Lock mechanism to prevent concurrent edits' 
    AFTER catatan_internal;
    SELECT 'Column `locked` added successfully' AS status;
ELSE
    SELECT 'Column `locked` already exists' AS status;
END IF;

-- Check and add 'batal_flag' column if it doesn't exist
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'registrasi' 
AND COLUMN_NAME = 'batal_flag' 
AND TABLE_SCHEMA = DATABASE();

IF @col_exists = 0 THEN
    ALTER TABLE registrasi 
    ADD COLUMN batal_flag tinyint(1) DEFAULT 0 COMMENT 'Flag to indicate cancellation status' 
    AFTER locked;
    SELECT 'Column `batal_flag` added successfully' AS status;
ELSE
    SELECT 'Column `batal_flag` already exists' AS status;
END IF;

-- ============================================================================
-- Verification: Show the updated table structure
-- ============================================================================
DESCRIBE registrasi;

-- ============================================================================
-- Summary
-- ============================================================================
-- If you see both columns in the table structure above, migration successful!
-- The new columns will NOT affect existing data (default values are 0 = false)
-- ============================================================================
