-- Data Insertion
USE `db_employee_infomation_manager`;
-- Insert 5 Departments
INSERT INTO `DEPARTMENT` (`department_name`, `location`) VALUES
('Human Resources', 'Building A, Floor 1'),
('Engineering', 'Building B, Floor 2'),
('Sales', 'Building A, Floor 2'),
('Marketing', 'Building A, Floor 3'),
('Finance', 'Building C, Floor 1');

-- Insert 20 Employees
-- (Dept 1: HR, 2: Engineer, 3: Sales, 4: Marketingg, 5: Finance)
INSERT INTO `EMPLOYEE` (`full_name`, `gender`, `date_of_birth`, `phone_number`, `email`, `address`, `hire_date`, `department_id`, `position`, `base_salary`) VALUES
-- HR (Dept 1)
('Bui Ngoc Anh', 'Female', '1985-03-15', '0967 428 315', 'ngocanh.bui@snakecompany.com', 'Số 12, Ngõ 45, Phố Nguyễn Trãi, Thanh Xuân, Hà Nội', '2015-06-01', 1, 'HR Manager', 90000.00),
('Bui Hien Vinh', 'Male', '1990-07-22', '0896 221 704', 'hienvinh.bui@snakecompany.com', 'Số 8, Ngõ 88, Phố Trần Duy Hưng, Cầu Giấy, Hà Nội', '2018-09-15', 1, 'HR Specialist', 65000.00),
('Bui Phuong Anh', 'Female', '1995-11-30', '0825 917 643', 'phuonganh.bui@snakecompany.com', 'Số 21, Ngách 12/3, Phố Khương Trung, Thanh Xuân, Hà Nội', '2020-02-10', 1, 'Recruiter', 60000.00),

-- Engineering (Dept 2)
('Dang Huy Gia Bao', 'Male', '1982-01-10', '0934 802 517', 'giabao.dang@snakecompany.com', 'Số 34, Ngõ 245, Phố Định Công, Hoàng Mai, Hà Nội', '2012-03-20', 2, 'Lead Engineer', 140000.00),
('Cao Thi Quynh Chi', 'Female', '1992-05-25', '0912 648 709', 'quynhchi.cao@snakecompany.com', 'Số 17, Ngõ 22, Phố Tôn Thất Tùng, Đống Đa, Hà Nội', '2019-07-01', 2, 'Software Engineer', 110000.00),
('Le Anh Minh', 'Male', '1996-09-12', '0983 152 664', 'anhminh.le@snakecompany.com', 'Số 5, Ngách 42/6, Phố Hoàng Quốc Việt, Cầu Giấy, Hà Nội', '2021-01-20', 2, 'Junior Engineer', 85000.00),
('Doan Hoang Linh', 'Female', '1993-02-18', '0905 782 119', 'hoanglinh.doan@snakecompany.com', 'Số 19, Ngõ 89, Phố Láng Hạ, Đống Đa, Hà Nội', '2020-05-11', 2, 'QA Engineer', 95000.00),
('Le Khang Minh', 'Male', '1988-12-05', '0888 340 957', 'khangminh.le@snakecompany.com', 'Số 66, Ngõ 100, Phố Trung Kính, Cầu Giấy, Hà Nội', '2017-11-05', 2, 'DevOps Engineer', 120000.00),

-- Sales (Dept 3)
('Dinh Tung Duong', 'Male', '1987-08-19', '0946 212 880', 'tungduong.dinh@snakecompany.com', 'Số 9, Ngõ 12, Phố Nguyễn Xiển, Thanh Xuân, Hà Nội', '2016-04-25', 3, 'Sales Manager', 100000.00),
('Dinh Ngoc Mai', 'Female', '1991-04-02', '0327 549 633', 'ngocmai.dinh@snakecompany.com', 'Số 45, Ngõ 29, Phố Kim Mã, Ba Đình, Hà Nội', '2018-08-01', 3, 'Account Executive', 70000.00),
('Le Thanh Hai', 'Male', '1994-06-14', '0386 720 418', 'thanhhai.le@snakecompany.com', 'Số 3, Ngõ 43, Phố Giải Phóng, Hai Bà Trưng, Hà Nội', '2021-03-12', 3, 'Sales Associate', 55000.00),

