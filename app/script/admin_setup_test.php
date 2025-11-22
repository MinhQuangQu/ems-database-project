<?php
// script/run_admin_setup.php

// Simple database connection
function getSimpleDBConnection() {
    $servername = "localhost";
    $username = "appuser";
    $password = "ted1234";
    $dbname = "db_employee_infomation_manager";

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

echo "Running Admin Setup Script\n";

$conn = getSimpleDBConnection();

// Create admin table if not exists
$createTableSQL = "CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'manager', 'viewer') DEFAULT 'manager',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($createTableSQL)) {
    echo "Admin table ready\n";
} else {
    echo "Failed to create table: " . $conn->error . "\n";
}

// Check if admin already exists
$checkSQL = "SELECT admin_id FROM admin WHERE username = 'admin'";
$result = $conn->query($checkSQL);

if ($result && $result->num_rows > 0) {
    echo "Admin account already exists\n";
} else {
    // Create default admin
    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    $insertSQL = "INSERT INTO admin (username, name, email, password, role) 
                  VALUES ('admin', 'System Administrator', 'admin@company.com', ?, 'super_admin')";
    
    $stmt = $conn->prepare($insertSQL);
    $stmt->bind_param("s", $passwordHash);
    
    if ($stmt->execute()) {
        echo "Default admin created successfully\n";
        echo "Username: admin\n";
        echo "Password: 123456\n";
    } else {
        echo "Failed to create admin: " . $stmt->error . "\n";
    }
    
    $stmt->close();
}

$conn->close();
echo "Setup completed!\n";