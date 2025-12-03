<?php
require_once __DIR__ . '/../core/Model.php';
;

class Department extends Model
{
    public function getAllDepartments(): array
    {
        $sql = "
            SELECT d.department_id, d.department_name, d.location,
                COUNT(e.employee_id) AS employee_count
            FROM department d
            LEFT JOIN employee e ON e.department_id = d.department_id
            GROUP BY d.department_id
            ORDER BY d.department_id ASC
        ";
        return $this->fetchAll($sql);
    }


    public function searchDepartmentEmployees(string $keyword = ''): array
    {
        $sql = "
            SELECT e.employee_id, e.full_name, e.email, e.phone_number, e.position, d.department_name
            FROM employee e
            INNER JOIN department d ON e.department_id = d.department_id
            WHERE d.department_name LIKE :keyword
            ORDER BY e.employee_id ASC
        ";
        return $this->fetchAll($sql, ['keyword' => "%$keyword%"]);
    }




    public function getDepartmentById(int $id): ?array
    {
        $sql = "
            SELECT d.*,
                e.full_name AS manager_name
            FROM department d
            LEFT JOIN employee e 
                ON d.manager_id = e.employee_id
            WHERE d.department_id = :id
            LIMIT 1
        ";

        return $this->fetch($sql, ['id' => $id]);
    }



    public function insertDepartment(string $name, string $location = '', int $managerId = null): bool
    {
        $sql = "INSERT INTO department (department_name, location, manager_id) 
                VALUES (:name, :location, :manager_id)";
        return $this->execute($sql, [
            'name'       => $name,
            'location'   => $location,
            'manager_id' => $managerId
        ]);
    }

    public function updateDepartment(int $id, string $name, string $location = '', int $managerId = null): bool
    {
        $sql = "UPDATE department 
                SET department_name = :name, location = :location, manager_id = :manager_id
                WHERE department_id = :id";
        return $this->execute($sql, [
            'id'         => $id,
            'name'       => $name,
            'location'   => $location,
            'manager_id' => $managerId
        ]);
    }

    public function deleteDepartment(int $id): bool
    {
        $sql = "DELETE FROM department WHERE department_id = :id";
        return $this->execute($sql, ['id' => $id]);
    }

    public function countDepartments(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM department";
        $result = $this->fetch($sql);
        return (int)$result['total'];
    }
}
