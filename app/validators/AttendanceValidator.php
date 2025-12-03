<?php

class AttendanceValidator
{
    private array $validStatuses = ['present', 'absent', 'late', 'half_day', 'holiday', 'leave'];

    /**
     * Validate dữ liệu điểm danh
     *
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function validate(array $data): array
    {
        $required = ['employee_id', 'work_date', 'status'];

        // 1. Kiểm tra trường bắt buộc
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Trường '{$field}' là bắt buộc"];
            }
        }

        // 2. Validate employee_id
        if (!is_numeric($data['employee_id']) || (int)$data['employee_id'] <= 0) {
            return ['success' => false, 'message' => 'Mã nhân viên không hợp lệ'];
        }

        // 3. Validate ngày làm việc
        if (!strtotime($data['work_date'])) {
            return ['success' => false, 'message' => 'Định dạng ngày làm việc không hợp lệ'];
        }

        if (strtotime($data['work_date']) > strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'Ngày làm việc không thể ở tương lai'];
        }

        // 4. Validate giờ check-in/check-out
        if (!empty($data['check_in']) && !$this->isValidTime($data['check_in'])) {
            return ['success' => false, 'message' => 'Định dạng giờ Check-in không hợp lệ (HH:MM)'];
        }

        if (!empty($data['check_out']) && !$this->isValidTime($data['check_out'])) {
            return ['success' => false, 'message' => 'Định dạng giờ Check-out không hợp lệ (HH:MM)'];
        }

        // 5. Check-out phải sau check-in
        if (!empty($data['check_in']) && !empty($data['check_out'])) {
            if (strtotime($data['check_out']) <= strtotime($data['check_in'])) {
                return ['success' => false, 'message' => 'Giờ Check-out phải sau giờ Check-in'];
            }
        }

        // 6. Validate trạng thái
        if (!in_array($data['status'], $this->validStatuses)) {
            return ['success' => false, 'message' => 'Trạng thái điểm danh không hợp lệ'];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Kiểm tra định dạng giờ hợp lệ (HH:MM)
     */
    private function isValidTime(string $time): bool
    {
        return (bool)preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    }
}
