<?php
require_once __DIR__ . "/Payroll.php";

class PayrollService
{
    private Payroll $payrollModel;

    public function __construct()
    {
        $this->payrollModel = new Payroll();
    }

    // ===========================
    // 1. Tính tổng lương cho nhân viên
    // ===========================
    public function calculateTotalSalary(float $baseSalary, float $bonus = 0, float $deduction = 0): float
    {
        return max(0, $baseSalary + $bonus - $deduction);
    }

    // ===========================
    // 2. Tạo bảng lương mới
    // ===========================
    public function createPayroll(array $data): bool
    {
        $data['total_salary'] = $this->calculateTotalSalary(
            $data['base_salary'],
            $data['bonus'] ?? 0,
            $data['deduction'] ?? 0
        );

        return $this->payrollModel->insertPayroll($data);
    }

    // ===========================
    // 3. Cập nhật bảng lương
    // ===========================
    public function updatePayroll(int $id, array $data): bool
    {
        $data['total_salary'] = $this->calculateTotalSalary(
            $data['base_salary'],
            $data['bonus'] ?? 0,
            $data['deduction'] ?? 0
        );

        return $this->payrollModel->updatePayroll($id, $data);
    }

    // ===========================
    // 4. Xóa bảng lương
    // ===========================
    public function deletePayroll(int $id): bool
    {
        return $this->payrollModel->deletePayroll($id);
    }

    // ===========================
    // 5. Lấy báo cáo bảng lương theo tháng
    // ===========================
    public function getPayrollReport(string $month, int $departmentId = 0): array
    {
        return $this->payrollModel->getPayrollReportByMonth($month, $departmentId);
    }

    // ===========================
    // 6. Tính tổng lương theo tháng
    // ===========================
    public function getTotalPayrollByMonth(string $month, int $departmentId = 0): float
    {
        return $this->payrollModel->getTotalPayrollByMonth($month, $departmentId);
    }

    // ===========================
    // 7. Xuất CSV bảng lương
    // ===========================
    public function exportPayrollCsv(array $data, string $filename = 'payroll.csv'): void
    {
        if (empty($data)) {
            throw new Exception("Không có dữ liệu để xuất CSV.");
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8

        // CSV header
        fputcsv($output, ['Mã NV','Họ Tên','Phòng ban','Tháng','Lương cơ bản','Bonus','Deduction','Tổng lương']);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['employee_code'] ?? '',
                $row['full_name'] ?? '',
                $row['department_name'] ?? '',
                $row['pay_month'] ?? '',
                $row['base_salary'] ?? 0,
                $row['bonus'] ?? 0,
                $row['deduction'] ?? 0,
                $row['total_salary'] ?? 0
            ]);
        }

        fclose($output);
        exit();
    }
}
