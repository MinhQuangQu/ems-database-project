<?php
// Thông tin kết nối MySQL (host, user, pass)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_employee_infomation_management";

$conn = new mysqli($host, $user, $pass);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tạo database nếu chưa tồn tại
$sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8 COLLATE utf8_general_ci";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' ready.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Chọn database
$conn->select_db($dbname);

// Tạo bảng admin nếu chưa có
$sql = "CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
if ($conn->query($sql) === TRUE) {
    echo "Table 'admin' ready.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Hash password mặc định
$defaultPassword = password_hash("123456", PASSWORD_DEFAULT);

// Chèn admin mặc định nếu chưa tồn tại
$sql = "INSERT INTO `admin` (username, password, full_name, email)
        VALUES ('admin', '$defaultPassword', 'Admin System', 'admin@example.com')
        ON DUPLICATE KEY UPDATE username=username";

if ($conn->query($sql) === TRUE) {
    echo "Admin default user ready. <br>";
    echo "Username: admin <br>Password: 123456 <br>";
} else {
    die("Error inserting admin: " . $conn->error);
}

$conn->close();
?>
