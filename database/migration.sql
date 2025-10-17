-- Migration for Advanced Todo App Enhancement
-- This script adds new features to the existing todo_db database

-- Step 1: Add new columns to tasks table if they don't exist
ALTER TABLE tasks
ADD COLUMN IF NOT EXISTS priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium' AFTER status,
ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT NULL AFTER priority,
ADD COLUMN IF NOT EXISTS archived TINYINT(1) DEFAULT 0 AFTER category;

-- Step 2: Create task_notes table for storing task notes/comments
CREATE TABLE IF NOT EXISTS task_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    note_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    INDEX idx_task_id (task_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_status ON tasks(status);
CREATE INDEX IF NOT EXISTS idx_priority ON tasks(priority);
CREATE INDEX IF NOT EXISTS idx_category ON tasks(category);
CREATE INDEX IF NOT EXISTS idx_archived ON tasks(archived);
CREATE INDEX IF NOT EXISTS idx_due_date ON tasks(due_date);
CREATE INDEX IF NOT EXISTS idx_created_at ON tasks(created_at);

-- Step 4: Update existing tasks to have default priority if NULL
UPDATE tasks SET priority = 'medium' WHERE priority IS NULL;

-- Migration completed successfully!
-- New features added:
-- 1. Priority levels (low, medium, high, urgent)
-- 2. Category/tags for task organization
-- 3. Archive functionality
-- 4. Task notes/comments system
-- 5. Performance indexes for filtering and sorting
