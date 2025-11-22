<?php

declare(strict_types=1);

class Admin 
{
    private mysqli $conn;
    private string $table = "admins";

    public int $admin_id;
    public string $username;
    public string $password;
    public string $full_name;

    public function __construct(mysqli $db) {
        $this->conn = $db;
    }

    /**
     * Lấy admin theo username
     */
    public function getByUsername(string $username): ?array 
    {
        try {
            $sql = "SELECT admin_id, username, password, full_name, created_at 
                    FROM {$this->table} 
                    WHERE username = ? AND deleted_at IS NULL 
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            $stmt->close();
            return null;
            
        } catch (Exception $e) {
            error_log("Admin getByUsername error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy admin theo ID
     */
    public function getById(int $admin_id): ?array 
    {
        try {
            $sql = "SELECT admin_id, username, full_name, created_at 
                    FROM {$this->table} 
                    WHERE admin_id = ? AND deleted_at IS NULL 
                    LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            $stmt->close();
            return null;
            
        } catch (Exception $e) {
            error_log("Admin getById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra đăng nhập
     */
    public function login(string $username, string $password): bool 
    {
        try {
            $admin = $this->getByUsername($username);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Khởi tạo session nếu chưa có
                if (session_status() === PHP_SESSION_NONE) {
                    session_start([
                        'cookie_httponly' => true,
                        'cookie_secure' => isset($_SERVER['HTTPS']),
                        'use_strict_mode' => true
                    ]);
                }
                
                // Regenerate session ID để chống fixation attack
                session_regenerate_id(true);
                
                // Lưu session information
                $_SESSION['admin_id'] = (int)$admin['admin_id'];
                $_SESSION['username'] = htmlspecialchars($admin['username']);
                $_SESSION['full_name'] = htmlspecialchars($admin['full_name']);
                $_SESSION['authenticated'] = true;
                $_SESSION['login_time'] = time();
                
                // Ghi log đăng nhập thành công
                $this->logLoginAttempt($username, true);
                
                return true;
            }
            
            // Ghi log đăng nhập thất bại
            $this->logLoginAttempt($username, false);
            return false;
            
        } catch (Exception $e) {
            error_log("Admin login error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo admin mới
     */
    public function create(string $username, string $password, string $full_name): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Kiểm tra username đã tồn tại chưa
            if ($this->isUsernameExists($username)) {
                throw new Exception("Username already exists");
            }

            // Validate input
            if (strlen($username) < 3 || strlen($username) > 50) {
                throw new Exception("Username must be between 3 and 50 characters");
            }

            if (strlen($password) < 6) {
                throw new Exception("Password must be at least 6 characters long");
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO {$this->table} (username, password, full_name, created_at) 
                    VALUES (?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_password, $full_name);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Admin create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật thông tin admin
     */
    public function update(int $admin_id, array $data): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Kiểm tra admin tồn tại
            $existing = $this->getById($admin_id);
            if (!$existing) {
                throw new Exception("Admin not found");
            }

            $sql = "UPDATE {$this->table} 
                    SET full_name = ?, updated_at = NOW() 
                    WHERE admin_id = ? AND deleted_at IS NULL";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $data['full_name'], $admin_id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Admin update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(int $admin_id, string $current_password, string $new_password): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Lấy thông tin admin
            $admin = $this->getById($admin_id);
            if (!$admin) {
                throw new Exception("Admin not found");
            }

            // Lấy password hash từ database
            $sql = "SELECT password FROM {$this->table} WHERE admin_id = ? AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin_data = $result->fetch_assoc();
            $stmt->close();

            // Verify current password
            if (!password_verify($current_password, $admin_data['password'])) {
                throw new Exception("Current password is incorrect");
            }

            // Validate new password
            if (strlen($new_password) < 6) {
                throw new Exception("New password must be at least 6 characters long");
            }

            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $sql = "UPDATE {$this->table} 
                    SET password = ?, updated_at = NOW() 
                    WHERE admin_id = ? AND deleted_at IS NULL";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $hashed_password, $admin_id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Admin changePassword error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa mềm admin (không cho xóa chính mình)
     */
    public function delete(int $admin_id, int $current_admin_id): bool 
    {
        $this->conn->begin_transaction();
        
        try {
            // Không cho xóa chính mình
            if ($admin_id === $current_admin_id) {
                throw new Exception("Cannot delete your own account");
            }

            // Kiểm tra admin tồn tại
            $existing = $this->getById($admin_id);
            if (!$existing) {
                throw new Exception("Admin not found");
            }

            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE admin_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Admin delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả admin (không bao gồm đã xóa)
     */
    public function getAll(): array 
    {
        try {
            $sql = "SELECT admin_id, username, full_name, created_at 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    ORDER BY admin_id ASC";
            
            $result = $this->conn->query($sql);
            $admins = [];
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $admins[] = [
                        'admin_id' => (int)$row['admin_id'],
                        'username' => htmlspecialchars($row['username']),
                        'full_name' => htmlspecialchars($row['full_name']),
                        'created_at' => $row['created_at']
                    ];
                }
            }
            
            return $admins;
            
        } catch (Exception $e) {
            error_log("Admin getAll error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra session đăng nhập
     */
    public static function isLoggedIn(): bool 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_id'], $_SESSION['authenticated']) && 
               $_SESSION['authenticated'] === true;
    }

    /**
     * Lấy thông tin admin từ session
     */
    public static function getCurrentUser(): ?array 
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'admin_id' => $_SESSION['admin_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name']
        ];
    }

    /**
     * Logout
     */
    public function logout(): void 
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
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
    }

    /**
     * Kiểm tra username đã tồn tại chưa
     */
    private function isUsernameExists(string $username, ?int $exclude_id = null): bool 
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM {$this->table} 
                    WHERE username = ? AND deleted_at IS NULL";
            
            $params = [$username];
            $types = "s";
            
            if ($exclude_id !== null) {
                $sql .= " AND admin_id != ?";
                $params[] = $exclude_id;
                $types .= "i";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("Admin isUsernameExists error: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Ghi log đăng nhập
     */
    private function logLoginAttempt(string $username, bool $success): void 
    {
        $logMessage = sprintf(
            "[%s] Login attempt: username=%s, success=%s, IP=%s, User-Agent=%s",
            date('Y-m-d H:i:s'),
            $username,
            $success ? 'true' : 'false',
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        error_log($logMessage);
    }
}