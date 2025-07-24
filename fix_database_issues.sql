-- Fix marks table status enum to include 'submitted' and other status values
-- Current enum: ENUM('pass', 'fail', 'absent', 'incomplete')
-- New enum: ENUM('pass', 'fail', 'absent', 'incomplete', 'draft', 'submitted', 'verified')

ALTER TABLE marks MODIFY COLUMN status ENUM('pass', 'fail', 'absent', 'incomplete', 'draft', 'submitted', 'verified') DEFAULT 'pass';

-- Update exams table status enum for examination requirements
-- Current enum: ENUM('scheduled', 'ongoing', 'completed', 'cancelled')
-- New enum: ENUM('completed', 'incomplete', 'scheduled', 'cancelled')

ALTER TABLE exams MODIFY COLUMN status ENUM('completed', 'incomplete', 'scheduled', 'cancelled') DEFAULT 'scheduled';

-- Update exams table exam_type enum for examination requirements
-- Current enum: ENUM('internal', 'board', 'practical', 'midterm', 'annual', 'quiz', 'test', 'final', 'assignment')
-- New enum: ENUM('first_assessment', 'first_terminal', 'second_assessment', 'second_terminal', 'third_assessment', 'final_term', 'monthly_term', 'weekly_test')

ALTER TABLE exams MODIFY COLUMN exam_type ENUM('first_assessment', 'first_terminal', 'second_assessment', 'second_terminal', 'third_assessment', 'final_term', 'monthly_term', 'weekly_test') DEFAULT 'first_assessment';

-- Verify the changes
DESCRIBE marks;
DESCRIBE exams;

-- Check if the changes were successful
SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'marks' AND COLUMN_NAME = 'status' AND TABLE_SCHEMA = DATABASE();

SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'exams' AND COLUMN_NAME = 'status' AND TABLE_SCHEMA = DATABASE();

SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'exams' AND COLUMN_NAME = 'exam_type' AND TABLE_SCHEMA = DATABASE();
