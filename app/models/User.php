<?php
class User
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Lấy user theo username
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Đăng ký user
    public function register(string $username, string $password): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO users (username, password)
            VALUES (:username, :password)
        ");
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([
            'username' => $username,
            'password' => $hashed
        ]);
    }
}
