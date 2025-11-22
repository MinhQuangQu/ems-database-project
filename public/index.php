<?php
// public/index.php

// DEBUG: Log all requests
error_log("=== NEW REQUEST ===");
error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
error_log("QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'N/A'));
error_log("PATH: " . ($_GET['path'] ?? 'N/A'));
error_log("SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A'));

// Rest of your code...

// Khởi tạo session
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true
    ]);
}

// Debug info
error_log("=== ACCESS LOG ===");
error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? ''));
error_log("PATH: " . ($_GET['path'] ?? ''));
error_log("SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? ''));

// Load configuration và database connection
try {
    // Kiểm tra file config tồn tại
    $configPath = __DIR__ . '/../config/database.php';
    if (!file_exists($configPath)) {
        throw new Exception("Database configuration file not found: " . $configPath);
    }
    
    $config = require $configPath;
    
    // Kết nối database
    $conn = new mysqli(
        $config['host'],
        $config['username'], 
        $config['password'],
        $config['database'],
        $config['port']
    );
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset và timezone
    $conn->set_charset($config['charset']);
    $conn->query("SET time_zone = '{$config['timezone']}'");
    
    // Set options
    foreach ($config['options'] as $option => $value) {
        $conn->options($option, $value);
    }
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Load controllers
$controllersPath = __DIR__ . '/../app/controllers/';
$controllerFiles = [
    'AuthController.php',
    'DashboardController.php',
    'EmployeeController.php', 
    'DepartmentController.php'
];

foreach ($controllerFiles as $file) {
    $filePath = $controllersPath . $file;
    if (file_exists($filePath)) {
        require_once $filePath;
    }
}

// Xử lý routing
$path = $_GET['path'] ?? '/';

try {
    switch ($path) {
        case '/':
        case '/login':
            $authController = new AuthController($conn);
            $authController->login();
            break;
            
        case '/authenticate':
            $authController = new AuthController($conn);
            $authController->authenticate();
            break;
            
        case '/dashboard':
            $dashboardController = new DashboardController($conn);
            echo $dashboardController->index();
            break;
            
        case '/employees':
            $employeeController = new EmployeeController($conn);
            $employeeController->index();
            break;
            
        case '/departments':
            $departmentController = new DepartmentController($conn);
            $departmentController->index();
            break;
            
        case '/logout':
            $authController = new AuthController($conn);
            $authController->logout();
            break;
            
        default:
            // Redirect based on authentication
            if (isset($_SESSION['admin_id']) && ($_SESSION['authenticated'] ?? false)) {
                header("Location: index.php?path=/dashboard");
            } else {
                header("Location: index.php?path=/login");
            }
            exit;
    }
} catch (Exception $e) {
    error_log("Routing error: " . $e->getMessage());
    
    // Hiển thị trang lỗi thân thiện
    if (isset($_SESSION['admin_id']) && ($_SESSION['authenticated'] ?? false)) {
        echo "<h1>System Error</h1><p>Please try again later.</p>";
    } else {
        header("Location: index.php?path=/login");
    }
}