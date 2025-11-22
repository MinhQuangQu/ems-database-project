<?php

declare(strict_types=1);

class DepartmentModel 
{
    private mysqli $conn;

    public function __construct(mysqli $conn) 
    {
        $this->conn = $conn;
    }

    /**
     * Lấy tất cả departments với phân trang và tìm kiếm
     */
    public function getAll(int $page = 1, int $perPage = 10, string $search = ''): array 
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT d.*, 
                           COUNT(e.employee_id) as employee_count,
                           m.full_name as manager_name
                    FROM departments d
                    LEFT JOIN employees e ON d.department_id = e.department_id AND e.deleted_at IS NULL
                    LEFT JOIN employees m ON d.manager_id = m.employee_id
                    WHERE d.deleted_at IS NULL";
            
            $params = [];
            $types = "";
            
            // Add search filter
            if (!empty($search)) {
                $sql .= " AND (d.department_name LIKE ? OR d.department_code LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
                $types .= "ss";
            }
            
            // Group and pagination
            $sql .= " GROUP BY d.department_id 
                     ORDER BY d.department_name ASC 
                     LIMIT ? OFFSET ?";
            
            $params[] = $perPage;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                // Sửa lỗi: truyền các biến thay vì giá trị trực tiếp
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $departments = [];
            while ($row = $result->fetch_assoc()) {
                $departments[] = $this->sanitizeDepartmentData($row);
            }
            
