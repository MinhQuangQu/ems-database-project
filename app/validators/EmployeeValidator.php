<?php

class EmployeeValidator
{
    /**
     * Validate dữ liệu nhân viên
     *
     * @param array $data
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function validate(array $data): array
    {
        $required = ['full_name', 'gender', 'date_of_birth', 'email', 'phone_number', 'department_id'];

        // 1. Kiểm tra các trường bắt buộc
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Trường '{$field}' là bắt buộc"];
            }
        }

        // 2. Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }

        // 3. Validate số điện thoại (VD: chỉ chứa số, 9-12 ký tự)
        if (!preg_match('/^\d{9,12}$/', $data['phone_number'])) {
            return ['success' => false, 'message' => 'Số điện thoại không hợp lệ'];
        }

        // 4. Validate gender
        $validGenders = ['male', 'female', 'other'];
        if (!in_array(strtolower($data['gender']), $validGenders)) {
            return ['success' => false, 'message' => 'Giới tính không hợp lệ'];
        }

        // 5. Validate date_of_birth
        if (!strtotime($data['date_of_birth'])) {
            return ['success' => false, 'message' => 'Ngày sinh không hợp lệ'];
        }

        if (strtotime($data['date_of_birth']) > strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'Ngày sinh không thể ở tương lai'];
        }

        // 6. Validate department_id
        if (!is_numeric($data['department_id']) || (int)$data['department_id'] <= 0) {
            return ['success' => false, 'message' => 'Mã phòng ban không hợp lệ'];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }
}