-- Marketing (Dept 4)
('Do Thuy Ngoc', 'Female', '1989-10-20', '0379 664 251', 'thuynoc.do@snakecompany.com', 'Số 11, Ngõ 52, Phố Nguyễn Chí Thanh, Ba Đình, Hà Nội', '2017-01-30', 4, 'Marketing Manager', 95000.00),
('Le Quang Huy', 'Male', '1993-01-08', '0703 928 146', 'quanghuy.le@snakecompany.com', 'Số 23, Ngõ 124, Phố Hoàng Hoa Thám, Tây Hồ, Hà Nội', '2019-10-01', 4, 'Content Strategist', 72000.00),
('Le My Anh', 'Female', '1997-03-28', '0794 651 803', 'myanh.le@snakecompany.com', 'Số 7, Ngõ 14, Phố Xuân Thủy, Cầu Giấy, Hà Nội', '2022-01-15', 4, 'Social Media Coordinator', 58000.00),
('Le Vo Viet Khoi', 'Male', '1990-08-05', '0859 317 204', 'vietkhoi.le@snakecompany.com', 'Số 88, Ngách 25/2, Phố Trương Định, Hoàng Mai, Hà Nội', '2018-05-20', 4, 'SEO Specialist', 70000.00),

-- Finance (Dept 5)
('Le Nguyen Phuong Thao', 'Female', '1986-06-11', '0847 560 998', 'phuongthao.le@snakecompany.com', 'Số 10, Ngõ 191, Phố Minh Khai, Hai Bà Trưng, Hà Nội', '2014-07-10', 5, 'Finance Manager', 110000.00),
('Luong Ngoc Huy', 'Male', '1992-09-03', '0813 240 556', 'ngochuy.luong@snakecompany.com', 'Số 4, Ngõ 76, Phố Nguyễn An Ninh, Hoàng Mai, Hà Nội', '2019-11-20', 5, 'Financial Analyst', 80000.00),
('Ngo Thi Viet Ha', 'Female', '1995-12-19', '0924 874 330', 'vietha.ngo@snakecompany.com', 'Số 31, Ngõ 120, Phố Cầu Giấy, Cầu Giấy, Hà Nội', '2021-06-05', 5, 'Accountant', 75000.00),
('Nguyen Phuc Chinh', 'Male', '1998-02-27', '0899 705 412', 'phucchinh.nguyen@snakecompany.com', 'Số 14, Ngách 8/5, Phố Tây Sơn, Đống Đa, Hà Nội', '2022-04-01', 5, 'Payroll Specialist', 62000.00),
('Nguyen Khanh Huyen', 'Female', '1991-07-16', '0392 146 875', 'khanhhuyen.nguyen@snakecompany.com', 'Số 6, Ngõ 332, Phố Nguyễn Trãi, Thanh Xuân, Hà Nội', '2023-01-05', 1, 'HR Assistant', 50000.00);



-- Update Department Managers (Employee IDs are auto-incremented)
UPDATE `DEPARTMENT` SET `manager_id` = 1 WHERE `department_id` = 1; -- Bui Ngoc Anh for HR
UPDATE `DEPARTMENT` SET `manager_id` = 4 WHERE `department_id` = 2; -- Dang Huy Gia Bao for Engineering
UPDATE `DEPARTMENT` SET `manager_id` = 9 WHERE `department_id` = 3; -- Dinh Tung Duong for Sales
UPDATE `DEPARTMENT` SET `manager_id` = 12 WHERE `department_id` = 4; -- Do Thuy Ngoc for Marketing
UPDATE `DEPARTMENT` SET `manager_id` = 16 WHERE `department_id` = 5; -- Le Nguyen Phuong Thao for Finance

-- Projects
INSERT INTO `PROJECT` (`project_name`, `start_date`, `end_date`, `budget`, `department_id`) VALUES
('SmartFactory Sensor Hub', '2024-01-15', '2024-12-15', 500000.00, 2), -- Engineering
('Unified Login Gateway', '2024-03-01', '2025-02-28', 750000.00, 2), -- Engineering
('B2B Lead Qualification Framework', '2024-02-01', '2024-08-01', 200000.00, 3), -- Sales
('GenZ Social Engagement Program', '2024-05-01', '2024-09-30', 150000.00, 4), -- Marketing
('Customer Journey Mapping Initiativeh', '2024-04-01', '2024-10-31', 300000.00, 4), -- Marketing
('Skill Matrix & Upskill Roadmap', '2024-01-10', '2024-06-30', 120000.00, 1), -- HR
('Automated Expense Audit System', '2024-09-01', '2024-11-30', 50000.00, 5), -- Finance
('Real-Time Fleet Tracker', '2024-06-01', '2024-12-31', 180000.00, 2); -- Engineering

