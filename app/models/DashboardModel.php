<?php
require_once __DIR__ . '/../core/Model.php';

class DashboardModel extends Model
{
    public function __construct(PDO $conn)
    {
        parent::__construct($conn);
    }

    public function getTotalEmployees(): int
    {
        $row = $this->fetch("SELECT COUNT(*) AS total FROM employee");
        return $row['total'] ?? 0;
    }

    public function getTotalDepartments(): int
    {
        $row = $this->fetch("SELECT COUNT(*) AS total FROM department");
        return $row['total'] ?? 0;
    }

    public function getTotalPayroll(): float
    {
        $row = $this->fetch("SELECT SUM(total_amount) AS total FROM salary_payment");
        return $row['total'] ?? 0;
    }

    public function getRecentEmployees(int $limit = 5): array
    {
        return $this->fetchAll(
            "SELECT e.employee_id AS id, e.full_name AS name, e.email, d.department_name AS department
            FROM employee e
            LEFT JOIN department d ON e.department_id = d.department_id
            ORDER BY e.employee_id DESC
            LIMIT :limit",
            ['limit' => $limit]
        );
    }

}
