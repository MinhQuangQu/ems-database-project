-- 1. DATABASE CREATION
DROP DATABASE IF EXISTS db_employee_infomation_manager;
CREATE DATABASE `db_employee_infomation_manager`;
USE `db_employee_infomation_manager`;

-- 2. TABLE CREATION
-- We create DEPARTMENT first, but without the manager_id FK, it will be filled later 
CREATE TABLE `DEPARTMENT` (
    `department_id` INT AUTO_INCREMENT PRIMARY KEY,
    `department_name` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150),
    `manager_id` INT NULL -- this will be filled when create employee table
);

CREATE TABLE `EMPLOYEE` (
    `employee_id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
    `date_of_birth` DATE NOT NULL,
    `phone_number` VARCHAR(20) UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `address` VARCHAR(255),
    `hire_date` DATE NOT NULL,
    `department_id` INT NOT NULL,
    `position` VARCHAR(100) NOT NULL,
    `base_salary` DECIMAL(10, 2) NOT NULL,
    CONSTRAINT `fk_emp_dept` FOREIGN KEY (`department_id`) REFERENCES `DEPARTMENT`(`department_id`)
);

-- add the manager_id FOREIGN KEY constraint to DEPARTMENT
ALTER TABLE `DEPARTMENT`
ADD CONSTRAINT `fk_dept_manager`
FOREIGN KEY (`manager_id`) REFERENCES `EMPLOYEE`(`employee_id`)
ON DELETE SET NULL; -- If manager leaves, set dept manager to NULL

CREATE TABLE `PROJECT` (
    `project_id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_name` VARCHAR(200) NOT NULL,
    `start_date` DATE,
    `end_date` DATE,
    `budget` DECIMAL(12, 2),
    `department_id` INT NOT NULL,
    CONSTRAINT `fk_proj_dept` FOREIGN KEY (`department_id`) REFERENCES `DEPARTMENT`(`department_id`)
);

CREATE TABLE `ASSIGNMENT` (
    `assignment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `project_id` INT NOT NULL,
    `role` VARCHAR(100),
    `assigned_date` DATE NOT NULL,
    `hours_worked` DECIMAL(5, 2) DEFAULT 0.00,
    CONSTRAINT `fk_assign_emp` FOREIGN KEY (`employee_id`) REFERENCES `EMPLOYEE`(`employee_id`),
    CONSTRAINT `fk_assign_proj` FOREIGN KEY (`project_id`) REFERENCES `PROJECT`(`project_id`),
    UNIQUE KEY `uq_emp_proj` (`employee_id`, `project_id`) -- An employee can have one role per project
);

CREATE TABLE `ATTENDANCE` (
    `attendance_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `work_date` DATE NOT NULL,
    `check_in` TIME,
    `check_out` TIME,
    `status` ENUM('Present', 'Absent', 'On Leave') NOT NULL,
    CONSTRAINT `fk_att_emp` FOREIGN KEY (`employee_id`) REFERENCES `EMPLOYEE`(`employee_id`),
    UNIQUE KEY `uq_emp_date` (`employee_id`, `work_date`) -- One record per employee per day
);

CREATE TABLE `BONUS_DEDUCTION` (
    `bd_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `type` ENUM('Bonus', 'Deduction') NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `effective_date` DATE NOT NULL,
    CONSTRAINT `fk_bd_emp` FOREIGN KEY (`employee_id`) REFERENCES `EMPLOYEE`(`employee_id`)
);

CREATE TABLE `SALARY_PAYMENT` (
    `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `payment_date` DATE NOT NULL,
    `month` INT NOT NULL,
    `year` INT NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `payment_status` ENUM('Unpaid', 'Paid', 'Pending') NOT NULL DEFAULT 'Pending',
    CONSTRAINT `fk_sp_emp` FOREIGN KEY (`employee_id`) REFERENCES `EMPLOYEE`(`employee_id`),
    UNIQUE KEY `uq_emp_month_year` (`employee_id`, `month`, `year`) -- One payment record per employee per month
);

CREATE TABLE `USERS` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,       
    `password` VARCHAR(255) NOT NULL
);