-- Insert 30 Assignments
INSERT INTO `ASSIGNMENT` (`employee_id`, `project_id`, `role`, `assigned_date`, `hours_worked`) VALUES
-- SmartFactory Sensor Hub (Proj 1)
(4, 1, 'Project Lead', '2024-01-15', 150.5),
(5, 1, 'Senior Developer', '2024-01-15', 220.0),
(6, 1, 'Junior Developer', '2024-01-20', 180.0),
(7, 1, 'QA Lead', '2024-01-20', 190.5),
-- Unified Login Gateway (Proj 2)
(4, 2, 'Architect', '2024-03-01', 80.0),
(5, 2, 'Lead Developer', '2024-03-01', 160.0),
(8, 2, 'DevOps Specialist', '2024-03-05', 140.0),
-- B2B Lead Qualification Framework (Proj 3)
(9, 3, 'Project Manager', '2024-02-01', 100.0),
(10, 3, 'User Testing', '2024-02-15', 60.5),
(11, 3, 'Sales Tester', '2024-02-15', 45.0),
-- GenZ Social Engagement Program (Proj 4)
(12, 4, 'Campaign Lead', '2024-05-01', 110.0),
(13, 4, 'Content Writer', '2024-05-01', 130.0),
(14, 4, 'Social Media', '2024-05-10', 120.0),
-- Customer Journey Mapping Initiativeh (Proj 5)
(12, 5, 'Marketing Lead', '2024-04-01', 50.0),
(15, 5, 'SEO Lead', '2024-04-01', 90.0),
(5, 5, 'Backend Developer', '2024-04-05', 70.0), 
-- Skill Matrix & Upskill Roadmap (Proj 6)
(1, 6, 'Project Sponsor', '2024-01-10', 40.0),
(2, 6, 'HR Lead', '2024-01-10', 120.0),
(3, 6, 'User Testing', '2024-01-15', 90.0),
-- Automated Expense Audit System (Proj 7)
(16, 7, 'Audit Lead', '2024-09-01', 0.0), 
(17, 7, 'Analyst', '2024-09-01', 0.0),
(18, 7, 'Accountant', '2024-09-01', 0.0),
-- Real-Time Fleet Tracker (Proj 8)
(8, 8, 'DevOps Lead', '2024-06-01', 80.0),
(6, 8, 'Developer', '2024-06-01', 65.0),
(7, 8, 'QA', '2024-06-05', 70.0),
-- Extra Assignments
(10, 5, 'Sales Consultant', '2024-04-15', 30.0),
(13, 6, 'Content Migration', '2024-03-01', 55.5),
(17, 3, 'Financial Analyst', '2024-02-05', 25.0),
(19, 7, 'Payroll Data', '2024-09-01', 0.0),
(20, 6, 'HR Data Entry', '2024-02-01', 75.0);

