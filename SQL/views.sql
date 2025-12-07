CREATE OR REPLACE VIEW v_employees_by_department AS
SELECT
    d.department_id,          -- Get the department ID from the DEPARTMENT table
    d.department_name,        -- Get the department name from the DEPARTMENT table
    e.employee_id,            -- Get the employee ID from the EMPLOYEE table
    e.full_name AS employee_name, -- Get the employee name and alias it as 'employee_name'
    e.position,               -- Get the employee's job position
    e.base_salary             -- Get the employee's base salary

-- START FROM the main table, EMPLOYEE,  as 'e'
FROM
    EMPLOYEE e

-- JOIN it with the DEPARTMENT table, considered as 'd'
-- The join condition: the 'department_id' in table 'e' must match the 'department_id' in table 'd'
JOIN
    DEPARTMENT d ON e.department_id = d.department_id

-- ORDER BY (sort) the final results
ORDER BY
    d.department_name,  -- Sort by department name (A-Z)
    e.full_name;        -- sort by employee name (A-Z) within each department
    
    
    
-- ---------------------------------------------------
CREATE OR REPLACE VIEW v_monthly_salary_summary AS
SELECT
    sp.employee_id,               -- Get employee ID from SALARY_PAYMENT
    e.full_name AS employee_name, -- Get employee name from EMPLOYEE
    sp.month,                     -- Get the month from SALARY_PAYMENT
    sp.year,                      -- Get the year from SALARY_PAYMENT
    e.base_salary,                -- Get the ANNUAL base salary from EMPLOYEE
    
    -- Get total bonuses from subquery 'b'. If it's NULL (no bonus), show 0.
    IFNULL(b.total_bonuses, 0) AS total_bonuses,
    
    -- Get total deductions from subquery 'd'. If it's NULL (no deduction), show 0.
    IFNULL(d.total_deductions, 0) AS total_deductions,
    
    -- Calculate the expected net salary: (Annual Salary / 12) + Bonuses - Deductions
    (e.base_salary / 12 + IFNULL(b.total_bonuses, 0) - IFNULL(d.total_deductions, 0)) AS calculated_net_salary,
    
    -- Show the actual amount that was recorded in the SALARY_PAYMENT table
    sp.total_amount AS recorded_payment_amount,
    sp.payment_status             -- Show the payment status

-- Start from the SALARY_PAYMENT table (aliased as 'sp')
FROM
    SALARY_PAYMENT sp
    
-- Join with EMPLOYEE (aliased as 'e') to get name and base salary
JOIN
    EMPLOYEE e ON sp.employee_id = e.employee_id

-- LEFT JOIN with a subquery 'b' (which calculates total bonuses)
LEFT JOIN
    -- This subquery 'b' SUMS all 'Bonus' amounts for each employee, per month, per year
    (SELECT
        employee_id,
        MONTH(effective_date) AS month,
        YEAR(effective_date) AS year,
        SUM(amount) AS total_bonuses
     FROM BONUS_DEDUCTION
     WHERE type = 'Bonus'  -- Filter for 'Bonus' types only
     GROUP BY employee_id, MONTH(effective_date), YEAR(effective_date)
    ) b ON sp.employee_id = b.employee_id AND sp.month = b.month AND sp.year = b.year

-- LEFT JOIN with a subquery 'd' (which calculates total deductions)
LEFT JOIN
    -- This subquery 'd' SUMS all 'Deduction' amounts for each employee, per month, per year
    (SELECT
        employee_id,
        MONTH(effective_date) AS month,
        YEAR(effective_date) AS year,
        SUM(amount) AS total_deductions
     FROM BONUS_DEDUCTION
     WHERE type = 'Deduction' -- Filter for 'Deduction' types only
     GROUP BY employee_id, MONTH(effective_date), YEAR(effective_date)
    ) d ON sp.employee_id = d.employee_id AND sp.month = d.month AND sp.year = d.year;
    
    
    
CREATE OR REPLACE VIEW v_project_participation AS
SELECT
    p.project_id,                 -- Get the project ID from the PROJECT table
    p.project_name,               -- Get the project name from the PROJECT table
    
    -- Count the number of UNIQUE (DISTINCT) employee IDs assigned to this project
    COUNT(DISTINCT a.employee_id) AS total_employees,
    
    -- Calculate the SUM of all 'hours_worked' for this project
    SUM(a.hours_worked) AS total_hours_worked

-- Start from the PROJECT table (aliased as 'p')
FROM
    PROJECT p

-- LEFT JOIN with the ASSIGNMENT table (aliased as 'a')
-- (Using LEFT JOIN ensures we still see projects even if they have 0 employees)
LEFT JOIN
    ASSIGNMENT a ON p.project_id = a.project_id

-- GROUP BY all rows based on the project ID and Name
-- (This makes the COUNT and SUM functions apply to each project individually)
GROUP BY
    p.project_id, p.project_name

-- Sort the final result by the project's name
ORDER BY
    p.project_name;
