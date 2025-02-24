-- ---------------------------------------------------------------------------
-- EXAMPLE OF DATABASE MIGRATION: REFACTORING TASK STATUS FIELD/RELATIONSHIP
-- ---------------------------------------------------------------------------
--
-- In this migration we are going to replace the "task_status_id" foreign key field to a VARCHAR to apply a different
-- approach to the schema avoiding the existence of a task_status table.

SET FOREIGN_KEY_CHECKS = 0;

-- Remove "task_status_id" foreign key and index
ALTER TABLE `docplanner-mysql-task`.`tasks`
    DROP FOREIGN KEY `fk_tasks_task_status_id`;
ALTER TABLE `docplanner-mysql-task`.`tasks`
    DROP INDEX `fk_tasks_task_status_id_idx`;

-- Remove the index designed to improve performance on common queries
ALTER TABLE `docplanner-mysql-task`.`tasks`
    DROP INDEX `user_id_task_status_id_deleted_at_idx`;

-- Add new column "status"
ALTER TABLE `docplanner-mysql-task`.`tasks`
    ADD COLUMN `status` VARCHAR(100) NULL AFTER `user_id`;

-- Migrate all status
UPDATE `docplanner-mysql-task`.`tasks` AS t
    JOIN `docplanner-mysql-task`.`task_status` AS ts ON t.task_status_id = ts.id
    SET t.status = CASE
        WHEN ts.name = 'Pending' THEN 'pending'
        WHEN ts.name = 'In progress' THEN 'in_progress'
        WHEN ts.name = 'Completed' THEN 'completed'
        ELSE LOWER(REPLACE(ts.name, ' ', '_')) -- Just in case (shouldn't be necessary)
    END;

-- Set not null definition to "status" field after status migration
ALTER TABLE `docplanner-mysql-task`.`tasks`
    MODIFY COLUMN `status` VARCHAR(100) NOT NULL;

-- Create again the index designed to improve performance on common queries
ALTER TABLE `docplanner-mysql-task`.`tasks`
    ADD INDEX `user_id_status_deleted_at_idx` (`user_id`, `status`, `deleted_at`);

-- "task_status_id" field is not used anymore
ALTER TABLE `docplanner-mysql-task`.`tasks`
DROP COLUMN `task_status_id`;

-- "task_status" table is not used anymore
DROP TABLE `docplanner-mysql-task`.`task_status`;

SET FOREIGN_KEY_CHECKS = 1;