-- Insert 60 Attendance Records
-- (Covering 3 workdays for all 20 employees)
INSERT INTO `ATTENDANCE` (`employee_id`, `work_date`, `check_in`, `check_out`, `status`) VALUES
-- Day 1: 2024-10-01
(1, '2024-10-01', '08:55:00', '17:05:00', 'Present'),
(2, '2024-10-01', '09:01:00', '17:02:00', 'Present'),
(3, '2024-10-01', '08:49:00', '16:50:00', 'Present'),
(4, '2024-10-01', '09:15:00', '17:30:00', 'Present'),
(5, '2024-10-01', '09:05:00', '17:10:00', 'Present'),
(6, '2024-10-01', NULL, NULL, 'Absent'),
(7, '2024-10-01', '09:00:00', '17:00:00', 'Present'),
(8, '2024-10-01', '08:58:00', '17:08:00', 'Present'),
(9, '2024-10-01', '08:30:00', '17:15:00', 'Present'),
(10, '2024-10-01', '08:45:00', '17:00:00', 'Present'),
(11, '2024-10-01', '09:02:00', '17:01:00', 'Present'),
(12, '2024-10-01', NULL, NULL, 'On Leave'),
(13, '2024-10-01', '09:00:00', '17:00:00', 'Present'),
(14, '2024-10-01', '09:10:00', '17:05:00', 'Present'),
(15, '2024-10-01', '09:03:00', '17:00:00', 'Present'),
(16, '2024-10-01', '08:50:00', '17:00:00', 'Present'),
(17, '2024-10-01', '08:59:00', '17:02:00', 'Present'),
(18, '2024-10-01', '09:00:00', '16:55:00', 'Present'),
(19, '2024-10-01', '09:00:00', '17:00:00', 'Present'),
(20, '2024-10-01', '08:57:00', '17:03:00', 'Present'),
-- Day 2: 2024-10-02
(1, '2024-10-02', '08:58:00', '17:00:00', 'Present'),
(2, '2024-10-02', '09:03:00', '17:00:00', 'Present'),
(3, '2024-10-02', '08:50:00', '16:55:00', 'Present'),
(4, '2024-10-02', '09:10:00', '17:30:00', 'Present'),
(5, '2024-10-02', '09:00:00', '17:11:00', 'Present'),
(6, '2024-10-02', '09:05:00', '17:00:00', 'Present'),
(7, '2024-10-02', '09:01:00', '17:00:00', 'Present'),
(8, '2024-10-02', '08:59:00', '17:05:00', 'Present'),
(9, '2024-10-02', '08:35:00', '17:10:00', 'Present'),
(10, '2024-10-02', NULL, NULL, 'On Leave'),
(11, '2024-10-02', '09:00:00', '17:00:00', 'Present'),
(12, '2024-10-02', '09:00:00', '17:00:00', 'Present'), 
(13, '2024-10-02', '09:01:00', '17:05:00', 'Present'),
(14, '2024-10-02', '09:12:00', '17:10:00', 'Present'),
(15, '2024-10-02', '09:05:00', '17:00:00', 'Present'),
(16, '2024-10-02', '08:55:00', '17:00:00', 'Present'),
(17, '2024-10-02', '08:58:00', '17:00:00', 'Present'),
(18, '2024-10-02', '09:00:00', '17:00:00', 'Present'),
(19, '2024-10-02', NULL, NULL, 'Absent'),
(20, '2024-10-02', '08:55:00', '17:00:00', 'Present'),
-- Day 3: 2024-10-03
(1, '2024-10-03', '09:00:00', '17:00:00', 'Present'),
(2, '2024-10-03', '09:00:00', '17:00:00', 'Present'),
(3, '2024-10-03', '08:51:00', '16:50:00', 'Present'),
(4, '2024-10-03', '09:12:00', '17:35:00', 'Present'),
(5, '2024-10-03', '09:02:00', '17:15:00', 'Present'),
(6, '2024-10-03', '09:00:00', '17:00:00', 'Present'),
(7, '2024-10-03', '09:00:00', '17:01:00', 'Present'),
(8, '2024-10-03', '08:57:00', '17:02:00', 'Present'),
(9, '2024-10-03', '08:40:00', '17:20:00', 'Present'),
(10, '2024-10-03', '08:45:00', '17:00:00', 'Present'),
(11, '2024-10-03', '09:00:00', '17:00:00', 'Present'),
(12, '2024-10-03', '09:01:00', '17:00:00', 'Present'),
(13, '2024-10-03', '09:00:00', '17:03:00', 'Present'),
(14, '2024-10-03', '09:08:00', '17:00:00', 'Present'),
(15, '2024-10-03', '09:00:00', '17:00:00', 'Present'),
(16, '2024-10-03', '08:50:00', '17:00:00', 'Present'),
(17, '2024-10-03', '08:59:00', '17:00:00', 'Present'),
(18, '2024-10-03', '09:00:00', '16:58:00', 'Present'),
(19, '2024-10-03', '09:00:00', '17:00:00', 'Present'), 
(20, '2024-10-03', '08:59:00', '17:00:00', 'Present');

-- Insert 20 Bonus/Deduction Records
INSERT INTO `BONUS_DEDUCTION` (`employee_id`, `description`, `type`, `amount`, `effective_date`) VALUES
(1, 'Management Bonus', 'Bonus', 1000.00, '2024-09-15'),
(4, 'Project Lead Bonus', 'Bonus', 2500.00, '2024-09-15'),
(5, 'Performance Bonus', 'Bonus', 1500.00, '2024-09-15'),
(10, 'Sales Commission', 'Bonus', 1200.00, '2024-09-10'),
(12, 'Campaign Success Bonus', 'Bonus', 750.00, '2024-10-05'),
(16, 'Finance Bonus', 'Bonus', 1000.00, '2024-09-15'),
(2, 'Health Insurance Premium', 'Deduction', 150.00, '2024-09-01'),
(5, 'Health Insurance Premium', 'Deduction', 150.00, '2024-09-01'),
(8, '401k Contribution', 'Deduction', 500.00, '2024-09-01'),
(10, '401k Contribution', 'Deduction', 300.00, '2024-09-01'),
(13, 'Dental Plan', 'Deduction', 40.00, '2024-09-01'),
(17, 'Dental Plan', 'Deduction', 40.00, '2024-09-01'),
(6, 'Unpaid Leave', 'Deduction', 326.92, '2024-10-01'),
(1, 'Health Insurance Premium', 'Deduction', 150.00, '2024-10-01'),
(4, '401k Contribution', 'Deduction', 1000.00, '2024-10-01'),
(9, 'Sales Commission', 'Bonus', 1800.00, '2024-10-10'),
(14, 'Social Media Award', 'Bonus', 250.00, '2024-10-12'),
(18, 'Accountant Certification Bonus', 'Bonus', 500.00, '2024-09-20'),
(20, 'Overtime Pay', 'Bonus', 300.00, '2024-09-30'),
(11, 'Parking Fee', 'Deduction', 50.00, '2024-10-01');

