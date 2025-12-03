<?php
require_once __DIR__ . '/../core/Model.php';

class Attendance extends Model
{
    public function getAllAttendance(): array
    {
        $sql = "SELECT a.*, e.full_name 
                FROM attendance a
                LEFT JOIN employee e ON a.employee_id = e.employee_id
                ORDER BY a.work_date DESC";
        return $this->fetchAll($sql);
    }

    public function searchAttendance(string $search): array
    {
        $sql = "SELECT a.*, e.full_name 
                FROM attendance a
                LEFT JOIN employee e ON a.employee_id = e.employee_id
                WHERE e.full_name LIKE :search
                ORDER BY a.work_date DESC";
        return $this->fetchAll($sql, ['search' => "%$search%"]);
    }

    public function insertAttendance(array $data): bool
    {
        $sql = "INSERT INTO attendance (employee_id, work_date, checkin_time, checkout_time, status)
                VALUES (:employee_id, :work_date, :checkin_time, :checkout_time, :status)";
        return $this->execute($sql, $data);
    }
}
