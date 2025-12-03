<?php
class AuthController extends Controller
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (!$username || !$password) {
                $this->view('auth/register', ['error' => 'Vui lòng điền đầy đủ thông tin.']);
                return;
            }

            $userModel = $this->model('User');

            if ($userModel->findByUsername($username)) {
                $this->view('auth/register', ['error' => 'Username đã tồn tại!']);
                return;
            }

            if ($userModel->register($username, $password)) {
                $_SESSION['flash'] = 'Đăng ký thành công! Mời login.';
                $this->redirect(BASE_URL . '/auth/login');
            } else {
                $this->view('auth/register', ['error' => 'Lỗi hệ thống!']);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (!$username || !$password) {
                $this->view('auth/login', ['error' => 'Vui lòng điền đầy đủ thông tin.']);
                return;
            }

            $userModel = $this->model('User');
            $user = $userModel->findByUsername($username);

            if (!$user) {
                $this->view('auth/login', ['error' => 'Username không tồn tại!']);
                return;
            }

            if (!password_verify($password, $user['password'])) {
                $this->view('auth/login', ['error' => 'Sai mật khẩu.']);
                return;
            }

            // ✔ lưu session user
            $_SESSION['user'] = [
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'] ?? null
            ];
            session_regenerate_id(true);

            $this->redirect(BASE_URL . '/dashboard');
        } else {
            $this->view('auth/login');
        }
    }

    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        $this->redirect(BASE_URL . '/auth/login');
    }
}