-- Insert 30 Salary Payment Records
-- Base salaries (per month):
INSERT INTO `SALARY_PAYMENT` (`employee_id`, `payment_date`, `month`, `year`, `total_amount`, `payment_status`) VALUES
-- September Payments (Paid)
(1, '2024-09-30', 9, 2024, 8500.00, 'Paid'), -- 7500 + 1000 bonus
(2, '2024-09-30', 9, 2024, 5263.46, 'Paid'), -- (65000/12) - 150
(3, '2024-09-30', 9, 2024, 5000.00, 'Paid'), -- (60000/12)
(4, '2024-09-30', 9, 2024, 14166.67, 'Paid'), -- (140000/12) + 2500
(5, '2024-09-30', 9, 2024, 10516.67, 'Paid'), -- (110000/12) + 1500 - 150
(6, '2024-09-30', 9, 2024, 7083.33, 'Paid'), -- (85000/12)
(7, '2024-09-30', 9, 2024, 7916.67, 'Paid'), -- (95000/12)
(8, '2024-09-30', 9, 2024, 9500.00, 'Paid'), -- (120000/12) - 500
(9, '2024-09-30', 9, 2024, 8333.33, 'Paid'), -- (100000/12)
(10, '2024-09-30', 9, 2024, 6733.33, 'Paid'), -- (70000/12) + 1200 - 300
(11, '2024-09-30', 9, 2024, 4583.33, 'Paid'), -- (55000/12)
(12, '2024-09-30', 9, 2024, 7916.67, 'Paid'), -- (95000/12)
(13, '2024-09-30', 9, 2024, 5960.00, 'Paid'), -- (72000/12) - 40
(14, '2024-09-30', 9, 2024, 4833.33, 'Paid'), -- (58000/12)
(15, '2024-09-30', 9, 2024, 5833.33, 'Paid'), -- (70000/12)
(16, '2024-09-30', 9, 2024, 10166.67, 'Paid'), -- (110000/12) + 1000
(17, '2024-09-30', 9, 2024, 6626.67, 'Paid'), -- (80000/12) - 40
(18, '2024-09-30', 9, 2024, 6750.00, 'Paid'), -- (75000/12) + 500
(19, '2024-09-30', 9, 2024, 5166.67, 'Paid'), -- (62000/12)
(20, '2024-09-30', 9, 2024, 4466.67, 'Paid'), -- (50000/12) + 300
-- October Payments (Pending)
(1, '2024-10-31', 10, 2024, 7350.00, 'Pending'), -- 7500 - 150
(2, '2024-10-31', 10, 2024, 5416.67, 'Pending'),
(3, '2024-10-31', 10, 2024, 5000.00, 'Pending'),
(4, '2024-10-31', 10, 2024, 10666.67, 'Pending'), -- 11666.67 - 1000
(5, '2024-10-31', 10, 2024, 9166.67, 'Pending'),
(6, '2024-10-31', 10, 2024, 6756.41, 'Pending'), -- 7083.33 - 326.92
(7, '2024-10-31', 10, 2024, 7916.67, 'Pending'),
(8, '2024-10-31', 10, 2024, 10000.00, 'Pending'),
(9, '2024-10-31', 10, 2024, 10133.33, 'Pending'), -- 8333.33 + 1800
(10, '2024-10-31', 10, 2024, 5833.33, 'Pending'),
(11, '2024-10-31', 10, 2024, 4533.33, 'Pending'), -- 4583.33 - 50
(12, '2024-10-31', 10, 2024, 8666.67, 'Pending'), -- 7916.67 + 750
(14, '2024-10-31', 10, 2024, 5083.33, 'Pending'), -- 4833.33 + 250
(16, '2024-10-31', 10, 2024, 9166.67, 'Pending'),
(19, '2024-10-31', 10, 2024, 5166.67, 'Unpaid');

INSERT INTO `USERS` ( `id`, `username`,`password` )  VALUES
(4,'minh','123456'); 
