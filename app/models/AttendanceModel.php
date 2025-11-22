<?php

declare(strict_types=1);

class AttendanceModel 
{
    private mysqli $conn;

    public function __construct(mysqli $conn) 
    {
        $this->conn = $conn;
    }

    /**
     * Lấy tất cả bản ghi attendance với phân trang và filter
     * @return array
     */
    public function getAll(int $page = 1, int $perPage = 10, string $search = '', string $date = '', string $status = ''): array 
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            // Base query với JOIN
            $sql = "SELECT a.attendance_id, a.employee_id, a.work_date, a.check_in, a.check_out, 
                           a.status, a.created_at, e.full_name, e.employee_code
                    FROM attendance a
                    JOIN employees e ON a.employee_id = e.employee_id
                    WHERE a.deleted_at IS NULL";
            
            $params = [];
            $types = "";
            
            // Add search filter
            if (!empty($search)) {
                $sql .= " AND (e.full_name LIKE ? OR e.employee_code LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }
            
            // Add date filter
            if (!empty($date)) {
                $sql .= " AND a.work_date = ?";
                $params[] = $date;
                $types .= "s";
            }
            
            // Add status filter
            if (!empty($status) && $status !== 'all') {
                $sql .= " AND a.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            
            // Add ordering and pagination
            $sql .= " ORDER BY a.work_date DESC, a.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->conn->prepare($sql);
            
            // Bind parameters if any
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeAttendanceData($row);
            }
            
            $stmt->close();
            return $data;
            
        } catch (Exception $e) {
            error_log("AttendanceModel getAll error: " . $e->getMessage());
            return [];
        }
    }

     public function getStatsByStatus(): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT status, COUNT(*) AS total 
                 FROM attendance 
                 WHERE deleted_at IS NULL 
                 AND work_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 GROUP BY status"
            );
            $stmt->execute();
            $result = $stmt->get_result();
            
            $stats = [];
            while ($row = $result->fetch_assoc()) {
                $stats[$row['status']] = (int)$row['total'];
            }
            
            $stmt->close();
            return $stats;
            
        } catch (Exception $e) {
            error_log("AttendanceModel getStatsByStatus error: " . $e->getMessage());
            return [];
        }
    }

    public function add(int $employeeId, string $workDate, ?string $checkIn, ?string $checkOut, string $status): bool 
    {
        try {
            // Kiểm tra trùng lặp
            if ($this->isDuplicate($employeeId, $workDate)) {
                throw new Exception("Attendance record already exists for this employee on selected date");
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO attendance (employee_id, work_date, check_in, check_out, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())"
            );
            $stmt->bind_param("issss", $employeeId, $workDate, $checkIn, $checkOut, $status);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("AttendanceModel add error: " . $e->getMessage());
            return false;
        }
    }

        public function getById(int $id): ?array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT a.attendance_id, a.employee_id, a.work_date, a.check_in, a.check_out, 
                        a.status, a.created_at, e.full_name, e.employee_code
                 FROM attendance a
                 JOIN employees e ON a.employee_id = e.employee_id
                 WHERE a.attendance_id = ? AND a.deleted_at IS NULL
                 LIMIT 1"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            
            return $data ? $this->sanitizeAttendanceData($data) : null;
            
        } catch (Exception $e) {
            error_log("AttendanceModel getById error: " . $e->getMessage());
            return null;
        }
    }

    public function update(int $attendanceId, int $employeeId, string $workDate, ?string $checkIn, ?string $checkOut, string $status): bool 
    {
        try {
            // Kiểm tra tồn tại
            $existing = $this->getById($attendanceId);
            if (!$existing) {
                throw new Exception("Attendance record not found");
            }

            // Kiểm tra trùng lặp (trừ bản ghi hiện tại)
            if ($this->isDuplicate($employeeId, $workDate, $attendanceId)) {
                throw new Exception("Another attendance record already exists for this employee on selected date");
            }

            $stmt = $this->conn->prepare(
                "UPDATE attendance 
                 SET employee_id = ?, work_date = ?, check_in = ?, check_out = ?, status = ?, updated_at = NOW()
                 WHERE attendance_id = ? AND deleted_at IS NULL"
            );
            $stmt->bind_param("issssi", $employeeId, $workDate, $checkIn, $checkOut, $status, $attendanceId);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("AttendanceModel update error: " . $e->getMessage());
            return false;
        }
    }

        public function delete(int $attendanceId): bool 
    {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE attendance SET deleted_at = NOW() WHERE attendance_id = ?"
            );
            $stmt->bind_param("i", $attendanceId);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("AttendanceModel delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tổng số bản ghi cho phân trang với filter
     */
    public function getTotalCount(string $search = '', string $date = '', string $status = ''): int 
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM attendance a
                    JOIN employees e ON a.employee_id = e.employee_id
                    WHERE a.deleted_at IS NULL";
            
            $params = [];
            $types = "";
            
            // Add search filter
            if (!empty($search)) {
                $sql .= " AND (e.full_name LIKE ? OR e.employee_code LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }
            
            // Add date filter
            if (!empty($date)) {
                $sql .= " AND a.work_date = ?";
                $params[] = $date;
                $types .= "s";
            }
            
            // Add status filter
            if (!empty($status) && $status !== 'all') {
                $sql .= " AND a.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            
            $stmt = $this->conn->prepare($sql);
            
            // Bind parameters if any
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return (int)($row['total'] ?? 0);
            
        } catch (Exception $e) {
            error_log("AttendanceModel getTotalCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy attendance theo khoảng thời gian (cho export)
     */
    public function getByDateRange(string $startDate, string $endDate): array 
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT a.attendance_id, a.employee_id, a.work_date, a.check_in, a.check_out, 
                        a.status, e.full_name, e.employee_code
                 FROM attendance a
                 JOIN employees e ON a.employee_id = e.employee_id
                 WHERE a.work_date BETWEEN ? AND ? 
                 AND a.deleted_at IS NULL
                 ORDER BY a.work_date ASC, e.full_name ASC"
            );
            $stmt->bind_param("ss", $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeAttendanceData($row);
            }
            
            $stmt->close();
            return $data;
            
        } catch (Exception $e) {
            error_log("AttendanceModel getByDateRange error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Thống kê attendance theo tháng và phòng ban
     */
    public function getMonthlyStats(string $yearMonth, int $departmentId = 0): array 
    {
        try {
            $startDate = $yearMonth . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $sql = "SELECT e.department_id, d.department_name, 
                           COUNT(a.attendance_id) as total_records,
                           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                           SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                           SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
                           SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_day_count
                    FROM employees e
                    LEFT JOIN attendance a ON e.employee_id = a.employee_id 
                       AND a.work_date BETWEEN ? AND ? 
                       AND a.deleted_at IS NULL
                    LEFT JOIN departments d ON e.department_id = d.department_id
                    WHERE e.deleted_at IS NULL";
            
            $params = [$startDate, $endDate];
            $types = "ss";
            
            // Add department filter
            if ($departmentId > 0) {
                $sql .= " AND e.department_id = ?";
                $params[] = $departmentId;
                $types .= "i";
            }
            
            $sql .= " GROUP BY e.department_id, d.department_name";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $stats = [];
            while ($row = $result->fetch_assoc()) {
                $stats[] = $row;
            }
            
            $stmt->close();
            return $stats;
            
        } catch (Exception $e) {
            error_log("AttendanceModel getMonthlyStats error: " . $e->getMessage());
            return [];
        }
    }

    // ... giữ nguyên các method khác (getById, add, update, delete, etc.)

    /**
     * Lấy các status có sẵn (cho dropdown)
     */
    public function getAvailableStatuses(): array
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'holiday' => 'Holiday'
        ];
    }
    /**
     * Kiểm tra bản ghi trùng lặp
     */
    private function isDuplicate(int $employeeId, string $workDate, ?int $excludeId = null): bool 
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM attendance 
                    WHERE employee_id = ? 
                    AND work_date = ? 
                    AND deleted_at IS NULL";
            
            $params = [$employeeId, $workDate];
            $types = "is";
            
            if ($excludeId !== null) {
                $sql .= " AND attendance_id != ?";
                $params[] = $excludeId;
                $types .= "i";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("AttendanceModel isDuplicate error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sanitize attendance data
     */
    private function sanitizeAttendanceData(array $data): array 
    {
        return [
            'attendance_id' => (int)($data['attendance_id'] ?? 0),
            'employee_id' => (int)($data['employee_id'] ?? 0),
            'employee_code' => htmlspecialchars($data['employee_code'] ?? ''),
            'full_name' => htmlspecialchars($data['full_name'] ?? ''),
            'work_date' => htmlspecialchars($data['work_date'] ?? ''),
            'check_in' => $data['check_in'] ? htmlspecialchars($data['check_in']) : null,
            'check_out' => $data['check_out'] ? htmlspecialchars($data['check_out']) : null,
            'status' => htmlspecialchars($data['status'] ?? ''),
            'created_at' => $data['created_at'] ?? null
        ];
    }

    /**
     * Tính tổng giờ làm việc
     */
    public function calculateWorkingHours(string $checkIn, string $checkOut): float 
    {
        if (empty($checkIn) || empty($checkOut)) {
            return 0.0;
        }
        
        $checkInTime = strtotime($checkIn);
        $checkOutTime = strtotime($checkOut);
        
        if ($checkInTime === false || $checkOutTime === false || $checkOutTime <= $checkInTime) {
            return 0.0;
        }
        
        return round(($checkOutTime - $checkInTime) / 3600, 2); // Convert to hours
    }
}