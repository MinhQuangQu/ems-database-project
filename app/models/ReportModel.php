<?php
// models/Report.php

declare(strict_types=1);

class ReportModel 
{
    private mysqli $conn;

    public function __construct(mysqli $conn) 
    {
        $this->conn = $conn;
    }

    /**
     * Lấy báo cáo tổng quan lương hàng tháng từ VIEW
     */
    public function getMonthlySalaryView(?string $yearMonth = null): array 
    {
        try {
            if ($yearMonth) {
                $sql = "SELECT * FROM v_monthly_salary_summary WHERE year_month = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $yearMonth);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $sql = "SELECT * FROM v_monthly_salary_summary ORDER BY year_month DESC";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getMonthlySalaryView error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Gọi Stored Procedure để lấy báo cáo payroll theo phòng ban
     */
    public function getDepartmentPayroll(?string $yearMonth = null): array 
    {
        try {
            if ($yearMonth) {
                $stmt = $this->conn->prepare("CALL sp_department_payroll_report(?)");
                $stmt->bind_param("s", $yearMonth);
            } else {
                $stmt = $this->conn->prepare("CALL sp_department_payroll_report(NULL)");
            }
            
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            // Close statement and clear results
            $stmt->close();
            while ($this->conn->more_results()) {
                $this->conn->next_result();
            }

            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getDepartmentPayroll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lương trung bình theo phòng ban
     */
    public function getAvgSalaryByDepartment(int $departmentId = 0): array 
    {
        try {
            $sql = "SELECT d.department_id, d.department_name, 
                           COUNT(e.employee_id) as employee_count,
                           AVG(e.salary) AS avg_salary,
                           MIN(e.salary) as min_salary,
                           MAX(e.salary) as max_salary
                    FROM employees e
                    JOIN departments d ON e.department_id = d.department_id
                    WHERE e.deleted_at IS NULL 
                    AND e.status = 'active'";

            $params = [];
            $types = "";

            if ($departmentId > 0) {
                $sql .= " AND d.department_id = ?";
                $params[] = $departmentId;
                $types .= "i";
            }

            $sql .= " GROUP BY d.department_id, d.department_name 
                     ORDER BY avg_salary DESC";

            $stmt = $this->conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getAvgSalaryByDepartment error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Số lượng nhân viên theo dự án
     */
    public function getEmployeePerProject(int $projectId = 0): array 
    {
        try {
            $sql = "SELECT p.project_id, p.project_name,
                           COUNT(pa.employee_id) AS employee_count,
                           p.start_date, p.end_date, p.status
                    FROM projects p
                    LEFT JOIN project_assignments pa ON p.project_id = pa.project_id
                    WHERE p.deleted_at IS NULL";

            $params = [];
            $types = "";

            if ($projectId > 0) {
                $sql .= " AND p.project_id = ?";
                $params[] = $projectId;
                $types .= "i";
            }

            $sql .= " GROUP BY p.project_id, p.project_name, p.start_date, p.end_date, p.status
                     ORDER BY employee_count DESC";

            $stmt = $this->conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getEmployeePerProject error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Báo cáo attendance theo tháng
     */
    public function getRecentActive(string $yearMonth, int $departmentId = 0): array 
    {
        try {
            $startDate = $yearMonth . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            $sql = "SELECT e.employee_id, e.full_name, e.employee_code,
                           d.department_name,
                           COUNT(a.attendance_id) as total_days,
                           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                           SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                           SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                           SUM(CASE WHEN a.status = 'half_day' THEN 1 ELSE 0 END) as half_days,
                           ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.attendance_id)), 2) as attendance_rate
                    FROM employees e
                    LEFT JOIN attendance a ON e.employee_id = a.employee_id 
                           AND a.work_date BETWEEN ? AND ?
                           AND a.deleted_at IS NULL
                    LEFT JOIN departments d ON e.department_id = d.department_id
                    WHERE e.deleted_at IS NULL AND e.status = 'active'";

            $params = [$startDate, $endDate];
            $types = "ss";

            if ($departmentId > 0) {
                $sql .= " AND e.department_id = ?";
                $params[] = $departmentId;
                $types .= "i";
            }

            $sql .= " GROUP BY e.employee_id, e.full_name, e.employee_code, d.department_name
                     ORDER BY attendance_rate DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getAttendanceReport error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Báo cáo overtime theo nhân viên
     */
    public function getOvertimeReport(string $startDate, string $endDate, int $departmentId = 0): array 
    {
        try {
            $sql = "SELECT e.employee_id, e.full_name, e.employee_code,
                           d.department_name,
                           SUM(a.overtime_hours) as total_overtime_hours,
                           COUNT(a.attendance_id) as overtime_days,
                           AVG(a.overtime_hours) as avg_overtime_per_day
                    FROM attendance a
                    JOIN employees e ON a.employee_id = e.employee_id
                    LEFT JOIN departments d ON e.department_id = d.department_id
                    WHERE a.work_date BETWEEN ? AND ?
                    AND a.overtime_hours > 0
                    AND a.deleted_at IS NULL
                    AND e.deleted_at IS NULL";

            $params = [$startDate, $endDate];
            $types = "ss";

            if ($departmentId > 0) {
                $sql .= " AND e.department_id = ?";
                $params[] = $departmentId;
                $types .= "i";
            }

            $sql .= " GROUP BY e.employee_id, e.full_name, e.employee_code, d.department_name
                     ORDER BY total_overtime_hours DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getOvertimeReport error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Báo cáo turnover rate (tỷ lệ nghỉ việc)
     */
    public function getTurnoverRateReport(string $startDate, string $endDate): array 
    {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as employees_joined,
                        (SELECT COUNT(*) FROM employees 
                         WHERE deleted_at IS NOT NULL 
                         AND DATE(deleted_at) BETWEEN ? AND ?) as employees_left,
                        ROUND(
                            (SELECT COUNT(*) FROM employees 
                             WHERE deleted_at IS NOT NULL 
                             AND DATE(deleted_at) BETWEEN ? AND ?) * 100.0 / 
                            NULLIF(COUNT(*), 0), 2
                        ) as turnover_rate
                    FROM employees 
                    WHERE deleted_at IS NULL
                    AND date_of_joining BETWEEN ? AND ?
                    GROUP BY month
                    ORDER BY month DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssss", $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getTurnoverRateReport error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Báo cáo salary distribution
     */
    public function getSalaryDistributionReport(): array 
    {
        try {
            $sql = "SELECT 
                        CASE 
                            WHEN salary < 1000 THEN 'Under 1000'
                            WHEN salary BETWEEN 1000 AND 2000 THEN '1000-2000'
                            WHEN salary BETWEEN 2001 AND 3000 THEN '2001-3000'
                            WHEN salary BETWEEN 3001 AND 5000 THEN '3001-5000'
                            ELSE 'Over 5000'
                        END as salary_range,
                        COUNT(*) as employee_count,
                        ROUND(AVG(salary), 2) as avg_salary_in_range
                    FROM employees 
                    WHERE deleted_at IS NULL AND status = 'active'
                    GROUP BY salary_range
                    ORDER BY MIN(salary)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getSalaryDistributionReport error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Báo cáo department performance
     */
    public function getDepartmentPerformanceReport(string $yearMonth): array 
    {
        try {
            $startDate = $yearMonth . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            $sql = "SELECT d.department_id, d.department_name,
                           COUNT(DISTINCT e.employee_id) as total_employees,
                           ROUND(AVG(a.attendance_rate), 2) as avg_attendance_rate,
                           ROUND(AVG(e.salary), 2) as avg_salary,
                           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present_days
                    FROM departments d
                    LEFT JOIN employees e ON d.department_id = e.department_id AND e.deleted_at IS NULL
                    LEFT JOIN attendance a ON e.employee_id = a.employee_id 
                           AND a.work_date BETWEEN ? AND ?
                           AND a.deleted_at IS NULL
                    WHERE d.deleted_at IS NULL
                    GROUP BY d.department_id, d.department_name
                    ORDER BY avg_attendance_rate DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $this->sanitizeReportData($row);
            }

            $stmt->close();
            return $data;

        } catch (Exception $e) {
            error_log("ReportModel getDepartmentPerformanceReport error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sanitize report data
     */
    private function sanitizeReportData(array $data): array 
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_numeric($value) && strpos($key, 'id') === false && strpos($key, 'count') === false) {
                // Giữ nguyên số
                $sanitized[$key] = $value;
            } elseif (is_string($value)) {
                // Sanitize string
                $sanitized[$key] = htmlspecialchars($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Lấy các loại báo cáo có sẵn
     */
    public function getAvailableReportTypes(): array
    {
        return [
            'attendance' => 'Attendance Report',
            'salary' => 'Salary Report',
            'overtime' => 'Overtime Report',
            'department' => 'Department Performance',
            'turnover' => 'Turnover Rate',
            'salary_distribution' => 'Salary Distribution',
            'bonus' => 'Bonus Report',
            'project' => 'Project Assignment'
        ];
    }
}