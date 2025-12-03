<?php
require_once __DIR__ . "/User.php";
require_once __DIR__ . "/EmailService.php";

class UserService
{
    private User $userModel;

    // ===========================
    // 1. Đăng nhập người dùng
    // ===========================
    public function login(string $username, string $password): ?array
    {
        $user = $this->userModel->authenticate($username, $password);
        if ($user) {
            // Bạn có thể lưu session ở đây
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role_id' => $user['role_id']
            ];
        }
        return $user;
    }

    // ===========================
    // 2. Đăng xuất
    // ===========================
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    // ===========================
    // 3. Tạo người dùng mới
    // ===========================
    public function createUser(array $data): bool
    {
        return $this->userModel->insertUser($data);
    }

    // ===========================
    // 4. Cập nhật người dùng
    // ===========================
    public function updateUser(int $id, array $data): bool
    {
        return $this->userModel->updateUser($id, $data);
    }

    // ===========================
    // 5. Xóa người dùng
    // ===========================
    public function deleteUser(int $id): bool
    {
        return $this->userModel->deleteUser($id);
    }

    // ===========================
    // 6. Thay đổi mật khẩu
    // ===========================
    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->userModel->getUserById($userId);
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return false;
        }

        return $this->userModel->updateUser($userId, ['password' => $newPassword]);
    }

    // ===========================
    // 8. Lấy danh sách tất cả người dùng
    // ===========================
    public function getAllUsers(): array
    {
        return $this->userModel->getAllUsers();
    }

    // ===========================
    // 9. Lấy thông tin người dùng theo ID
    // ===========================
    public function getUserById(int $id): ?array
    {
        return $this->userModel->getUserById($id);
    }
}
