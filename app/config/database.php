<?php
// Bắt đầu session nếu cần


// Cấu hình database
$dbHost = '127.0.0.1';
$dbName = 'db_employee_infomation_manager';
$dbUser = 'appuser';
$dbPass = 'ted1234';
$dbCharset = 'utf8mb4';
$dbPort = 3306;

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$dbCharset;port=$dbPort";

try {
    $conn = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Trả về kết nối PDO
return $conn;
