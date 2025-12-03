<?php
require_once __DIR__ . '/../core/Model.php';

class Report extends Model
{
    // Search employee by name
    public function searchEmployeeByName(string $name): array
    {
        $sql = "SELECT e.employee_id, e.full_name, d.department_name AS department_name
                FROM employee e
                LEFT JOIN department d ON e.department_id = d.department_id
                WHERE e.full_name LIKE :name
                LIMIT 1";

        return $this->fetch($sql, ['name' => "%$name%"]) ?? [];
    }

    // Lấy 1 nhân viên theo tên (trả về employee đầu tiên match)
    public function getEmployeeByName(string $name): array
    {
        $sql = "SELECT e.*, d.department_name
                FROM employee e
                LEFT JOIN department d ON e.department_id = d.department_id
                WHERE e.full_name LIKE :name
                LIMIT 1";
        return $this->fetch($sql, ['name' => "%$name%"]);
    }


    // Get attendance stats of employee
    public function getAttendanceByEmployee(int $employeeId, string $month = null): array
    {
        if (!$month) $month = date("Y-m");

        $sql = "SELECT status, COUNT(*) AS total
                FROM attendance
                WHERE employee_id = :empId AND DATE_FORMAT(work_date, '%Y-%m') = :month
                GROUP BY status";

        $rows = $this->fetchAll($sql, ['empId' => $employeeId, 'month' => $month]);

        $stats = [
            'Present' => 0,
            'Absent' => 0,
            'On Leave' => 0,
        ];

        foreach ($rows as $row) {
            $stats[$row['status']] = (int)$row['total'];
        }

        return $stats;

    }

    // Get most recent payroll of employee
    public function getPayrollByEmployee(int $employeeId): array
    {
        $sql = "SELECT *
                FROM salary_payment
                WHERE employee_id = :empId
                ORDER BY payment_date DESC
                LIMIT 1";

        return $this->fetch($sql, ['empId' => $employeeId]) ?? [];
    }
}
