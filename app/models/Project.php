<?php
require_once __DIR__ . '/../core/Model.php';

class Project {
    protected PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // Lấy tất cả dự án, có search theo tên dự án, dùng pagination
    public function getAll($search = '', $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, d.department_name
                FROM project p
                LEFT JOIN department d ON p.department_id = d.department_id
                WHERE p.project_name LIKE :search
                ORDER BY p.project_id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartments() {
        $stmt = $this->conn->query("SELECT * FROM department ORDER BY department_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Đếm tổng số dự án (dùng cho pagination)
    public function countAll($search = '') {
        $sql = "SELECT COUNT(*) as total FROM project WHERE project_name LIKE :search";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':search' => "%$search%"]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Lấy dự án theo id
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM project WHERE project_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm dự án mới
    public function add($project_name, $start_date, $end_date, $budget, $status = 'active', $department_id = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO project (project_name, start_date, end_date, budegt, status, department_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$project_name, $start_date, $end_date, $budget, $status, $department_id]);
    }

    // Cập nhật dự án
    public function update($id, $project_name, $start_date, $end_date, $budget, $status = 'active', $department_id = null) {
        $stmt = $this->conn->prepare(
            "UPDATE project SET project_name=?, start_date=?, end_date=?, budget=?, status=?, department_id=?
             WHERE project_id=?"
        );
        return $stmt->execute([$project_name, $start_date, $end_date, $budget, $status, $department_id, $id]);
    }

    // Xóa dự án
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM project WHERE project_id=?");
        return $stmt->execute([$id]);
    }
}
