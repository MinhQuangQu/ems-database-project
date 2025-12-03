<?php

// Load config database
$configPath = __DIR__ . '/../config/database.php';

if (!file_exists($configPath)) {
    throw new Exception("Config file not found: $configPath");
}

// File config trả về $conn là PDO
return require $configPath;
