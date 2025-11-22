<?php

declare(strict_types=1);

class EmployeeModel 
{
    
    private mysqli $conn;

    public function __construct() 
    {
        require_once __DIR__ . "/../../config/database.php"; 
        $this->conn = $conn;
    }

    /**
     * Lấy tất cả employees với phân trang và filter
     */
    public function getAll(int $page = 1, int $perPage = 15, string $search = '', int $departmentId = 0, string $status = ''): array 
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT e.*, 
                           d.department_name,
                           m.full_name as manager_name,
                           (SELECT COUNT(*) FROM attendance att WHERE att.employee_id = e.employee_id AND att.deleted_at IS NULL) as attendance_count
                    FROM employees e
                    LEFT JOIN departments d ON e.department_id = d.department_id
                    LEFT JOIN employees m ON e.manager_id = m.employee_id
                    WHERE e.deleted_at IS NULL";
            
            $params = [];
            $types = "";
            
            // Add search filter
            if (!empty($search)) {
                $sql .= " AND (e.full_name LIKE ? OR e.employee_code LIKE ? OR e.email LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }
            
            // Add department filter
            if ($departmentId > 0) {
                $sql .= " AND e.department_id = ?";
                $params[] = $departmentId;
                $types .= "i";
            }
            
            // Add status filter
            if (!empty($status) && $status !== 'all') {
                $sql .= " AND e.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            
            // Add ordering and pagination
            $sql .= " ORDER BY e.full_name ASC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $employees = [];
            while ($row = $result->fetch_assoc()) {
                $employees[] = $this->sanitizeEmployeeData($row);
            }
            
            $stmt->close();
            return $employees;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tổng số employees cho phân trang
     */
    public function getTotalCount(string $search = '', int $departmentId = 0, string $status = ''): int 
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM employees 
                    WHERE deleted_at IS NULL";
            
            $params = [];
            $types = "";
            
            if (!empty($search)) {
                $sql .= " AND (full_name LIKE ? OR employee_code LIKE ? OR email LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }
            
            if ($departmentId > 0) {
                $sql .= " AND department_id = ?";
                $params[] = $departmentId;
                $types .= "i";
            }
            
            if (!empty($status) && $status !== 'all') {
                $sql .= " AND status = ?";
                $params[] = $status;
                $types .= "s";
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
            error_log("EmployeeModel getTotalCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy employee theo ID
     */
    public function getById(int $id): ?array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT e.*, 
                        d.department_name,
                        d.department_code,
                        m.full_name as manager_name,
                        (SELECT COUNT(*) FROM attendance att WHERE att.employee_id = e.employee_id AND att.deleted_at IS NULL) as attendance_count
                 FROM employees e
                 LEFT JOIN departments d ON e.department_id = d.department_id
                 LEFT JOIN employees m ON e.manager_id = m.employee_id
                 WHERE e.employee_id = ? AND e.deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $employee = $result->fetch_assoc();
            $stmt->close();
            
            return $employee ? $this->sanitizeEmployeeData($employee) : null;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy employee theo employee code
     */
    public function getByCode(string $employeeCode): ?array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM employees WHERE employee_code = ? AND deleted_at IS NULL LIMIT 1"
            );
            $stmt->bind_param("s", $employeeCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $employee = $result->fetch_assoc();
            $stmt->close();
            
            return $employee ? $this->sanitizeEmployeeData($employee) : null;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getByCode error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy employee theo email
     */
    public function getByEmail(string $email): ?array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM employees WHERE email = ? AND deleted_at IS NULL LIMIT 1"
            );
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $employee = $result->fetch_assoc();
            $stmt->close();
            
            return $employee ? $this->sanitizeEmployeeData($employee) : null;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getByEmail error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy tất cả employees active (cho dropdown)
     */
    public function getAllActive(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT employee_id, employee_code, full_name, position, department_id
                 FROM employees 
                 WHERE deleted_at IS NULL AND status = 'active'
                 ORDER BY full_name ASC"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            $employees = [];
            while ($row = $result->fetch_assoc()) {
                $employees[] = $this->sanitizeEmployeeData($row);
            }
            
            $stmt->close();
            return $employees;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getAllActive error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả managers (cho dropdown)
     */
    public function getAllManagers(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT employee_id, employee_code, full_name, position
                 FROM employees 
                 WHERE deleted_at IS NULL AND status = 'active'
                 AND (position LIKE '%Manager%' OR position LIKE '%Head%' OR position LIKE '%Director%')
                 ORDER BY full_name ASC"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            $managers = [];
            while ($row = $result->fetch_assoc()) {
                $managers[] = $this->sanitizeEmployeeData($row);
            }
            
            $stmt->close();
            return $managers;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getAllManagers error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy employees theo department
     */
    public function getByDepartment(int $departmentId): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT employee_id, employee_code, full_name, email, position, phone
                 FROM employees 
                 WHERE department_id = ? AND deleted_at IS NULL AND status = 'active'
                 ORDER BY full_name ASC"
            );
            $stmt->bind_param("i", $departmentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $employees = [];
            while ($row = $result->fetch_assoc()) {
                $employees[] = $this->sanitizeEmployeeData($row);
            }
            
            $stmt->close();
            return $employees;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getByDepartment error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Thêm employee mới với transaction
     */
    public function add(array $data): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Kiểm tra trùng employee code
            if ($this->isCodeDuplicate($data['employee_code'])) {
                throw new Exception("Employee code already exists");
            }

            // Kiểm tra trùng email
            if ($this->isEmailDuplicate($data['email'])) {
                throw new Exception("Email already exists");
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO employees (employee_code, full_name, email, phone, address, 
                                      position, department_id, manager_id, salary, 
                                      date_of_birth, date_of_joining, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            
            // Prepare data
            $employeeCode = $data['employee_code'];
            $fullName = $data['full_name'];
            $email = $data['email'];
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $position = $data['position'];
            $departmentId = !empty($data['department_id']) ? (int)$data['department_id'] : null;
            $managerId = !empty($data['manager_id']) ? (int)$data['manager_id'] : null;
            $salary = !empty($data['salary']) ? (float)$data['salary'] : null;
            $dateOfBirth = !empty($data['date_of_birth']) ? $data['date_of_birth'] : null;
            $dateOfJoining = !empty($data['date_of_joining']) ? $data['date_of_joining'] : null;
            $status = $data['status'] ?? 'active';
            
            $stmt->bind_param("ssssssiiddss", 
                $employeeCode,
                $fullName,
                $email,
                $phone,
                $address,
                $position,
                $departmentId,
                $managerId,
                $salary,
                $dateOfBirth,
                $dateOfJoining,
                $status
            );
            
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("EmployeeModel add error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật employee với transaction
     */
    public function update(int $id, array $data): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Kiểm tra tồn tại
            $existing = $this->getById($id);
            if (!$existing) {
                throw new Exception("Employee not found");
            }

            // Kiểm tra trùng employee code (trừ bản ghi hiện tại)
            if ($this->isCodeDuplicate($data['employee_code'], $id)) {
                throw new Exception("Employee code already exists");
            }

            // Kiểm tra trùng email (trừ bản ghi hiện tại)
            if ($this->isEmailDuplicate($data['email'], $id)) {
                throw new Exception("Email already exists");
            }

            $stmt = $this->conn->prepare(
                "UPDATE employees 
                 SET employee_code = ?, full_name = ?, email = ?, phone = ?, address = ?, 
                     position = ?, department_id = ?, manager_id = ?, salary = ?, 
                     date_of_birth = ?, date_of_joining = ?, status = ?, updated_at = NOW()
                 WHERE employee_id = ? AND deleted_at IS NULL"
            );
            
            // Prepare data
            $employeeCode = $data['employee_code'];
            $fullName = $data['full_name'];
            $email = $data['email'];
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $position = $data['position'];
            $departmentId = !empty($data['department_id']) ? (int)$data['department_id'] : null;
            $managerId = !empty($data['manager_id']) ? (int)$data['manager_id'] : null;
            $salary = !empty($data['salary']) ? (float)$data['salary'] : null;
            $dateOfBirth = !empty($data['date_of_birth']) ? $data['date_of_birth'] : null;
            $dateOfJoining = !empty($data['date_of_joining']) ? $data['date_of_joining'] : null;
            $status = $data['status'] ?? 'active';
            
            $stmt->bind_param("ssssssiiddssi",
                $employeeCode,
                $fullName,
                $email,
                $phone,
                $address,
                $position,
                $departmentId,
                $managerId,
                $salary,
                $dateOfBirth,
                $dateOfJoining,
                $status,
                $id
            );
            
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("EmployeeModel update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm employee
     */
    public function delete(int $id): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Kiểm tra xem employee có attendance records không
            if ($this->hasAttendanceRecords($id)) {
                throw new Exception("Cannot delete employee that has attendance records");
            }

            $stmt = $this->conn->prepare(
                "UPDATE employees SET deleted_at = NOW() WHERE employee_id = ?"
            );
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("EmployeeModel delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy thống kê employees
     */
    public function getStats(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT 
                    COUNT(*) as total_employees,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_employees,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_employees,
                    COUNT(DISTINCT department_id) as departments_with_employees,
                    AVG(salary) as avg_salary,
                    MIN(date_of_joining) as earliest_join_date,
                    MAX(date_of_joining) as latest_join_date
                 FROM employees 
                 WHERE deleted_at IS NULL"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $stats = $result->fetch_assoc();
            $stmt->close();
            
            return $stats ?: [
                'total_employees' => 0,
                'active_employees' => 0,
                'inactive_employees' => 0,
                'departments_with_employees' => 0,
                'avg_salary' => 0,
                'earliest_join_date' => null,
                'latest_join_date' => null
            ];
            
        } catch (Exception $e) {
            error_log("EmployeeModel getStats error: " . $e->getMessage());
            return [
                'total_employees' => 0,
                'active_employees' => 0,
                'inactive_employees' => 0,
                'departments_with_employees' => 0,
                'avg_salary' => 0,
                'earliest_join_date' => null,
                'latest_join_date' => null
            ];
        }
    }

    /**
     * Thống kê employees theo department
     */
    public function getStatsByDepartment(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT d.department_name, 
                        COUNT(e.employee_id) as employee_count,
                        AVG(e.salary) as avg_salary,
                        SUM(CASE WHEN e.status = 'active' THEN 1 ELSE 0 END) as active_count
                 FROM departments d
                 LEFT JOIN employees e ON d.department_id = e.department_id AND e.deleted_at IS NULL
                 WHERE d.deleted_at IS NULL
                 GROUP BY d.department_id, d.department_name
                 ORDER BY employee_count DESC"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            $stats = [];
            while ($row = $result->fetch_assoc()) {
                $stats[] = $row;
            }
            
            $stmt->close();
            return $stats;
            
        } catch (Exception $e) {
            error_log("EmployeeModel getStatsByDepartment error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra employee có attendance records không
     */
    private function hasAttendanceRecords(int $employeeId): bool 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as count 
                 FROM attendance 
                 WHERE employee_id = ? AND deleted_at IS NULL"
            );
            $stmt->bind_param("i", $employeeId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("EmployeeModel hasAttendanceRecords error: " . $e->getMessage());
            return true; // Trả về true để ngăn xóa nếu có lỗi
        }
    }

    /**
     * Kiểm tra trùng employee code
     */
    private function isCodeDuplicate(string $code, ?int $excludeId = null): bool 
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM employees 
                    WHERE employee_code = ? AND deleted_at IS NULL";
            
            $params = [];
            $types = "s";
            $params[] = $code;
            
            if ($excludeId !== null) {
                $sql .= " AND employee_id != ?";
                $types .= "i";
                $params[] = $excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("EmployeeModel isCodeDuplicate error: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Kiểm tra trùng email
     */
    private function isEmailDuplicate(string $email, ?int $excludeId = null): bool 
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM employees 
                    WHERE email = ? AND deleted_at IS NULL";
            
            $params = [];
            $types = "s";
            $params[] = $email;
            
            if ($excludeId !== null) {
                $sql .= " AND employee_id != ?";
                $types .= "i";
                $params[] = $excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("EmployeeModel isEmailDuplicate error: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Tìm kiếm employees cho autocomplete
     */
    public function search(string $query): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT employee_id, employee_code, full_name, email, position
                 FROM employees 
                 WHERE deleted_at IS NULL 
                 AND (full_name LIKE ? OR employee_code LIKE ? OR email LIKE ?)
                 ORDER BY full_name ASC
                 LIMIT 10"
            );
            
            $searchTerm = "%{$query}%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $employees = [];
            while ($row = $result->fetch_assoc()) {
                $employees[] = [
                    'id' => (int)$row['employee_id'],
                    'code' => htmlspecialchars($row['employee_code']),
                    'name' => htmlspecialchars($row['full_name']),
                    'email' => htmlspecialchars($row['email']),
                    'position' => htmlspecialchars($row['position'])
                ];
            }
            
            $stmt->close();
            return $employees;
            
        } catch (Exception $e) {
            error_log("EmployeeModel search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sanitize employee data
     */
    private function sanitizeEmployeeData(array $data): array 
    {
        return [
            'employee_id' => (int)($data['employee_id'] ?? 0),
            'employee_code' => htmlspecialchars($data['employee_code'] ?? ''),
            'full_name' => htmlspecialchars($data['full_name'] ?? ''),
            'email' => htmlspecialchars($data['email'] ?? ''),
            'phone' => $data['phone'] ? htmlspecialchars($data['phone']) : null,
            'address' => $data['address'] ? htmlspecialchars($data['address']) : null,
            'position' => htmlspecialchars($data['position'] ?? ''),
            'department_id' => isset($data['department_id']) ? (int)$data['department_id'] : null,
            'department_name' => isset($data['department_name']) ? htmlspecialchars($data['department_name']) : null,
            'department_code' => isset($data['department_code']) ? htmlspecialchars($data['department_code']) : null,
            'manager_id' => isset($data['manager_id']) ? (int)$data['manager_id'] : null,
            'manager_name' => isset($data['manager_name']) ? htmlspecialchars($data['manager_name']) : null,
            'salary' => isset($data['salary']) ? (float)$data['salary'] : null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'date_of_joining' => $data['date_of_joining'] ?? null,
            'status' => htmlspecialchars($data['status'] ?? 'active'),
            'attendance_count' => isset($data['attendance_count']) ? (int)$data['attendance_count'] : 0,
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
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            'terminated' => 'Terminated'
        ];
    }

    /**
     * Lấy số lượng employees cho dashboard
     */
    public function getTotalEmployees(): int 
    {
        return $this->getTotalCount();
    }

    /**
     * Lấy số lượng active employees
     */
    public function getActiveEmployeesCount(): int 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total 
                 FROM employees 
                 WHERE deleted_at IS NULL AND status = 'active'"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return (int)($row['total'] ?? 0);
            
        } catch (Exception $e) {
            error_log("EmployeeModel getActiveEmployeesCount error: " . $e->getMessage());
            return 0;
        }
    }
}