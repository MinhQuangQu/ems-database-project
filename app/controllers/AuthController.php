<?php
class AuthController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->initSession();
    }

    /**
     * Khởi tạo session an toàn
     */
    private function initSession() {
        if(session_status() === PHP_SESSION_NONE) {
            // Cấu hình session an toàn hơn
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1); // Nếu dùng HTTPS
            session_start();
            
            // Regenerate session ID để chống fixation attack
            if (empty($_SESSION['admin_id'])) {
                session_regenerate_id(true);
            }
        }
    }

    /**
     * Hiển thị form login
     */
public function login() {
    // Nếu đã login → redirect dashboard
    if($this->isAuthenticated()) {
        header("Location: /CSDL/public/index.php?path=/dashboard");
        exit;
    }

    $error = $_SESSION['login_error'] ?? null;
    $username = $_SESSION['login_username'] ?? '';
    unset($_SESSION['login_error'], $_SESSION['login_username']);

    include __DIR__ . '/../../resource/views/auth/login.php';
}
    /**
     * Xử lý login form submit
     */
public function authenticate() {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // DEBUG
    error_log("Username: " . $username);
    error_log("Password length: " . strlen($password));

    if(empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Vui lòng điền đầy đủ thông tin!";
        header("Location: /CSDL/public/index.php?path=/login");
        exit;
    }

    $stmt = $this->conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()) {
        error_log("User found in database");
        error_log("Stored password hash: " . $row['password']);
        
        if(password_verify($password, $row['password'])) {
            error_log("Password verified successfully");
            
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['authenticated'] = true;
            
            error_log("Session variables set");
            
            // SỬA REDIRECT THÀNH ABSOLUTE PATH
            $redirect_url = "/CSDL/public/index.php?path=/dashboard";
            error_log("Redirecting to: " . $redirect_url);
            
            header("Location: " . $redirect_url);
            exit;
            
        } else {
            error_log("Password verification failed");
            $_SESSION['login_error'] = "Sai mật khẩu!";
        }
    } else {
        error_log("User not found in database");
        $_SESSION['login_error'] = "Tài khoản không tồn tại!";
    }

    header("Location: /CSDL/public/index.php?path=/login");
    exit;
}

    /**
     * Lấy thông tin user bằng username
     */
    private function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id, username, password, full_name, is_active FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Xác thực mật khẩu
     */
    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Tạo session cho user
     */
    private function createUserSession($user) {
        // Xóa session cũ hoàn toàn
        $_SESSION = [];
        
        // Tạo session mới
        $_SESSION['admin_id'] = $user['admin_id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = time();

        error_log("Session after login: " . print_r($_SESSION, true));
        
        // Regenerate session ID sau khi login thành công
        session_regenerate_id(true);
    }

    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    public function isAuthenticated() {
        return isset($_SESSION['authenticated']) && 
               $_SESSION['authenticated'] === true &&
               isset($_SESSION['admin_id']);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function getCurrentUser() {
        if(!$this->isAuthenticated()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['admin_id'],
            'name' => $_SESSION['admin_name']
        ];
    }

    /**
     * Logout
     */
    public function logout() {
        // Xóa tất cả session variables
        $_SESSION = [];

        // Xóa session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Hủy session
        session_destroy();
        
        header("Location: /CSDL/public/index.php?path=/login");
        exit;
    }
    /**
     * Ghi log đăng nhập (tuỳ chọn)
     */
    private function logLoginAttempt($username, $success) {
        // Có thể ghi vào file log hoặc database
        $logMessage = sprintf(
            "[%s] Login attempt: username=%s, success=%s, IP=%s",
            date('Y-m-d H:i:s'),
            $username,
            $success ? 'true' : 'false',
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        );
        
        error_log($logMessage);
    }
}
?>