<?php
// Cấu hình kết nối MySQL
$servername = "localhost";
$username = "appuser";
$password = "ted1234";
$dbname = "db_employee_infomation_manager";

// Tạo kết nối mysqli

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra lỗi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Charset UTF-8
$conn->set_charset("utf8");
