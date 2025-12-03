<?php
require_once __DIR__ . '/../core/Model.php';

class Employee {
    protected $conn;

    public function __construct(PDO $c) {
        $this->conn = $c;
    }

    public function all() {
        $sql = "SELECT e.employee_id AS id, e.full_name, e.email, e.phone_number, 
                       e.date_of_birth, e.gender, e.department_id, d.department_name AS department_name
                FROM employee e
                LEFT JOIN department d ON e.department_id = d.department_id
                ORDER BY e.employee_id DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllEmployees(): array {
        return $this->all();
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM employee WHERE employee_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($full_name, $email, $phone_number, $date_of_birth, $gender, $department_id) {
        $stmt = $this->conn->prepare(
            "INSERT INTO employee (full_name, email, phone_number, date_of_birth, gender, department_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$full_name, $email, $phone_number, $date_of_birth, $gender, $department_id]);
    }

    public function update($id, $full_name, $email, $phone_number, $date_of_birth, $gender, $department_id) {
        $stmt = $this->conn->prepare(
            "UPDATE employee SET full_name=?, email=?, phone_number=?, date_of_birth=?, gender=?, department_id=?
             WHERE employee_id=?"
        );
        return $stmt->execute([$full_name, $email, $phone_number, $date_of_birth, $gender, $department_id, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM employee WHERE employee_id=?");
        return $stmt->execute([$id]);
    }
}
