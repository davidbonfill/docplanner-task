-- -----------------------------------------------------
-- KEY POINTS OF THIS DATABASE SCHEMA APPROACH
-- -----------------------------------------------------
--
-- A "tasks" table in order to accomplish the tasks CRUD operations
-- A "users" table in order to assign tasks to users by a constrained foreign key ("tasks"."user_id")
-- A "task_status" table in order to:
--  1. Create more status without changing the database schema (it's one possible approach; other approaches could be using MySQL enums or using a varchar that relies on code-based constants)
--  2. Associate a status to a task by a constrained foreign key ("tasks"."task_status_id")
-- The index "user_id_task_status_id_deleted_at_idx" has been created to be useful on these situations:
--  1. Queries filtering by "user_id" (because it's the first column of the index)
--  2. Queries filtering by "user_id" and "deleted_at" (common use: getting non-deleted user tasks)
--  3. Queries filtering by "user_id", "task_status_id" and "deleted_at" (common use: getting non-deleted user tasks filtered by status)
-- Every table has a "deleted_at" field in order to apply soft-deleting


SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema docplanner-mysql-task
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `docplanner-mysql-task` ;

-- -----------------------------------------------------
-- Schema docplanner-mysql-task
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `docplanner-mysql-task` ;
USE `docplanner-mysql-task` ;

-- -----------------------------------------------------
-- Table `docplanner-mysql-task`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `docplanner-mysql-task`.`users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docplanner-mysql-task`.`task_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `docplanner-mysql-task`.`task_status` (
    `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT, -- Any integer-type bigger than TINYINT would be unnecessary
    `name` VARCHAR(100) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `docplanner-mysql-task`.`tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `docplanner-mysql-task`.`tasks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `task_status_id` TINYINT UNSIGNED NOT NULL,
    `description` VARCHAR(255) NOT NULL, -- Any text-type may be appropriate as well depending on the requirements
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tasks_user_id_idx` (`user_id` ASC) VISIBLE,
    INDEX `fk_tasks_task_status_id_idx` (`task_status_id` ASC) VISIBLE,
    INDEX `user_id_task_status_id_deleted_at_idx` (`user_id` ASC, `task_status_id` ASC, `deleted_at` ASC) VISIBLE, -- Index to improve performance on the most common query
    CONSTRAINT `fk_tasks_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `docplanner-mysql-task`.`users` (`id`)
        ON DELETE RESTRICT
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_tasks_task_status_id`
        FOREIGN KEY (`task_status_id`)
        REFERENCES `docplanner-mysql-task`.`task_status` (`id`)
        ON DELETE RESTRICT
        ON UPDATE NO ACTION)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8mb4;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


-- -----------------------------------------------------
-- DEMO DATA
-- -----------------------------------------------------

INSERT INTO `docplanner-mysql-task`.users (name) VALUES('John Doe');

INSERT INTO `docplanner-mysql-task`.task_status (name) VALUES('Pending');
INSERT INTO `docplanner-mysql-task`.task_status (name) VALUES('In progress');
INSERT INTO `docplanner-mysql-task`.task_status (name) VALUES('Completed');

INSERT INTO `docplanner-mysql-task`.tasks (user_id, task_status_id, description) VALUES(1, 1, 'A');
INSERT INTO `docplanner-mysql-task`.tasks (user_id, task_status_id, description) VALUES(1, 1, 'B');
INSERT INTO `docplanner-mysql-task`.tasks (user_id, task_status_id, description) VALUES(1, 2, 'C');
INSERT INTO `docplanner-mysql-task`.tasks (user_id, task_status_id, description) VALUES(1, 3, 'D');