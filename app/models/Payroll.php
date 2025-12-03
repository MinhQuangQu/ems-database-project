<?php
require_once __DIR__ . '/../core/Model.php';

class Payroll extends Model
{
    // ===========================
    // 1. Lấy tất cả bảng lương
    // ===========================
    public function getAllPayrolls(): array
    {
        $sql = "SELECT sp.*, e.full_name, d.department_name AS department_name
                FROM salary_payment sp
                LEFT JOIN employee e ON sp.employee_id = e.employee_id
                LEFT JOIN department d ON e.department_id = d.department_id
                ORDER BY sp.payment_date DESC";
        return $this->fetchAll($sql);
    }

    // ===========================
    // 2. Lấy bảng lương theo ID
    // ===========================
    public function getPayrollById(int $id): ?array
    {
        $sql = "SELECT sp.*, e.full_name, d.department_name AS department_name
                FROM salary_payment sp
                LEFT JOIN employee e ON sp.employee_id = e.employee_id
                LEFT JOIN department d ON e.department_id = d.department_id
                WHERE sp.payment_id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    // ===========================
    // 3. Thêm bảng lương mới
    // ===========================
    public function insertPayroll(array $data): bool
    {
        $sql = "INSERT INTO salary_payment
                (employee_id, payment_date, month, year, total_amount, payment_status)
                VALUES (:employee_id, :payment_date, :month, :year, :total_amount, :payment_status)";
        return $this->execute($sql, [
            'employee_id'   => $data['employee_id'],
            'payment_date'  => $data['payment_date'],
            'month'         => $data['month'],
            'year'          => $data['year'],
            'total_amount'  => $data['total_amount'],
            'payment_status'=> $data['payment_status'] ?? 'unpaid'
        ]);
    }

    // ===========================
    // 4. Cập nhật bảng lương
    // ===========================
    public function updatePayroll(int $id, array $data): bool
    {
        $sql = "UPDATE salary_payment SET
                    employee_id = :employee_id,
                    payment_date = :payment_date,
                    month = :month,
                    year = :year,
                    total_amount = :total_amount,
                    payment_status = :payment_status
                WHERE payment_id = :id";
        return $this->execute($sql, [
            'id'            => $id,
            'employee_id'   => $data['employee_id'],
            'payment_date'  => $data['payment_date'],
            'month'         => $data['month'],
            'year'          => $data['year'],
            'total_amount'  => $data['total_amount'],
            'payment_status'=> $data['payment_status'] ?? 'unpaid'
        ]);
    }

    // ===========================
    // 5. Xóa bảng lương
    // ===========================
    public function deletePayroll(int $id): bool
    {
        $sql = "DELETE FROM salary_payment WHERE payment_id = :id";
        return $this->execute($sql, ['id' => $id]);
    }

    // ===========================
    // 6. Lấy báo cáo lương theo tháng và năm
    // ===========================
    public function getPayrollReportByMonthYear(int $month, int $year, int $departmentId = 0): array
    {
        $params = ['month' => $month, 'year' => $year];
        $sql = "SELECT sp.*, e.full_name, d.department_name AS department_name
                FROM salary_payment sp
                LEFT JOIN employee e ON sp.employee_id = e.employee_id
                LEFT JOIN department d ON e.department_id = d.department_id
                WHERE sp.month = :month AND sp.year = :year";

        if ($departmentId > 0) {
            $sql .= " AND d.department_id = :deptId";
            $params['deptId'] = $departmentId;
        }

        $sql .= " ORDER BY e.full_name ASC";
        return $this->fetchAll($sql, $params);
    }

    // ===========================
    // 7. Tổng lương theo tháng và năm
    // ===========================
    public function getTotalPayrollByMonthYear(int $month, int $year, int $departmentId = 0): float
    {
        $params = ['month' => $month, 'year' => $year];
        $sql = "SELECT SUM(total_amount) AS total
                FROM salary_payment sp
                LEFT JOIN employee e ON sp.employee_id = e.employee_id
                LEFT JOIN department d ON e.department_id = d.department_id
                WHERE sp.month = :month AND sp.year = :year";

        if ($departmentId > 0) {
            $sql .= " AND d.department_id = :deptId";
            $params['deptId'] = $departmentId;
        }

        $result = $this->fetch($sql, $params);
        return (float)($result['total'] ?? 0);
    }
}
