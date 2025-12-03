<?php
require_once __DIR__ . "/Attendance.php";

class AttendanceService
{
    private Attendance $attendanceModel;

    public function __construct()
    {
        $this->attendanceModel = new Attendance();
    }

    // ===========================
    // 1. Tính tổng giờ làm việc cho một bản ghi
    // ===========================
    public function calculateWorkingHours(?string $checkIn, ?string $checkOut): string
    {
        if (empty($checkIn) || empty($checkOut)) {
            return '0:00';
        }

        $start = strtotime($checkIn);
        $end   = strtotime($checkOut);

        if ($end <= $start) {
            return '0:00';
        }

        $seconds = $end - $start;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return sprintf("%d:%02d", $hours, $minutes);
    }

    // ===========================
    // 5. Xuất dữ liệu CSV
    // ===========================
    public function exportCsv(array $data, string $filename = 'attendance.csv'): void
    {
        if (empty($data)) {
            throw new Exception("Không có dữ liệu để xuất CSV.");
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8

        // CSV header
        fputcsv($output, ['Mã NV','Họ Tên','Ngày','Check In','Check Out','Trạng Thái','Giờ Làm Việc']);

        foreach ($data as $row) {
            $workingHours = $this->calculateWorkingHours($row['check_in'] ?? null, $row['check_out'] ?? null);

            fputcsv($output, [
                $row['employee_code'] ?? '',
                $row['full_name'] ?? '',
                $row['work_date'] ?? '',
                $row['check_in'] ?? '',
                $row['check_out'] ?? '',
                $row['status'] ?? '',
                $workingHours
            ]);
        }

        fclose($output);
        exit();
    }
}
