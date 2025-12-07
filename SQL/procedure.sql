DROP PROCEDURE IF EXISTS sp_add_salary_payment;
DELIMITER $$
CREATE PROCEDURE sp_add_salary_payment(
    IN p_employee_id INT,
    IN p_month INT,
    IN p_year INT
)
BEGIN
    SELECT
        e.employee_id,
        e.full_name,
        dpt.department_name,
        (e.base_salary / 12) AS monthly_base_salary,
        IFNULL(b.total_bonuses, 0) AS total_bonuses,
        IFNULL(ded.total_deductions, 0) AS total_deductions,
        ( (e.base_salary / 12) + IFNULL(b.total_bonuses, 0) - IFNULL(ded.total_deductions, 0) ) AS net_salary_calculated
    FROM
        EMPLOYEE e
    JOIN
        DEPARTMENT dpt ON e.department_id = dpt.department_id
    LEFT JOIN
        (
            SELECT employee_id, SUM(amount) AS total_bonuses FROM BONUS_DEDUCTION
            WHERE type = 'Bonus' AND MONTH(effective_date) = p_month AND YEAR(effective_date) = p_year
            GROUP BY employee_id
        ) b ON e.employee_id = b.employee_id
    LEFT JOIN
        (
            SELECT employee_id, SUM(amount) AS total_deductions FROM BONUS_DEDUCTION
            WHERE type = 'Deduction' AND MONTH(effective_date) = p_month AND YEAR(effective_date) = p_year
            GROUP BY employee_id
        ) ded ON e.employee_id = ded.employee_id
    WHERE
        -- LỌC THEO ID ĐƯỢC CHỈ ĐỊNH
        e.employee_id = p_employee_id
    ORDER BY
        dpt.department_name, e.employee_id;
END$$

DELIMITER ;
CALL sp_add_salary_payment(4,10, 2024);





DROP PROCEDURE IF EXISTS sp_department_payroll_report;
DELIMITER $$

CREATE PROCEDURE sp_department_payroll_report(
    IN p_department_id INT,
    IN p_month INT,
    IN p_year INT
)
BEGIN
    SELECT
        d.department_name,
        COUNT(e.employee_id) AS total_employees,
        SUM(e.base_salary / 12) AS total_base_payroll,
        SUM(IFNULL(b.total_bonuses, 0)) AS total_bonuses,
        SUM(IFNULL(d.total_deductions, 0)) AS total_deductions,
        SUM( (e.base_salary / 12) + IFNULL(b.total_bonuses, 0) - IFNULL(d.total_deductions, 0) ) AS total_net_payroll
    FROM
        EMPLOYEE e
    JOIN
        DEPARTMENT d ON e.department_id = d.department_id
    LEFT JOIN
        (SELECT
            employee_id, SUM(amount) AS total_bonuses
         FROM BONUS_DEDUCTION
         WHERE type = 'Bonus' AND MONTH(effective_date) = p_month AND YEAR(effective_date) = p_year
         GROUP BY employee_id
        ) b ON e.employee_id = b.employee_id
    LEFT JOIN
        (SELECT
            employee_id, SUM(amount) AS total_deductions
         FROM BONUS_DEDUCTION
         WHERE type = 'Deduction' AND MONTH(effective_date) = p_month AND YEAR(effective_date) = p_year
         GROUP BY employee_id
        ) d ON e.employee_id = d.employee_id
    WHERE
        (p_department_id IS NULL OR p_department_id = 0 OR e.department_id = p_department_id)
    GROUP BY
        d.department_id, d.department_name;
END$$

DELIMITER ;

CALL sp_department_payroll_report(1, 10, 2024);
