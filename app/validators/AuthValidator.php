<?php

class AuthValidator
{
    /**
     * Validate dữ liệu đăng nhập
     *
     * @param array $data ['username' => '', 'password' => '']
     * @return array ['success' => bool, 'message' => string]
     */
    public function validateLogin(array $data): array
    {
        if (empty($data['username'])) {
            return ['success' => false, 'message' => 'Tên đăng nhập là bắt buộc'];
        }

        if (empty($data['password'])) {
            return ['success' => false, 'message' => 'Mật khẩu là bắt buộc'];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Validate dữ liệu đăng ký hoặc tạo user mới
     *
     * @param array $data ['username','email','password','confirm_password','full_name','role_id']
     */
    public function validateRegister(array $data): array
    {
        $required = ['username', 'email', 'password', 'confirm_password', 'full_name', 'role_id'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Trường '{$field}' là bắt buộc"];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }

        if ($data['password'] !== $data['confirm_password']) {
            return ['success' => false, 'message' => 'Mật khẩu và xác nhận mật khẩu không trùng khớp'];
        }

        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Validate đổi mật khẩu
     *
     * @param array $data ['old_password','new_password','confirm_password']
     */
    public function validateChangePassword(array $data): array
    {
        $required = ['old_password', 'new_password', 'confirm_password'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Trường '{$field}' là bắt buộc"];
            }
        }

        if ($data['new_password'] !== $data['confirm_password']) {
            return ['success' => false, 'message' => 'Mật khẩu mới và xác nhận mật khẩu không trùng khớp'];
        }

        if (strlen($data['new_password']) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Validate email cho reset password
     *
     * @param string $email
     */
    public function validateEmail(string $email): array
    {
        if (empty($email)) {
            return ['success' => false, 'message' => 'Email là bắt buộc'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }
}
