<?php
$config = require __DIR__ . '/../../config/config.php';
$base_url = $config['base_url'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'EMS Dashboard' ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Nếu muốn dùng custom CSS thêm -->
    <link rel="stylesheet" href="/CSDL/public/assets/css/dashboard.css">
</head>

<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen">