            $stmt->close();
            return $departments;
            
        } catch (Exception $e) {
            error_log("DepartmentModel getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tổng số departments cho phân trang
     */
    public function getTotalDepartment(string $search = ''): int 
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM departments 
                    WHERE deleted_at IS NULL";
            
            $params = [];
            $types = "";
            
            if (!empty($search)) {
                $sql .= " AND (department_name LIKE ? OR department_code LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
                $types .= "ss";
            }
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return (int)($row['total'] ?? 0);
            
        } catch (Exception $e) {
            error_log("DepartmentModel getTotalCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy department theo ID
     */
    public function getById(int $id): ?array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT d.*, 
                        m.full_name as manager_name,
                        COUNT(e.employee_id) as employee_count
                 FROM departments d
                 LEFT JOIN employees e ON d.department_id = e.department_id AND e.deleted_at IS NULL
                 LEFT JOIN employees m ON d.manager_id = m.employee_id
                 WHERE d.department_id = ? AND d.deleted_at IS NULL
                 GROUP BY d.department_id
                 LIMIT 1"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $department = $result->fetch_assoc();
            $stmt->close();
            
            return $department ? $this->sanitizeDepartmentData($department) : null;
            
        } catch (Exception $e) {
            error_log("DepartmentModel getById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy tất cả departments active (cho dropdown)
     */
    public function getAllActive(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT department_id, department_name, department_code
                 FROM departments 
                 WHERE deleted_at IS NULL AND status = 'active'
                 ORDER BY department_name ASC"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            $departments = [];
            while ($row = $result->fetch_assoc()) {
                $departments[] = $this->sanitizeDepartmentData($row);
            }
            
            $stmt->close();
            return $departments;
            
        } catch (Exception $e) {
            error_log("DepartmentModel getAllActive error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Thêm department mới
     */
    public function add(array $data): bool 
    {
        try {
            // Kiểm tra trùng mã department
            if ($this->isCodeDuplicate($data['department_code'])) {
                throw new Exception("Department code already exists");
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO departments (department_name, department_code, description, manager_id, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())"
            );
            
            // Sửa lỗi: tạo biến riêng cho từng tham số
            $departmentName = $data['department_name'];
            $departmentCode = $data['department_code'];
            $description = $data['description'] ?? '';
            $managerId = !empty($data['manager_id']) ? (int)$data['manager_id'] : null;
            $status = $data['status'] ?? 'active';
            
            $stmt->bind_param("sssis", 
                $departmentName,
                $departmentCode,
                $description,
                $managerId,
                $status
            );
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("DepartmentModel add error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật department
     */
    public function update(int $id, array $data): bool 
    {
        try {
            // Kiểm tra tồn tại
            $existing = $this->getById($id);
            if (!$existing) {
                throw new Exception("Department not found");
            }

            // Kiểm tra trùng mã department (trừ bản ghi hiện tại)
            if ($this->isCodeDuplicate($data['department_code'], $id)) {
                throw new Exception("Department code already exists");
            }

            $stmt = $this->conn->prepare(
                "UPDATE departments 
                 SET department_name = ?, department_code = ?, description = ?, 
                     manager_id = ?, status = ?, updated_at = NOW()
                 WHERE department_id = ? AND deleted_at IS NULL"
            );
            
            // Sửa lỗi: tạo biến riêng cho từng tham số
            $departmentName = $data['department_name'];
            $departmentCode = $data['department_code'];
            $description = $data['description'] ?? '';
            $managerId = !empty($data['manager_id']) ? (int)$data['manager_id'] : null;
            $status = $data['status'] ?? 'active';
            
            $stmt->bind_param("sssisi",
                $departmentName,
                $departmentCode,
                $description,
                $managerId,
                $status,
                $id
            );
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("DepartmentModel update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm department
     */
    public function delete(int $id): bool 
    {
        try {
            // Kiểm tra xem department có employees không
            if ($this->hasEmployees($id)) {
                throw new Exception("Cannot delete department that has employees");
            }

            $stmt = $this->conn->prepare(
                "UPDATE departments SET deleted_at = NOW() WHERE department_id = ?"
            );
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("DepartmentModel delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy thống kê departments
     */
    public function getStats(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT 
                    COUNT(*) as total_departments,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_departments,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_departments,
                    (SELECT COUNT(DISTINCT department_id) FROM employees WHERE deleted_at IS NULL) as departments_with_employees
                 FROM departments 
                 WHERE deleted_at IS NULL"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $stats = $result->fetch_assoc();
            $stmt->close();
            
            return $stats ?: [
                'total_departments' => 0,
                'active_departments' => 0,
                'inactive_departments' => 0,
                'departments_with_employees' => 0
            ];
            
        } catch (Exception $e) {
            error_log("DepartmentModel getStats error: " . $e->getMessage());
            return [
                'total_departments' => 0,
                'active_departments' => 0,
                'inactive_departments' => 0,
                'departments_with_employees' => 0
            ];
        }
    }

    /**
     * Lấy employees trong department
     */
    public function getEmployees(int $departmentId): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT employee_id, employee_code, full_name, email, position, phone
                 FROM employees 
                 WHERE department_id = ? AND deleted_at IS NULL
                 ORDER BY full_name ASC"
            );
            $stmt->bind_param("i", $departmentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $employees = [];
            while ($row = $result->fetch_assoc()) {
                $employees[] = [
                    'employee_id' => (int)$row['employee_id'],
                    'employee_code' => htmlspecialchars($row['employee_code']),
                    'full_name' => htmlspecialchars($row['full_name']),
                    'email' => htmlspecialchars($row['email']),
                    'position' => htmlspecialchars($row['position']),
                    'phone' => htmlspecialchars($row['phone'])
                ];
            }
            
            $stmt->close();
            return $employees;
            
        } catch (Exception $e) {
            error_log("DepartmentModel getEmployees error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra department có employees không
     */
    private function hasEmployees(int $departmentId): bool 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as count 
                 FROM employees 
                 WHERE department_id = ? AND deleted_at IS NULL"
            );
            $stmt->bind_param("i", $departmentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("DepartmentModel hasEmployees error: " . $e->getMessage());
            return true; // Trả về true để ngăn xóa nếu có lỗi
        }
    }

    /**
     * Kiểm tra trùng mã department
     */
    private function isCodeDuplicate(string $code, ?int $excludeId = null): bool 
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM departments 
                    WHERE department_code = ? AND deleted_at IS NULL";
            
            $params = [];
            $types = "s";
            
            // Sửa lỗi: tạo biến riêng
            $departmentCode = $code;
            $params[] = &$departmentCode;
            
            if ($excludeId !== null) {
                $sql .= " AND department_id != ?";
                $types .= "i";
                $params[] = &$excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            
            // Sửa lỗi: sử dụng call_user_func_array để bind tham số
            $bindParams = [$types];
            foreach ($params as &$param) {
                $bindParams[] = &$param;
            }
            
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("DepartmentModel isCodeDuplicate error: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Tìm kiếm departments cho autocomplete
     */
    public function search(string $query): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT department_id, department_name, department_code
                 FROM departments 
                 WHERE deleted_at IS NULL 
                 AND (department_name LIKE ? OR department_code LIKE ?)
                 ORDER BY department_name ASC
                 LIMIT 10"
            );
            
            // Sửa lỗi: tạo biến riêng
            $searchTerm = "%{$query}%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $departments = [];
            while ($row = $result->fetch_assoc()) {
                $departments[] = [
                    'id' => (int)$row['department_id'],
                    'name' => htmlspecialchars($row['department_name']),
                    'code' => htmlspecialchars($row['department_code'])
                ];
            }
            
            $stmt->close();
            return $departments;
            
        } catch (Exception $e) {
            error_log("DepartmentModel search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sanitize department data
     */
    private function sanitizeDepartmentData(array $data): array 
    {
        return [
            'department_id' => (int)($data['department_id'] ?? 0),
            'department_name' => htmlspecialchars($data['department_name'] ?? ''),
            'department_code' => htmlspecialchars($data['department_code'] ?? ''),
            'description' => htmlspecialchars($data['description'] ?? ''),
            'manager_id' => isset($data['manager_id']) ? (int)$data['manager_id'] : null,
            'manager_name' => isset($data['manager_name']) ? htmlspecialchars($data['manager_name']) : null,
            'status' => htmlspecialchars($data['status'] ?? 'active'),
            'employee_count' => (int)($data['employee_count'] ?? 0),
            'created_at' => $data['created_at'] ?? null,
            'updated_at' => $data['updated_at'] ?? null
        ];
    }

    /**
     * Lấy các status có sẵn
     */
    public function getAvailableStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive'
        ];
    }
}